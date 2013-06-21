<?php
class BillingController extends Controller
{
    protected $error_message;

    function paymenthistoryAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->_redirect('/');
        }
        $userObject = new User;
        if($user['type']=='admin') {
            $userId = $this->_getParam('id');
        } else {
            $userId = $user['id'];
        }


        $select = $this->db()->select()
            ->from('user_payments')
            ->where('user_id=?', $userId)
            ->order('payment_id DESC');
        $payments = $select->query()->fetchAll();

        $this->view->payments = $payments;
        $this->view->user = $this->userObjectForBillingCalculations($userId);
        $this->render('payment-history');
    }

    function cancelplanAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] && !$user['type'] == 'admin') {
            return $this->_redirect('/');
        }

        $userId = $this->_getParam('id');

        if(!$this->getParam('confirm')) {
            $this->view->user = $this->userData($userId);
            return $this->render('cancel-confirm');
        }

        $this->db()->update('user_subscriptions', array(
            'end' => new Zend_Db_Expr('NOW()')
        ), 'user_id=' . (int)$userId . ' and `end` is null');

        $this->db()->update('user', array('member' => 0), 'id=' . (int)$userId);
        $this->_helper->FlashMessenger->addMessage('Canceled User Plan');
        return $this->_redirect('/user/manage');
    }

    function planAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->_redirect('/');
        }

        if($user['type']=='admin') {
            $userId = $this->_getParam('id');
        } else {
            $userId = $user['id'];
        }

        $userObject = $this->userObjectForBillingCalculations($userId);

        $form = $this->planForm();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $values = $form->getValues();
            $values['amount'] = $userObject->monthlyRate($values['plan']);
            if($user['type']=='admin') {
                $transactionID = 0;
            } else {
                $transactionID = $this->startSubscription($values);
            }
            if (false === $transactionID) {
                $form->addError($this->error_message);
                $form->markAsError();
            } elseif(is_numeric($transactionID)) {
                $this->db()->update('user_subscriptions', array(
                    'end' => new Zend_Db_Expr('NOW()')
                ), 'user_id=' . (int)$userId . ' and `end` is null');

                $this->db()->insert('user_subscriptions', array(
                    'user_id' => $userId,
                    'start' => new Zend_Db_Expr('NOW()'),
                    'plan' => $this->_getParam('plan'),
                    'authorizenet_transaction_id' => $transactionID
                ));
                $this->db()->update('user', array('member' => (int)$this->_getParam('plan')), 'id=' . (int)$userId);

                $this->_helper->FlashMessenger->addMessage('Added User Plan');
                if($user['type']=='admin') {
                    return $this->_redirect('/user/manage');
                } else {
                    return $this->_redirect('/billing/plan');
                }

            } else {
                throw new Exception('no transaction id set');
            }
        }

        $select = $this->db()->select()
            ->from('user_subscriptions')
            ->where('user_id=?', $userId)
            ->where('end IS NOT NULL')
            ->order('end DESC');
        $plans = $select->query()->fetchAll();

        $this->view->past_plans = $plans;

        $select = $this->db()->select()
            ->from('user_subscriptions')
            ->where('user_id=?', $userId)
            ->where('end IS NULL');
        $plans = $select->query()->fetchAll();

        $this->view->current_plan = isset($plans[0]) ? $plans[0] : array('plan'=>'guest','end'=>null);
        $this->view->form = $form;

        $this->view->user = $userObject;

        $this->render('plan');
    }

    function planForm()
    {
        $user = bootstrap::getInstance()->getUser();
        $userObject = new User;
        if($user['type']=='admin') {
            $form = new Zend_Form;
        }else{
            $form = new PaymentForm(array('user' => $user));
        }
        $form->addElement('radio', 'plan', array(
            'label' => 'Plan',
            'multiOptions' => array(
                1 => 'Basic @ $' . $userObject->monthlyRate(1) . '/month',
                2 => 'Silver @ $' . $userObject->monthlyRate(2) . '/month',
                3 => 'Gold @ $' . $userObject->monthlyRate(3) . '/month',
                4 => 'Month of classes @ $'. $userObject->monthlyRate(4) .'/month',
            ),
            'required' => true,
            'separator'=>''
        ));

        $form->addElement('submit', 'add', array(
            'label' => 'Add Plan',
            'class' => 'btn btn-primary'
        ));
        return $form;
    }

    function servicesusedAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->_redirect('/');
        }

        if($user['type']=='admin') {
            $userId = $this->_getParam('id');
        } else {
            $userId = $user['id'];
        }
        
        $this->view->trainer_appointments = $this->trainingAppointments($userId);
        $this->view->trainer_appointments_total_duration = $this->trainingAppointmentsTotalDuration($userId);

        $this->view->massage_appointments = $this->lister($userId)->massageAppointments();
        $this->view->massage_appointments_total_duration = $this->massageAppointmentsTotalDuration($userId);

        $this->view->class_enrollments = $this->lister($userId)->classEnrollments('user');

        $this->view->user = $this->userObjectForBillingCalculations($userId);
        $this->view->user_data = $this->userData($userId);
        if($user['type']=='admin') {
            $this->render('services-used-admin');
        }
        $this->render('services-used');
    }

}