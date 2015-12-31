<?php

namespace socket;

use ZPHP\ZPHP;
use ZPHP\Protocol\Request;
use ZPHP\Protocol\Response;
use ZPHP\Socket\Callback\SwooleWebSocket as ZSwooleWebSocket;
use ZPHP\Core\Config as ZConfig;
use ZPHP\Core\Route as ZRoute;

class WebSocket extends ZSwooleWebSocket
{

    private $buff = [];
    private $ulist = [];
    private $flist = [];

    private $mimes = array(
        'jpg' => 'image/jpeg',
        'bmp' => 'image/bmp',
        'ico' => 'image/x-icon',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'bin' => 'application/octet-stream',
        'js' => 'application/javascript',
        'css' => 'text/css',
        'html' => 'text/html',
        'xml' => 'text/xml',
        'tar' => 'application/x-tar',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pdf' => 'application/pdf',
        'swf' => 'application/x-shockwave-flash',
        'zip' => 'application/x-zip-compressed',
    );

    public function onOpen($server, $request)
    {
        $this->log($request->fd . "connect");
        $uid = (substr(strval(microtime(true)), 6, 7) * 100) % 1000000;
        if ($uid < 100000) {
            $uid += 100000;
        }
        $server->task([
            'cmd' => 'open',
            'fd' => $request->fd,
            'uid' => $uid
        ], 0);
    }

    public function onClose()
    {
        list($server, $fd, $fromId) = func_get_args();
        $this->log("{$fd} close" . PHP_EOL);
        $server->task([
            'cmd' => 'close',
            'fd' => $fd
        ], 0);
    }

    public function onRequest($request, $response)
    {
        $filename = ZPHP::getRootPath() . DS . 'webroot' . $request->server['path_info'];
        if (is_file($filename)) {  //解析静态文件
            $response->header("Content-Type", $this->getMime($filename) . '; charset=utf-8');
            $response->end(file_get_contents($filename));
            return;
        }
        $param = [];
        if (!empty($request->get)) {
            $param = $request->get;
        }

        if (!empty($request->post)) {
            $param += $request->post;
        }
        $_SERVER['HTTP_USER_AGENT'] = $request->header['user-agent'];
        Request::parse($param);
        Request::setViewMode('Php');
        Request::setHttpServer(1);
        Response::setResponse($response);
        try {
            $result = ZRoute::route();
        } catch (\Exception $e) {
            $model = Formater::exception($e);
            $model['_view_mode'] = 'Json';
            $result = Response::display($model);
        }
        $response->end($result);
        Request::setViewMode(ZConfig::getField('project', 'view_mode', 'Json'));
        Request::setHttpServer(0);
    }

    public function onMessage($server, $frame)
    {
        if (empty($frame->finish)) { //数据未完
            if (empty($this->buff[$frame->fd])) {
                $this->buff[$frame->fd] = $frame->data;
            } else {
                $this->buff[$frame->fd] .= $frame->data;
            }
        } else {
            if (!empty($this->buff[$frame->fd])) {
                $frame->data = $this->buff[$frame->fd] . $frame->data;
                unset($this->buff[$frame->fd]);
            }
        }
        $server->task([
            'cmd' => 'message',
            'fd' => $frame->fd,
            'data' => $frame->data
        ], 0);
    }

    public function onTask($server, $taskId, $fromId, $data)
    {
        switch ($data['cmd']) {
            case 'open':
                $this->ulist[$data['fd']] = $data['uid'];
                $this->flist[$data['uid']] = $data['fd'];
                $server->push($data['fd'], '{"type":"welcome","id":' . $data['uid'] . '}');
                break;
            case 'close':
                $uid = $this->ulist[$data['fd']];
                unset($this->ulist[$data['fd']]);
                unset($this->flist[$uid]);
                $this->sendAll($server, json_encode(array('type' => 'closed', 'id' => $uid)));
                break;
            case 'message':
                $message_data = json_decode($data['data'], true);
                $fd = $data['fd'];
                $uid = $this->ulist[$fd];
                switch ($message_data['type']) {
                    // 更新用户
                    case 'update':
                        // 转播给所有用户
                        $this->sendAll($server, json_encode(
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
                        return $this->sendAll($server, json_encode($new_message));
                    case 'robot':
                        $uid = $this->ulist[$data['fd']];
                        unset($this->ulist[$data['fd']]);
                        unset($this->flist[$uid]);
                        return;

                }
                break;
        }
    }


    public function sendAll($server, $data)
    {
        foreach ($this->flist as $fd) {
            $this->log("send {$fd}: {$data}");
            $ret = $server->push($fd, $data);
            if (!$ret) {
                $this->log("{$fd} send error");
            }
        }
    }


    public function log($msg)
    {
        if (!ZConfig::getField('socket', 'daemonize', 0)) {
            echo $msg . PHP_EOL;
        }
    }

    public function getMime($filename)
    {
        $ext = strtolower(trim(substr(strrchr($filename, '.'), 1)));
        if (isset($this->mimes[$ext])) {
            return $this->mimes[$ext];
        } else {
            return 'text/html';
        }
    }

    public function onWorkerStart($server, $workerId)
    {
        parent::onWorkerStart($server, $workerId);
    }

}