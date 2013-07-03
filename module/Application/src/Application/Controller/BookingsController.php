<?php
class BookingsController extends CalendarController
{
    function servicesAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->_redirect('/');
        }

        $this->viewParams['user = $this->userObjectForBillingCalculations();
        $this->viewParams['trainer_appointments_total_duration = $this->trainingAppointmentsTotalDuration($user['id']);
        $this->viewParams['massage_appointments_total_duration = $this->massageAppointmentsTotalDuration($user['id']);
    }

    function indexAction()
    {
        $user = \bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->render('index');
        }

        $this->viewParams['trainer_appointments = $this->lister($user['id'])->trainerAppointments();
        $this->viewParams['massage_appointments = $this->lister($user['id'])->massageAppointments();
        $this->viewParams['class_enrollment = $this->lister($user['id'])->classEnrollments('user');

        switch ($user['type']) {
            case 'client':
                $this->viewParams['mode = $this->params('mode');
                $this->render('client-view');
                if($this->params('mode')=='list') {
                    $this->render('index-client-list');
                } else {
                    $this->render('index-client-calendar');
                }
            break;
            case 'trainer':
                return $this->render('index-trainer');

            case 'class-instructor':
                return $this->render('index-instructor');

            case 'staff':
                return $this->render('index-therapist');

            case 'admin':
                return $this->render('index-admin');

        }

    }

    function lister($userId)
    {
        $lister = new AppointmentsLister;
        $lister->userId($userId);
        return $lister;
    }


}