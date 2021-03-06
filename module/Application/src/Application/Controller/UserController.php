<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;

class UserController extends \Application\Controller
{

    function loginAction()
    {
        /**
         * Single Sign on
         */
        if($this->params()->fromQuery('sso') && $this->params()->fromQuery('username')) {
            $db = \Zend_Registry::get('db');
            $select = $db->select()
                ->from('user')
                ->where('username=?', $this->params()->fromQuery('username'));

            $user = $select->query()->fetch();

            if (sha1($user['username'].$user['password'].'secret123') == $this->params()->fromQuery('sso')) {
                $this->updateUserDataIntoSession($user['username']);
                return $this->redirect()->toRoute('admin_tutorial');
            } else {
                throw new \Exception('Invalid SSO credentials');
            }
        }

        /**
         * Login Form
         */
        $form = new \Application\LoginForm;

        if ($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {
            $db = \Zend_Registry::get('db');
            $select = $db->select()
                ->from('user')
                ->where('username=?', $this->params()->fromPost('username'));

            $user = $select->query()->fetch();

            if (sha1($form->getValue('password')) == $user['password']) {
                $this->updateUserDataIntoSession($user['username']);
                return $this->redirect()->toRoute('home');
            } else {
                $form->getElement('password')->markAsError()->addError('Invalid password or username not found');
            }
        }

        $this->viewParams['form'] = $form;
        return $this->viewParams;
    }

    function logoutAction()
    {
        \bootstrap::getInstance()->userLogout();
        $this->redirect()->toRoute('home');
        return false;
    }

    function manageAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $paginationAdapter = new \Zend_Paginator_Adapter_DbSelect($this->selectUsers());
        $this->viewParams['paginator'] = new \Zend_Paginator($paginationAdapter);
        $this->viewParams['paginator']->setCurrentPageNumber($this->params('page'));
        return new ViewModel($this->viewParams);
    }

    function editAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $db = \Zend_Registry::get('db');
        $select = $db->select()
            ->from('user')
            ->where('id=?', $this->params('id'));
        $userBeingEdited = $select->query()->fetch();

        $form = new \Application\UserForm;
        $form->removeElement('password');
        $form->removeElement('verifypassword');

        $form->populate($userBeingEdited);

        $this->viewParams['form'] = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {
            $db = \Zend_Registry::get('db');
            $db->update('user', array(
                'username' => $form->getValue('username'),
                'first_name' => $form->getValue('first_name'),
                'last_name' => $form->getValue('last_name'),
                'email' => $form->getValue('email'),
                'type' => $form->getValue('type'),
                'phone' => $form->getValue('phone'),
            ), 'id=' . (int)$this->params('id'));

            $this->flashMessenger()->addMessage('User Updated');
            return $this->redirect()->toRoute('manage-staff');
        }

        return new ViewModel($this->viewParams);
    }

    function registerAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        $form = new \Application\UserForm;
        $this->viewParams['form'] = $form;

        if($user['type']!='admin') {
            $form->removeElement('type');
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {
            $db = \Zend_Registry::get('db');
            $data = array(
                'username' => $form->getValue('username'),
                'email' => $form->getValue('email'),
                'password' => sha1($form->getValue('password')),
                'type' => 'staff',
                'phone' => $form->getValue('phone'),
                'first_name' => $form->getValue('first_name'),
                'last_name' => $form->getValue('last_name'),
            );
            $db->insert('user', $data);

            $this->viewParams['email'] = $form->getValue('email');
            $this->viewParams['username'] = $form->getValue('username');
            $this->flashMessenger()->addMessage('Created User');
            return $this->redirect()->toUrl('manage-staff');
        }
        return $this->viewParams;
    }

    function admintutorialAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if($user['type'] == 'admin') {
            $_SESSION['admin_setup'] = 1;
            return $this->redirect()->toRoute('new-service');
        }
    }

    function selectUsers()
    {
        $db = \Zend_Registry::get('db');
        $select = $db->select()
            ->from('user')
            ->where('type=?','staff')
            ->orWhere('type=?', 'admin');

        return $select;
    }

}