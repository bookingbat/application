<?php
class Zend_View_Helper_Dayofweek
{
    public function dayofweek($daynumber)
    {
        $names = array(1 => 'Sunday', 2 => 'Monday', 3 => 'Tuesday', 4 => 'Wednesday', 5 => 'Thursday', 6 => 'Friday', '7' => 'Saturday');
        return $names[$daynumber];
    }
}