<?php
class User_DataMapper
{
    protected $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    function find($parameters)
    {
        $select = $this->db->select()
            ->from('user');
        foreach($parameters as $field=>$value) {
            $select->where("$field=?",$value);
        }
        return $select->query()->fetch();
    }

    function insert($parameters)
    {
        $this->db->insert('user', array(
            'username'=>$parameters['username'],
            'password'=>sha1($parameters['password']),
            'type'=>$parameters['type']
        ));
    }

    function assignMultiple($services,$userID)
    {
        foreach($services as $serviceID) {
            $this->assign($serviceID,$userID);
        }
    }

    function assign($serviceID,$userID)
    {
        $this->db->insert('staff_services', array(
            'staff_user_id' => $userID,
            'service_id' => $serviceID
        ));
    }

    function unassignServices($userID)
    {
        $this->db->delete('staff_services', 'staff_user_id=' . (int)$userID);
    }
}