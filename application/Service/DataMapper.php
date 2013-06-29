<?php
class Service_DataMapper
{
    protected $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    function find($condition)
    {
        if(is_numeric($condition)) {
            return $this->db->select()
                ->from('services')
                ->where('id=?',$condition)
                ->query()->fetch();
        }

        $select = $this->db->select()
            ->from('services');

        foreach($condition as $field=>$value) {
            $select->where("$field=?",$value);
        }

        return $select->query()->fetch();
    }

    function findAll()
    {
        $select = $this->db->select()
            ->from('services');
        return $select->query()->fetchAll();
    }

    function servicesForStaff($id)
    {
        $services_for_staff = $this->db->select()
            ->from('staff_services')
            ->where('staff_user_id=?',$id)
            ->query()->fetchAll();

        $services = array();
        foreach($services_for_staff as $service) {
            $services[] = $service['service_id'];
        }

        return $services;
    }

    function insert($parameters)
    {
        $this->db->insert('services', $parameters);
    }

    function update($id,$parameters)
    {
        $this->db->update('services',$parameters,'id='.$id);
    }
}