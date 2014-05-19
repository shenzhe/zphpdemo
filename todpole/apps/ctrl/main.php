<?php
namespace ctrl;

use common;
use ZPHP\Controller\IController;

class main implements IController
{
	protected $server;
    protected $params = array();

    public function setServer($server)
    {
        $this->server = $server;
        $this->params = $server->getParams();
    }

    public function getServer()
    {
        return $this->server;
    }

    public function _before()
    {
        return true;
    }

    public function _after()
    {

    }

    /**
     * 网站首页
     */
    public function main()
    {
        return array();
    }


}