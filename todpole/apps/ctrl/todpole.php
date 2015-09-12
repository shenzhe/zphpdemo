<?php
namespace ctrl;

use common;
use ZPHP\Controller\IController;
use ZPHP\Protocol\Request;

class todpole implements IController
{
    private $params = [];
    public function _before()
    {
        $this->params = Request::getParams();
        return true;
    }

    public function _after()
    {

    }

    public function open()
    {

        return [
            'to'=> $this->params['fd'],
            'data'=> [
                'type'=>'welcome',
                'id'=>$this->params['uid']
            ]
        ];
    }

    public function close()
    {
        return [
            'to'=> 0,
            'data'=> [
                'type'=>'closed',
                'id'=>$this->params['uid']
            ]
        ];
    }

    public function message()
    {

    }


}