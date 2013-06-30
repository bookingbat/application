<?php
namespace Application;
class BookingForm4 extends \Zend_Form
{
    function init()
    {
        $this->addElement('text','name',array(
            'label'=>'Name',
            'require'=>true
        ));

        $this->addElement('text', 'email', array(
            'label' => 'Email Address',
            'required' => true,
            'validators' => array('EmailAddress')
        ));

        $this->addElement('text', 'phone', array(
            'label' => 'Phone #',
            'required' => true,
            'validators' => array(
                array('regex', false, array(
                    'pattern' => '/^[0-9 -]+$/',
                    'messages' => array('regexNotMatch' => 'Must not contain characters other than spaces, dashes & digits.')
                )),
                array('stringLength', false, array('min' => 10, 'max' => 20))
            )
        ));
    }
}