<?php
use ZPHP\ZPHP;
$config =  array(
    'server_mode' => 'Socket',
    'project_name' => 'zphpdemo-todpole',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'project' => array(
        'log_path' => 'socket',
        'static_url'=> 'http://hs.static.45117.com/',
        'app_host'=> 'zchat.45117.com',
    ),
    'socket' => array(
        'host' => '0.0.0.0', //socket 监听ip
        'port' => 8995, //socket 监听端口
        'adapter' => 'Swoole', //socket 驱动模块
        'daemonize' => 1, //是否开启守护进程
        'work_mode' => 3,
        'worker_num' => 1,
        'client_class' => 'socket\\WebSocket', //socket 回调类
        'protocol' => 'Rpc', //socket通信数据协议
        'call_mode' => 'ZPHP', //业务处理模式
        'max_request' => 0,
        'dispatch_mode' => 2,
        //'heartbeat_idle_time'=>3600,
        //'heartbeat_check_interval'=>3650,
    ),
);
$publicConfig = array('cache.php', 'connection.php');
foreach($publicConfig as $file) {
    $file = ZPHP::getRootPath() . DS . 'config' . DS . 'public'. DS . $file;
    $config += include "{$file}";
}
return $config;
