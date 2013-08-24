<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
class AvailabilityController extends \Application\Controller
{

    function indexAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id'] || ($user['type'] != 'staff' && $user['type'] != 'admin')) {
            return $this->_redirect('/');
        }

        if($user['type'] == 'admin' && !$this->params('staff')) {
            throw new Exception('Admin must have a staff selected');
        }

        $this->viewParams['availability'] = array();
        for ($day = 1; $day <= 7; $day++) {
            $staff = $user['type'] == 'admin' ? $this->params('staff') : $user['id'];
            $this->viewParams['availability'][$day] = $this->selectAvailability($day, null, $staff);
        }

        $db = \Zend_Registry::get('db');

        if ($this->params()->fromQuery('remove')) {
            $db->delete('availability', array(
                'id = ' . (int)$this->params()->fromQuery('remove')
            ));
            $this->flashMessenger()->addMessage('Deleted Availability');
            return $this->redirect()->toUrl($this->url()->fromRoute('staff-availability',array(), true));
        }

        $form = new \Application\AvailabilityForm;

        if ($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {
            $staff_userid = $user['type'] == 'admin' ?  $this->params('staff'): $user['id'];
            $parameters = array(
                'staff_userid' => $staff_userid,
                'day_of_week' => $form->getValue('day'),
                'start' => $form->getValue('start'),
                'end' => $form->getValue('end'),
            );
            $this->availabilityDataMapper()->insert($parameters);

            $this->flashMessenger()->addMessage('Added Availability');

            if(isset($_SESSION['admin_setup'])) {
                return $this->redirect()->toRoute('home');
            }
            return $this->redirect()->toUrl($this->url()->fromRoute('staff-availability',array(),true));
        }
        $this->viewParams['form'] = $form;

        if(isset($_SESSION['admin_setup'])) {
            $this->viewParams['admin_setup'] = 1;
        }

        $viewModel = new ViewModel($this->viewParams);
        $viewModel->setTemplate('application/availability-manage.phtml');

        return $viewModel;
    }

    function availabilityDataMapper()
    {
        return new \Application\Availability\DataMapper($this->db());
    }
}