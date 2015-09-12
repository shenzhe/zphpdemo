<?php
namespace ctrl;

use common;
use ZPHP\Controller\IController;
use ZPHP\Core\Config as ZConfig;

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
        return [
            'static_url'=>ZConfig::getField('project', 'static_url'),
        ];
    }


}