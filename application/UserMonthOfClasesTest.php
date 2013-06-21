<?php
require_once('User.php');
class UserMonthOfClassesTest extends PHPUnit_Framework_TestCase
{
    function testShouldBillHourRateForTraining()
    {
        $user = new User(array('member' => 1));
        $this->assertEquals(62.5, $user->overage(array('training' => 60)), ' should be billed $62.50 for basic user @ full hour training');
    }
}