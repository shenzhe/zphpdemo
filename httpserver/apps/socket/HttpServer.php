<?php

namespace socket;
use ZPHP\Socket\Callback\HttpServer as ZHttpServer;
use ZPHP\Socket\Route;
use ZPHP\Core\Config as ZConfig;

class HttpServer extends ZHttpServer
{

    private $_route;

    public function onSend($fd, $data)
    {
        $result = $this->route($data, $fd);
        $this->sendTo($fd, $result);
    }

    private function sendTo($fd, $data)
    {
        $keepalive = ZConfig::getField('project', 'keepalive', 1);
        $response = join(
            "\r\n",
            array(
                'HTTP/1.1 200 OK',
                'Content-Type: text/html; charset=utf-8',
                'Connection: '.$keepalive ? 'keep-alive' : 'Close',
                'Server:zserver 0.1',
                'Content-Length: '.strlen($data),
                'Date: '. gmdate("D, d M Y H:i:s T"),
                '',
                $data));
        $this->serv->send($fd, $response);

        if(!$keepalive) {
            $this->serv->close($fd);
        }
    }

    private function route($data, $fd)
    {
        if(empty($this->_route)) {
            $this->_route = Route::getInstance(ZConfig::getField('socket', 'call_mode', 'ZPHP'));
        }
        try {
            return $this->_route->run($data, $fd);
        } catch (\Exception $e) {
            //$result =  Formater::exception($e);
            return null;
        }
    }
}
