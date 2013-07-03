<?php
namespace Application\Controller;
class BookingsController extends \Application\Controller\CalendarController
{
    function servicesAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->_redirect('/');
        }

        $this->viewParams['user'] = $this->userObjectForBillingCalculations();
        $this->viewParams['trainer_appointments_total_duration'] = $this->trainingAppointmentsTotalDuration($user['id']);
        $this->viewParams['massage_appointments_total_duration'] = $this->massageAppointmentsTotalDuration($user['id']);

        return $this->viewParams;
    }

    function indexAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->render('index');
        }

        $this->viewParams['trainer_appointments'] = $this->lister($user['id'])->trainerAppointments();
        $this->viewParams['massage_appointments'] = $this->lister($user['id'])->massageAppointments();
        $this->viewParams['class_enrollment'] = $this->lister($user['id'])->classEnrollments('user');

        $viewModel = new ViewModel($this->viewParams);
        switch ($user['type']) {
            case 'client':
                $this->viewParams['mode'] = $this->params('mode');
                $this->render('client-view');
                if($this->params('mode')=='list') {
                    $this->render('index-client-list');
                } else {
                    $this->render('index-client-calendar');
                }
            break;

            case 'staff':
                $viewModel->setTemplate('application/appointments/index-staff');
                break;
            case 'admin':
                $viewModel->setTemplate('application/appointments/index-admin');
                break;

        }
        return $viewModel;

    }

    function lister($userId)
    {
        $lister = new \Application\AppointmentsLister;
        $lister->userId($userId);
        return $lister;
    }


}