<?php
class MassagecalendarController extends CalendarchooseController
{
    function indexAction()
    {
        $user = bootstrap::getInstance()->getUser();

        $therapistSelector = $this->therapistSelector();

        if(!is_null($therapistSelector->getValue('therapist'))) {
            $this->therapist_selection = $therapistSelector->getValue('therapist');
        } else {
            $this->therapist_selection = $this->therapistsForCondo($user['condo_id']);
        }

        $this->view->therapistSelector = $therapistSelector;
        $this->render('therapist-selector');
        $this->view->therapist_id = $this->getParam('therapist');

        $this->view->controller = 'massage';
        $this->renderCalendar();
    }

    function therapistSelector()
    {
        $therapists = $this->therapistsForCondo();

        $form = new Zend_Form;
        $form->setMethod("GET");
        $form->addElement('select', 'therapist', array(
            'label' => 'Therapist',
            'multiOptions' => array('All' => 'All') + $therapists,
            'value' => $this->_getParam('therapist') == 'All' ? null : $this->_getParam('therapist')
        ));
        $form->addElement('submit', 'submitbutton', array(
            'label' => 'Go',
            'class'=>'btn'
        ));
        return $form;
    }

    function therapistsForCondo()
    {
        $condition = 'therapist_condos.therapist_userid = user.id';

        $db = Zend_Registry::get('db');
        $therapistsResult = $db->select()
            ->from('user')
            ->where('type=?', 'massage-therapist')
            ->joinRight('therapist_condos', $condition, array())
            ->query()->fetchAll();

        $therapists = array();
        foreach ($therapistsResult as $therapistsResult) {
            $therapists[$therapistsResult['id']] = $therapistsResult['username'];
        }
        return $therapists;
    }

    /** Select availability for either all massage therapists at the client's condo, or the selected therapist */
    function selectAvailability($dayNumber)
    {
        $therapist = $this->therapist_selection;
        if(is_array($therapist)) {
            $therapist = array_keys($therapist);
        }
        return $this->selectMassageAvailability($dayNumber, $therapist);
    }

    /**
     * Remove the bookings from the selected therapist, if none selected removes all therapists bookings
     * @todo Oops, it removes bookings from all therapists, even ones not at this user's condo!!
     */
    function removeBookingsFrom($availability, $dayString)
    {
        return $this->removeMassageBookingsFrom($availability, $dayString, $this->_getParam('therapist'));
    }
}