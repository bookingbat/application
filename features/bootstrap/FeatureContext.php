<?php

use Behat\MinkExtension\Context\MinkContext,
    Behat\Behat\Event\SuiteEvent,
    Behat\Behat\Context\Step\Given,
    Behat\Behat\Exception\PendingException;

class FeatureContext extends MinkContext
{
    /** @var  The PID of the php-cli server */
    static $pid;

    /**
     * @BeforeSuite
     */
    public static function prepare(SuiteEvent $event)
    {
        `mysql --user=root -e "drop database IF EXISTS bookingbat_tests"`;
        `mysql --user=root -e "create database bookingbat_tests"`;
        `mysql --user=root bookingbat_tests < install.sql`;

        self::$pid = (int)`php --server=localhost:8000 --docroot="html" >> var/php-cli-server.log 2>&1 & echo $!`;
        sleep(1);
    }

    /**
     * @AfterSuite
     */
    public static function tearDown(SuiteEvent $event)
    {
        $cmd = 'kill '.self::$pid;
        exec($cmd);
    }

    /** @AfterScenario */
    public function after($event)
    {
        if(4==$event->getResult()) {
            echo $this->getSession()->getPage()->getContent();
        }
        $this->db()->query('truncate `user`');
    }

    /**
     * @Given /^I am logged in as admin$/
     */
    public function iAmLoggedInAsAdmin()
    {
        return array(
            new Given('I have an admin "admin" with password "admin123"'),
            new Given('I am on "/user/login"'),
            new Given('I fill in "username" with "admin"'),
            new Given('I fill in "password" with "admin123"'),
            new Given('I press "login"')
        );
    }

    /**
     * @Then /^I should be on "\/user\/register$/
     */
    public function iShouldBeOnUserRegister()
    {
        throw new PendingException();
    }

    /**
     * @When /^I click "([^"]*)"$/
     */
    public function iClick($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I have an admin "([^"]*)" with password "([^"]*)"$/
     */
    public function iHaveAnAdminWithPassword($username, $password)
    {
        $userDataMapper = new User_DataMapper($this->db());
        $userDataMapper->insert(array(
            'username' => $username,
            'password' => $password,
            'type'=>'admin'
        ));
    }

    function db()
    {
        return Zend_Registry::get('db');
    }
}
