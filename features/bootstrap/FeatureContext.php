<?php

use Behat\MinkExtension\Context\MinkContext,
    Behat\Behat\Event\ScenarioEvent,
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
    public static function startServer()
    {
        self::$pid = (int)`php --server=localhost:8000 --docroot="html" >> var/php-cli-server.log 2>&1 & echo $!`;
        sleep(1);
    }

    /**
     * @BeforeScenario
     */
    public static function resetDB(ScenarioEvent $event)
    {
        `mysql --user=root -e "drop database IF EXISTS bookingbat_tests"`;
        `mysql --user=root -e "create database bookingbat_tests"`;
        `mysql --user=root bookingbat_tests < install.sql`;
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
            //echo $this->getSession()->getPage()->getContent();
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
     * @When /^I follow "([^"]*)" for "([^"]*)"$/
     */
    public function iFollowFor($linkCSS, $recordName)
    {
        $page = $this->getMink()->getSession()->getPage();

        $element = $page->find('css',".record-$recordName a.$linkCSS");
        $element->click();
    }

    /**
     * @Then /^I should be on "\/user\/register$/
     */
    public function iShouldBeOnUserRegister()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I have a staff "([^"]*)"$/
     */
    public function iHaveAStaff($username)
    {
        $userDataMapper = new User_DataMapper($this->db());
        $userDataMapper->insert(array(
            'username' => $username,
            'password' => '',
            'type'=>'staff'
        ));
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

    /**
     * @Given /^I have a service "([^"]*)"$/
     */
    public function iHaveAService($name)
    {
        $serviceDataMapper = new Service_DataMapper($this->db());
        $serviceDataMapper->insert(array(
            'name' => $name
        ));
    }

    /**
     * @Given /^the service "([^"]*)" is assigned to "([^"]*)"$/
     */
    public function theServiceIsAssignedTo($service, $staff)
    {
        $userDataMapper = new User_DataMapper($this->db());
        $staff = $userDataMapper->find(array(
            'username'=>$staff
        ));

        $serviceDataMapper = new Service_DataMapper($this->db());
        $service = $serviceDataMapper->find(array(
            'name' => $service
        ));
        var_dump($service);var_dump($staff);
        $userDataMapper->assign($service['id'],$staff['id']);
    }

    /**
     * @Then /^I dump the page$/
     */
    public function iDumpThePage()
    {
        echo $this->getMink()->getSession()->getPage()->getContent();
    }

    /** Patches bug https://github.com/Behat/Behat/issues/298 */
    public function assertCheckboxChecked($checkbox)
    {
        $checked = $this->assertSession()->fieldExists($checkbox)->getAttribute('checked');
        if(!$checked) {
            throw new Exception('Checkbox should be checked');
        }
    }

    function db()
    {
        return Zend_Registry::get('db');
    }
}
