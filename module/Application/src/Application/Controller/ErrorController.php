<?php
/**
 * Error controller, handles user denied & 404s and other errors
 *
 * @package default
 * @subpackage controllers
 */
class ErrorController extends Zend_Controller_Action
{
    function errorAction()
    {
        // Grab the error object from the request
        $errors = $this->params('error_handler');

        // $errors will be an object set as a parameter of the request object,
        // type is a property
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->viewParams['message = 'Page not found';
                break;
            default:
                // application error 
                $this->getResponse()->setHttpResponseCode(500);
                $this->viewParams['message = 'Application error';
                break;
        }

        // pass the environment to the view script so we can conditionally
        // display more/less information
        $this->viewParams['env = APPLICATION_ENVIRONMENT;

        // pass the actual exception object to the view
        $this->viewParams['exception = $errors->exception;

        // pass the request to the view
        $this->viewParams['request = $errors->request;
        $viewModel->setTemplate('error', null, true);

    }

    function deniedAction()
    {
        /*if( !$this->getUser()->isAuthenticated() )
        {
           return $this->_forward( 'index', 'Login', 'User' );
        } */
        $this->viewParams['user = $this->getUser();
    }

    function notfoundAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
    }

} 