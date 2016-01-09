<?php

namespace ctrl;

use ZPHP\Controller\IController,
    common;
use ZPHP\Protocol\Request;


class Base implements IController
{
    protected $server;
    protected $params = array();

    public function _before()
    {
        $this->params = Request::getParams();
        return true;
    }

    public function _after()
    {
        //common\loadClass::getDao('User')->closeDb();
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
