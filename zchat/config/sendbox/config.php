<?php

return array(
    'server_mode' => 'Socket',
    'project_name' => 'zphp',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'socket' => array(
        'host' => '0.0.0.0', //socket 监听ip
        'port' => 843, //socket 监听端口
        'adapter' => 'Swoole', //socket 驱动模块
        'daemonize' => 1, //是否开启守护进程
        'work_mode' => 1,
        'worker_num' => 2,
        'client_class' => 'socket\\Sendbox', //socket 回调类
        'protocol' => 'Json', //socket通信数据协议
        'call_mode' => 'RPC', //业务处理模式
        'max_request' => 1000,
    ),
);
