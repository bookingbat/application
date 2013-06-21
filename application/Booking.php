<?php
class Booking extends \Bookingbat\Availability\Booking
{
    function allowCancelLostByUser()
    {
        return !$this->completed();
    }

    function allowCancelByUser()
    {
        $today = strtotime($this->options['today']);
        $booking = strtotime($this->options['date']);

        if ($today > $booking) {
            return false;
        }

        $difference = ($booking - $today) / 60 / 60;
        if ($difference >= 24) {
            return true;
        }
        return false;
    }

    function completed()
    {
        $today = strtotime($this->options['today']);
        $booking = strtotime($this->options['date']);
        if ($today > $booking) {
            return true;
        }
        return false;
    }
}