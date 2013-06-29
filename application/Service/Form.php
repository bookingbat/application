<?php
class Service_Form extends Zend_Form
{
    function init()
    {
        $this->addElement('multiCheckbox','services',array(
            'label'=>'Services',
            'separator'=>''
        ));
    }

    function setPossibleServices($services)
    {
        $services = $this->servicesByID($services);
        $this->getElement('services')->setMultiOptions($services);
    }

    function servicesByID($services)
    {
        $servicesByID = array();
        foreach($services as $service) {
            $servicesByID[$service['id']] = $service['name'];
        }
        return $servicesByID;
    }
}