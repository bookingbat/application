<?php
namespace Application\Helper;
use Zend\View\Helper\AbstractHelper;
class Datetime extends AbstractHelper
{
    public function __invoke($date, $time = null)
    {
        return date('n/j/Y h:ia', strtotime($date . ' ' . $time));
    }
}