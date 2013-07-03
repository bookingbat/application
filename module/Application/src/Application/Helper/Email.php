<?php
class Zend_View_Helper_Email extends Zend_View_Helper_Abstract
{
    public function email($email)
    {
        return '<a href="mailto:'.$this->viewParams['escape($email).'">'.$this->viewParams['escape($email).'</a>';
    }
}