<?php
class Service_Form extends Zend_Form
{
    function __construct($services)
    {
        $this->addElement('multiCheckbox','services',array(
            'label'=>'Services',
            'multiOptions'=>$services,
            'separator'=>''
        ));
        return parent::__construct();
    }
}