<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
class CalendarController extends \Application\AbstractCalendarController
{
    protected $staff_selection;


    function indexAction()
    {
        $this->init();

        $layoutViewModel = $this->layout();

        $progress = new ViewModel(['step'=>1]);
        $progress->setTemplate('application/progress');
        $layoutViewModel->addChild($progress, 'progress');

        $user = \bootstrap::getInstance()->getUser();

        if(!$this->params('service')) {
            return $viewModel->setTemplate('no-service-selected');
        }

        $staffSelector = $this->staffSelector(true,$this->params('service'));

        if(!is_null($staffSelector->getValue('staff'))) {
            $this->staff_selection = $staffSelector->getValue('staff');
        } else {
            $this->staff_selection = $this->listStaff($this->params('service'));
        }

        $this->viewParams = $this->viewParams + [
            'staffSelector'=>$staffSelector,
            'therapist_id'=>$this->params('staff'),
            'service'=>$this->params('service'),
        ];

        $this->renderCalendar($this->viewParams);

        /** Display controls to toggle between week & calendar views */
        $this->viewParams['mode'] = $this->params('mode');

        $viewModel = new ViewModel($this->viewParams);
        $viewModel->setTemplate('application/calendar-mode-chooser');
        //return $viewModel;

        $viewModel = new ViewModel($this->viewParams);
        if($this->params('mode')=='week') {
            /** Display the week view */
            $viewModel->setTemplate('week-choose');
        } else {
            /** Display the calendar */
            $viewModel->setTemplate('application/calendar-choose');
        }
        return $viewModel;
    }

    /** Renders the calendar view script so the client can pick the day off the calendar to book their appointment */
    function renderCalendar()
    {
        $this->viewParams['availability'] = array();
        $this->viewParams['booked'] = array();

        /** Create an array index for each day in this month */
        for ($day = 1; $day <= $this->number_of_days_in_month; $day++) {
            $dayString = $this->dayString($day);
            $dayNumber = date('N', strtotime($dayString));

            /** Select the availability & bookings for this day */
            $availability = $this->selectAvailability($dayNumber, $this->params('service'));
            $availabilityModel = $this->removeBookingsFrom($availability, $dayString);

            $availability = $availabilityModel->mergeOverlappingRanges();

            /** Assign them to the view */
            $this->viewParams['availability'][$day] = $availability;
            $this->viewParams['booked'][$day] = $availabilityModel->getBookedTimes();
        }
    }

    /** Select availability for either all massage therapists at the client's condo, or the selected therapist */
    function selectAvailability($dayNumber,$service=null, $filterByTherapist=null)
    {
        $filterByTherapist = $this->staff_selection;
        if(is_array($filterByTherapist)) {
            $filterByTherapist = array_keys($filterByTherapist);
        }
        return parent::selectAvailability($dayNumber, $service, $filterByTherapist);
    }

    /**
     * Remove the bookings from the selected therapist, if none selected removes all therapists bookings
     * @todo Oops, it removes bookings from all therapists, even ones not at this user's condo!!
     */
    function removeBookingsFrom($availability, $dayString, $filterByTherapist=null)
    {
        return parent::removeBookingsFrom($availability, $dayString, $this->_params('therapist'));
    }
}