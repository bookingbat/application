<?php
class MassagebookingController extends Controller
{
    function preDispatch()
    {
        $user = bootstrap::getInstance()->getUser();
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
        $form = new MassageBookingForm2;
        $form->getElement('appointment_duration')->setValue($this->_getParam('appointment_duration'));

        $availability = $this->selectAvailability(date('N', strtotime($this->_getParam('day'))), $this->_getParam('therapist'));

        $availabilityModel = $this->removeBookingsFrom($availability, $this->_getParam('day'), $this->_getParam('therapist'));
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
        $form = new MassageBookingForm3;
        $availabilityArray = $this->selectAvailability(date('N', strtotime($this->_getParam('day'))), $this->_getParam('therapist'));
        $availabilityModel = $this->removeBookingsFrom($availabilityArray, $this->_getParam('day'), $this->_getParam('therapist'));

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
            ->where('type=?', 'staff')
            ->where('id IN(' . implode(',', $possibleUserIdsForBooking) . ')')
            ->query()->fetchAll();

        $therapists = array();
        foreach ($therapistsResult as $therapistsResult) {
            $therapists[$therapistsResult['id']] = $therapistsResult['username'];
        }

        $form->getElement('therapist')->setMultiOptions($therapists);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {

            $db = Zend_Registry::get('db');
            $db->insert('appointments', array(
                'staff_userid' => $this->_getParam('therapist'),
                'user_id' =>0,
                'date' => $this->_getParam('day'),
                'time' => $form->getValue('time'),
                'duration' => $form->getValue('appointment_duration'),
            ));

            $therapistData = $this->staffData($this->_getParam('therapist'));

            $this->view->therapist = $therapistData;
            $this->view->date = $this->_getParam('day');
            $this->view->time = $form->getValue('time');
            $this->view->duration = $form->getValue('appointment_duration');
            $html = $this->view->render('appointments/appointment-confirmation.phtml');

            $mail = new Zend_Mail;
            //$mail->addTo($user['email']);
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