<?php
namespace Application;
class BookingForm2 extends BookingForm
{
    function init()
    {
        $this->setMethod('POST');

        $this->addElement('radio', 'time', array(
            'label' => 'Appointment Start Time',
            'separator'=>''
        ));

        $this->addElement('hidden', 'appointment_duration', array());

    }

    function setAvailability($availabilityTimes)
    {
        $availability = new \Bookingbat\Engine\Availability($availabilityTimes);

        $availabilityTimes = $availability->incrementize($availabilityTimes, 30, $this->getElement('appointment_duration')->getValue());
        foreach ($availabilityTimes as $time) {
            $timeFormatted = $this->time($time);
            $this->getElement('time')->addMultiOption($time, $timeFormatted);
        }
    }

    function time($time)
    {
        $helper = new Helper\Time;
        return $helper->__invoke($time);
    }
}