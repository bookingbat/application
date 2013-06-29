<?php
class Service_Form extends Zend_Form
{
    function init()
    {
        $this->addElement('text','name',array(
            'label'=>'Service Name',
            'required'=>true
        ));
    }
}