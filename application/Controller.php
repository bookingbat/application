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

    function staffSelector($allOption=true, $serviceId=null)
    {
        $staff = $this->listStaff($serviceId);

        $form = new Zend_Form;
        $form->setMethod("GET");
        if($allOption) {
            $form->addElement('select', 'staff', array(
                'label' => 'Staff',
                'multiOptions' => array('All' => 'All') + $staff,
                'value' => $this->_getParam('staff') == 'All' ? null : $this->_getParam('staff')
            ));
        } else {
            $form->addElement('select', 'staff', array(
                'label' => 'Staff',
                'multiOptions' => $staff,
                'value' => $this->_getParam('staff')
            ));
        }
        $form->addElement('submit', 'submitbutton', array(
            'label' => 'Go',
            'class'=>'btn'
        ));
        return $form;
    }

    function listStaff($serviceId=null)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('user')
            ->where('type=?', 'staff');

        if($serviceId) {
            $condition = 'staff_services.staff_user_id = user.id && staff_services.service_id = '.(int)$serviceId;
            $select->joinRight('staff_services', $condition, array());
        }

        $staffResult = $select->query()
            ->fetchAll();

        $staff = array();
        foreach ($staffResult as $staffResult) {
            $staff[$staffResult['id']] = $staffResult['username'];
        }
        return $staff;
    }

    /**
     * Get the availability for a service [optionally for specific staff(s)]
     * @param $dayOfWeek integer 1 (for Monday) through 7 (for Sunday)
     * @param integer $service - service ID to get availability for
     * @param mixed $filterByTherapist - null for all, integer for specific staff, array of user IDs for specific staff(s).
     * @return array database rows representing time(s) available for this day.
     */
    function selectAvailability($dayOfWeek, $service=null, $filterByTherapist = null)
    {
        if(is_array($filterByTherapist) && !count($filterByTherapist)) {
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
        if (is_array($filterByTherapist)) {
            $select->where('staff_userid IN ('. implode(',', $filterByTherapist).')');
        }else if ($filterByTherapist) {
            $select->where('staff_userid=?', $filterByTherapist);
        }

        if($service) {
            // get availability of only the staff that do this service
            $select->where('staff_userid IN (?)', new Zend_Db_Expr('select staff_user_id from staff_services where service_id='.(int)$service));
        }

        return $db->query($select)->fetchAll();
    }

    function removeBookingsFrom($availability, $dayString, $filterByTherapist = null)
    {
        $availabilityModel = new \Bookingbat\Engine\Availability($availability);

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

    function staffData($therapistID)
    {
        return $this->db()->select()
            ->from('user')
            ->where('id=?',$therapistID)
            ->query()
            ->fetch();
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

    function queueMail($mail)
    {
        $mail->setFrom('no-reply@bookingbat.com','Appointment Confirmation');
        $this->db()->insert('email_queue', array(
            'data'=>serialize($mail)
        ));
    }
}