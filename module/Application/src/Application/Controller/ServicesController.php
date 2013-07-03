<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
class ServicesController extends \Application\Controller
{
    function chooseAction()
    {
        $layoutViewModel = $this->layout();

        $progress = new ViewModel(['step'=>1]);
        $progress->setTemplate('application/progress');
        $layoutViewModel->addChild($progress, 'progress');

        return new ViewModel([
            'services'=>$this->serviceDataMapper()->findValid()
        ]);
    }

    function manageAction()
    {
        $this->viewParams['services'] = $this->listServices();
    }

    function newAction()
    {
        $form = $this->form();
        if($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {
            $this->serviceDataMapper()->insert($form->getValues());
            $this->flashMessenger()->addMessage('Service Created');
            $url = $this->url(array('action'=>'manage'),'services',true);
            return $this->_redirect($url);
        }
        $this->viewParams['form'] = $form;
    }

    function editAction()
    {
        $id = $this->params('id');
        $service = $this->serviceDataMapper()->find($id);

        $form = $this->form();
        $form->populate($service);

        if($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {
            $this->serviceDataMapper()->update($id, $form->getValues());

            $this->flashMessenger()->addMessage('Service Updated');
            $url = $this->url(array('action'=>'manage'),'services',true);
            return $this->_redirect($url);
        }
        $this->viewParams['form'] = $form;
    }

    function assignAction()
    {
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
            return $this->_redirect('/user/manage');
        }

        $this->viewParams['staff'] = $staff;
        $this->viewParams['form'] = $form;
        return $this->viewParams;
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