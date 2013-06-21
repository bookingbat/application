<?php
/**
 * Provides common functionality for controllers that need to output a calendar from which the user chooses the day for
 * their appointment. Provides some abstract template methods for selecting the availability & bookings for a particular
 * service type (massages, personal training)
 */
abstract class CalendarchooseController extends CalendarController
{

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

    /** Overridden in subclass to select the bookings & availability */

    abstract function selectAvailability($dayNumber);
    abstract function removeBookingsFrom($availability, $dayString);
}