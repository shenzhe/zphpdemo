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
    public function onOffline( $serv, $client_id, $from_id )
    {
        $resMsg = array(
            'cmd' => 'offline',
            'fd' => $client_id,
            'from' => 0,
            'channal' => 0 ,
            'data' => $this->connections[$client_id]['name']."下线了。。",
        );
        //将下线消息发送给所有人
        $this->log("onOffline: ".$client_id );
        foreach ( $this->connections as $clid => $info )
        {
            if( $client_id != $clid )
            {
                $this->send( $clid , json_encode( $resMsg ) );
            }
        }
    }


    /**
    * 接收到消息时
    * @see WSProtocol::onMessage()
    */
    public function onMessage($client_id, $ws)
    {
        $this->log("onMessage: ".$ws['message']);
        $msg = json_decode( $ws['message'] , true );
        if( $msg['cmd'] == 'login' )
        {
            $this->connections[$client_id]['name'] = $msg['name'];
            $this->connections[$client_id]['avatar'] = $msg['avatar'];

            //回复给登录用户
            $resMsg = array(
                'cmd' => 'login',
                'fd' => $client_id,
                'name' => $msg['name'],
                'avatar' => $msg['avatar'],
            );
            $this->send( $client_id , json_encode( $resMsg ) );

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
                if( $client_id != $clid )
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
            $this->send( $client_id , json_encode( $resMsg ) );
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
