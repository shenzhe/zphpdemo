<?php
use ZPHP\Socket\Adapter\Swoole;

return array(
    'server_mode' => 'Socket',
    'project_name' => 'zphpdemo-todpole',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'project' => array(
        'log_path' => 'socket',
        'default_ctrl_name' => 'main',
        'static_url' => '/static/',
    ),
    'socket' => array(
        'host' => '0.0.0.0',                          //socket 监听ip
        'port' => 8992,                             //socket 监听端口
        'adapter' => 'Swoole',                          //socket 驱动模块
        'server_type' => Swoole::TYPE_WEBSOCKET,              //socket 业务模型 tcp/udp/http/websocket
        'protocol' => 'Json',                         //socket通信数据协议
        'daemonize' => 1,                             //是否开启守护进程
        'client_class' => 'socket\\WebSocket',            //socket 回调类
        'work_mode' => 3,                             //工作模式：1：单进程单线程 2：多线程 3： 多进程
        'worker_num' => 4,                                 //工作进程数
        'task_worker_num' => 2,                                 //task进程数
        'max_request' => 0,                            //单个进程最大处理请求数
        'debug_mode' => 0,                                  //打开调试模式
    ),
);