<?php
class TrainerController extends Controller
{
    function availabilityAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'trainer') {
            return $this->_redirect('/');
        }

        $this->view->availability = array();
        for ($day = 1; $day <= 7; $day++) {
            $this->view->availability[$day] = $this->selectTrainerAvailability($day, $user['id']);
        }

        $db = Zend_Registry::get('db');

        if ($this->getRequest()->getParam('remove')) {
            $db->delete('trainer_availability', array(
                'id = ' . (int)$this->_getParam('remove')
            ));
            $this->_helper->FlashMessenger->addMessage('Deleted Availability');
            return $this->_redirect('/trainer/availability');
        }

        $form = new AvailabilityForm;
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $db->insert('trainer_availability', array(
                'trainer_userid' => $user['id'],
                'day_of_week' => $form->getValue('day'),
                'start' => $form->getValue('start'),
                'end' => $form->getValue('end'),
            ));
            $this->_helper->FlashMessenger->addMessage('Added Availability');
            return $this->_redirect('/trainer/availability');
        }
        $this->view->form = $form;

        $this->render('availability-manage', null, true);
    }

    function appointmentsAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'trainer') {
            return $this->_redirect('/');
        }

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('trainer_appointments')
            ->joinLeft('user', 'user.id=trainer_appointments.user_id', array('first_name', 'last_name', 'email', 'phone'))
            ->where('trainer_userid=?', $user['id']);

        $paginationAdapter = new Zend_Paginator_Adapter_DbSelect($select);
        $this->view->paginator = new Zend_Paginator($paginationAdapter);
        $this->view->paginator->setCurrentPageNumber($this->getParam('page'));
        $this->render('appointments-trainer');
    }

    function cancelAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->_redirect('/');
        }

        $db = Zend_Registry::get('db');
        $newValues = array('canceled' => 1);

        $condition = 'id=' . (int)$this->_getParam('id');
        if ($user['type'] == 'client') {
            $appointment_date = $db->select()
                ->from('trainer_appointments', array('date'))
                ->where('id=?', $this->_getParam('id'))
                ->query()->fetchColumn();

            $booking = new Booking(array(
                'today' => date('Y-m-d'),
                'date' => $appointment_date
            ));

            if($booking->allowCancelByUser()) {
                $newValues['lost_credit'] = 0;
            } elseif($booking->allowCancelLostByUser()) {
                $newValues['lost_credit'] = 1;
            } else {
                throw new Exception('not allowed to cancel');
            }

            $condition .= ' && user_id = ' . (int)$user['id'];
        } else if ($user['type'] == 'trainer') {
            $condition .= ' && trainer_userid = ' . (int)$user['id'];
        } else if ($user['type'] !== 'admin') {
            return $this->_redirect('/');
        }

        $trainerData = $this->trainerData($this->assignedTrainerForUser());

        $this->view->date = $appointment_date;
        $this->view->trainer_data = $trainerData;
        $this->view->user_data = $user;

        if(!$this->getParam('confirm')) {
            $this->view->id = $this->getParam('id');
            return $this->render('cancel-confirm');
        }

        $db->update('trainer_appointments', $newValues, $condition);

        $logMessage = 'Training appointment #' . (int)$this->_getParam('id');
        $logMessage .= ' cancelled by user #' . $user['id'];
        $this->logger()->log($logMessage, Zend_Log::INFO);

        $html = $this->view->render('trainer/appointment-cancel.phtml');

        $mail = new Zend_Mail;
        $mail->addTo($user['email']);
        $mail->addTo($trainerData['email']);
        $mail->setBodyText($html);
        $this->queueMail($mail);
        echo $html;
        $this->_helper->viewRenderer->setNoRender(true);
    }

}