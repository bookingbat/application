<?php
class MakebookingController extends Controller
{
    protected $durationLabels = array(
        '30' => '30 Minutes',
        '60' => '1 Hour',
        '90' => '1.5 Hour',
        '120' => '2 Hours',
    );

    function preDispatch()
    {
        $user = bootstrap::getInstance()->getUser();
    }

    function bookingAction()
    {
        $this->view->step = 3;
        $this->render('progress',null,true);

        $service = $this->serviceDataMapper()->find($this->getParam('service'));

        $durations = array();
        foreach($service['durations'] as $duration) {
            $durations[$duration] = $this->durationLabels[$duration];
        }

        $form = new Zend_Form;
        $form->addElement('radio', 'appointment_duration', array(
            'label' => 'Appointment Duration',
            'multiOptions' => $durations
        ));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            return $this->_redirect($this->view->url(array(
                'action' => 'booking2',
                'appointment_duration' => $this->_getParam('appointment_duration')
            ),'make-booking'));
        }

        $this->view->form = $form;
        $this->render('booking', null, true);
    }

    function booking2Action()
    {
        $this->view->step = 4;
        $this->render('progress',null,true);

        $form = new BookingForm2;
        $form->getElement('appointment_duration')->setValue($this->_getParam('appointment_duration'));

        $day = date('N', strtotime($this->_getParam('day')));
        $availability = $this->selectAvailability($day, $this->getParam('service'), $this->getParam('staff'));

        $availabilityModel = $this->removeBookingsFrom($availability, $this->_getParam('day'), $this->_getParam('staff'));
        $availabilityModel->mergeOverlappingRanges();

        $form->setAvailability($availabilityModel->getAvailabilityTimes());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            return $this->_redirect($this->view->url(array(
                'action' => 'booking3',
                'day' => $this->_getParam('day'),
                'time' => $this->_getParam('time'),
                'therapist' => $this->_getParam('therapist'),
            )));
        }

        $this->view->form = $form;
        $this->render('booking', null, true);
    }

    function booking3Action()
    {
        $this->view->step = 5;
        $this->render('progress',null,true);

        $form = new BookingForm3;

        $day = date('N', strtotime($this->_getParam('day')));
        $availabilityArray = $this->selectAvailability($day, $this->getParam('service'), $this->_getParam('staff'));
        $availabilityModel = $this->removeBookingsFrom($availabilityArray, $this->_getParam('day'), $this->_getParam('staff'));

        $form->setAvailability($availabilityModel->getAvailabilityTimes());
        $form->populate($this->getRequest()->getParams());

        $booking = new Booking(array(
            'start' => $this->_getParam('time'),
            'duration' => $this->_getParam('appointment_duration')
        ));


        $availabilityObject = new \Bookingbat\Engine\Availability($availabilityModel->getAvailabilityTimes());
        $possibleUserIdsForBooking = $availabilityObject->possibleUserIdsForBooking($booking);

        if(!$possibleUserIdsForBooking) {
            throw new Exception('No staff available to take this appointment');
        }

        $db = Zend_Registry::get('db');

        $stafResult = $db->select()
            ->from('user')
            ->where('type=?', 'staff')
            ->where('id IN(' . implode(',', $possibleUserIdsForBooking) . ')')
            ->query()->fetchAll();

        $staff = array();
        foreach ($stafResult as $stafResult) {
            $staff[$stafResult['id']] = $stafResult['username'];
        }

        $form->getElement('staff')->setMultiOptions($staff);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            return $this->_redirect($this->view->url(array(
                'action' => 'booking4',
                'staff' => $this->_getParam('staff'),
            )));
        }

        $this->view->form = $form;
        $this->render('booking', null, true);
    }

    function booking4Action()
    {


        $form = new BookingForm4;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {

            $this->view->step = 999;
            $this->render('progress',null,true);

            $db = Zend_Registry::get('db');
            $db->insert('appointments', array(
                'staff_userid' => $this->_getParam('staff'),
                'user_id' =>0,
                'date' => $this->_getParam('day'),
                'time' => $this->getParam('time'),
                'duration' => $this->getParam('appointment_duration'),
                'guest_name' => $form->getValue('name'),
                'guest_email' => $form->getValue('email'),
                'guest_phone' => $form->getValue('phone'),
            ));

            $therapistData = $this->staffData($this->_getParam('staff'));

            $this->view->therapist = $therapistData;
            $this->view->date = $this->getParam('day');
            $this->view->time = $this->getParam('time');
            $this->view->duration = $this->getParam('appointment_duration');
            $html = $this->view->render('appointments/appointment-confirmation.phtml');

            $mail = new Zend_Mail;
            //$mail->addTo($user['email']);
            $mail->addTo($therapistData['email']);
            $mail->setBodyText($html);
            $this->queueMail($mail);

            $this->view->message = $html;
            $this->render('booking-confirmation', null, true);
            return;
        }

        $this->view->step = 6;
        $this->render('progress',null,true);

        $this->view->form = $form;
        $this->render('booking', null, true);
    }

}