<?php


class WebSocketChatParse
{
    public function parse($zphp, $data)
    {
        //{"cmd":"message","from":0,"channal":0,"data":"adfadf"}
        $param = json_decode($data, true);
        $param['a'] = 'chat';
        $param['m'] = $param['cmd'];
        $_REQUEST = $param;
        $zphp->run();
    }

    public function open($zphp, $fd)
    {
        return;
    }

    public function close($zphp , $fd)
    {
        $_REQUEST =  [
            'a'=>'chat',
            'm'=>'offline',
            'fd'=>$fd
        ];
        $zphp->run();
    }
} 