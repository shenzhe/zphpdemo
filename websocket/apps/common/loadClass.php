<?php

namespace common;

use ZPHP\Core\Factory;

/**
 * 获取class实例的工具类
 *
 * @package service
 *
 */
class loadClass
{

    /**
     * @param $service
     * @return \service\Base
     */
    public static function getService($service)
    {
        return Factory::getInstance("service\\{$service}");
    }

    /**
     * @param $dao
     * @return \dao\Base
     */
    public static function getDao($dao)
    {
        return Factory::getInstance("dao\\{$dao}");
    }
}
