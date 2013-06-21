<?php
class FoodController extends Controller
{
    function indexAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->_redirect('/');
        }

        $form = $this->form();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $this->logger()->log('food request submitted with data: '.print_r($form->getValues(),1), Zend_Log::INFO);
            $values = $form->getValues();

            $values['amount'] = 25;

            $transactionID = $this->startSubscription($values);

            if (false === $transactionID) {
                $form->addError($this->error_message);
                $form->markAsError();
            } elseif(is_numeric($transactionID)) {

                $this->view->values = $values;
                $success = $this->view->render('food/success.phtml');
                echo $success;

                $mail = new Zend_Mail('utf-8');

                $mail->addTo($user['email']);
                $mail->addTo('jshpro2@gmail.com');
                $mail->setSubject('Food Delivery Form Received');
                $mail->setBodyHtml($success);
                $this->queueMail($mail);
                $this->_helper->viewRenderer->setNoRender(true);
                return;
            } else {
                throw new Exception('no transaction id set');
            }
        }
        $this->view->form = $form;
    }

    function form()
    {
        $user = bootstrap::getInstance()->getUser();
        $form = new PaymentForm(array('user' => $user));

        $form
            ->addElement('radio', 'delivery_type', array(
                'label' => 'Delivery To',
                'multiOptions' => array('Home', 'Business'),
                'required' => true,
                'separator'=>''
            ))
            ->addElement('text', 'company_name', array(
                'label' => 'Company Name'
            ))

            ->addElement('text', 'community_building', array(
                'label' => 'Community/Building'
            ))
            ->addElement('text', 'apt_suite', array(
                'label' => 'Apt/Suite'
            ))
            ->addElement('text', 'phone', array(
                'label' => 'Phone',
                'required' => true
            ))
            ->addElement('radio', 'meals_per_day', array(
                'label' => 'Meal Package',
                'multiOptions' => array(
                    2 => '2 Meals/Day',
                    3 => '3 Meals/Day',
                    4 => '4 Meals/Day',
                    5 => '5 Meals/Day',
                ),
                'required' => true,
                'separator'=>''
            ))
            ->addElement('radio', 'days_per_week', array(
                'label' => 'Days Per Week',
                'multiOptions' => array(
                    4 => '4 Days',
                    5 => '5 Days',
                    6 => '6 Days'
                ),
                'required' => true,
                'separator'=>''
            ))
            ->addElement('text', 'allergies', array(
                'label' => 'Allergies',
                'description' => 'If none, write "NONE"',
                'required' => true
            ))
            ->addElement('textarea', 'food_dislikes', array(
                'label' => 'Food Ingredient Dislikes',
            ))
            ->addElement('checkbox', 'terms', array(
                'label' => 'Terms',
                'description' => "I agree to the terms & allow Fame Fitness for Automated Recurring Billing \n I certify that all information provided is correct and truthful.  I acknowledge that my meal plan is assumed continuous without a cancellation date.  Fame Fitness has authorization for automatic recurring billing on service given according to terms agreed upon.  The member is personally responsible to provide notice via email (info@bfamebfit.com) one week prior to their next billing day for any and account change requests, including but not limited to packages, changes in billing information, account freeze and cancellations.  Automated Reoccurring Billing is processed on Thursday prior to week of meals received.  No package change requests can be fulfilled midweek.  Signature below confirms agreement and understanding of all information on this form.  Changes to meal package can only be fulfilled after billing cycle is complete.  Email notice of any changes one week prior to next payment due.",
                'required' => true
            ));
        $form->addElement('submit', 'send', array(
            'label' => 'Pay & Send Me Food!',
            'class'=>'btn btn-primary'
        ));
        return $form;
    }
}