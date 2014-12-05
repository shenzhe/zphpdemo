<?php


class WebSocketChatParse
{
    public function parse($data)
    {
        //{"cmd":"message","from":0,"channal":0,"data":"adfadf"}
        $param = json_decode($data, true);
        $param['a'] = 'chat';
        $param['m'] = $param['cmd'];
        return $param;
    }

    public function close($fd)
    {
        return [
            'a'=>'chat',
            'm'=>'offline',
            'fd'=>$fd
        ];
    }
} 