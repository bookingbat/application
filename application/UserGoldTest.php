<?php
require_once('User.php');
class UserGoldTest extends PHPUnit_Framework_TestCase
{

    function testShouldBillGoldUserMonthlyRate()
    {
        $user = new User(array('member' => 3));
        $this->assertEquals(650, $user->billing(), 'should bill gold user at 650/month');
    }

    function testShouldBillGoldUserAtHourRate()
    {
        $user = new User(array('member' => 3));
        $this->assertEquals(54.20, $user->overage(array('training' => 60)), 'for basic user @ full hour, should be billed $54.20');
    }

    function testShouldInclude12Hours()
    {
        $user = new User(array('member' => 3));
        $this->assertEquals(650, $user->billing(array('training' => 60 * 12)), 'should include 12hrs training at no additional charge');
    }

    function testShouldBillForOver8hrs()
    {
        $user = new User(array('member' => 3));
        $this->assertEquals(704.2, $user->billing(array('training' => 60 * 13)), 'should include bill for 1hr overage, $599 month rate + $54.20 overage = $704.2');
    }

    function testShouldInclude1hrMassageFree()
    {
        $user = new User(array('member' => 3));
        $this->assertEquals(650, $user->billing(array('massage' => 60)), 'should include 1hr massage for free');
    }

    function testShouldCalculate1hrMassageOverageIntoTotalBill()
    {
        $user = new User(array('member' => 3));
        $this->assertEquals(722, $user->billing(array('massage' => 120)), 'should add 1hr massage overage ($72) onto monthly rate ($650) and bill user $722');
    }
}