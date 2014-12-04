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
        if(!empty($this->params['from'])) {
            \HttpServer::$response->message(json_encode([
                'cmd' => 'fromMsg',
                'from' => \HttpServer::$response->fd,
                'data' => $this->params['data'],
            ]));
        }else{
            $this->boardcast([
                'cmd' => 'fromMsg',
                'from' => \HttpServer::$response->fd,
                'data' => $this->params['data'],
            ]);
        }
    }

    public function login()
    {

        $this->boardcast([
            'cmd' => 'newUser',
            'fd' => \HttpServer::$response->fd,
            'name' => $this->params['name'],
            'avatar' => $this->params['avatar'],
        ]);

        \HttpServer::$response->message(
            json_encode([
                'cmd' => 'login'
            ])
        );


        ZCache::getInstance('Yac', ZConfig::getField('cache', 'locale'))->add(\HttpServer::$response->fd, json_encode([
            'name'=>$this->params['name'],
            'avatar'=>$this->params['avatar']
        ]));

    }

    public function getOnline()
    {
        $resMsg = array(
            'cmd' => 'getOnline',
        );
        $start_fd = 0;
        while(true)
        {
            $conn_list = \HttpServer::$http->connection_list($start_fd, 10);
            if($conn_list===false or count($conn_list) === 0)
            {
                return;
            }
            $start_fd = end($conn_list);
            foreach($conn_list as $fd)
            {
                $conn = \HttpServer::$http->connection_info($fd);
                if($conn['websocket_status'] > 1) {
                    $uinfo = ZCache::getInstance('Yac', ZConfig::getField('cache', 'locale'))->get($fd);
                    $uinfo = json_decode($uinfo, true);
                    $resMsg['list'][] = array(
                        'fd' => $fd,
                        'name' => $uinfo['name'],
                        'avatar' => $uinfo['avatar'],
                    );
                }

            }
        }
        $this->boardcast(json_encode($resMsg));
    }

    public function offline()
    {
        ZCache::getInstance('Yac', ZConfig::getField('cache', 'locale'))->delete(\HttpServer::$response->fd);
        $this->boardcast([
            'cmd' => 'offline',
            'fd' => \HttpServer::$response->fd,
            'from' => 0,
            'channal' => 0,
            'data' => "下线了。。",
        ]);

    }


    private function boardcast($data)
    {
        var_dump(\HttpServer::$response);
        $data = json_encode($data);
        $start_fd = 0;
        while(true)
        {
            $conn_list = \HttpServer::$http->connection_list($start_fd, 10);
            if($conn_list===false or count($conn_list) === 0)
            {
                return;
            }
            $start_fd = end($conn_list);
            foreach($conn_list as $fd)
            {
//                if($fd == \HttpServer::$response->fd) {
//                    continue;
//                }
                $conn = \HttpServer::$http->connection_info($fd);
                if($conn['websocket_status'] > 1) {
                    echo "send fd: {$fd}: {$data}" . PHP_EOL;
                    \HttpServer::$response->message($data, $fd);
                }
            }
        }
    }
}

