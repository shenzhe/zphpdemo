<?php

namespace ctrl;

use ZPHP\Controller\IController,
    ZPHP\Core\Config as ZConfig,
    common;


class Base implements IController
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
        $ehConfig = ZConfig::getField('project', 'exception_handler');
        if (!empty($ehConfig)) {
            \set_exception_handler($ehConfig);
        }
        return true;
    }

    public function _after()
    {
        common\loadClass::getDao('User')->closeDb();
    }

    public function getParams()
    {
        return $this->params;
    }

    protected function getInteger(array $params, $key, $default = null, $abs = true, $notEmpty = false)
    {

        if (!isset($params[$key])) {
            if ($default !== null) {
                return $default;
            }
            throw new \Exception("no params {$key}", common\ERROR::PARAM_ERROR);
        }

        $integer = isset($params[$key]) ? \intval($params[$key]) : 0;

        if ($abs) {
            $integer = \abs($integer);
        }

        if ($notEmpty && empty($integer)) {
            throw new \Exception('params no empty', common\ERROR::PARAM_ERROR);
        }

        return $integer;
    }

    protected function getString($params, $key, $default = null, $notEmpty = false)
    {
        $params = (array)$params;

        if (!isset($params[$key])) {
            if (null !== $default) {
                return $default;
            }
            throw new \Exception("no params {$key}", common\ERROR::PARAM_ERROR);
        }

        $string = \trim($params[$key]);

        if (!empty($notEmpty) && empty($string)) {
            throw new \Exception('params no empty', common\ERROR::PARAM_ERROR);
        }

        return \addslashes($string);
    }
}
