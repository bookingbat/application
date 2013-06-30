<?php
class DurationFitterTest extends PHPUnit_Framework_TestCase
{

    function testShouldCalculateSizeOfWindow()
    {
        $fitter = new DurationFitter(array(
            'availability' => array(
                'start'=>'01:00',
                'end'=>'02:00',
            )
        ));

        $this->assertEquals(60, $fitter->windowSize(), 'it should calculate the size of the window');
    }

    function testShouldAllowDurationsThatFit()
    {
        $fitter = new DurationFitter(array(
            'durations' => array(30,60),
            'availability' => array(
                'start'=>'01:00',
                'end'=>'02:00',
            )
        ));

        $this->assertEquals(array(30,60), $fitter->allowed(), 'it should allow durations that fit');
    }

    function testShouldDisallowDurationThatDoNotFit()
    {
        $fitter = new DurationFitter(array(
            'durations' => array(30,60),
            'availability' => array(
                'start'=>'01:00',
                'end'=>'01:30',
            )
        ));

        $this->assertEquals(array(30), $fitter->allowed(), 'it should disallow duration that do not fit');
    }
}