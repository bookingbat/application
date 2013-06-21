<?php
require_once('User.php');
class UserTest extends PHPUnit_Framework_TestCase
{
    function testShouldNonMembersBilledAt30PerHalfHour()
    {
        $user = new User(array('member' => 0));
        $this->assertEquals(30, $user->billing(array('training' => 30)), 'non member for a half hour, should be billed $30');
    }

    function testShouldNonMembersBilledAt60PerHour()
    {
        $user = new User(array('member' => 0));
        $this->assertEquals(60, $user->billing(array('training' => 60)), 'non member for an hour, should be billed $60');
    }

    function testShouldBill80For1hrMassage()
    {
        $user = new User(array('member' => 0));
        $this->assertEquals(80, $user->billing(array('massage' => 60)), 'should bill $80 for 1hr massage for non members');
    }

    function testShouldBill110For1andhalfhrMassage()
    {
        $user = new User(array('member' => 0));
        $this->assertEquals(110, $user->billing(array('massage' => 90)), 'should bill $110 for 1.5hr massage for non members');
    }

    function testShouldGetPlanNameForGuest()
    {
        $user = new User;
        $this->assertEquals('guest',$user->planName(), 'should get plan name for guest (when $member is null)');
    }

}