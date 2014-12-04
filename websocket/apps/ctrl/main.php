<?php
namespace ctrl;
use ZPHP\Controller\IController;
use ZPHP\Core\Config as ZConfig;

class main implements IController
{
    private $_server;
    public function setServer($server)
    {
        $this->_server = $server;
    }

    public function _before()
    {
        return true;
    }

    public function _after()
    {
        //
    }

    public function main()
    {
        $project = ZConfig::getField('project', 'name', 'zphp');
        $data = $project." runing!\n";
        $params = $this->_server->getParams();
        if(!empty($params)) {
            foreach($params as $key=>$val) {
                $data.= "key:{$key}=>{$val}\n";
            }
        }
        $this->_server->setViewMode('String');
        return $data;
    }
}

