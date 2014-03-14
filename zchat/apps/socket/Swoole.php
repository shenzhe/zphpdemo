<?php

namespace socket;

use ZPHP\Socket\ICallback;
use ZPHP\Core\Config as ZConfig;
use ZPHP\Common\Formater;
use ZPHP\Protocol;
use ZPHP\Core;
use common;
use ZPHP\Conn\Factory as CFactory;


class Swoole implements ICallback
{

    const LOGIN = 1; //登录
    const LOGIN_SUCC = 2; //登录成功
    const RELOGIN = 3;      //重复登录
    const NEED_LOGIN = 4; //需要登录
    const LOGIN_ERROR = 5;  //登录失败
    const HB = 6;           //心跳
    const CHAT = 7;         //聊天
    const OLLIST = 8;       //获取在线列表
    const LOGOUT = 9;       //退出登录
    const ERROR = -1;
    private $connection = null;
    private $_rpc = null;
    private function getConnection()
    {
        if (empty($this->connection)) {
            $config = ZConfig::get('connection');
            $this->connection = CFactory::getInstance($config['adapter'], $config);
        }
        return $this->connection;
    }

    public function onStart()
    {

        $params = func_get_args();
        $serv = $params[0];
        echo 'server start, swoole version: ' . SWOOLE_VERSION . PHP_EOL;
        $times = ZConfig::getField('socket', 'times');
        if(!empty($times)) {
            foreach ($times as $time) {
                $serv->addtimer($time);
            }
        }
    }

    public function onConnect()
    {
        $params = func_get_args();
        $fd = $params[1];
//        echo "Client {$fd}：Connect" . PHP_EOL;
        $this->getConnection()->addFd($fd);
    }

    public function onReceive()
    {
        $params = func_get_args();
        $_data = $params[3];
        $serv = $params[0];
        $fd = $params[1];
//        echo "from {$fd}: get data: {$_data}".PHP_EOL;
        $result = json_decode($_data, true);
        if(!is_array($result)) {
            return null;
        }

        switch ($result[0]) {
            /*
             *  array(
             *        1, array(uid, token)
             * )
             * */
            case self::LOGIN:
                $routeResult = $this->_route(array(
                    'a'=>'chat/main',
                    'm'=>'check',
                    'uid'=>$result[1][0],
                    'token'=>$result[1][1],
                ));

                if($routeResult) {  //登录成功
                    $uinfo = $this->getConnection()->get($result[1][0]);
                    if (!empty($uinfo)) {  //已登录过
                        $this->sendOne($serv, $uinfo['fd'], self::RELOGIN, []);
                        $this->getConnection()->delete($uinfo['fd'], $result[1][0]);
                        \swoole_server_close($serv, $uinfo['fd']);
                    }

                    /**
                     * 加入到fd列表中
                     */
                    $this->getConnection()->add($result[1][0], $fd);
                    $this->getConnection()->addFd($fd, $result[1][0]);
                    $this->sendToChannel($serv, self::LOGIN_SUCC, $routeResult);
                } else {       //登录失败
                    $this->sendOne($serv, $fd, self::LOGIN_ERROR, array($routeResult, $result[1][0], $result[1][1]));
                }
                break;
            case self::HB:  //心跳处理
                $uid = $this->getConnection()->getUid($fd);
                $this->getConnection()->uphb($uid);
                return null;
                break;
            case self::CHAT:
                $toId = \intval($result[1][0]);
                $msg = \strip_tags($result[1][1]);
                $uid = $this->getConnection()->getUid($fd);
                if(empty($toId)) {  //公共聊天
                    $this->sendToChannel($serv, self::CHAT, array($uid, $msg, $toId));
                } else { //私聊
                    $toInfo = $this->getConnection()->get($toId);
                    if(!empty($toInfo)) {
                        $this->sendOne($serv, $toInfo['fd'], self::CHAT, array($uid, $msg, $toId));
                        $this->sendOne($serv, $fd, self::CHAT, array($uid, $msg, $toId));
                    }
                }
                break;
            case self::OLLIST:
                $routeResult = $this->_route(array(
                    'a'=>'chat/main',
                    'm'=>'online',
                ));
                if(!empty($routeResult)) {
                    $this->sendOne($serv, $fd, self::OLLIST, $routeResult);
                }
                break;

        }
    }

    public function onClose()
    {
        $params = func_get_args();
        $serv = $params[0];
        $fd = $params[1];
        $uid = $this->getConnection()->getUid($fd);
        $this->getConnection()->delete($fd, $uid);
        $this->sendToChannel($serv, self::LOGOUT, array($uid));
    }

    public function onShutdown()
    {
        echo "server shut dowm\n";
        $this->getConnection()->clear();
    }

    public function sendOne($serv, $fd, $cmd, $data)
    {
        if (empty($serv) || empty($fd) || empty($cmd)) {
            return;
        }
        //echo "send {$fd} cmd: {$cmd}, len:".json_encode($data).PHP_EOL;
        $data = json_encode(array($cmd, $data));
        return \swoole_server_send($serv, $fd, $data);
    }

    public function sendToChannel($serv, $cmd, $data, $channel = 'ALL')
    {
        $list = $this->getConnection()->getChannel($channel);
        if (empty($list)) {
//            echo "{$channel} empty==".PHP_EOL;
            return;
        }

        foreach ($list as $fd) {
//            echo "send to {$fd}===".PHP_EOL;
            $this->sendOne($serv, $fd, $cmd, $data);
        }
    }

    public function heartbeat()
    {

    }

    public function hbcheck($serv)
    {
        $list = $this->getConnection()->getChannel();
        if (empty($list)) {
            return;
        }

        foreach ($list as $uid => $fd) {
            if (!$this->getConnection()->heartbeat($uid)) {
                $this->getConnection()->delete($fd, $uid);
                \swoole_server_close($serv, $fd);
            }
        }
    }

    public function onTimer()
    {
        $params = func_get_args();
        $serv = $params[0];
        $interval = $params[1];
        switch ($interval) {
            case 66000: //heartbeat check
                $this->hbcheck($serv);
                break;
        }

    }

    public function rpc($params)
    {
        if ($this->_rpc === null) {
            $this->_rpc = new \Yar_Client(ZConfig::getField('socket', 'rpc_host'));
        }
        try {
            $result = $this->_rpc->api($params);
            return $result;
        } catch (\Exception $e) {
            $result =  Formater::exception($e);
            return $result;
        }
    }


    private function _route($data)
    {
        try {
            $server = Protocol\Factory::getInstance(ZConfig::getField('socket', 'protocol', 'Rpc'));
            $server->parse($data);
            $result =  Core\Route::route($server);
            return $result;
        } catch (\Exception $e) {
            $result =  Formater::exception($e);
            ZPHP\Common\Log::info('zchat', [var_export($result, true)]);
            return null;
        }
    }


    public function onWorkerStart()
    {
        $params = func_get_args();
        $worker_id = $params[1];
        echo "WorkerStart[$worker_id]|pid=" . posix_getpid() . ".\n";
    }

    public function onWorkerStop()
    {
        $params = func_get_args();
        $worker_id = $params[1];
        echo "WorkerStop[$worker_id]|pid=" . posix_getpid() . ".\n";
    }

    public function onTask()
    {

    }

    public function onFinish()
    {
        
    }
}
