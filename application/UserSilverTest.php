<?php
require_once('User.php');
class UserSilverTest extends PHPUnit_Framework_TestCase
{
    function testShouldBillSilverUserMonthlyRate()
    {
        $user = new User(array('member' => 2));
        $this->assertEquals(450, $user->billing(), 'should bill silver user at $450/month');
    }

    function testShouldBillSilverUserAtHourRate()
    {
        $user = new User(array('member' => 2));
        $this->assertEquals(56.25, $user->overage(array('training' => 60)), 'for silver user @ full hour, should be billed $56.25');
    }

    function testShouldInclude8Hours()
    {
        $user = new User(array('member' => 2));
        $this->assertEquals(450, $user->billing(array('training' => 60 * 8)), 'should include 8hrs training at no additional charge');
    }

    function testShouldBillForOver8hrs()
    {
        $user = new User(array('member' => 2));
        $this->assertEquals(506.25, $user->billing(array('training' => 60 * 9)), 'should include bill for 1hr overage, $450 month rate + $56.25 overage = $455.25');
    }

    function testShouldBillFor1hrMassage()
    {
        $user = new User(array('member' => 2));
        $this->assertEquals(72, $user->massageHourlyRate(), 'should bill $72, given 10% discount off $80 for 1hr massage for basic members');
    }

    function testShouldBill80For1AndHalfhrMassage()
    {
        $user = new User(array('member' => 2));
        $this->assertEquals(99, $user->massageHourAndAHalfRate(), 'should bill $99, given 10% discount off $110 for 1.5hr massage for basic members');
    }

    function testShouldInclude1hrMassageFree()
    {
        $user = new User(array('member' => 2));
        $this->assertEquals(450, $user->billing(array('massage' => 60)), 'should include 1hr massage for free');
    }

    function testShouldCalculate1hrMassageOverageIntoTotalBill()
    {
        $user = new User(array('member' => 2));
        $this->assertEquals(522, $user->billing(array('massage' => 120)), 'should add 1hr massage overage ($72) onto monthly rate ($450) and bill user $522');
    }

    function testShouldCalculate1AndHalfhrMassageOverageIntoTotalBill()
    {
        $user = new User(array('member' => 2));
        $this->assertEquals(549, $user->billing(array('massage' => 60 + 90)), 'should add 1.5hr massage ($99) onto monthly rate ($450) and bill user $549');
    }

    function testShouldCredit1hrOff1andhalfHourMassage()
    {
        $user = new User(array('member' => 2));
        $this->assertEquals(486, $user->billing(array('massage' => 90)), 'should credit the user 1hr towards a single 1.5hr massage, netting 1/2hr overage billed at $36 + monthly rate of $450 = $435');
    }

    function testShouldBillTwoTimes90MinuteRateFor3hrMassage()
    {
        $user = new User(array('member' => 2));
        $this->assertEquals(648, $user->billing(array('massage' => 60 + 60 * 3)), 'should bill 3hrs of massaging as two 1.5hr sessions ($99 X 2) + monthly rate of $450 = $648');
    }

    function testWhenUnderMaximumTrainingShouldAllowMore()
    {
        $user = new User(array('member' => 2));
        $this->assertTrue($user->isWithinIncluded(array('training' => 60 * 8)), 'when under maximum should allow another session');
    }

    function testWhenAtMaximumTrainingShouldNotAllowMore()
    {
        $user = new User(array('member' => 2));
        $this->assertFalse($user->isWithinIncluded(array('training' => 60 * 9)), 'when at maximum should not allow another session');
    }

    function testWhenUnderMaxMassageShouldAllowMore()
    {
        $user = new User(array('member' => 2));
        $this->assertTrue($user->isWithinIncluded(array('massage' => 60)), 'when under maximum should allow another session');
    }

    function testAtMaxMassageShouldNotAllowMore()
    {
        $user = new User(array('member' => 2));
        $this->assertFalse($user->isWithinIncluded(array('massage' => 60 * 2)), 'when at maximum should allow another session');
    }

}