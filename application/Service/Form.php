<?php
class Service_Form extends Zend_Form
{
    function init()
    {
        $this->addElement('text','name',array(
            'label'=>'Service Name',
            'required'=>true
        ));
        $this->addElement('select','padding',array(
            'label'=>'Padding',
            'multiOptions'=>array(
                '0'=>'No Padding',
                '30'=>'30 Minutes Padding',
                '60'=>'1 Hour Padding',
            )
        ));
    }
}