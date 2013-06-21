<?php
class AppointmentsLister
{
    protected $userId;
    protected $month;
    protected $year;

    const EXCLUDE_CANCELLED = 'exclude_canceled';

    function classEnrollments($context, $class = null, $date = null)
    {
        switch ($context) {
            case'user':
                $select = $this->db()->select()
                    ->from('class_enrollment', array('user_id', 'date',))
                    ->joinLeft('user', 'user.id=class_enrollment.user_id', array('user' => 'username'))
                    ->joinLeft('class_schedule', 'class_schedule.id=class_enrollment.class_id', array('id', 'name', 'class' => 'name'))
                    ->where('class_enrollment.user_id=?', $this->userId())
                    ->order('date DESC');
                if ($this->month() && $this->year()) {
                    $select
                        ->where('MONTH(`date`)=?', $this->month())
                        ->where('YEAR(`date`)=?', $this->year());
                }
                return $select->query()->fetchAll();
                break;
            case 'instructor':
                $select = $this->db()->select()
                    ->from('class_enrollment', array('id' => 'user_id'))
                    ->joinLeft('class_schedule', 'class_schedule.id=class_enrollment.class_id', array())
                    ->where('class_schedule.instructor_userid=?', $this->userId())
                    ->where('class_schedule.id=?', $class)
                    ->where('class_enrollment.class_id=?', $class)
                    ->where('class_enrollment.date=?', $date)
                    ->order('date DESC');
                $usersManuallyEnrolled = $select->query()->fetchAll();

                $select = $this->db()->select()
                    ->from('user', array('id'))
                    ->where('member>0');
                $usersAutomaticallyEnrolled = $select->query()->fetchAll();
                $userIdsEnrolled = array_merge($usersManuallyEnrolled, $usersAutomaticallyEnrolled);
                $userids = array();
                foreach ($userIdsEnrolled as $userid) {
                    $userids[] = $userid['id'];
                }

                if (!count($userids)) {
                    return array();
                }
                $userids = implode(',', $userids);
                return $this->db()->select()
                    ->from('user')
                    ->where('ID IN (' . $userids . ')')
                    ->query()
                    ->fetchAll();
                break;
        }

    }

    function trainerAppointments($mode = null)
    {
        $select = $this->db()->select()
            ->from('trainer_appointments')
            ->joinLeft('user', 'user.id=trainer_appointments.trainer_userid', array('first_name', 'last_name', 'email', 'phone'))
            ->where('user_id=?', $this->userId())
            ->order('date DESC')
            ->order('time DESC');
        if ($this->month() && $this->year()) {
            $select
                ->where('MONTH(`date`)=?', $this->month())
                ->where('YEAR(`date`)=?', $this->year());
        }
        if (self::EXCLUDE_CANCELLED == $mode) {
            $select->where('canceled=0');
        }
        return $select->query()->fetchAll();
    }

    function trainingAppointmentsTotalDuration()
    {
        $trainer_appointments_total_duration = 0;
        foreach ($this->trainerAppointments(self::EXCLUDE_CANCELLED) as $trainer_appointment) {
            $trainer_appointments_total_duration += $trainer_appointment['duration'];
        }
        return $trainer_appointments_total_duration;
    }

    function massageAppointments($mode = null)
    {
        $select = $this->db()->select()
            ->from('therapist_appointments')
            ->joinLeft('user', 'user.id=therapist_appointments.therapist_userid', array('first_name', 'last_name', 'email', 'phone'))
            ->where('user_id=?', $this->userId())
            ->order('date DESC')
            ->order('time DESC');
        if ($this->month() && $this->year()) {
            $select
                ->where('MONTH(`date`)=?', $this->month())
                ->where('YEAR(`date`)=?', $this->year());
        }
        if (self::EXCLUDE_CANCELLED == $mode) {
            $select->where('canceled=0');
        }
        return $select->query()->fetchAll();
    }

    function massageAppointmentsTotalDuration()
    {
        $massage_appointments_total_duration = 0;
        foreach ($this->massageAppointments(self::EXCLUDE_CANCELLED) as $massage_appointment) {
            $massage_appointments_total_duration += $massage_appointment['duration'];
        }
        return $massage_appointments_total_duration;
    }

    function db()
    {
        return Zend_Registry::get('db');
    }

    function month($month = null)
    {
        if (!is_null($month)) {
            $this->month = $month;
        }
        return $this->month;
    }

    function year($year = null)
    {
        if (!is_null($year)) {
            $this->year = $year;
        }
        return $this->year;
    }

    function userId($userId = null)
    {
        if (!is_null($userId)) {
            $this->userId = $userId;
        }
        return $this->userId;
    }
}