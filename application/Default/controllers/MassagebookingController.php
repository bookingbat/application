<?php
class MassagebookingController extends Controller
{
    function preDispatch()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$userid = $user['id']) {
            return $this->_redirect('/');
        }

        $user = $this->userObjectForBillingCalculations();

        $massageUsed = $this->massageAppointmentsTotalDuration($userid);

        if ($user->isAtMaximum(array('massage' => $massageUsed))) {
            $this->_forward('massage', 'payment', null, array());
        }

        if ($this->_getParam('appointment_duration') && ($user->massageAllowed() < $massageUsed + $this->_getParam('appointment_duration'))) {
            $this->_redirect('/payment/massage');
        }
    }

    function bookingAction()
    {
        $form = new Zend_Form;
        $form->addElement('select', 'appointment_duration', array(
            'label' => 'Appointment Duration',
            'multiOptions' => array(
                '60' => '1 Hour',
                '90' => '1.5 Hour'
            )
        ));
        $form->addElement('submit', 'next', array('label' => 'Next'));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            return $this->_redirect($this->view->url(array(
                'action' => 'booking2',
                'appointment_duration' => $this->_getParam('appointment_duration')
            )));
        }

        $this->view->form = $form;
        $this->render('booking', null, true);
    }

    function booking2Action()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->_redirect('/');
        }

        $form = new MassageBookingForm2;
        $form->getElement('appointment_duration')->setValue($this->_getParam('appointment_duration'));
        //$form->getElement('therapist')->setValue($this->_getParam('therapist'));

        $availability = $this->selectMassageAvailability(date('N', strtotime($this->_getParam('day'))), $this->_getParam('therapist'));

        $availabilityModel = $this->removeMassageBookingsFrom($availability, $this->_getParam('day'), $this->_getParam('therapist'));
        $availabilityModel->mergeOverlappingRanges();

        $form->setAvailability($availabilityModel->availability);

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
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->_redirect('/');
        }

        $form = new MassageBookingForm3;
        $availabilityArray = $this->selectMassageAvailability(date('N', strtotime($this->_getParam('day'))), $this->_getParam('therapist'));
        $availabilityModel = $this->removeMassageBookingsFrom($availabilityArray, $this->_getParam('day'), $this->_getParam('therapist'));

        $form->setAvailability($availabilityModel->availability);
        $form->populate($this->getRequest()->getParams());

        $booking = new Booking(array(
            'start' => $this->_getParam('time'),
            'duration' => $this->_getParam('appointment_duration')
        ));


        $availabilityObject = new MassageAvailability($availabilityModel->availability);
        $possibleUserIdsForBooking = $availabilityObject->possibleUserIdsForBooking($booking);

        if(!$possibleUserIdsForBooking) {
            throw new Exception('No therapists available to take this appointment');
        }

        $db = Zend_Registry::get('db');

        $therapistsResult = $db->select()
            ->from('user')
            ->where('type=?', 'massage-therapist')
            ->where('id IN(' . implode(',', $possibleUserIdsForBooking) . ')')
            ->query()->fetchAll();

        $therapists = array();
        foreach ($therapistsResult as $therapistsResult) {
            $therapists[$therapistsResult['id']] = $therapistsResult['username'];
        }

        $form->getElement('therapist')->setMultiOptions($therapists);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {

            $db = Zend_Registry::get('db');
            $db->insert('therapist_appointments', array(
                'therapist_userid' => $this->_getParam('therapist'),
                'user_id' => $user['id'],
                'date' => $this->_getParam('day'),
                'time' => $form->getValue('time'),
                'duration' => $form->getValue('appointment_duration'),
            ));

            $therapistData = $this->therapistData($this->_getParam('therapist'));

            $this->view->therapist = $therapistData;
            $this->view->date = $this->_getParam('day');
            $this->view->time = $form->getValue('time');
            $this->view->duration = $form->getValue('appointment_duration');
            $html = $this->view->render('massage/appointment-confirmation.phtml');

            $mail = new Zend_Mail;
            $mail->addTo($user['email']);
            $mail->addTo($therapistData['email']);
            $mail->setBodyText($html);
            $this->queueMail($mail);
            echo $html;

            $this->_helper->viewRenderer->setNoRender(true);
            return;
        }

        $this->view->form = $form;
        $this->render('booking', null, true);
    }

}