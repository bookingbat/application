<?php
require_once('Booking.php');
use Application\Booking;
class BookingTest extends PHPUnit_Framework_TestCase
{
    function testShouldNotAllowCancelByClientWithin24Hours()
    {
        $booking = new Booking(array(
            'date' => '2013-04-18',
            'today' => '2013-04-18'
        ));
        $this->assertFalse($booking->allowCancelByUser(), 'should not allow cancel by user within 24hrs');
    }

    function testShouldAllowCancelLostByClientWithin24Hours()
    {
        $booking = new Booking(array(
            'date' => '2013-04-18',
            'today' => '2013-04-18'
        ));
        $this->assertTrue($booking->allowCancelLostByUser(), 'should allow cancel w/ lost credit by user within 24hrs');
    }

    function testShouldAllowCancelByClientBefore24Hours()
    {
        $booking = new Booking(array(
            'date' => '2013-04-18',
            'today' => '2013-04-17'
        ));
        $this->assertTrue($booking->allowCancelByUser(), 'should allow cancel by user before 24hrs from appointment start');
    }

    function testShouldNotAllowCancelByClientAfterAppointment()
    {
        $booking = new Booking(array(
            'date' => '2013-04-18',
            'today' => '2013-04-25'
        ));
        $this->assertFalse($booking->allowCancelByUser(), 'should not allow cancel by user after appointment');
    }

    function testShouldNotAllowCancelLostByClientAfterAppointment()
    {
        $booking = new Booking(array(
            'date' => '2013-04-18',
            'today' => '2013-04-25'
        ));
        $this->assertFalse($booking->allowCancelLostByUser(), 'should not allow cancel w/ lost credit by user after appointment');
    }

    function testShouldConsiderPastAppointmentCompleted()
    {
        $booking = new Booking(array(
            'date' => '2013-04-18',
            'today' => '2013-04-25'
        ));
        $this->assertTrue($booking->completed(), 'should consider past appointment completed');
    }

    function testShouldConsiderCurrentAppointmentPending()
    {
        $booking = new Booking(array(
            'date' => '2013-04-25',
            'today' => '2013-04-25'
        ));
        $this->assertFalse($booking->completed(), 'should consider current appointment pending');
    }
}