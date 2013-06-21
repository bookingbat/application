<?php
class User
{
    protected $options;
    protected $additional_class;
    protected $additional_training;
    protected $additional_massage;

    function __construct($options = array())
    {
        $this->options = $options;
    }

    function overage($params)
    {
        $durationInMinutes = isset($params['training']) ? $params['training'] : 0;
        $durationInHours = $durationInMinutes / 60;
        return $durationInHours * $this->trainingHourlyRate();
    }

    function paidForAdditional($params)
    {
        $this->additional_class = isset($params['class']) ? $params['class'] : 0;
        $this->additional_training = isset($params['training']) ? $params['training'] : 0;
        $this->additional_massage = isset($params['massage']) ? $params['massage'] : 0;
    }

    function billing($params = array())
    {
        $trainingDurationInMinutes = isset($params['training']) ? $params['training'] : 0;
        $massageDurationInMinutes = isset($params['massage']) ? $params['massage'] : 0;
        if (!$trainingDurationInMinutes && !$massageDurationInMinutes) {
            return $this->monthlyRate();
        }

        $trainingDurationInMinutes -= $this->includedTraining();

        if ($trainingDurationInMinutes < 0) {
            $trainingDurationInMinutes = 0;
        }

        if (!$trainingDurationInMinutes && !$massageDurationInMinutes) {
            return $this->monthlyRate();
        }

        $trainingDurationInHours = $trainingDurationInMinutes / 60;
        $bill = $trainingDurationInHours * $this->trainingHourlyRate() + $this->monthlyRate();

        $massageDurationInMinutes -= $this->includedMassage();

        if ($massageDurationInMinutes < 0) {
            $massageDurationInMinutes = 0;
        }

        if ($massageDurationInMinutes % 90 == 0) {
            $bill += $massageDurationInMinutes / 90 * $this->massageHourAndAHalfRate();
        } else {
            $bill += $massageDurationInMinutes / 60 * $this->massageHourlyRate();
        }

        return $bill;
    }

    function isAtMaximum($params)
    {
        if (isset($params['class'])) {
            $class = isset($params['class']) ? $params['class'] : 0;
            if ($class >= $this->additional_class) {
                return true;
            }
        }

        if (isset($params['training'])) {
            $trainingDurationInMinutes = isset($params['training']) ? $params['training'] : 0;
            if ($trainingDurationInMinutes >= $this->includedTraining() + $this->additional_training) {
                return true;
            }
        }

        if (isset($params['massage'])) {
            $massageDurationInMinutes = isset($params['massage']) ? $params['massage'] : 0;
            if ($massageDurationInMinutes >= $this->includedMassage() + $this->additional_massage) {
                return true;
            }
        }
        return false;
    }

    function isWithinIncluded($params)
    {
        $trainingDurationInMinutes = isset($params['training']) ? $params['training'] : 0;
        if ($trainingDurationInMinutes > $this->includedTraining()) {
            return false;
        }
        $massageDurationInMinutes = isset($params['massage']) ? $params['massage'] : 0;
        if ($massageDurationInMinutes > $this->includedMassage()) {
            return false;
        }
        return true;
    }

    function trainingHourlyRate()
    {
        switch ($this->options['member']) {
            default:
                return 60;
            case 1:
                return 62.5;
            case 2:
                return 56.25;
            case 3:
                return 54.20;
        }
    }

    function massageHourlyRate()
    {
        switch ($this->options['member']) {
            default:
                return 80;
            case 1:
                return 72;
            case 2:
                return 72;
            case 3:
                return 72;
        }
    }

    function massageHourAndAHalfRate()
    {
        switch ($this->options['member']) {
            default:
                return 110;
            case 1:
                return 99;
            case 2:
                return 99;
            case 3:
                return 0;
        }
    }

    function monthlyRate($memberLevel = null)
    {
        $memberLevel = $memberLevel ? $memberLevel : $this->options['member'];
        switch ($memberLevel) {
            case 1:
                return 250;
            case 2:
                return 450;
            case 3:
                return 650;
            case 4:
                return 1;
        }
    }

    function trainingAllowed()
    {
        return $this->includedTraining() + $this->additional_training;
    }

    function massageAllowed()
    {
        return $this->includedMassage() + $this->additional_massage;
    }

    function includedTraining()
    {
        switch ($this->options['member']) {
            case 1:
                return 60 * 4;
            case 2:
                return 60 * 8;
            case 3:
                return 60 * 12;
        }
        return 0;
    }

    function includedMassage()
    {
        switch ($this->options['member']) {
            case 2:
                return 60;
            case 3:
                return 60;
        }
        return 0;
    }

    function planName($planNumber = null)
    {
        if(!isset($this->options['member'])) {
            $this->options['member'] = 0;
        }
        $planNumber = $planNumber ? $planNumber : $this->options['member'];
        switch ($planNumber) {
            default:
                return 'guest';
            case 1:
                return 'basic';
            case 2:
                return 'silver';
            case 3:
                return 'gold';
            case 4:
                return 'month of classes';
        }
    }
}