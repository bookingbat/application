<?php
define('FAMEFIT_BASE_PATH', dirname(__FILE__) . '/..');
require_once 'vendor/autoload.php';
require_once('vendor/bookingbat/engine/Bookingbat/Availability/Availability.php');
require_once('vendor/bookingbat/engine/Bookingbat/Availability/MassageAvailability.php');

class bootstrap
{

    static $_instance;
    protected $router;
    protected $frontController;

    /** @var Zend_Session */
    protected $session;

    /* @return bootstrap */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    function execute()
    {
        $this->setTimezone();
        $this->setIncludePath();
        $this->setupAutoloading();

        $this->frontController = Zend_Controller_Front::getInstance();
        $this->getRouter();
        $this->addRoutes();
        $this->setupDatabaseConfig();
        $this->startDb();
        $this->setupSession();

        Zend_Controller_Front::getInstance()->setControllerDirectory(
            array(
                'default' => FAMEFIT_BASE_PATH . '/application/Default/controllers',
            )
        );
        Zend_Layout::startMvc(FAMEFIT_BASE_PATH . '/layout/');
        $this->setupViewHelperPaths();
        $this->getUser();
        return $this;
    }

    function setTimezone()
    {
        date_default_timezone_set('America/New_York');
    }

    function addRoutes()
    {
        $this->router->addRoute(
            'default',
            new Zend_Controller_Router_Route('/:controller/:action/*',
                array(
                    'controller' => 'calendar',
                    'action'=>'index'
                ))
        );
        $this->router->addRoute(
            'services',
            new Zend_Controller_Router_Route('/services/:action/*',
                array(
                    'controller' => 'services',
                    'action'=>'choose'
                ))
        );
        $this->router->addRoute(
            'calendar',
            new Zend_Controller_Router_Route('/calendar/*',
                array(
                    'controller' => 'calendar',
                    'action'=>'index'
                ))
        );
        $this->router->addRoute(
            'appointments',
            new Zend_Controller_Router_Route('appointments/:action/*',
                array(
                    'controller' => 'appointments',
                    'action'=>'index'
                ))
        );
        $this->router->addRoute(
            'availability',
            new Zend_Controller_Router_Route('availability/*',
                array(
                    'controller' => 'availability',
                    'action'=>'index'
                ))
        );
        $this->router->addRoute(
            'make-booking',
            new Zend_Controller_Router_Route('make-booking/:action/*',
                array(
                    'controller' => 'makebooking',
                    'action'=>'booking'
                ))
        );
    }

    function setupViewHelperPaths()
    {

    }

    /** @return Zend_Session */
    public function getSession()
    {
        return $this->session;
    }

    /** @return User */
    function getUser()
    {
        if (isset($this->session->user) && $this->session->user) {
            $this->user = $this->session->user;
            Zend_Registry::set('user', $this->user);
        } else {
            return false;
        }
        return $this->user;
    }

    function userLogout()
    {
        unset($this->getSession()->user);
    }

    function basePath()
    {
        return FAMEFIT_BASE_PATH;
    }

    function setupSession()
    {
        $this->session = new Zend_Session_Namespace('famefitness');
    }

    function getRouter()
    {
        if (!is_null($this->router)) {
            return $this->router;
        }
        if (is_null($this->frontController)) {
            throw new Exception('No front controller');
        }
        $this->router = $this->frontController->getRouter();
        return $this->router;
    }

    function setIncludePath()
    {
        set_include_path(
            FAMEFIT_BASE_PATH . '/library/' . PATH_SEPARATOR .
                FAMEFIT_BASE_PATH . '/application/' . PATH_SEPARATOR .
                get_include_path()
        );
    }

    function setupAutoloading()
    {
        require_once 'Zend/Loader/Autoloader.php';
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);
    }

    function setupDatabaseConfig()
    {
        if(preg_match('#^([0-9]+)\.bookingbat#',$_SERVER['HTTP_HOST'],$matches)) {
            $id = $matches[1];
            $file = '../website/var/website_configs/'.$id;
            if(!file_exists($file)) {
                throw new Exception('Unkown client');
            }
            define('APPLICATION_ENVIRONMENT','production');
        } else {
            define('APPLICATION_ENVIRONMENT','localhost');
            $file = 'database-config.ini';
        }

        $config = new Zend_Config_Ini($file, APPLICATION_ENVIRONMENT);
        Zend_Registry::set('database_config', $config);
        Zend_Registry::set('mysql_command', $config->mysql_command);
    }

    function startDb()
    {
        $configuration = Zend_Registry::get('database_config');
        Zend_Registry::set('db', new Zend_Db_Adapter_Pdo_Mysql($configuration->database->params));
    }

    function getRequest()
    {
        return $this->frontController->getRequest();
    }

    function authorizenetLogin()
    {
        return '2Scf4XP24';
    }

    function authorizenetKey()
    {
        return '7qq8j3Sz429D9Zdg';
    }

}

$bootstrap = bootstrap::getInstance();
$bootstrap->execute();