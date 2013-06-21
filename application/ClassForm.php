<?php
class ClassForm extends Zend_Form
{
    function init()
    {
        $this->setMethod('POST');
        $this->setAction('/class/add');
        $this->addElement('text', 'name', array(
            'required' => true,
            'label' => 'Class Name'
        ));

        $this->addElement('select', 'day_of_week', array(
            'label' => 'Day Of Week',
            'required' => true,
            'multiOptions' => array(1 => 'Sunday', 2 => 'Monday', 3 => 'Tuesday', 4 => 'Wednesday', 5 => 'Thursday', 6 => 'Friday', '7' => 'Saturday')
        ));

        $this->addElement('select', 'time', array(
            'label' => 'Start Time',
            'multiOptions' => $this->times()
        ));

        $this->addElement('select', 'instructor', array(
            'label' => 'Instructor'
        ));

        $this->addElement('select', 'condo', array(
            'label' => 'Condo'
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Add New Class',
            'class' => 'btn btn-primary'
        ));

    }

    function times()
    {
        $date = new DateTime('2011-06-28 00:00:00');
        $count = 24 * 60 / 30;
        $times = array();
        while ($count--) {
            $interval = $date->add(new DateInterval("P0Y0DT0H30M"));
            $format = $interval->format("H:i");
            $label = $interval->format("h:i a");
            $times[$format] = strtoupper($label);
        }

        return $times;
    }
}