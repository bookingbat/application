<?php
class Zend_View_Helper_Phone
{
    public function phone($number)
    {
        if(ctype_digit($number) && strlen($number) == 10) {
            $number = '('.substr($number, 0, 3) .') '.
                substr($number, 3, 3) .'-'.
                substr($number, 6);
        }
        return $number;
    }
}