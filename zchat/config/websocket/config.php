<?php
use ZPHP\ZPHP;
use ZPHP\Socket\Adapter\Swoole;
define('TPL_PATH', ZPHP::getRootPath() . DS  . 'template'. DS . 'zchat' . DS);
define('STATIC_URL', '/static/');
return array(
    'server_mode' => 'Server\WebSocket',
    'project_name' => 'zwebsocket',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'lib_path' => ZPHP::getRootPath().DS.'..'.DS.'lib',
    'project' => array(
        'default_ctrl_name'=>'main',
        'log_path' => 'socket',
        'static_url'=> STATIC_URL,
        'tpl_path'=> TPL_PATH,
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
    'pdo' => [     //数据库配置
        'dsn' => 'mysql:host=localhost;port=3306',
        'name' => 'cd',
        'user' => 'laya_log',
        'pass' => 'NXdfHu3aWfXmGMjE',
        'dbname' => 'laya_log',
        'charset' => 'UTF8',
        'pconnect' => true,
        'ping' => 1
    ],
    'route'=>[  //url重写
        'static'=> [
            '/'=>['main', 'main'],
        ],

        'dynamic'=>[
            '/^\/(.+)\/(.+)$/iU' => [
                '{1}',
                '{2}',
                [],
                '/{a}/{m}',
            ]
        ]
    ],
);
