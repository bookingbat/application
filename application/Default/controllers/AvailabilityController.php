<?php
class AvailabilityController extends Controller
{

    function indexAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'staff') {
            return $this->_redirect('/');
        }

        $this->view->availability = array();
        for ($day = 1; $day <= 7; $day++) {
            $this->view->availability[$day] = $this->selectMassageAvailability($day, $user['id']);
        }

        $db = Zend_Registry::get('db');

        if ($this->getRequest()->getParam('remove')) {
            $db->delete('therapist_availability', array(
                'id = ' . (int)$this->_getParam('remove')
            ));
            $this->_helper->FlashMessenger->addMessage('Deleted Availability');
            return $this->_redirect('/massage/availability');
        }

        $form = new AvailabilityForm;
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $db->insert('therapist_availability', array(
                'therapist_userid' => $user['id'],
                'day_of_week' => $form->getValue('day'),
                'start' => $form->getValue('start'),
                'end' => $form->getValue('end'),
            ));
            $this->_helper->FlashMessenger->addMessage('Added Availability');
            return $this->_redirect('/massage/availability');
        }
        $this->view->form = $form;

        $this->render('availability-manage', null, true);
    }

}