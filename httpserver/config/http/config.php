<?php
use ZPHP\ZPHP;
define('NOW_TIME', time());
$config =  array(
    'server_mode' => 'Socket',
    'project_name' => 'zhttp',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'project' => array(
        'log_path' => 'socket',
        'static_url'=> 'http://web.zphp.45117.com/',
        'app_host'=> 'web.zphp.45117.com',
    ),
    'socket' => array(
        'host' => '0.0.0.0', //socket 监听ip
        'port' => 8992, //socket 监听端口
        'adapter' => 'Swoole', //socket 驱动模块
        'daemonize' => 1, //是否开启守护进程
        'work_mode' => 3,
        'worker_num' => 2,
        'client_class' => 'socket\\HttpServer', //socket 回调类
        'protocol' => 'Rpc', //socket通信数据协议
        'call_mode' => 'ZPHP', //业务处理模式
        'max_request' => 10000,
        'dispatch_mode' => 2,
    ),
);
$publicConfig = array('cache.php', 'pdo.php');
foreach($publicConfig as $file) {
    $file = ZPHP::getRootPath() . DS . 'config' . DS . 'public'. DS . $file;
    $config += include "{$file}";
}
return $config;
