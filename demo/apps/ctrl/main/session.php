<?php
namespace ctrl\main;
use ZPHP\Controller\IController,
    ZPHP\View;
use ZPHP\Session\Factory as ZSession;

class session implements IController
{
    public function _before()
    {
        ZSession::start();
        return true;
    }

    public function _after()
    {
        //
    }

    public function set()
    {
        $_SESSION = [
            'k1'=>'v1',
            'a',

        ];

        return [
            '_view_mode'=>'Json',
            'set' => 'ok'
        ];
    }

    public function get()
    {
        return [
            '_view_mode'=>'Json',
            'session' => $_SESSION
        ];;
    }

    public function delete()
    {
        unset($_SESSION);
        return [
            '_view_mode'=>'Json',
            'delete' => 'ok'
        ];
    }
}

