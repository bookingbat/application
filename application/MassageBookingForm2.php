<?php
class MassageBookingForm2 extends BookingForm
{
    function init()
    {
        $this->setMethod('POST');

        $this->addElement('select', 'time', array(
            'label' => 'Appointment Start Time',

        ));

        $this->addElement('hidden', 'appointment_duration', array());


        $this->addElement('submit', 'submit', array(
            'label' => 'Submit'
        ));

    }

    function setAvailability($availabilityTimes)
    {
        $availability = new \Bookingbat\Engine\Availability($availabilityTimes);

        $availabilityTimes = $availability->incrementize($availabilityTimes, 30, $this->getElement('appointment_duration')->getValue());
        foreach ($availabilityTimes as $time) {
            $timeFormatted = date('h:i a', strtotime($time));
            $this->getElement('time')->addMultiOption($time, $timeFormatted);
        }
    }
}