<?php
class ClassController extends Controller
{
    protected $limitMonths = 3;

    function preDispatch()
    {
        if ($this->getRequest()->getActionName() == 'index') {
            // don't require payment to view the class schedule
            return;
        }
        $user = bootstrap::getInstance()->getUser();
        if ($user['type'] == 'admin') {
            return;
        }
        if (!$userid = $user['id']) {
            return $this->_redirect('/');
        }

        $user = $this->userObjectForBillingCalculations();
        if ($user->planName() == 'guest') {
            $classesUsed = count($this->lister($userid)->classEnrollments('user'));
            if ($user->isAtMaximum(array('class' => $classesUsed))) {
                $this->_forward('class', 'payment', null, array());
            }
        }
    }

    function init()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id']) {
            return $this->_redirect('/');
        }
    }

    function month()
    {
        return $this->_getParam('month') ? $this->_getParam('month') : date('m');
    }

    function year()
    {
        return $this->_getParam('year') ? $this->_getParam('year') : date('Y');
    }

    function indexAction()
    {
        $user = bootstrap::getInstance()->getUser();

        $month = $this->month();
        $year = $this->year();

        $requestedCalendar = mktime(0, 0, 0, $month, 0, $year);

        if (round(($requestedCalendar - time()) / 60 / 60 / 24 / 30) >= $this->limitMonths) {
            $this->view->limitMonths = $this->limitMonths;
            return $this->render('limited', null, true);
        }

        $this->view->availability = array();
        $number_of_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for ($day = 1; $day <= $number_of_days_in_month; $day++) {
            $dayString = sprintf('%s-%s-%s', $day, $month, $year);

            $availability = $this->selectAvailability(date('N', strtotime($dayString)), $user['condo_id']);
            $availability = $this->removeAlreadyEnrolledIn($availability, date('Y-m-d', strtotime($dayString)));
            $this->view->availability[$day] = $availability;
        }

        $this->view->year = $year;
        $this->view->month = $month;
        $this->view->month_name = date('F', strtotime('1-' . $month . '-' . $year));
        $this->view->number_of_days_in_month = $number_of_days_in_month;

        $db = Zend_Registry::get('db');
        $this->render('calendar-choose', null, false);
    }

    function enrollAction()
    {
        $user = bootstrap::getInstance()->getUser();

        $db = Zend_Registry::get('db');
        $db->insert('class_enrollment', array(
            'user_id' => $user['id'],
            'class_id' => $this->_getParam('id'),
            'date' => $this->_getParam('date')
        ));

        $this->_helper->FlashMessenger->addMessage('Enrolled For Class');
        return $this->_redirect('/');
    }

    function scheduleAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $db = Zend_Registry::get('db');
        $this->view->schedule = $db->select()
            ->from('class_schedule')
            ->joinLeft('user', 'class_schedule.instructor_userid = user.id', array('instructor' => 'username'))
            ->joinLeft('condo', 'condo.id = class_schedule.condo_id', array('condo' => 'name'))
            ->query()
            ->fetchAll();
        $this->view->form = new ClassForm;
        $this->view->form->getElement('instructor')->setMultiOptions($this->listInstructors());
        $this->view->form->getElement('condo')->setMultiOptions($this->listCondos());
    }

    function rosterAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'class-instructor') {
            return $this->_redirect('/');
        }
        $db = Zend_Registry::get('db');

        $form = new Zend_Form;
        $form->addElement('select', 'class', array(
            'label' => 'Class',
            'multiOptions' => $this->thisInstructorsClasses($user['id'])
        ));
        $form->addElement('text', 'date', array(
            'label' => 'Date'
        ));
        $form->addElement('submit', 'view');

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {

            $class = $form->getValue('class');
            $date = $form->getValue('date');

            $className = $db->select()
                ->from('class_schedule', array('name'))
                ->where('id=?', $class)
                ->query()
                ->fetchColumn();

            $lister = new AppointmentsLister;
            $lister->userId($user['id']);
            $this->view->classes = $lister->classEnrollments('instructor', $class, $date);
            $this->view->class = $className;
            $this->view->date = $date;
        }
        $this->view->form = $form;
    }

    function thisInstructorsClasses($userid)
    {
        $db = Zend_Registry::get('db');
        $thisInstructorsClasses = $db->select()
            ->from('class_schedule', array('id', 'name'))
            ->where('instructor_userid=?', $userid)
            ->query()
            ->fetchAll();
        $return = array();
        foreach ($thisInstructorsClasses as $class) {
            $return[$class['id']] = $class['name'];
        }
        return $return;
    }

    function addAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $db = Zend_Registry::get('db');


        $form = new ClassForm;
        $form->getElement('instructor')->setMultiOptions($this->listInstructors());
        $form->getElement('condo')->setMultiOptions($this->listCondos());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $db->insert('class_schedule', array(
                'name' => $form->getValue('name'),
                'day_of_week' => $form->getValue('day_of_week'),
                'time' => $form->getValue('time'),
                'instructor_userid' => $form->getValue('instructor'),
                'condo_id' => $form->getValue('condo')
            ));
            $this->_helper->FlashMessenger->addMessage('Scheduled New Class');
            return $this->_redirect('/class/schedule');
        }
        $this->view->form = $form;
        $this->render('schedule-add');
    }

    function activateAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $db = Zend_Registry::get('db');
        $db->update('class_schedule', array(
                'active'=>1
            ),
            'id='.$this->getParam('id'));
        $this->_helper->FlashMessenger->addMessage('Activated Class');
        return $this->_redirect('/class/schedule');
    }

    function deactivateAction()
    {
        $user = bootstrap::getInstance()->getUser();
        if (!$user['id'] || $user['type'] != 'admin') {
            return $this->_redirect('/');
        }

        $db = Zend_Registry::get('db');
        $db->update('class_schedule', array(
                'active'=>0
            ),
            'id='.$this->getParam('id'));
        $this->_helper->FlashMessenger->addMessage('Deactivated Class');
        return $this->_redirect('/class/schedule');
    }

    function listCondos()
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('condo')
            ->where('active=1');

        $condos = array();
        foreach ($select->query()->fetchAll() as $condo) {
            $condos[$condo['id']] = $condo['name'];
        }

        return $condos;
    }

    function removeAlreadyEnrolledIn($availability, $date)
    {
        foreach ($this->enrolled($date) as $classUserEnrolledIn) {
            foreach ($availability as $key => $availableClass) {
                if ($availableClass['id'] == $classUserEnrolledIn['class_id']) {
                    unset($availability[$key]);
                }
            }
        }
        return $availability;
    }

    function enrolled($date)
    {
        $user = bootstrap::getInstance()->getUser();
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('class_enrollment')
            ->where('user_id=?', $user['id'])
            ->where('date=?', $date);

        return $db->query($select)->fetchAll();
    }

    function selectAvailability($dayOfWeek, $condo_id)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from('class_schedule')
            ->where('day_of_week=?', $dayOfWeek)
            ->where('condo_id=?', $condo_id)
            ->where('active=?',1);

        return $db->query($select)->fetchAll();
    }

    function listInstructors()
    {
        $db = Zend_Registry::get('db');
        $select = $db->select(array('id', 'username'))
            ->from('user')
            ->where('type=?', 'class-instructor');
        $instructors = array();
        foreach ($select->query()->fetchAll() as $instructor) {
            $instructors[$instructor['id']] = $instructor['username'];
        }

        return $instructors;
    }
}