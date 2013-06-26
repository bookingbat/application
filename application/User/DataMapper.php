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
        $this->db->insert('user', array(
            'username'=>$parameters['username'],
            'password'=>sha1($parameters['password']),
            'type'=>$parameters['type']
        ));
    }
}