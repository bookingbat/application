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

    function insert($parameters)
    {
        $this->db->insert('services', $parameters);
    }

    function update($id,$parameters)
    {
        $this->db->update('services',$parameters,'id='.$id);
    }
}