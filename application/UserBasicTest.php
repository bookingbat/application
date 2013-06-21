<?php
require_once('User.php');
class UserBasicTest extends PHPUnit_Framework_TestCase
{
    function testShouldBillMonthlyRate()
    {
        $user = new User(array('member' => 1));
        $this->assertEquals(250, $user->billing(), 'should bill basic user at $250/month');
    }

    function testShouldBillHalfHourRateForTraining()
    {
        $user = new User(array('member' => 1));
        $this->assertEquals(31.25, $user->overage(array('training' => 30)), 'should be billed $31.25 for basic user @ half hour training');
    }

    function testShouldBillHourRateForTraining()
    {
        $user = new User(array('member' => 1));
        $this->assertEquals(62.5, $user->overage(array('training' => 60)), ' should be billed $62.50 for basic user @ full hour training');
    }

    function testShouldInclude4HoursTraining()
    {
        $user = new User(array('member' => 1));
        $this->assertEquals(250, $user->billing(array('training' => 60 * 4)), 'should include 4hrs training at no additional charge');
    }

    function testShouldNotBeAtMaximumTraining()
    {
        $user = new User(array('member' => 1));
        $this->assertFalse($user->isAtMaximum(array('training' => 60 * 3)), 'should not be at maximum training');
    }

    function testShouldBeAtMaximumTrainingWhenAtLimit()
    {
        $user = new User(array('member' => 1));
        $this->assertTrue($user->isAtMaximum(array('training' => 60 * 4)), 'should be at maximum training when at limit');
    }

    function testShouldBeAtMaximumTrainingWhenOver()
    {
        $user = new User(array('member' => 1));
        $this->assertTrue($user->isAtMaximum(array('training' => 60 * 5)), 'should be at maximum training when over limit');
    }

    function testShouldNotBeAtMaximumIfPaidAdditional()
    {
        $user = new User(array('member' => 1));
        $user->paidForAdditional(array('training' => 60));
        $this->assertFalse($user->isAtMaximum(array('training' => 60 * 4)), 'should not be at maximum training when user has paid for additional');
    }

    function testShouldCalculateTotalTrainingAllowedWhenPaidForExtra()
    {
        $user = new User(array('member' => 1));
        $user->paidForAdditional(array('training' => 60));
        $this->assertEquals(60 * 5, $user->trainingAllowed(), 'should calculate total training allowed when paid for extra');
    }

    function testWhenUnderMaximumTrainingShouldAllowMore()
    {
        $user = new User(array('member' => 1));
        $this->assertTrue($user->isWithinIncluded(array('training' => 60 * 4)), 'when under maximum should allow another session');
    }

    function testWhenAtMaximumTrainingShouldNotAllowMore()
    {
        $user = new User(array('member' => 1));
        $this->assertFalse($user->isWithinIncluded(array('training' => 60 * 5)), 'when at maximum should not allow another session');
    }

    function testWhenAtMaximumMassageShouldNotAllowMore()
    {
        $user = new User(array('member' => 1));
        $this->assertFalse($user->isWithinIncluded(array('massage' => 60)), 'when at maximum should not allow another session');
    }

    function testShouldBillForOver4hrsTraining()
    {
        $user = new User(array('member' => 1));
        $this->assertEquals(312.5, $user->billing(array('training' => 60 * 5)), 'should include bill for 1hr training overage, $250 month rate + $62.5 overage = $261.50');
    }

    function testShouldBill72For1hrMassage()
    {
        $user = new User(array('member' => 1));
        $this->assertEquals(72, $user->massageHourlyRate(), 'should bill $72, given 10% discount off $80 for 1hr massage for basic members');
    }

    function testShouldBill80For1AndHalfhrMassage()
    {
        $user = new User(array('member' => 1));
        $this->assertEquals(99, $user->massageHourAndAHalfRate(), 'should bill $99, given 10% discount off $110 for 1.5hr massage for basic members');
    }

    function testShouldCalculate1hrMassageIntoTotalBill()
    {
        $user = new User(array('member' => 1));
        $this->assertEquals(322, $user->billing(array('massage' => 60)), 'should add 1hr massage ($72) onto monthly rate ($250) and bill user $322');
    }

    function testShouldCalculate1AndHalfhrMassageIntoTotalBill()
    {
        $user = new User(array('member' => 1));
        $this->assertEquals(349, $user->billing(array('massage' => 90)), 'should add 1.5hr massage ($99) onto monthly rate ($250) and bill user $349');
    }

    function testShouldAddOverageForMassageAndTraining()
    {
        $user = new User(array('member' => 1));
        $bill = $user->billing(array(
            'training' => 60 * 5,
            'massage' => 60
        ));
        $this->assertEquals(384.5, $bill, 'should add 1hr massage ($72) + 1hr training ($62.5) + monthly fee ($250) = $384.50');
    }

    function testShouldHaveNOMassage()
    {
        $user = new User(array('member' => 1));
        $this->assertTrue($user->isAtMaximum(array('massage' => 0)), 'should not be at maximum massage');
    }

    function testShouldBeAtMaximumMassage()
    {
        $user = new User(array('member' => 1));
        $this->assertTrue($user->isAtMaximum(array('massage' => 60)), 'should be at maximum massage');
    }

    function testShouldBeAtMaximumMassageWhenOver()
    {
        $user = new User(array('member' => 1));
        $this->assertTrue($user->isAtMaximum(array('massage' => 60 * 2)), 'should be at maximum massage when over');
    }

    function testShouldNotBeAtMaximumIfPaidAdditionalMassage()
    {
        $user = new User(array('member' => 1));
        $user->paidForAdditional(array('massage' => 120));
        $this->assertFalse($user->isAtMaximum(array('massage' => 60)), 'should not be at maximum massage when user has paid for additional');
    }

    function testShouldCalculateTotalMassageAllowedWhenPaidForExtra()
    {
        $user = new User(array('member' => 1));
        $user->paidForAdditional(array('massage' => 60));
        $this->assertEquals(60, $user->massageAllowed(), 'should calculate total massage allowed when paid for extra');
    }

}