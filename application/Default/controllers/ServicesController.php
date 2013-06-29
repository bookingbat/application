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
            $this->db()->insert('services',$form->getValues());
            $this->_helper->FlashMessenger->addMessage('Service Created');
            $url = $this->view->url(array('action'=>'manage'),'services',true);
            return $this->_redirect($url);
        }
        $this->view->form = $form;
    }

    function editAction()
    {
        $service = $this->serviceDataMapper()->find($this->getParam('id'));

        $form = $this->form();
        $form->populate($service);

        if($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $this->serviceDataMapper()->update($this->getParam('id'), $form->getValues());

            $this->_helper->FlashMessenger->addMessage('Service Updated');
            $url = $this->view->url(array('action'=>'manage'),'services',true);
            return $this->_redirect($url);
        }
        $this->view->form = $form;
    }

    function assignAction()
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('user')
            ->where('id=?', $this->_getParam('staff'));
        $staff = $select->query()->fetch();

        $form = $this->servicesForm();

        if($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {

            $this->userDataMapper()->unassignServices($staff['id']);
            $this->userDataMapper()->assignMultiple($form->getValue('services'), $staff['id']);

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
        $form = new Zend_Form;
        $form->addElement('text','name',array(
            'label'=>'Service Name',
            'required'=>true
        ));

        return $form;
    }

    function servicesForm()
    {
        $form = new Zend_Form;

        $services = array();
        foreach($this->listServices() as $service) {
            $services[$service['id']] = $service['name'];
        }

        $form->addElement('multiCheckbox','services',array(
            'label'=>'Services',
            'multiOptions'=>$services,
            'separator'=>''
        ));

        $form->populate(array('services' => $this->servicesForStaff()));

        return $form;
    }

    function servicesForStaff()
    {
        return $this->serviceDataMapper()->servicesForStaff($this->getParam('staff'));
    }

    function serviceDataMapper()
    {
        return new Service_DataMapper($this->db());
    }

    function userDataMapper()
    {
        return new User_DataMapper($this->db());
    }

}