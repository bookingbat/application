<?php
class Service_DataMapper
{
    protected $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    function find($id)
    {
        return $this->db->select()
            ->from('services')
            ->where('id=?',$id)
            ->query()->fetch();
    }

    function findAll()
    {
        $select = $this->db->select()
            ->from('services');
        return $select->query()->fetchAll();
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