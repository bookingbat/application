<?php
class Service_DataMapper
{
    protected $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    function insert($parameters)
    {
        $this->db->insert('services', $parameters);
    }
}