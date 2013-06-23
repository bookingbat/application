<?php
class BookingForm3 extends BookingForm2
{
    function init()
    {
        $this->setMethod('POST');

        $this->addElement('select', 'time', array(
            'label' => 'Time',
            'disabled' => 'disabled'
        ));

        $this->addElement('select', 'staff', array(
            'label' => 'Staff',
            'required' => true
        ));

        $this->addElement('hidden', 'appointment_duration', array());

        $this->addElement('hidden', 'day', array());

    }

}