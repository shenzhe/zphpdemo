<?php

namespace socket;
use ZPHP\Socket\Callback\WSServer;
use ZPHP\Socket\Route;
use ZPHP\Core\Config as ZConfig;

class WebSocket extends WSServer
{

    /**
    * 下线时，通知所有人
    */
    public function onOffline( $serv, $fd, $from_id )
    {
        $resMsg = array(
            'cmd' => 'offline',
            'fd' => $fd,
            'from' => 0,
            'channal' => 0 ,
            'data' => $this->connections[$fd]['name']."下线了。。",
        );
        //将下线消息发送给所有人
        $this->log("onOffline: ".$fd );
        foreach ( $this->connections as $clid => $info )
        {
            if( $fd != $clid )
            {
                $this->send( $clid , json_encode( $resMsg ) );
            }
        }
    }


    /**
    * 接收到消息时
    * @see WSProtocol::onSend()
    */
    public function onSend($fd, $ws)
    {
        $this->log("onSend: ".$ws['message']);
        $msg = json_decode( $ws['message'] , true );
        if( $msg['cmd'] == 'login' )
        {
            $this->connections[$fd]['name'] = $msg['name'];
            $this->connections[$fd]['avatar'] = $msg['avatar'];

            //回复给登录用户
            $resMsg = array(
                'cmd' => 'login',
                'fd' => $fd,
                'name' => $msg['name'],
                'avatar' => $msg['avatar'],
            );
            $this->send( $fd , json_encode( $resMsg ) );

            //广播给其它在线用户
            $resMsg['cmd'] = 'newUser';

            $loginMsg = array(
                'cmd' => 'fromMsg',
                'from' => 0,
                'channal' => 0 ,
                'data' => $msg['name']."上线鸟。。",
            );

            //将上线消息发送给所有人
            foreach ( $this->connections as $clid => $info )
            {
                if( $fd != $clid )
                {
                    $this->send( $clid , json_encode( $resMsg ) );
                    $this->send( $clid , json_encode( $loginMsg ) );
                }
            }
        }
        /**
        * 获取在线列表
        */
        elseif ( $msg['cmd'] == 'getOnline' )
        {
            $resMsg = array(
                'cmd' => 'getOnline',
            );
            foreach ( $this->connections as $clid => $info )
            {
                $resMsg['list'][] = array(
                'fd' => $clid,
                'name' => $info['name'],
                'avatar' => $info['avatar'],
                );
            }
            $this->send( $fd , json_encode( $resMsg ) );
        }
            /**
            * 发送信息请求
            */
        elseif( $msg['cmd'] == 'message' )
        {
            $resMsg = $msg;
            $resMsg['cmd'] = 'fromMsg';

            //表示群发
            if( $msg['channal'] == 0 )
            {
            foreach ( $this->connections as $clid => $info )
            {
            $this->send( $clid , json_encode( $resMsg ) );  
            }

            }
            //表示私聊
            elseif ( $msg['channal'] == 1 )
            {
            $this->send( $msg['to'] , json_encode( $resMsg ) );
            $this->send( $msg['from'] , json_encode( $resMsg ) );
            }
        }
    }

}
