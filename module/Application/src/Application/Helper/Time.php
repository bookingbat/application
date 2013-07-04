<?php
namespace Application\Helper;
use Zend\View\Helper\AbstractHelper;
class Time extends AbstractHelper
{
    public function __invoke($time)
    {
        if(date('i', strtotime($time)) == 0) {
            return date('ga', strtotime($time));
        }
        return date('g:ia', strtotime($time));
    }
}