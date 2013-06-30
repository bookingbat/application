<?php
namespace Application;
class UserForm extends \Zend_Form
{
    function init()
    {
        $this->setMethod('POST');
        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'required' => true,
        ));
        $this->addElement('text', 'first_name', array(
            'label' => 'First Name',
            'required' => true,
        ));
        $this->addElement('text', 'last_name', array(
            'label' => 'Last Name',
            'required' => true,
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
                array('regex', false, array('pattern' => '/^[0-9 -]+$/', 'messages' => array('regexNotMatch' => 'Must not contain characters other than spaces, dashes & digits.'))),
                array('stringLength', false, array('min' => 10, 'max' => 20))
            )
        ));

        $this->addElement('hidden', 'type');
        $this->addElement('password', 'password', array(
            'label' => 'Password'
        ));
        $this->addElement('password', 'verifypassword', array(
            'label' => 'Verify Password:',
            'required' => true,
            'validators' => array(
                array('identical', true, array('password'))
            )
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Save',
            'class' => 'btn btn-primary',
            'order' => 9999
        ));

    }
}