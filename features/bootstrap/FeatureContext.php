<?php

use Behat\MinkExtension\Context\MinkContext,
    Behat\Behat\Event\ScenarioEvent,
    Behat\Behat\Event\SuiteEvent,
    Behat\Behat\Context\Step\Given,
    Behat\Behat\Exception\PendingException,
    Behat\Gherkin\Node\TableNode;

class FeatureContext extends MinkContext
{
    /** @var  The PID of the php-cli server */
    static $pid;

    /**
     * @BeforeSuite
     */
    public static function startServer()
    {
        self::$pid = (int)`php --server=localhost:8000 --docroot="public" >> var/php-cli-server.log 2>&1 & echo $!`;
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
        `vendor/bin/phinx migrate -e testing`;
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
    }

    /**
     * @Given /^I am logged in as admin$/
     */
    public function iAmLoggedInAsAdmin()
    {
        return array(
            new Given('I have an admin "admin" with password "admin123"'),
            new Given('I am on "/login"'),
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
        $userDataMapper = new \Application\User\DataMapper($this->db());
        $userDataMapper->insert(array(
            'username' => $username,
            'password' => '',
            'type'=>'staff'
        ));
    }

    /**
     * @Given /^I have the following user:$/
     */
    public function iHaveTheFollowingUser(TableNode $table)
    {
        $userDataMapper = new \Application\User\DataMapper($this->db());
        $userDataMapper->insert($table->getRowsHash());
    }

    /**
     * @Given /^I have an admin "([^"]*)" with password "([^"]*)"$/
     */
    public function iHaveAnAdminWithPassword($username, $password)
    {
        $userDataMapper = new \Application\User\DataMapper($this->db());
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
        $serviceDataMapper = new \Application\Service\DataMapper($this->db());
        $serviceDataMapper->insert(array(
            'name' => $name
        ));
    }

    /**
     * @Given /^the service "([^"]*)" is assigned to "([^"]*)"$/
     */
    public function theServiceIsAssignedTo($service, $staff)
    {
        $userDataMapper = new \Application\User\DataMapper($this->db());
        $staff = $userDataMapper->find(array(
            'username'=>$staff
        ));

        $serviceDataMapper = new \Application\Service\DataMapper($this->db());
        $service = $serviceDataMapper->find(array(
            'name' => $service
        ));

        $userDataMapper->assign($service['id'],$staff['id']);
    }

    /**
     * @Given /^the staff "([^"]*)" has the following availability:$/
     */
    public function theStaffHasTheFollowingAvailability($staff, TableNode $availability)
    {
        $userDataMapper = new \Application\User\DataMapper($this->db());
        $staff = $userDataMapper->find(array(
            'username'=>$staff
        ));

        $availability = $availability->getRowsHash();
        foreach($availability as $day => $times) {
            $availabilityDataMapper = new \Application\Availability\DataMapper($this->db());
            $availabilityDataMapper->insert(array(
                'staff_userid'=>$staff['id'],
                'day_of_week'=>$day,
                'start'=>$times[0],
                'end'=>$times[1]
            ));
        }
    }

    /**
     * @Given /^the service "([^"]*)" has the durations "([^"]*)"$/
     */
    public function theServiceHasTheDurations($service, $durations)
    {
        $serviceDataMapper = new \Application\Service\DataMapper($this->db());
        $service = $serviceDataMapper->find(array(
            'name' => $service
        ));

        $serviceDataMapper->update($service['id'], array(
            'durations'=>$durations
        ));
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

    /**
     * @Then /^field "([^"]*)" should have value "([^"]*)"$/
     */
    public function fieldShouldHaveValue($field, $value)
    {
        $this->assertSession()->fieldValueEquals($field, $value);
    }

    function db()
    {
        return Zend_Registry::get('db');
    }
}