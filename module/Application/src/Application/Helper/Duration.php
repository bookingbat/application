<?php
namespace Application\Helper;
use Zend\View\Helper\AbstractHelper;
class Duration extends AbstractHelper
{
    public function __invoke($appointmentDurationInMinutes)
    {
        return '<div class="label label-inverse">'.$appointmentDurationInMinutes / 60 . 'hr</div>';
    }
}