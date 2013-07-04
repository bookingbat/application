<?php
namespace Application;
class BookingForm3 extends BookingForm2
{
    function init()
    {
        $this->setMethod('POST');

        $this->addElement('select', 'staff', array(
            'label' => 'Staff',
            'required' => true
        ));

    }

}