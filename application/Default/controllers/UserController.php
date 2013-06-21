<?php
class UserController extends Controller
{

    function loginAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if ($user) {
            return $this->_redirect('/');
        }

        $form = new LoginForm;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $db = Zend_Registry::get('db');
            $select = $db->select()
                ->from('user')
                ->where('username=?', $this->getParam('username'));

            $user = $select->query()->fetch();

            if (sha1($form->getValue('password')) == $user['password']) {
                $this->updateUserDataIntoSession($user['username']);
                $this->_forward('index', 'Index');
            } else {
                $form->getElement('password')->markAsError()->addError('Invalid password or username not found');
            }
        }

        $this->view->form = $form;
    }

    function logoutAction()
    {
        bootstrap::getInstance()->userLogout();
        $this->_redirect('/');
    }

    function manageAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $paginationAdapter = new Zend_Paginator_Adapter_DbSelect($this->selectUsers());
        $this->view->paginator = new Zend_Paginator($paginationAdapter);
        $this->view->paginator->setCurrentPageNumber($this->getParam('page'));
    }

    function editAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('user')
            ->where('id=?', $this->_getParam('id'));
        $userBeingEdited = $select->query()->fetch();

        $form = new UserForm;
        $form->removeElement('password');
        $form->removeElement('verifypassword');

        if($userBeingEdited['type'] == 'client') {
            $form->addElement('select', 'assigned_trainer_userid', array(
                'label' => 'Assigned Personal Trainer',
                'multiOptions' => array(0 => '') + $this->listTrainers()
            ));
        }

        $form->populate($userBeingEdited);

        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $db = Zend_Registry::get('db');
            $db->update('user', array(
                'username' => $form->getValue('username'),
                'first_name' => $form->getValue('first_name'),
                'last_name' => $form->getValue('last_name'),
                'email' => $form->getValue('email'),
                'type' => $form->getValue('type'),
                'phone' => $form->getValue('phone'),
            ), 'id=' . (int)$this->_getParam('id'));

            if ($userBeingEdited['type'] == 'massage-therapist') {
                $db->delete('therapist_condos', 'therapist_userid=' . (int)$userBeingEdited['id']);
                foreach ($form->getValue('condo_id') as $condo_id) {
                    $db->insert('therapist_condos', array(
                        'therapist_userid' => $userBeingEdited['id'],
                        'condo_id' => $condo_id
                    ));
                }
            }

            $this->view->type = $form->getValue('type');
            $this->view->email = $form->getValue('email');
            $this->view->username = $form->getValue('username');
            $this->_helper->FlashMessenger->addMessage('User Updated');
            return $this->_redirect('/user/manage');
        }

    }

    function registerAction()
    {
        $user = bootstrap::getInstance()->getUser();
        $form = new UserForm;
        $this->view->form = $form;

        if($user['type']!='admin') {
            $form->removeElement('type');
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $db = Zend_Registry::get('db');
            $data = array(
                'username' => $form->getValue('username'),
                'email' => $form->getValue('email'),
                'password' => sha1($form->getValue('password')),
                'type' => $user['type'] == 'admin' ? $form->getValue('type') : 'client',
                'phone' => $form->getValue('phone'),
                'first_name' => $form->getValue('first_name'),
                'last_name' => $form->getValue('last_name'),
            );
            $db->insert('user', $data);

            $this->view->type = $form->getValue('type');
            $this->view->email = $form->getValue('email');
            $this->view->username = $form->getValue('username');
            return $this->render('success');
        }
    }

    function selectUsers()
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('user');

        return $select;
    }

}