<?php
class Zend_View_Helper_Money
{
    public function money($val)
    {
        return '$'.number_format($val);
    }
}