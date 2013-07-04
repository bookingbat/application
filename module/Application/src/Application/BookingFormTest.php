<?php
class BookingFormTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        date_default_timezone_set('America/New_York');
        $this->dateBefore = date('m-d-Y');
        shell_exec("date 03-10-2013");
    }

    function tearDown()
    {
        shell_exec("date " . $this->dateBefore);
    }

    function testShouldNotSkipAheadOnDST()
    {
        $input = array(
            array(
                'start' => '01:00:00',
                'end' => '02:30:00'
            )
        );

        $form = new \Application\BookingForm;
        $form->setAvailability($input,30);
        $values = $form->getElement('time')->getMultiOptions();

        $expected = array(
            '01:00:00' => '1am',
            '01:30:00' => '1:30am',
            '02:00:00' => '2am',
        );
        $this->assertEquals($expected, $values, 'should not skip ahead on DST');
    }
}