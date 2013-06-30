<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
class MakebookingController extends \Application\AbstractCalendarController
{
    protected $durationLabels = array(
        '30' => '30 Minutes',
        '60' => '1 Hour',
        '90' => '1.5 Hour',
        '120' => '2 Hours',
    );

    function preDispatch()
    {
        $user = bootstrap::getInstance()->getUser();
    }

    /** THis is where they pick the duration */
    function bookingAction()
    {
        $layoutViewModel = $this->layout();

        $progress = new ViewModel(['step'=>3]);
        $progress->setTemplate('application/progress');
        $layoutViewModel->addChild($progress, 'progress');

        $service = $this->serviceDataMapper()->find($this->params('service'));

        $durations = array();
        foreach($service['durations'] as $duration) {
            $durations[$duration] = $this->durationLabels[$duration];
        }

        $form = new \Zend_Form;
        $form->addElement('radio', 'appointment_duration', array(
            'label' => 'Appointment Duration',
            'multiOptions' => $durations,
            'separator'=>''
        ));

        if ($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {
            $url = $this->url()->fromRoute('make-booking',array(
                'action' => 'booking2',
                'duration' =>$form->getValue('appointment_duration'),
                'service' => $this->params('service'),
                'day' => $this->params('day'),
            ));
            $this->redirect()->toUrl($url);
            return;
        }

        $this->viewParams['form'] = $form;

        $viewModel = new ViewModel($this->viewParams);
        $viewModel->setTemplate('application/booking');
        return $viewModel;
    }

    /** This is where they pick the time */
    function booking2Action()
    {
        $layoutViewModel = $this->layout();

        $progress = new ViewModel(['step'=>4]);
        $progress->setTemplate('application/progress');
        $layoutViewModel->addChild($progress, 'progress');

        $form = new \Application\BookingForm2;
        $form->getElement('appointment_duration')->setValue($this->params('duration'));

        $day = date('N', strtotime($this->params('day')));
        $availability = $this->selectAvailability($day, $this->params('service'), $this->params('staff'));

        $availabilityModel = $this->removeBookingsFrom($availability, $this->params('day'), $this->params('staff'));
        $availabilityModel->mergeOverlappingRanges();

        $form->setAvailability($availabilityModel->getAvailabilityTimes());

        if ($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {
            $url = $this->url()->fromRoute('make-booking',array(
                'action' => 'booking3',
                'duration' => $this->params('duration'),
                'service' => $this->params('service'),
                'day' => $this->params('day'),
                'time' => $form->getValue('time')
            ));

            $this->redirect()->toUrl($url);
            return;
        }

        $this->viewParams['form'] = $form;

        $viewModel = new ViewModel($this->viewParams);
        $viewModel->setTemplate('application/booking');
        return $viewModel;
    }

    /** This is where they pick the staff */
    function booking3Action()
    {
        $layoutViewModel = $this->layout();

        $progress = new ViewModel(['step'=>5]);
        $progress->setTemplate('application/progress');
        $layoutViewModel->addChild($progress, 'progress');

        $form = new \Application\BookingForm3;

        $day = date('N', strtotime($this->params('day')));
        $availabilityArray = $this->selectAvailability($day, $this->params('service'), $this->params('staff'));
        $availabilityModel = $this->removeBookingsFrom($availabilityArray, $this->params('day'), $this->params('staff'));


        $form->populate($this->params()->fromRoute());

        $booking = new \Application\Booking(array(
            'start' => $this->params('time'),
            'duration' => $this->params('appointment_duration')
        ));


        $availabilityObject = new \Bookingbat\Engine\Availability($availabilityModel->getAvailabilityTimes());
        $possibleUserIdsForBooking = $availabilityObject->possibleUserIdsForBooking($booking);

        if(!$possibleUserIdsForBooking) {
            throw new \Exception('No staff available to take this appointment');
        }

        $db = \Zend_Registry::get('db');

        $stafResult = $db->select()
            ->from('user')
            ->where('type=?', 'staff')
            ->where('id IN(' . implode(',', $possibleUserIdsForBooking) . ')')
            ->query()->fetchAll();

        $staff = array();
        foreach ($stafResult as $stafResult) {
            $staff[$stafResult['id']] = $stafResult['username'];
        }

        $form->getElement('staff')->setMultiOptions($staff);

        if ($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {
            $url = $this->url()->fromRoute('make-booking',array(
                'action' => 'booking4',
                'duration' => $this->params('duration'),
                'service' => $this->params('service'),
                'day' => $this->params('day'),
                'time' => $this->params('time'),
                'staff'=>$form->getValue('staff')
            ));

            return $this->redirect()->toUrl($url);
        }

        $this->viewParams['form'] = $form;

        $viewModel = new ViewModel($this->viewParams);
        $viewModel->setTemplate('application/booking');
        return $viewModel;
    }

    function booking4Action()
    {


        $form = new \Application\BookingForm4;

        if ($this->getRequest()->isPost() && $form->isValid($this->params()->fromPost())) {

            $layoutViewModel = $this->layout();

            $progress = new ViewModel(['step'=>999]);
            $progress->setTemplate('application/progress');
            $layoutViewModel->addChild($progress, 'progress');

            $db = \Zend_Registry::get('db');
            $db->insert('appointments', array(
                'staff_userid' => $this->params('staff'),
                'user_id' =>0,
                'date' => $this->params('day'),
                'time' => $this->params('time'),
                'duration' => $this->params('duration'),
                'guest_name' => $form->getValue('name'),
                'guest_email' => $form->getValue('email'),
                'guest_phone' => $form->getValue('phone'),
            ));

            $staffData = $this->staffData($this->params('staff'));

            $this->viewParams['staff'] = $staffData;
            $this->viewParams['date'] = $this->params('day');
            $this->viewParams['time'] = $this->params('time');
            $this->viewParams['duration'] = $this->params('duration');


            $viewModel = new ViewModel($this->viewParams);
            $viewModel->setTemplate('application/appointments/appointment-confirmation.phtml');

            $htmlOutput = $this->getServiceLocator()
                ->get('viewrenderer')
                ->render($viewModel);

            $mail = new \Zend_Mail;
            //$mail->addTo($user['email']);
            $mail->addTo($staffData['email']);
            $mail->setBodyText($htmlOutput);
            $this->queueMail($mail);


            $this->viewParams['message'] = $htmlOutput;
            $viewModel = new ViewModel($this->viewParams);
            $viewModel->setTemplate('application/booking-confirmation');
            return $viewModel;
        }

        $layoutViewModel = $this->layout();

        $progress = new ViewModel(['step'=>6]);
        $progress->setTemplate('application/progress');
        $layoutViewModel->addChild($progress, 'progress');

        $this->viewParams['form'] = $form;

        $viewModel = new ViewModel($this->viewParams);
        $viewModel->setTemplate('application/booking');
        return $viewModel;
    }

}