<?php
class Zend_View_Helper_Time
{
    public function time($time)
    {
        if(date('i', strtotime($time)) == 0) {
            return date('ga', strtotime($time));
        }
        return date('g:i a', strtotime($time));
    }
}