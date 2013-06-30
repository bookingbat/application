<?php
namespace Application;
class BookingForm extends \Zend_Form
{
    function init()
    {
        $this->setMethod('POST');

        $this->addElement('select', 'time', array(
            'label' => 'Time',

        ));

        $this->addElement('hidden', 'trainer', array());
        $this->addElement('hidden', 'appointment_duration', array());

        $this->addElement('submit', 'submit', array(
            'label' => 'Submit',
            'class' => 'btn btn-primary btn-large'
        ));

    }

    function setAvailability($availabilityTimes)
    {
        $availability = new \Bookingbat\Engine\Availability($availabilityTimes);
        $incrementDuration = $this->getElement('appointment_duration')->getValue();
        $availabilityTimes = $availability->incrementize($availabilityTimes, $incrementDuration);
        foreach ($availabilityTimes as $time) {
            $start = new DateTime('2013-03-21 ' . $time);
            $start = $start->format('h:i a');

            $label = sprintf('%s', $start);
            $this->getElement('time')->addMultiOption($time, $label);
        }
    }

}