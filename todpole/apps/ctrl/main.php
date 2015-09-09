<?php
namespace ctrl;

use common;
use ZPHP\Controller\IController;

class main implements IController
{
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