<?php
class LoginForm extends Zend_Form
{
    function init()
    {
        $this->setAction('/user/login');
        $this->setMethod('POST');
        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'required' => true,
        ));
        $this->addElement('password', 'password', array(
            'label' => 'Password:',
            'required' => true,
        ));
        $this->addElement('submit', 'login', array(
            'label' => 'Login',
            'class' => 'btn btn-primary'
        ));
    }
}