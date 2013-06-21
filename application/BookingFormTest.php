<?php
require_once(__DIR__.'/../vendor/bookingbat/availability/Bookingbat/Availability/Availability.php');


set_include_path(get_include_path() . PATH_SEPARATOR
    . realpath(dirname(__FILE__) . '/../library') . '/');
require_once('Zend/Form.php');
require_once(dirname(__FILE__) . '/BookingForm.php');

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

        $form = new BookingForm;
        $form->setAvailability($input);
        $values = $form->getElement('time')->getMultiOptions();

        $expected = array(
            '01:00:00' => '01:00 am - 01:30 am',
            '01:30:00' => '01:30 am - 02:00 am',
            '02:00:00' => '02:00 am - 02:30 am',
        );
        $this->assertEquals($expected, $values, 'should not skip ahead on DST');
    }
}