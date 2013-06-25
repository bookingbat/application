<?php
use Goutte\Client;

class LoginTest extends PHPUnit_Framework_TestCase
{
    protected $port=7575;
    protected $baseURL;
    protected $pid;

    function setUp()
    {
        // Start the built in PHP server in background & get it's PID
        $this->pid = exec("php --server=localhost:$this->port --docroot=html >> php-server.log 2>&1 & echo $!");
        $this->baseURL = 'http://localhost:'.$this->port;
        // Wait a few seconds for it to start up
        sleep(1);
    }

    function tearDown()
    {
        // Kill the server when we are done with it
        `kill $this->pid`;
    }

    function testLogin()
    {
        $this->createUser('admin','admin');

        $driver = new \Behat\Mink\Driver\GoutteDriver();
        $session = new \Behat\Mink\Session($driver);
        $session->visit($this->baseURL);
        $page = $session->getPage();
        $el = $page->find('css', '.btn-login');
        $el->click();
        var_dump($session->getCurrentUrl());

    }

    function createUser($username, $password)
    {

    }
}