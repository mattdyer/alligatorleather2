<?php

namespace Patrickfuchshofer\Giftvoucher\Model\Libs;

class Wpdb
{
    public $prefix = '';
    public $insert_id = null;

    function get_row($sql)
    {
        //Get settings
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();

        //Select Data from table
        $result = $connection->fetchAll($sql);
        return (object) end($result);
    }

    function insert($tableName, $data)
    {
        $data = [
            $data
        ];

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();

        $result = $connection->insertMultiple($tableName, $data);
        if ($result) {
            $row = $this->get_row('SELECT * FROM ' . $tableName . ' ORDER BY id DESC LIMIT 1');
            $this->insert_id = $row->id;
        } else {
            $this->insert_id = null;
        }

        return $result;
    }

    public function query($sql, $params)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        return $connection->query($sql, $params);
    }

    public function prepare($sql, $params)
    {
        return vsprintf($sql, $params);
    }
}
