<?php
class PaymentForm extends Zend_Form
{
    protected $user;

    function setUser($user)
    {
        $this->user = $user;
    }

    function init()
    {
        $this->addElement('text', 'cardNumber', array(
            'label' => 'Card Number',
            'required' => true,
            'value' => '4111111111111111'
        ));
        $this->addElement('text', 'expirationDate', array(
            'label' => 'Expiration Date',
            'required' => true,
            'description' => 'YYYY-MM'
        ));
        $this->addElement('text', 'firstName', array(
            'label' => 'First Name',
            'required' => true
        ));
        $this->addElement('text', 'lastName', array(
            'label' => 'Last Name',
            'required' => true
        ));
        $this->addElement('text', 'address', array(
            'label' => 'Address',
            'required' => true
        ));
        $this->addElement('text', 'city', array(
            'label' => 'City',
            'required' => true
        ));
        $this->addElement('text', 'state', array(
            'label' => 'State',
            'required' => true
        ));
        $this->addElement('text', 'zip', array(
            'label' => 'Zip',
            'required' => true
        ));
        $this->addElement('text', 'email', array(
            'label' => 'Email',
            'required' => true,
            'value' => $this->user['email'],
            'validators' => array('EmailAddress')
        ));
    }

}