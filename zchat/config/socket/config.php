<?php
use ZPHP\ZPHP;
use ZPHP\Socket\Adapter\Swoole;
define('CHAT_MSG_EOF', '##||##');
$config =  array(
    'server_mode' => 'Socket',
    'project_name' => 'zchat',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'project' => array(
        'log_path' => 'socket',
        'default_ctrl_name'=>'main',
        ''
    ),
    'socket' => array(
        'host' => '0.0.0.0', //socket 监听ip
        'port' => 8991, //socket 监听端口
        'adapter' => 'Swoole',                          //socket 驱动模块
        'server_type' => Swoole::TYPE_TCP,              //socket 业务模型 tcp/udp/http/websocket
        'daemonize' => 0, //是否开启守护进程
        'heartbeat_check_interval' => 65, //每隔 30s 会做一次心跳检测, 需swoole 1.6.11版支持
        'heartbeat_idle_time' => 70,      //fd最后一次通信时间超过 35s，会被close
        'work_mode' => 3,
        'worker_num' => 2,
        'client_class' => 'socket\\Swoole', //socket 回调类
        'max_request' => 0,
        'dispatch_mode' => 2,
        'open_eof_check' => 1, //打开EOF检测
        'package_eof' => CHAT_MSG_EOF, //设置EOF
        'open_eof_split'=>1,
        'protocol' => 'Json',                         //socket通信数据协议
    ),
);
$publicConfig = array('pdo.php', 'connection.php', 'cache.php');
foreach($publicConfig as $file) {
    $file = ZPHP::getRootPath() . DS . 'config' . DS . 'public'. DS . $file;
    $config += include "{$file}";
}
return $config;
