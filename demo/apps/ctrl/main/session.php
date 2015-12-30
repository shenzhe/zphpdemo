<?php
namespace ctrl\main;
use ZPHP\Controller\IController,
    ZPHP\Core\Config,
    ZPHP\View;
use ZPHP\Protocol\Request;
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

        print_r($_SESSION);
    }

    public function get()
    {
        print_r($_SESSION);
    }

    public function delete()
    {
        unset($_SESSION);
    }
}

