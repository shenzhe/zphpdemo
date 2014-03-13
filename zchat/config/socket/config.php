<?php
use ZPHP\ZPHP;
define('NOW_TIME', time());
$config =  array(
    'server_mode' => 'Socket',
    'project_name' => 'zchat',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'project' => array(
        'log_path' => 'socket',
    ),
    'socket' => array(
        'host' => '0.0.0.0', //socket 监听ip
        'port' => 8991, //socket 监听端口
        'adapter' => 'Swoole', //socket 驱动模块
        'daemonize' => 1, //是否开启守护进程
        'heartbeat_check_interval' => 65, //每隔 30s 会做一次心跳检测, 需swoole 1.6.11版支持
        'heartbeat_idle_time' => 70,      //fd最后一次通信时间超过 35s，会被close
        'work_mode' => 3,
        'worker_num' => 2,
        'client_class' => 'socket\\Swoole', //socket 回调类
        'protocol' => 'Rpc', //socket通信数据协议
        'call_mode' => 'ROUTE', //业务处理模式
        'max_request' => 0,
        'dispatch_mode' => 2,
    ),
);
$publicConfig = array('connection.php', 'cache.php', 'pdo.php');
foreach($publicConfig as $file) {
    $file = ZPHP::getRootPath() . DS . 'config' . DS . 'public'. DS . $file;
    $config += include "{$file}";
}
return $config;
