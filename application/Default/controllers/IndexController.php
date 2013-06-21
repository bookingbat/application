<?php
class IndexController extends Zend_Controller_Action
{

    function indexAction()
    {
        $user = bootstrap::getInstance()->getUser();
        $this->view->isLoggedIn = $user['id'];
    }


}