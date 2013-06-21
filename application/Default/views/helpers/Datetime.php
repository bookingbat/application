<?php
class Zend_View_Helper_Datetime
{
    public function datetime($date, $time = null)
    {
        return date('h:ia n/j/Y', strtotime($date . ' ' . $time));
    }
}