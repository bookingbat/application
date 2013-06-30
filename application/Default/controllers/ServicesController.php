<?php
class ServicesController extends Controller
{
    function chooseAction()
    {
        $this->view->services = $this->listServices();
    }

    function manageAction()
    {
        $this->view->services = $this->listServices();
    }

    function newAction()
    {
        $form = $this->form();
        if($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $this->serviceDataMapper()->insert($form->getValues());
            $this->_helper->FlashMessenger->addMessage('Service Created');
            $url = $this->view->url(array('action'=>'manage'),'services',true);
            return $this->_redirect($url);
        }
        $this->view->form = $form;
    }

    function editAction()
    {
        $id = $this->getParam('id');
        $service = $this->serviceDataMapper()->find($id);

        $form = $this->form();
        $form->populate($service);

        if($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $this->serviceDataMapper()->update($id, $form->getValues());

            $this->_helper->FlashMessenger->addMessage('Service Updated');
            $url = $this->view->url(array('action'=>'manage'),'services',true);
            return $this->_redirect($url);
        }
        $this->view->form = $form;
    }

    function assignAction()
    {
        $staff_id = $this->getParam('staff');
        $staff = $this->userDataMapper()->find(array(
            'id'=>$staff_id,
            'type'=>'staff'
        ));

        $form = $this->servicesForm();

        if($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {

            $this->userDataMapper()->unassignServices($staff_id);
            $this->userDataMapper()->assignMultiple($form->getValue('services'), $staff_id);

            $this->_helper->FlashMessenger->addMessage('Staff\'s Services Updated');
            return $this->_redirect('/user/manage');
        }

        $this->view->staff = $staff;
        $this->view->form = $form;
    }

    function listServices()
    {
        return $this->serviceDataMapper()->findAll();
    }

    function form()
    {
        return new Service_Form;
    }

    function servicesForm()
    {
        $form = new Service_UserServicesForm();
        $form->setPossibleServices($this->listServices());

        $form->populate(array(
            'services' => $this->servicesForStaff()
        ));

        return $form;
    }

    function servicesForStaff()
    {
        return $this->serviceDataMapper()->servicesForStaff($this->getParam('staff'));
    }

}