<?php
class AppointmentsController extends Controller
{
    function indexAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id'] || ($user['type'] != 'staff' && $user['type'] != 'admin')) {
            return $this->_redirect('/');
        }

        $db = \Zend_Registry::get('db');
        $select = $db->select()
            ->from('appointments')
            ->joinLeft('user', 'user.id=appointments.user_id', array(
                'first_name',
                'last_name',
                'email',
                'phone'
            ))
            ->joinLeft(array('staff'=>'user'), 'staff.id=appointments.staff_userid', array(
                'staff_first_name'=>'first_name',
                'staff_last_name'=>'last_name'
            ))
            ->order('id DESC');

        if($user['type'] != 'admin') {
            $select->where('staff_userid=?', $user['id']);
        }

        $paginationAdapter = new Zend_Paginator_Adapter_DbSelect($select);

        $this->viewParams['show_delete_button'] = true;
        $this->viewParams['user_type'] = $user['type'];
        $this->viewParams['paginator'] = new Zend_Paginator($paginationAdapter);
        $this->viewParams['paginator']->setCurrentPageNumber($this->params('page'));

        $this->render('appointments-staff');
    }

    function cancelAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->_redirect('/');
        }

        $db = \Zend_Registry::get('db');
        $newValues = array('canceled' => 1);

        $condition = 'id=' . (int)$this->_params('id');

        $appointment_date = $db->select()
            ->from('appointments', array('date'))
            ->where('id=?', $this->_params('id'))
            ->query()->fetchColumn();

        if ($user['type'] == 'client') {
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
            $condition .= ' && staff_userid = ' . (int)$user['id'];
        } else if ($user['type'] !== 'admin') {
            return $this->_redirect('/');
        }

        $db->update('appointments', $newValues, $condition);

        $logMessage = 'Therapist appointment #' . (int)$this->_params('id');
        $logMessage .= ' cancelled by user #' . $user['id'];
        $this->cancelsLogger()->log($logMessage, Zend_Log::INFO);

        $this->viewParams['date = $appointment_date;

        $html = $this->viewParams['render('appointments/cancel.phtml');

        $mail = new Zend_Mail;
        $mail->addTo($user['email']);
        $mail->setBodyText($html);
        $this->queueMail($mail);
        echo $html;
        $this->_helper->viewRenderer->setNoRender(true);
    }

}