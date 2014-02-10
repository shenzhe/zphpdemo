<?php

namespace socket;
use ZPHP\Socket\ICallback;

class Sendbox implements ICallback
{

    public function onStart()
    {
        echo "sendbox server start".PHP_EOL;
    }

    public function onConnect()
    {
        $params = func_get_args();
        $fd = $params[1];
        echo "{$fd} connect".PHP_EOL;
    }

    public function onReceive()
    {
        $params = func_get_args();
        $data = trim($params[3]);
        if (empty($data)) {
            return;
        }
       echo $data.PHP_EOL;
        if ('<policy' == substr($data, 0, 7)) {
            \swoole_server_send($params[0], $params[1], "<cross-domain-policy>
                    <allow-access-from domain='*' to-ports='*' />
                    </cross-domain-policy>\0");
        }

    }

    public function onTimer()
    {
        return;
    }

    public function onClose()
    {
        $params = func_get_args();
        $fd = $params[1];
//        echo "Client {$fd}：close".PHP_EOL;
    }

    public function onShutdown()
    {
    }

    public function onWorkerStart()
    {
    }

    public function onWorkerStop()
    {
    }

    public function onTask()
    {

    }

    public function onFinish()
    {
        
    }
}
