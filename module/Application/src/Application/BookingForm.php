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

        $this->addElement('submit', 'submit', array(
            'label' => 'Submit',
            'class' => 'btn btn-primary btn-large'
        ));

    }

    function setAvailability($availabilityTimes,$incrementDuration)
    {
        $availability = new \Bookingbat\Engine\Availability($availabilityTimes);
        $availabilityTimes = $availability->incrementize($availabilityTimes, $incrementDuration);
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