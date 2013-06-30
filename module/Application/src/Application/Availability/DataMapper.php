<?php
class Availability_DataMapper
{
    protected $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    function insert($parameters)
    {
        if(!is_numeric($parameters['day_of_week'])) {
            switch (strtolower($parameters['day_of_week'])) {
                case 'sunday':
                case 'sundays':
                    $parameters['day_of_week']=0;
                    break;
                case 'monday':
                case 'mondays':
                    $parameters['day_of_week']=1;
                    break;
                case 'tuesday':
                case 'tuesdays':
                    $parameters['day_of_week']=2;
                    break;
                case 'wednesday':
                case 'wednesdays':
                    $parameters['day_of_week']=3;
                    break;
                case 'thursday':
                case 'thursdays':
                    $parameters['day_of_week']=4;
                    break;
                case 'friday':
                case 'fridays':
                    $parameters['day_of_week']=5;
                    break;
                case 'saturday':
                case 'saturdays':
                    $parameters['day_of_week']=6;
                    break;
            }
        }

        $this->db->insert('availability', $parameters);
    }

}