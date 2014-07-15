<?php

namespace socket;

use ZPHP\Socket\Callback\WSServer;
use ZPHP\Socket\Route;
use ZPHP\Core\Config as ZConfig;
use ZPHP\Conn\Factory as ZConn;

class WebSocket extends WSServer
{
    public $num = 0;

    public function getConnInfo($fd)
    {
        $this->num++;
        $info = $this->conn->getBuff($fd, 'info');
        $this->log("get {$fd} info {$info} num:" . $this->num . " time:" . date("Y-m-d H:i:s"));
        if (empty($info)) {
            return array();
        }
        return \json_decode($info, true);
    }

    public function addConnInfo($fd, array $data)
    {
        $this->log("{$fd} addinfo");
        $info = $this->getConnInfo($fd);
        foreach ($data as $key => $val) {
            $info[$key] = $val;
        }
        $this->conn->setBuff($fd, \json_encode($info), 'info');
    }

    public function wsOnOpen($fd, $data)
    {
        echo "{$fd} connect success\n";
        $uid = (substr(strval(microtime(true)), 6, 7) * 100) % 1000000;
        if ($uid < 100000) {
            $uid += 100000;
        }
        $this->send($fd, $data . pack("H*", '811e') . '{"type":"welcome","id":' . $uid . '}', self::OPCODE_BINARY_FRAME);
        $this->conn->addFd($fd, $uid);
        $this->conn->add($uid, $fd);

    }

    public function wsOnClose($fd)
    {
        //echo "{$fd} close\n";
        //print_r($this->_ws);
        //$this->sendAll($fd, json_encode(array('type'=>'closed', 'id'=>$this->_ws[$fd]['uid'])));
        $uid = $this->conn->getUid($fd);
        if (empty($uid)) return;
        $this->sendAll($fd, json_encode(array('type' => 'closed', 'id' => $uid)));
        $this->conn->delete($fd, $uid);
    }


    public function sendAll($fd, $data)
    {
        $channelList = $this->conn->getChannel();
        if (!empty($channelList)) {
            foreach ($channelList as $clid) {
                $ret = $this->send($clid, $data);
                if(!$ret) {
                    $this->log("{$clid} send error");
                }
            }
        }
    }


    /**
     * 接收到消息时
     * @see WSProtocol::onSend()
     */
    public function wsOnMessage($fd, $ws)
    {
        //$this->log("onSend: ".$ws['message']);
        $message_data = json_decode($ws['message'], true);
        if (!$message_data) {
            return;
        }

        $uid = $this->conn->getUid($fd);
        switch ($message_data['type']) {
            // 更新用户
            case 'update':
                // 转播给所有用户
                $this->sendAll($fd, json_encode(
                    array(
                        'type' => 'update',
                        'id' => $uid,
                        'angle' => $message_data["angle"] + 0,
                        'momentum' => $message_data["momentum"] + 0,
                        'x' => $message_data["x"] + 0,
                        'y' => $message_data["y"] + 0,
                        'life' => 1,
                        'name' => isset($message_data['name']) ? $message_data['name'] : 'Guest.' . $uid,
                        'authorized' => false,
                    )
                ));
                return;
            // 聊天
            case 'message':
                // 向大家说
                $new_message = array(
                    'type' => 'message',
                    'id' => $uid,
                    'message' => $message_data['message'],
                );
                return $this->sendAll($fd, json_encode($new_message));
            case 'robot':
                $this->conn->delChannel($uid, 'ALL');
                return;

        }
    }

    public function onWorkerError()
    {
        $params = func_get_args();
        echo "exit: {$params[3]}==============" . PHP_EOL;
    }

    public function log($msg)
    {
        //echo $msg.PHP_EOL;
    }

}