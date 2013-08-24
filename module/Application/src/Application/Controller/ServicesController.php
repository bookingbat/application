<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
class ServicesController extends \Application\Controller
{
    function chooseAction()
    {
        $services = $this->serviceDataMapper()->findValid();

        $layoutViewModel = $this->layout();

        $progress = new ViewModel(['step'=>1]);
        $progress->setTemplate('application/progress');
        $layoutViewModel->addChild($progress, 'progress');

        if(isset($_SESSION['admin_setup'])) {
            $this->viewParams['admin_setup'] = 1;
            unset($_SESSION['admin_setup']);
        }
        $this->viewParams['services'] = $services;
        return $this->viewParams;
    }

    function manageAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $this->viewParams['services'] = $this->listServices();
        return $this->viewParams;
    }

    function newAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $form = $this->form();
        if($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {
            $this->serviceDataMapper()->insert($form->getValues());
            $this->flashMessenger()->addMessage('Service Created');
            if(isset($_SESSION['admin_setup'])) {
                return $this->redirect()->toRoute('staff-services', ['staff'=>$user['id']]);
            }
            return $this->redirect()->toRoute('manage-services');
        }
        $this->viewParams['form'] = $form;
        if(isset($_SESSION['admin_setup'])) {
            $this->viewParams['admin_setup'] = 1;
        }
        return $this->viewParams;
    }

    function editAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $id = $this->params('id');
        $service = $this->serviceDataMapper()->find($id);

        $form = $this->form();
        $form->populate($service);

        if($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {
            $this->serviceDataMapper()->update($id, $form->getValues());

            $this->flashMessenger()->addMessage('Service Updated');
            return $this->redirect()->toRoute('manage-services');
        }
        $this->viewParams['form'] = $form;
        return $this->viewParams;
    }

    function deleteAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $this->serviceDataMapper()->update($this->params('id'), ['active'=>0]);
        $this->flashMessenger()->addMessage('Service Deleted');
        return $this->redirect()->toRoute('manage-services');
    }

    function assignAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $staff_id = $this->params('staff');
        $staff = $this->userDataMapper()->find(array(
            'id'=>$staff_id,
            'type'=>'staff'
        ));

        $form = $this->servicesForm();

        if($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {

            $this->userDataMapper()->unassignServices($staff_id);
            $this->userDataMapper()->assignMultiple($form->getValue('services'), $staff_id);

            $this->flashMessenger()->addMessage('Staff\'s Services Updated');
            if(isset($_SESSION['admin_setup'])) {
                return $this->redirect()->toRoute('staff-availability', ['staff'=>$user['id']]);
            }
            return $this->redirect()->toRoute('manage-staff');
        }

        $this->viewParams['staff'] = $staff;
        $this->viewParams['form'] = $form;

        if(isset($_SESSION['admin_setup'])) {
            $this->viewParams['admin_setup'] = 1;
        }

        return $this->viewParams;
    }

    function listServices()
    {
        return $this->serviceDataMapper()->findAll();
    }

    function form()
    {
        return new \Application\Service\Form;
    }

    function servicesForm()
    {
        $form = new \Application\Service\UserServicesForm();
        $form->setPossibleServices($this->listServices());

        $form->populate(array(
            'services' => $this->servicesForStaff()
        ));

        return $form;
    }

    function servicesForStaff()
    {
        return $this->serviceDataMapper()->servicesForStaff($this->params('staff'));
    }

}