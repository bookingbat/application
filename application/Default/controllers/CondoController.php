<?php
class CondoController extends Controller
{
    function init()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }
    }

    function manageAction()
    {
        $paginationAdapter = new Zend_Paginator_Adapter_DbSelect($this->selectCondos());
        $this->view->paginator = new Zend_Paginator($paginationAdapter);
        $this->view->paginator->setCurrentPageNumber($this->getParam('page'));
    }

    function editAction()
    {
        $db = Zend_Registry::get('db');
        $condoData = $db->select()
            ->from('condo')
            ->where('id=?', $this->_getParam('id'))
            ->limit(1)
            ->query()->fetch();
        $form = $this->form();
        $form->populate($condoData);
        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {

            $db->update('condo', array(
                'name' => $form->getValue('name'),
                'master_trainer_userid' => $form->getValue('master_trainer_userid')
            ), 'id=' . (int)$this->_getParam('id'));

            $this->_helper->FlashMessenger->addMessage('Condo saved!');
            return $this->_redirect('/condo/manage');

        }

    }

    function newAction()
    {
        $form = $this->form();
        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $db = Zend_Registry::get('db');
            $db->insert('condo', array(
                'name' => $form->getValue('name'),
                'master_trainer_userid' => $form->getValue('master_trainer_userid')
            ));

            $this->_helper->FlashMessenger->addMessage('Condo added!');
            return $this->_redirect('/condo/manage');

        }
        $this->render('edit');
    }

    function deactivateAction()
    {
        $db = Zend_Registry::get('db');
        $db->update('condo', array('active' => 0), 'id=' . (int)$this->_getParam('id'));
        $this->_helper->FlashMessenger->addMessage('Condo deactivated!');
        return $this->_redirect('/condo/manage');
    }

    function activateAction()
    {
        $db = Zend_Registry::get('db');
        $db->update('condo', array('active' => 1), 'id=' . (int)$this->_getParam('id'));
        $this->_helper->FlashMessenger->addMessage('Condo Activated!');
        return $this->_redirect('/condo/manage');
    }

    function form()
    {
        $form = new Zend_Form;
        $form->addElement('text', 'name', array(
            'label' => 'Condo Name',
            'required' => true
        ));
        $form->addElement('select', 'master_trainer_userid', array(
            'label' => 'Master Trainer',
            'required' => true,
            'multiOptions' => $this->listTrainers()
        ));
        $form->addElement('submit', 'submit', array(
            'label' => 'Save'
        ));
        return $form;
    }

    function selectCondos()
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('condo')
            ->joinleft('user', 'user.id = condo.master_trainer_userid', array('first_name', 'last_name', 'username'));

        return $select;
    }
}