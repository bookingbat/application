<?php
namespace Application\Helper;
use Zend\View\Helper\AbstractHelper;
class Email extends AbstractHelper
{
    public function __invoke($email)
    {
        return '<a href="mailto:'.$this->getView()->escapeHTML($email).'">'.$this->getView()->escapeHTML($email).'</a>';
    }
}