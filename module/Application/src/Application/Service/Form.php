<?php
namespace Application\Service;
class Form extends \Zend_Form
{
    function init()
    {
        $this->addElement('text','name',array(
            'label'=>'Service Name',
            'required'=>true
        ));

        $this->addElement('multiCheckbox','durations',array(
            'label'=>'Allowed Duration(s)',
            'required'=>true,
            'multiOptions'=>array(
                '30'=>'30 minutes',
                '60'=>'1 Hour',
                '90'=>'1.5 Hour',
                '120'=>'2 Hours',
            ),
            'separator'=>''
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