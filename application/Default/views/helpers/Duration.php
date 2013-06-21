<?php
class Zend_View_Helper_Duration
{
    public function duration($appointmentDurationInMinutes)
    {
        return '<div class="label label-inverse">'.$appointmentDurationInMinutes / 60 . 'hr</div>';
    }
}