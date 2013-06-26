<?php

use Behat\MinkExtension\Context\MinkContext,
    Behat\Behat\Event\SuiteEvent;

class FeatureContext extends MinkContext
{
    /** @var  The PID of the php-cli server */
    static $pid;

    /**
     * @BeforeSuite
     */
    public static function prepare(SuiteEvent $event)
    {
        self::$pid = (int)`php --server=localhost:8888 --docroot="html" >> var/php-cli-server.log 2>&1 & echo $!`;
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
    }

    /**
     * @Given /^I have a user "([^"]*)" with password "([^"]*)"$/
     */
    public function iHaveAUserWithPassword($username, $password)
    {
        $userDataMapper = new User_DataMapper($this->db());
        $userDataMapper->insert(array(
            'username' => $username,
            'password' => $password,
        ));
    }

    function db()
    {
        return Zend_Registry::get('db');
    }
}
