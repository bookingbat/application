<?php
class DurationFitter
{
    protected $parameters;

    function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    function allowed()
    {
        $allowed = array();
        foreach($this->parameters['durations'] as $duration) {
            if($duration>$this->windowSize()) {
                continue;
            }
            array_push($allowed,$duration);
        }
        return $allowed;
    }

    function windowSize()
    {
        $start = new DateTime('2013-03-21 ' . $this->parameters['availability']['start']);
        $end = new DateTime('2013-03-21 ' . $this->parameters['availability']['end']);
        $size_of_window = $end->diff($start);
        $hours = $size_of_window->format('%h');
        $minutes = $size_of_window->format('%i');
        return $hours*60+$minutes;
    }
}