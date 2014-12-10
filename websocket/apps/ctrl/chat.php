<?php
namespace ctrl;
use ZPHP\Cache\Factory as ZCache;
use ZPHP\Controller\IController;
use ZPHP\Core\Config as ZConfig;

class chat implements IController
{
    private $_server;
    public function setServer($server)
    {
        $this->_server = $server;
    }

    public function _before()
    {
        $this->params = $this->_server->getParams();
        return true;
    }

    public function _after()
    {
        //
    }

    public function message()
    {
        if(!empty($this->params['channel'])) {  //私聊
            \HttpServer::$wsresponse->message(json_encode([
                'cmd' => 'fromMsg',
                'from' => \HttpServer::$wsresponse->fd,
                'data' => $this->params['data'],
            ]));
            \HttpServer::$wsresponse->message(json_encode([
                'cmd' => 'fromMsg',
                'from' => \HttpServer::$wsresponse->fd,
                'data' => $this->params['data'],
            ]), intval($this->params['to']));
        }else{
            $this->boardcast([
                'cmd' => 'fromMsg',
                'from' => \HttpServer::$wsresponse->fd,
                'data' => $this->params['data'],
            ]);
        }
    }

    public function login()
    {

        $this->boardcast([
            'cmd' => 'newUser',
            'fd' => \HttpServer::$wsresponse->fd,
            'name' => $this->params['name'],
            'avatar' => $this->params['avatar'],
        ]);

        \HttpServer::$wsresponse->message(
            json_encode([
                'cmd' => 'login'
            ])
        );

//        echo \HttpServer::$wsresponse->fd." : ".$this->params['name']." login start".PHP_EOL;

        ZCache::getInstance('Redis', ZConfig::getField('cache', 'net'))->set(\HttpServer::$wsresponse->fd, json_encode([
            'name'=>$this->params['name'],
            'avatar'=>$this->params['avatar']
        ]));

//        echo ZCache::getInstance('Redis', ZConfig::getField('cache', 'net'))->get(\HttpServer::$wsresponse->fd).PHP_EOL;
//        echo "login end".PHP_EOL;

    }

    public function getOnline()
    {
        $resMsg = array(
            'cmd' => 'getOnline',
        );
//        echo "getOnline".PHP_EOL;
        $start_fd = 0;
        while(true)
        {
            $conn_list = \HttpServer::$http->connection_list($start_fd, 100);
//            var_dump($conn_list);
            if($conn_list===false or count($conn_list) === 0)
            {
                break;
            }
            $start_fd = end($conn_list);
            foreach($conn_list as $fd)
            {
//                echo "list {$fd} get start".PHP_EOL;
                $conn = \HttpServer::$http->connection_info($fd);
//                print_r($conn);
//                echo "list {$fd} get end".PHP_EOL;
                if($conn['websocket_status'] > 1) {
                    $uinfo = ZCache::getInstance('Redis', ZConfig::getField('cache', 'net'))->get($fd);
                    $uinfo = json_decode($uinfo, true);
                    $resMsg['list'][] = array(
                        'fd' => $fd,
                        'name' => $uinfo['name'],
                        'avatar' => $uinfo['avatar'],
                    );
                }

            }
        }
//        print_r($resMsg);
        $this->boardcast($resMsg);
    }

    public function offline()
    {
//        echo 'offline start'.PHP_EOL;
        ZCache::getInstance('Redis', ZConfig::getField('cache', 'net'))->delete($this->params['fd']);
        $this->boardcast([
            'cmd' => 'offline',
            'fd' => $this->params['fd'],
            'from' => 0,
            'channal' => 0
        ]);
//        echo 'offline end'.PHP_EOL;

    }


    private function boardcast($data)
    {
        $data = json_encode($data);
        $start_fd = 0;
        while(true)
        {
            $conn_list = \HttpServer::$http->connection_list($start_fd, 10);
            if($conn_list===false or count($conn_list) === 0)
            {
                break;
            }
            $start_fd = end($conn_list);
            foreach($conn_list as $fd)
            {
//                if($fd == \HttpServer::$wsresponse->fd) {
//                    continue;
//                }
                $conn = \HttpServer::$http->connection_info($fd);
                if($conn['websocket_status'] > 1) {
//                    echo "send fd: {$fd}: {$data}" . PHP_EOL;
                    \HttpServer::$wsresponse->message($data, $fd);
                }
            }
        }
    }
}

