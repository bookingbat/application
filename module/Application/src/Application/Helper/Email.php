<?php
class Zend_View_Helper_Email extends Zend_View_Helper_Abstract
{
    public function email($email)
    {
        return '<a href="mailto:'.$this->view->escape($email).'">'.$this->view->escape($email).'</a>';
    }
}