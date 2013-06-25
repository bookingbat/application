<?php
class User_DataMapper
{
    protected $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    function insert($parameters)
    {
        $this->db->insert('user', $parameters);
    }
}