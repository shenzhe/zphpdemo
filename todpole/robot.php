<?php
require "zphp/ZPHP/Common/WebSocketClient.php";
function getXY($id)
{
    return [
        'angle' => "-4.734",
        'momentum' => "0.000",
        'name' => "Robot." . $id,
        'type' => "update",
        'x' => mt_rand(-300, 300),
        'y' => mt_rand(-300, 300),
    ];
}

$send_count = 0;
$num = 100;  //机器人个数
$host = '127.0.0.1';
$prot = 8995;
$clients = [];
for ($i = 1; $i <= $num; $i++) {

    $client = new Common\WebSocketClient($host, $prot);
    $data = $client->connect();
    $id = substr($data, -7, 6);
    echo "{$i} : {$id}" . PHP_EOL;
    $result = $client->send(json_encode(getXY($id)));
    $fd = $client->getSocket()->sock;
    $clients[$fd] = [$id, $client];
    echo "robot {$fd} create success" . PHP_EOL;
    usleep(20000);
    $client->send(json_encode(array(
        'type'=>'robot'
    )));
}
echo "starttime: " . date("Y-m-d H:i:s") . PHP_EOL;
$starttime = time();
while (1) {
    foreach ($clients as $index => $cli) {
        $result = $cli[1]->send(json_encode(getXY($cli[0])));
        $send_count++;
        if ($result === false) {
            unset($clients[$index]);
            echo "{$cli[0]} error" . PHP_EOL;
        } else {
            $recvData = $cli[1]->recv();
            /*if(!$recvData) {
                unset($clients[$index]);
                echo "{$index} recv error" . PHP_EOL;
                return;
            }*/


        }
    }
    echo "send_count={$send_count}, etime: " . date("Y-m-d H:i:s") . PHP_EOL;
    if (empty($clients)) {
        echo "client empty, runing: " . (time() - $starttime) . PHP_EOL;
        return;
    }
    sleep(1);
}

echo 'finish'.PHP_EOL;
