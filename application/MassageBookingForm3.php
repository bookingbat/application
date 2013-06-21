<?php
class MassageBookingForm3 extends MassageBookingForm2
{
    function init()
    {
        $this->setMethod('POST');

        $this->addElement('select', 'time', array(
            'label' => 'Time',
            'disabled' => 'disabled'
        ));

        $this->addElement('select', 'therapist', array(
            'label' => 'Therapist',
            'required' => true
        ));

        $this->addElement('hidden', 'appointment_duration', array());

        $this->addElement('hidden', 'day', array());

        $this->addElement('submit', 'submit', array(
            'label' => 'Submit'
        ));

    }

}