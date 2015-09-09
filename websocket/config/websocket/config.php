<?php
use ZPHP\ZPHP;
$config =  array(
    'server_mode' => 'Server\WebSocket',
    'project_name' => 'zwebsocket',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'lib_path' => ZPHP::getRootPath().DS.'..'.DS.'lib',
    'project' => array(
        'default_ctrl_name'=>'main',
        'log_path' => 'socket',
        'static_url'=> 'http://hs.static.45117.com/',
        'app_host'=> $_SERVER['HTTP_HOST'],
    ),
    'socket' => array(
        'host' => '0.0.0.0', //socket 监听ip
        'port' => 8993, //socket 监听端口
        'adapter' => 'Swoole', //socket 驱动模块
        'adapter' => 'Swoole', //socket 驱动模块
        'socket_type' => '\ZPHP', //是否开启守护进程
        'work_mode' => 3,
        'worker_num' => 1,
        'client_class' => 'socket\\WebSocket', //socket 回调类
        'parse_class' => 'WebSocketChatParse', //socket 回调类
        'protocol' => 'Rpc', //socket通信数据协议
        'call_mode' => 'ZPHP', //业务处理模式
        'max_request' => 10000,
        'dispatch_mode' => 2,
        'heartbeat_idle_time'=>600,
        'heartbeat_check_interval'=>610,
    ),
);
$publicConfig = array('connection.php', 'cache.php');
foreach($publicConfig as $file) {
    $file = ZPHP::getRootPath() . DS . 'config' . DS . 'public'. DS . $file;
    $config += include "{$file}";
}
return $config;
