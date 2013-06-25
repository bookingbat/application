<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends \Behat\MinkExtension\Context\MinkContext
{
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

    public function clickLink($link)
    {
 //       var_dump($this->getSession()->getPage()->getContent());
        return parent::clickLink($link);
    }

    function db()
    {
        return Zend_Registry::get('db');
    }
}
