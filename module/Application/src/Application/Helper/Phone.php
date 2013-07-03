<?php
namespace Application\Helper;
use Zend\View\Helper\AbstractHelper;
class Phone extends AbstractHelper
{
    public function __invoke($number)
    {
        if(ctype_digit($number) && strlen($number) == 10) {
            $number = '('.substr($number, 0, 3) .') '.
                substr($number, 3, 3) .'-'.
                substr($number, 6);
        }
        return $number;
    }
}