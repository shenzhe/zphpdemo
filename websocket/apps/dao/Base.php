<?php

namespace dao;

use ZPHP\Core\Config as ZConfig,
    ZPHP\Db\Pdo as ZPdo;

abstract class Base
{
    private $entity;
    private $_db = null;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function useDb()
    {
        if (empty($this->_db)) {
            $config = ZConfig::get('pdo');
            $this->_db = new ZPdo($config, $this->entity, $config['dbname']);
            $this->_db->setClassName($this->entity);
        } else {
            $this->_db->checkPing();
        }
        return $this->_db;
    }

    public function closeDb()
    {
        if(!empty($this->_db)) {
            $this->_db->close();
        }
    }

    public function fetchById($id)
    {
        $this->useDb();
        return $this->_db->fetchEntity("id={$id}");
    }

    public function fetchAll(array $items=[])
    {
        $this->useDb();
        if(empty($items)) {
            return $this->_db->fetchAll();
        }
        $where = "1";
        foreach ($items as $k => $v) {
            $where .= " and {$k}={$v}";
        }
        return $this->_db->fetchAll($where);
    }

    public function fetchWhere($where='')
    {
        $this->useDb();
        return $this->_db->fetchAll($where);
    }

    public function update($attr)
    {
        $fields = array();
        $params = array();
        foreach ($attr as $key => $val) {
            $fields[] = $key;
            $params[$key] = $val;
        }
        $this->useDb();
        return $this->_db->update($fields, $params, 'id=' . $attr->id);
    }

    public function add($attr)
    {
        $this->useDb();
        return $this->_db->replace($attr, \array_keys(\get_object_vars($attr)));
    }

    public function remove($where)
    {
        $this->useDb();
        $this->dbHelper->remove($where);
    }
}
