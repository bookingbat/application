<?php
class Zend_View_Helper_Datetime
{
    public function datetime($date, $time = null)
    {
        return date('n/j/Y h:ia', strtotime($date . ' ' . $time));
    }
}