<?php
require_once('User.php');
class UserGuestTest extends PHPUnit_Framework_TestCase
{
    function testShouldBeAtMaximumWhenUserPaidForNoClasses()
    {
        $user = new User(array('member' => 0));
        $this->assertTrue($user->isAtMaximum(array('class' => 1)), 'should be at maximum classes when user has not paid for additional');
    }

    function testShouldNotBeAtMaximumIfPaidAdditionalClass()
    {
        $user = new User(array('member' => 0));
        $user->paidForAdditional(array('class' => 2));
        $this->assertFalse($user->isAtMaximum(array('class' => 1)), 'should not be at maximum classes when user has paid for additional');
    }
}