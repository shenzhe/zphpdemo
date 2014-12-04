<?php

namespace socket;

use ZPHP\Socket\Callback\WSServer;
use ZPHP\Socket\Route;
use ZPHP\Core\Config as ZConfig;

class WebSocket extends WSServer
{

    public function wsOnOpen($fd, $data)
    {
        //echo "{$fd} connect success";
        $this->send($fd, $data);
    }

    public function wsOnClose($fd)
    {
        $this->onOffline($fd);
    }

    /**
     * 下线时，通知所有人
     */
    public function onOffline($fd, $from_id = 0)
    {
        $resMsg = array(
            'cmd' => 'offline',
            'fd' => $fd,
            'from' => 0,
            'channal' => 0,
            'data' => $this->_ws[$fd]['name'] . "下线了。。",
        );
        //将下线消息发送给所有人
        $this->log("onOffline: " . $fd);
        foreach ($this->_ws as $clid => $info) {
            if ($fd != $clid) {
                $this->send($clid, json_encode($resMsg));
            }
        }
    }


    /**
     * 接收到消息时
     * @see WSProtocol::onSend()
     */
    public function wsOnMessage($fd, $ws)
    {
        $this->log("onSend: " . $ws['message']);
        $msg = json_decode($ws['message'], true);
        if ($msg['cmd'] == 'login') {
            $this->_ws[$fd]['name'] = $msg['name'];
            $this->_ws[$fd]['avatar'] = $msg['avatar'];

            //回复给登录用户
            $resMsg = array(
                'cmd' => 'login',
                'fd' => $fd,
                'name' => $msg['name'],
                'avatar' => $msg['avatar'],
            );
            $this->send($fd, json_encode($resMsg));

            //广播给其它在线用户
            $resMsg['cmd'] = 'newUser';

            $loginMsg = array(
                'cmd' => 'fromMsg',
                'from' => 0,
                'channal' => 0,
                'data' => $msg['name'] . "上线鸟。。",
            );

            //将上线消息发送给所有人
            foreach ($this->_ws as $clid => $info) {
                if ($fd != $clid) {
                    $this->send($clid, json_encode($resMsg));
                    $this->send($clid, json_encode($loginMsg));
                }
            }
        } /**
         * 获取在线列表
         */
        elseif ($msg['cmd'] == 'getOnline') {
            $resMsg = array(
                'cmd' => 'getOnline',
            );
            foreach ($this->_ws as $clid => $info) {
                $resMsg['list'][] = array(
                    'fd' => $clid,
                    'name' => $info['name'],
                    'avatar' => $info['avatar'],
                );
            }
            $this->send($fd, json_encode($resMsg));
        } /**
         * 发送信息请求
         */
        elseif ($msg['cmd'] == 'message') {
            $resMsg = $msg;
            $resMsg['cmd'] = 'fromMsg';

            //表示群发
            if ($msg['channal'] == 0) {
                foreach ($this->_ws as $clid => $info) {
                    $this->send($clid, json_encode($resMsg));
                }

            } //表示私聊
            elseif ($msg['channal'] == 1) {
                $this->send($msg['to'], json_encode($resMsg));
                $this->send($msg['from'], json_encode($resMsg));
            }
        }
    }

}
