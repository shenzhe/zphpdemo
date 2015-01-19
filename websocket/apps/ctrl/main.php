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
        return [
            'message'=>'very good'
        ];
    }
}

