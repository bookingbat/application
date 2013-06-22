<?php
class MassageController extends Controller
{


    function appointmentsAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'staff') {
            return $this->_redirect('/');
        }

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('therapist_appointments')
            ->joinLeft('user', 'user.id=therapist_appointments.user_id', array('first_name', 'last_name', 'email', 'phone'))
            ->where('therapist_userid=?', $user['id']);

        $paginationAdapter = new Zend_Paginator_Adapter_DbSelect($select);
        $this->view->show_delete_button = true;
        $this->view->paginator = new Zend_Paginator($paginationAdapter);
        $this->view->paginator->setCurrentPageNumber($this->getParam('page'));

        $this->render('appointments-therapist');
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
                ->from('therapist_appointments', array('date'))
                ->where('id=?', $this->_getParam('id'))
                ->query()->fetchColumn();

            $booking = new Booking(array(
                'today' => date('Y-m-d'),
                'date' => $appointment_date
            ));

            if (!$booking->allowCancelByUser()) {
                echo 'not allowed to cancel';
                exit;
            }
            $condition .= ' && user_id = ' . (int)$user['id'];
        } else if ($user['type'] == 'staff') {
            $condition .= ' && therapist_userid = ' . (int)$user['id'];
        } else if ($user['type'] !== 'admin') {
            return $this->_redirect('/');
        }

        $db->update('therapist_appointments', $newValues, $condition);

        $logMessage = 'Therapist appointment #' . (int)$this->_getParam('id');
        $logMessage .= ' cancelled by user #' . $user['id'];
        $this->logger()->log($logMessage, Zend_Log::INFO);


        $this->view->date = $appointment_date;

        $html = $this->view->render('massage/appointment-cancel.phtml');

        $mail = new Zend_Mail;
        $mail->addTo($user['email']);
        $mail->setBodyText($html);
        $this->queueMail($mail);
        echo $html;
        $this->_helper->viewRenderer->setNoRender(true);
    }

}