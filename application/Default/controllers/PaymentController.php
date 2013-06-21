<?php
class PaymentController extends Controller
{
    protected $error_message;

    function trainingAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$userid = $user['id']) {
            return $this->_redirect('/');
        }

        $user = $this->userObjectForBillingCalculations();

        $this->view->training_used = $this->trainingAppointmentsTotalDuration($userid);
        $this->view->training_allowed = $user->trainingAllowed();
        $this->view->usertype = $user->planName();
        $this->view->hourlyOverageCharge = $user->trainingHourlyRate();
        $this->view->halfhourOverageCharge = $user->trainingHourlyRate() / 2;

        $form = $this->trainingForm();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $dollarAmount = $form->getValue('quantity') == 30 ? $user->trainingHourlyRate() / 2 : $user->trainingHourlyRate();
            $values = $form->getValues() + array('user_id' => $userid, 'description' => 'Extra training');
            $values['amount'] = $dollarAmount;
            $transactionID = $this->makePayment($values);

            if (false === $transactionID) {
                $form->addError($this->error_message);
                $form->markAsError();
            } else {
                $this->db()->insert('user_payments', array(
                    'user_id' => $userid,
                    'service' => 'training',
                    'service_quantity' => $form->getValue('quantity'),
                    'amount_paid' => $dollarAmount,
                    'datetime' => new Zend_Db_Expr('NOW()')
                ));
                $this->_helper->FlashMessenger->addMessage('You paid $' . $dollarAmount . ' for ' . $form->getValue('quantity') . ' minutes of additional training');
                return $this->_redirect('/');
            }
        }

        $this->view->form = $form;
    }

    function massageAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$userid = $user['id']) {
            return $this->_redirect('/');
        }

        $user = $this->userObjectForBillingCalculations();

        $this->view->training_used = $this->trainingAppointmentsTotalDuration($userid);
        $this->view->training_allowed = $user->trainingAllowed();
        $this->view->usertype = $user->planName();
        $this->view->hourlyOverageCharge = $user->trainingHourlyRate();
        $this->view->halfhourOverageCharge = $user->trainingHourlyRate() / 2;

        $form = $this->massageForm();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $dollarAmount = $form->getValue('quantity') == 60 ? $user->massageHourlyRate() : $user->massageHourAndAHalfRate();

            $values = $form->getValues() + array('user_id' => $userid, 'description' => 'Extra massage');
            $values['amount'] = $dollarAmount;
            $transactionID = $this->makePayment($values);
            if (false === $transactionID) {
                $form->addError($this->error_message);
                $form->markAsError();
            } else {
                $this->db()->insert('user_payments', array(
                    'user_id' => $userid,
                    'service' => 'massage',
                    'service_quantity' => $form->getValue('quantity'),
                    'amount_paid' => $dollarAmount,
                    'datetime' => new Zend_Db_Expr('NOW()')
                ));
                $this->_helper->FlashMessenger->addMessage('You paid $' . $dollarAmount . ' for ' . $form->getValue('quantity') . ' minutes of additional massage');
                return $this->_redirect('/');
            }

        }

        $this->view->form = $form;
    }

    function classAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$userid = $user['id']) {
            return $this->_redirect('/');
        }

        $this->view->classes_used = count($this->lister($userid)->classEnrollments('user'));

        $form = $this->classForm();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $dollarAmount = $form->getValue('quantity') * 25;
            $values = $form->getValues() + array('user_id' => $userid, 'description' => 'Pay for a class');
            $values['amount'] = $dollarAmount;
            $transactionID = $this->makePayment($values);
            if (false === $transactionID) {
                $form->addError($this->error_message);
                $form->markAsError();
            } else {
                $this->db()->insert('user_payments', array(
                    'user_id' => $userid,
                    'service' => 'class',
                    'service_quantity' => $form->getValue('quantity'),
                    'amount_paid' => $dollarAmount,
                    'datetime' => new Zend_Db_Expr('NOW()')
                ));
                $this->_helper->FlashMessenger->addMessage('You paid $' . $dollarAmount . ' for ' . $form->getValue('quantity') . ' class enrollments');
                return $this->_redirect('/');
            }
        }

        $this->view->form = $form;
    }

    function makePayment($values)
    {
        try {

            $payment = new AuthnetAIM(bootstrap::getInstance()->authorizenetLogin(), bootstrap::getInstance()->authorizenetKey(), true);
            $payment->setTransaction($values['cardNumber'], $values['expirationDate'], $values['amount'], null, null, null);
            $payment->setParameter("x_duplicate_window", 180);
            $payment->setParameter("x_cust_id", $values['user_id']);
            $payment->setParameter("x_customer_ip", $_SERVER['REMOTE_ADDR']);
            $payment->setParameter("x_email", $values['email']);
            $payment->setParameter("x_email_customer", FALSE);
            $payment->setParameter("x_first_name", $values['firstName']);
            $payment->setParameter("x_last_name", $values['lastName']);
            $payment->setParameter("x_address", $values['address']);
            $payment->setParameter("x_city", $values['city']);
            $payment->setParameter("x_state", $values['state']);
            $payment->setParameter("x_zip", $values['zip']);

            $payment->setParameter("x_description", $values['description']);
            $payment->process();
            if ($payment->isApproved()) {
                return $payment->getTransactionID();
            } else {
                $this->error_message = $payment->getResponseText();
                return false;
            }
        } catch (AuthnetAIMException $e) {
            $this->error_message = $e->getMessage();
            return false;
        }
    }

    function trainingForm()
    {
        $userData = bootstrap::getInstance()->getUser();
        $user = new User($userData);

        $form = new PaymentForm(array('user' => $userData));
        $form->getElement('expirationDate')->setDescription('MM-YYYY');
        $form->addElement('radio', 'quantity', array(
            'label' => 'Pay For',
            'multiOptions' => array(
                60 => 'Hour @ $' . $user->trainingHourlyRate(),
                30 => 'Half Hour @ $' . $user->trainingHourlyRate() / 2,
            ),
            'required' => true
        ));

        $form->addElement('submit', 'submit');
        return $form;
    }

    function classForm()
    {
        $userData = bootstrap::getInstance()->getUser();
        $user = new User($userData);

        $form = new PaymentForm(array('user' => $userData));
        $form->getElement('expirationDate')->setDescription('MM-YYYY');
        $form->addElement('radio', 'quantity', array(
            'label' => 'Pay For',
            'multiOptions' => array(
                1 => 'One class',
                2 => 'Two classes'
            ),
            'required' => true
        ));

        $form->addElement('submit', 'submit');
        return $form;
    }

    function massageForm()
    {
        $userData = bootstrap::getInstance()->getUser();
        $user = new User($userData);

        $form = new PaymentForm(array('user' => $userData));
        $form->getElement('expirationDate')->setDescription('MM-YYYY');
        $form->addElement('radio', 'quantity', array(
            'label' => 'Pay For',
            'multiOptions' => array(
                60 => 'Hour @ $' . $user->massageHourlyRate(),
                90 => 'Hour and a Half @ $' . $user->massageHourAndAHalfRate(),
            ),
            'required' => true
        ));

        $form->addElement('submit', 'submit');
        return $form;
    }

}