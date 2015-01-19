<?php
namespace ctrl;
use ZPHP\Controller\IController;
use ZPHP\Core\Config as ZConfig;

class login implements IController
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

    public function check()
    {
        print_r($this->_server->getParams());
    }

    public function reg()
    {
        return [];
    }
}

