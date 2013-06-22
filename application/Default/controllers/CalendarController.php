<?php
class CalendarController extends AbstractCalendarController
{
    protected $staff_selection;

    function indexAction()
    {
        $user = bootstrap::getInstance()->getUser();

        $staffSelector = $this->staffSelector();

        if(!is_null($staffSelector->getValue('staff'))) {
            $this->staff_selection = $staffSelector->getValue('staff');
        } else {
            $this->staff_selection = $this->listStaff();
        }

        $this->view->staffSelector = $staffSelector;
        $this->render('staff-selector');
        $this->view->therapist_id = $this->getParam('staff');

        $this->view->controller = 'massage';
        $this->renderCalendar();
    }

    /** Renders the calendar view script so the client can pick the day off the calendar to book their appointment */
    function renderCalendar()
    {
        $this->view->availability = array();
        $this->view->booked = array();

        /** Create an array index for each day in this month */
        for ($day = 1; $day <= $this->number_of_days_in_month; $day++) {
            $dayString = $this->dayString($day);
            $dayNumber = date('N', strtotime($dayString));

            /** Select the availability & bookings for this day */
            $availability = $this->selectAvailability($dayNumber);
            $availabilityModel = $this->removeBookingsFrom($availability, $dayString);

            $availability = $availabilityModel->mergeOverlappingRanges();

            /** Assign them to the view */
            $this->view->availability[$day] = $availability;
            $this->view->booked[$day] = $availabilityModel->getBookedTimes();
        }


        /** Display controls to toggle between week & calendar views */
        $this->view->mode = $this->getParam('mode');
        $this->render('calendar-mode-chooser',null,true);

        if($this->getParam('mode')=='week') {
            /** Display the week view */
            $this->render('week-choose', null, true);
        } else {
            /** Display the calendar */
            $this->render('calendar-choose', null, true);
        }
    }

    /** Select availability for either all massage therapists at the client's condo, or the selected therapist */
    function selectAvailability($dayNumber)
    {
        $therapist = $this->staff_selection;
        if(is_array($therapist)) {
            $therapist = array_keys($therapist);
        }
        return parent::selectAvailability($dayNumber, $therapist);
    }

    /**
     * Remove the bookings from the selected therapist, if none selected removes all therapists bookings
     * @todo Oops, it removes bookings from all therapists, even ones not at this user's condo!!
     */
    function removeBookingsFrom($availability, $dayString)
    {
        return $this->removeMassageBookingsFrom($availability, $dayString, $this->_getParam('therapist'));
    }
}