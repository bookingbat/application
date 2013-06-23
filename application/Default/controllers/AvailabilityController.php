<?php
class AvailabilityController extends Controller
{

    function indexAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || ($user['type'] != 'staff' && $user['type'] != 'admin')) {
            return $this->_redirect('/');
        }

        if($user['type'] == 'admin' && !$this->getParam('staff')) {
            throw new Exception('Admin must have a staff selected');
        }

        $this->view->availability = array();
        for ($day = 1; $day <= 7; $day++) {
            $staff = $user['type'] == 'admin' ? $this->getParam('staff') : $user['id'];
            $this->view->availability[$day] = $this->selectAvailability($day, null, $staff);
        }

        $db = Zend_Registry::get('db');

        if ($this->getRequest()->getParam('remove')) {
            $db->delete('availability', array(
                'id = ' . (int)$this->_getParam('remove')
            ));
            $this->_helper->FlashMessenger->addMessage('Deleted Availability');
            return $this->_redirect($this->view->url(array(),'availability',true));
        }

        $form = new AvailabilityForm;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $db->insert('availability', array(
                'staff_userid' => $user['type'] == 'admin' ?  $this->getParam('staff'): $user['id'],
                'day_of_week' => $form->getValue('day'),
                'start' => $form->getValue('start'),
                'end' => $form->getValue('end'),
            ));
            $this->_helper->FlashMessenger->addMessage('Added Availability');
            return $this->_redirect($this->view->url(array(),'availability'));
        }
        $this->view->form = $form;

        $this->render('availability-manage', null, true);
    }

}