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

    function staffSelector()
    {
        $therapists = $this->listStaff();

        $form = new Zend_Form;
        $form->setMethod("GET");
        $form->addElement('select', 'staff', array(
            'label' => 'Staff',
            'multiOptions' => array('All' => 'All') + $therapists,
            'value' => $this->_getParam('staff') == 'All' ? null : $this->_getParam('staff')
        ));
        $form->addElement('submit', 'submitbutton', array(
            'label' => 'Go',
            'class'=>'btn'
        ));
        return $form;
    }

    function listStaff()
    {
        $db = Zend_Registry::get('db');
        $staffResult = $db->select()
            ->from('user')
            ->where('type=?', 'staff')
            ->query()->fetchAll();

        $staff = array();
        foreach ($staffResult as $staffResult) {
            $staff[$staffResult['id']] = $staffResult['username'];
        }
        return $staff;
    }

    /**
     * Get the availability for staff for a specific day [and optionally for specific staff(s)]
     * @param $dayOfWeek integer 1 (for Monday) through 7 (for Sunday)
     * @param mixed $staff - null for all, integer for specific staff, array of user IDs for specific staff(s).
     * @return array database rows representing time(s) available for this day.
     */
    function selectAvailability($dayOfWeek, $staff = null)
    {
        if(is_array($staff) && !count($staff)) {
            return array();
        }
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('availability', array(
                'id',
                'user_id' => 'staff_userid',
                'day_of_week',
                'start',
                'end'
            ))
            ->where('day_of_week=?', $dayOfWeek);
        if (is_array($staff)) {
            $select->where('staff_userid IN ('. implode(',', $staff).')');
        }else if ($staff) {
            $select->where('staff_userid=?', $staff);
        }

        return $db->query($select)->fetchAll();
    }

    function removeMassageBookingsFrom($availability, $dayString, $filterByTherapist = null)
    {
        $availabilityModel = new MassageAvailability($availability);

        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from('appointments')
            ->where('date=?', date('Y-m-d', strtotime($dayString)))
            ->where('canceled=0');
        if ($filterByTherapist) {
            $select->where('staff_userid=?', $filterByTherapist);
        }
        $bookings = $select->query()->fetchAll();

        foreach ($bookings as $bookingArray) {
            $booking = new Booking(array(
                'start' => $bookingArray['time'],
                'user_id' => $bookingArray['staff_userid'],
                'duration' => $bookingArray['duration']
            ));
            $availabilityModel->addBooking($booking);
        }

        return $availabilityModel;
    }

    function therapistData($therapistID)
    {
        return $this->db()->select()
            ->from('user')
            ->where('id=?',$therapistID)
            ->query()
            ->fetch();
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

    function cancelsLogger()
    {
        if (isset($this->logger)) {
            return $this->logger;
        }
        $this->logger = Zend_Log::factory(array(
            'timestampFormat' => 'Y-m-d h:i a',
            array(
                'writerName' => 'Stream',
                'writerParams' => array(
                    'stream' => 'var/cancels.log',
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