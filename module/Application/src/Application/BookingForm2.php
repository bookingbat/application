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

    }

}