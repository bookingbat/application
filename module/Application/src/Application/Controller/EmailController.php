<?php
class EmailController extends Controller
{
    function sendAction()
    {
        $select = $this->db()->select()
            ->from('email_queue')
            ->where('sent = 0')
            ->limit(10);
        $batch = $select->query()->fetchAll();
        foreach($batch as $batchItem) {
            $email = unserialize($batchItem['data']);
            $email->send();
            echo $batchItem['id'] . ' ';
            $this->db()->update('email_queue', array('sent'=>new Zend_Db_Expr('NOW()')), 'id='.$batchItem['id']);
        }
        echo 'batch done';
        $this->_helper->viewRenderer->setNoRender(true);
    }
}