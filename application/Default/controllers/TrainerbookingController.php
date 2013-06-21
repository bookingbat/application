<?php
class TrainerbookingController extends Controller
{
    function preDispatch()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$userid = $user['id']) {
            return $this->_redirect('/');
        }

        $user = $this->userObjectForBillingCalculations();

        $trainingUsed = $this->trainingAppointmentsTotalDuration($userid);
        if ($user->isAtMaximum(array('training' => $trainingUsed))) {
            $this->_forward('training', 'payment', null, array());
        }

        if ($this->_getParam('appointment_duration') && ($user->trainingAllowed() < $trainingUsed + $this->_getParam('appointment_duration'))) {
            echo 'this appointment would put you over the max.';
            exit();
        }
    }

    function bookingAction()
    {
        $form = new Zend_Form;
        $form->addElement('radio', 'appointment_duration', array(
            'label' => 'Appointment Duration',
            'multiOptions' => array(
                '30' => 'Half Hour',
                '60' => 'One Hour'
            ),
            'separator'=>''
        ));
        $form->addElement('submit', 'next', array(
            'label' => 'Next',
            'class' => 'btn btn-primary'
        ));

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

        $form = new BookingForm;
        $form->getElement('appointment_duration')->setValue($this->_getParam('appointment_duration'));
        $form->getElement('trainer')->setValue($this->assignedTrainerForUser());

        $availability = $this->selectTrainerAvailability(date('N', strtotime($this->_getParam('day'))));
        $availabilityModel = $this->removeTrainerBookingsFrom($availability, $this->_getParam('day'), $this->assignedTrainerForUser());
        $availability = $availabilityModel->getAvailabilityTimes();
        $form->setAvailability($availability);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $trainerData = $this->trainerData($this->assignedTrainerForUser());

            $db = Zend_Registry::get('db');
            $db->insert('trainer_appointments', array(
                'trainer_userid' => $this->assignedTrainerForUser(),
                'user_id' => $user['id'],
                'date' => $this->_getParam('day'),
                'time' => $form->getValue('time'),
                'duration' => $form->getValue('appointment_duration'),
                'consultation' => $this->getParam('consultation',0)
            ));

            $this->view->trainer = $trainerData;
            $this->view->date = $this->_getParam('day');
            $this->view->time = $form->getValue('time');
            $this->view->duration = $form->getValue('appointment_duration');
            $html = $this->view->render('trainer/appointment-confirmation.phtml');

            $mail = new Zend_Mail;
            $mail->addTo($user['email']);
            $mail->addTo($trainerData['email']);
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