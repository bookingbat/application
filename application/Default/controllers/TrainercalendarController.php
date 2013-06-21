<?php
class TrainercalendarController extends CalendarchooseController
{
    function indexAction()
    {
        if($this->getParam('consultation')) {
            /** Only the master trainer can do a "fitness consultation" */
            $this->trainer = $this->masterTrainerForUser();
            $this->view->consultation = 1;
        } else {
            /** Regular training appointments have some business logic for determining which trainer is assigned */
            $this->trainer = $this->assignedTrainerForUser();
        }

        /** If we can't find a trainer for this client, something went wrong. */
        if (!$this->trainer) {
            throw new Exception('Unable to locate a trainer for this client');
        }

        /** Let's show the client the first & last name of their assigned trainer */
        $db = Zend_Registry::get('db');
        $this->view->trainer = $db->select()
            ->from('user', array('first_name', 'last_name'))
            ->where('id=?', $this->trainer)
            ->limit(1)
            ->query()
            ->fetch();
        $this->render('assignment');

        /** Show the calendar */
        $this->view->trainer_id = $this->trainer;
        $this->view->controller = 'trainer';
        $this->renderCalendar($this->trainer);
    }

    /** Assign availability to that of the client's assigned trainer */
    function selectAvailability($dayNumber)
    {
        return $this->selectTrainerAvailability($dayNumber, $this->trainer);
    }

    /** Remove the bookings of the client's assigned trainer from the availability */
    function removeBookingsFrom($availability, $dayString)
    {
        return $this->removeTrainerBookingsFrom($availability, $dayString, $this->trainer);
    }
}