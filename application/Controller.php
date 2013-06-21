<?php
abstract class Controller extends Zend_Controller_Action
{
    function init()
    {
        $user = bootstrap::getInstance()->getUser();
        if ($user) {
            $this->updateUserDataIntoSession($user['username']);
        }
    }

    function updateUserDataIntoSession($username)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('user')
            ->where('username=?', $username);

        $user = $select->query()->fetch();

        Zend_Registry::set('user', $user);
        bootstrap::getInstance()->getSession()->user = $user;
    }

    function userData($id=null)
    {
        $select = $this->db()->select()
            ->from('user')
            ->where('id=?', $id ? $id : $this->_getParam('id'));
        return $select->query()->fetch();
    }

    function trainingAppointmentsTotalDuration($userID)
    {
        return $this->lister($userID)->trainingAppointmentsTotalDuration();
    }

    function trainingAppointments($userID)
    {
        return $this->lister($userID)->trainerAppointments();
    }

    function massageAppointmentsTotalDuration($userID)
    {
        return $this->lister($userID)->massageAppointmentsTotalDuration();
    }

    function lister($userId)
    {
        $lister = new AppointmentsLister;
        $lister->month($this->month());
        $lister->year($this->year());
        $lister->userId($userId);
        return $lister;
    }

    function db()
    {
        return Zend_Registry::get('db');
    }

    function month()
    {
        return $this->_getParam('month') ? $this->_getParam('month') : date('m');
    }

    function year()
    {
        return $this->_getParam('year') ? $this->_getParam('year') : date('Y');
    }

    /**
     * Get the availability for massages for a specific day [and optionally for specific therapist(s)]
     * @param $dayOfWeek integer 1 (for Monday) through 7 (for Sunday)
     * @param mixed $therapist - null for all, integer for specific therapist, array of therapist IDs for specific therapist(s).
     * @return array database rows representing time(s) available for this day.
     */
    function selectMassageAvailability($dayOfWeek, $therapist = null)
    {
        if(is_array($therapist) && !count($therapist)) {
            return array();
        }
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('therapist_availability', array('id', 'user_id' => 'therapist_userid', 'day_of_week', 'start', 'end'))
            ->where('day_of_week=?', $dayOfWeek);
        if (is_array($therapist)) {
            $select->where('therapist_userid IN ('. implode(',', $therapist).')');
        }else if ($therapist) {
            $select->where('therapist_userid=?', $therapist);
        }

        return $db->query($select)->fetchAll();
    }

    function removeMassageBookingsFrom($availability, $dayString, $filterByTherapist = null)
    {
        $availabilityModel = new MassageAvailability($availability);

        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from('therapist_appointments')
            ->where('date=?', date('Y-m-d', strtotime($dayString)))
            ->where('canceled=0');
        if ($filterByTherapist) {
            $select->where('therapist_userid=?', $filterByTherapist);
        }
        $bookings = $select->query()->fetchAll();

        foreach ($bookings as $bookingArray) {
            $booking = new Booking(array(
                'start' => $bookingArray['time'],
                'user_id' => $bookingArray['therapist_userid'],
                'duration' => $bookingArray['duration']
            ));
            $availabilityModel->addBooking($booking);
        }

        return $availabilityModel;
    }

    /** Get the trainer the admin assigned to this client. If none set, gets the master trainer for this client's condo */
    function assignedTrainerForUser()
    {
        $user = bootstrap::getInstance()->getUser();
        $db = Zend_Registry::get('db');
        $trainerArray = $db->select()
            ->from('user', array())
            ->where('user.id=?', $user['id'])
            ->joinLeft('user as t', 't.id = user.assigned_trainer_userid', array('trainer' => 't.username', 't.id'))
            ->limit(1)
            ->query()->fetch();
        $trainer = $trainerArray['id'];
        if ($trainer) {
            return $trainer;
        }
        return $this->masterTrainerForUser();
    }

    /** Get the master trainer for the condo this client is at */
    function masterTrainerForUser()
    {
        $user = bootstrap::getInstance()->getUser();

        $trainer = $this->masterTrainer($user['condo_id']);
        if (!$trainer) {
            throw new Exception('No master trainer set!');
        }
        return $trainer;
    }

    function trainerData($trainerUserId)
    {
        return $this->db()->select()
            ->from('user')
            ->where('id=?',$trainerUserId)
            ->query()
            ->fetch();
    }

    function therapistData($therapistID)
    {
        return $this->db()->select()
            ->from('user')
            ->where('id=?',$therapistID)
            ->query()
            ->fetch();
    }

    function masterTrainer($condo_id)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('condo', array('master_trainer_userid'))
            ->where('id=?', $condo_id);
        return $select->query()->fetchColumn();
    }

    function selectTrainerAvailability($dayOfWeek, $userID=null)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('trainer_availability')
            ->where('day_of_week=?', $dayOfWeek)
            ->where('trainer_userid=?', $userID ? $userID : $this->assignedTrainerForUser());

        return $db->query($select)->fetchAll();
    }

    function removeTrainerBookingsFrom($availability, $dayString, $trainer)
    {
        $availabilityModel = new Availability($availability);
        $db = Zend_Registry::get('db');

        $bookings = $db->select()
            ->from('trainer_appointments')
            ->where('date=?', date('Y-m-d', strtotime($dayString)))
            ->where('trainer_userid=?', $trainer)
            ->where('canceled=0')
            ->query()->fetchAll();

        foreach ($bookings as $bookingArray) {
            $booking = new Booking(array(
                'start' => $bookingArray['time'],
                'duration' => $bookingArray['duration']
            ));

            $availabilityModel->addBooking($booking);
        }
        return $availabilityModel;
    }

    function userObjectForBillingCalculations($userId = null)
    {
        if($userId==null) {
            $user = bootstrap::getInstance()->getUser();
        } else {
            $user = $this->userData($userId);
        }

        $additionalTraining = $this->db()->select()
            ->from('user_payments', array(new Zend_Db_Expr('SUM(service_quantity)')))
            ->where('user_id=?', $user['id'])
            ->where('MONTH(datetime)=?', date('m'))
            ->where('YEAR(datetime)=?', date('Y'))
            ->where('service=?', 'training')
            ->query()->fetchColumn();

        $additionalMassage = $this->db()->select()
            ->from('user_payments', array(new Zend_Db_Expr('SUM(service_quantity)')))
            ->where('user_id=?', $user['id'])
            ->where('MONTH(datetime)=?', date('m'))
            ->where('YEAR(datetime)=?', date('Y'))
            ->where('service=?', 'massage')
            ->query()->fetchColumn();

        $additionalClass = $this->db()->select()
            ->from('user_payments', array(new Zend_Db_Expr('SUM(service_quantity)')))
            ->where('user_id=?', $user['id'])
            ->where('MONTH(datetime)=?', date('m'))
            ->where('YEAR(datetime)=?', date('Y'))
            ->where('service=?', 'class')
            ->query()->fetchColumn();

        $user = new User($user);
        $user->paidForAdditional(array(
            'class' => $additionalClass,
            'training' => $additionalTraining,
            'massage' => $additionalMassage
        ));

        return $user;
    }

    function listTrainers()
    {
        $db = Zend_Registry::get('db');
        $select = $db->select(array('id', 'first_name', 'last_name'))
            ->from('user')
            ->where('type=?', 'trainer');
        $trainers = array();
        foreach ($select->query()->fetchAll() as $trainer) {
            $trainers[$trainer['id']] = $trainer['first_name'] . ' ' . $trainer['last_name'] . ' (' . $trainer['username'] . ')';
        }
        return $trainers;
    }

    function logger()
    {
        if (isset($this->logger)) {
            return $this->logger;
        }
        $this->logger = Zend_Log::factory(array(
            'timestampFormat' => 'Y-m-d h:i a',
            array(
                'writerName' => 'Stream',
                'writerParams' => array(
                    'stream' => FAMEFIT_BASE_PATH . '/famefit.log',
                ),
                'formatterName' => 'Simple',
                'formatterParams' => array(
                    'format' => '%timestamp%: %message% -- %info%',
                )
            ),
        ));
        return $this->logger;
    }

    function startSubscription($values)
    {
        try {
            $subscription = new AuthnetARB(bootstrap::getInstance()->authorizenetLogin(), bootstrap::getInstance()->authorizenetKey(), AuthnetARB::USE_DEVELOPMENT_SERVER);

            foreach ($values as $key => $value) {
                $subscription->setParameter($key, $value);
            }

            $subscription->setParameter('amount', $values['amount']);
            $subscription->setParameter('interval_length', 1);
            $subscription->setParameter('startDate', date("Y-m-d"));

            $subscription->createAccount();

            // Check the results of our API call
            if ($subscription->isSuccessful()) {
                // Get the subscription ID
                return $subscription->getSubscriberID();
            } else {
                // The subscription was not created!
                $this->error_message = $subscription->getResponse();
                return false;
            }
        } catch (AuthnetARBException $e) {
            $this->error_message = 'There was a problem communicating with our billing API. Please contact us.';
            $this->logger()->log($e->getMessage(), Zend_Log::CRIT);
            return false;
        }
    }

    function queueMail($mail)
    {
        $mail->setFrom('no-reply@famefitness.com','Fame Fitness');
        $this->db()->insert('email_queue', array(
            'data'=>serialize($mail)
        ));
    }
}