<?php
namespace Application;
class AvailabilityForm extends \Zend_Form
{
    function init()
    {
        $this->setMethod('POST');
        $this->addElement('radio', 'day', array(
            'label' => 'Day',
            'required' => true,
            'multiOptions' => array(7 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'),
            'separator'=>''
        ));

        $this->addElement('select', 'start', array(
            'label' => 'Start Time',
            'multiOptions' => $this->times(),
            'separator'=>''
        ));

        $this->addElement('select', 'end', array(
            'label' => 'End Time',
            'multiOptions' => $this->times(),
            'separator'=>''
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Add',
            'separator'=>'',
            'class'=>'btn btn-primary'
        ));

    }

    function times()
    {
        $date = new \DateTime('2011-06-28 00:00:00');
        $count = 24 * 60 / 30;
        $times = array();
        while ($count--) {
            $interval = $date->add(new \DateInterval("P0Y0DT0H30M"));
            $format = $interval->format("H:i");
            $label = $interval->format("h:i a");
            $times[$format] = strtoupper($label);
        }

        return $times;
    }
}