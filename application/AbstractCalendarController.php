<?php
abstract class AbstractCalendarController extends Controller
{
    protected $limitMonths = 3;
    protected $number_of_days_in_month;

    function init()
    {
        $user = bootstrap::getInstance()->getUser();

        if ($this->requestedCalendarOutOfRange()) {
            $this->view->limitMonths = $this->limitMonths;
            $this->render('limited', null, true);
            $month = date('m');
            $year = date('Y');
        } else {
            $month = $this->month();
            $year = $this->year();
        }

        $this->number_of_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $this->view->year = $year;
        $this->view->month = $month;
        $this->view->month_name = date('F', strtotime('1-' . $month . '-' . $year));
        $this->view->number_of_days_in_month = $this->number_of_days_in_month;
    }

    function requestedCalendar()
    {
        return mktime(0, 0, 0, $this->month(), 0, $this->year());
    }

    function requestedCalendarOutOfRange()
    {
        if($this->tooFarInFuture()) {
            return true;
        }
        if($this->inPast()) {
            return true;
        }
        return false;
    }

    function tooFarInFuture()
    {
        return round(($this->requestedCalendar() - time()) / 60 / 60 / 24 / 30) >= $this->limitMonths;
    }

    function inPast()
    {
        return $this->month() < date('m') && $this->year() <= date('Y');
    }

    function dayString($day)
    {
        $dayString = sprintf('%s-%s-%s', $day, $this->month(), $this->year());
        return $dayString;
    }
}