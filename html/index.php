<?php
ini_set('display_errors', 'on');
chdir('..');
try {
    require 'application/bootstrap.php';
    Zend_Controller_Front::getInstance()->dispatch();
} catch (Exception $e) {

    if (defined('APPLICATION_ENVIRONMENT') && APPLICATION_ENVIRONMENT != 'production'  )
    {
        $message = 'Unexpected exception of type [' . get_class($e) .
            '] with message [' . $e->getMessage() .
            '] in [' . $e->getFile() .
            ' line ' . $e->getLine() . ']';
        echo '<html><body><center>' . $message;

        echo '<br /><br />' . $e->getMessage() . '<br />' . '<div align="left">Stack Trace:' . '<pre>' . $e->getTraceAsString() . '</pre></div>';
    } else {
        echo 'An exception occurred';
    }
    echo '</body></html>';
    exit(1);
}

