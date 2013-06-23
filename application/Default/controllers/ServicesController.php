<?php
class ServicesController extends Controller
{
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
        $service = $this->db()->select()
            ->from('services')
            ->where('id=?',$this->getParam('id'))
            ->query()->fetch();

        $form = $this->form();
        $form->populate($service);

        if($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $this->db()->update('services',$form->getValues(),'id='.$this->getParam('id'));
            $this->_helper->FlashMessenger->addMessage('Service Updated');
            $url = $this->view->url(array('action'=>'manage'),'services',true);
            return $this->_redirect($url);
        }
        $this->view->form = $form;
    }

    function chooseAction()
    {

    }

    function listServices()
    {
        $select = $this->db()->select()
            ->from('services');
        return $select->query()->fetchAll();
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

}