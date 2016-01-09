<?php

namespace socket;


use ZPHP\Protocol\Request;
use ZPHP\Socket\Callback\Swoole as ZSwoole;
use ZPHP\Core\Route as ZRoute;
use common;

class Swoole extends ZSwoole
{
    public function onReceive()
    {
        list($serv, $fd, $fromId, $data) = func_get_args();
        echo $data.PHP_EOL;
        if (empty($data)) {
            return;
        }
        $datas = explode(CHAT_MSG_EOF, $data);
        foreach($datas as $_data) {
            if(empty($_data)) {
                continue;
            }
            Request::parse(common\Cmd::parseData(json_decode($_data)));
            $result = ZRoute::route();
            if($result) {
                $serv->send($fd, $result);
            }
        }
    }

    public function onClose()
    {
        Request::setFd(func_get_arg(1));
        Request::parse([
            'a'=>'chat',
            'm'=>'offline'
        ]);
        ZRoute::route();
    }
}
