<?php
use ZPHP\ZPHP;
define('NOW_TIME', isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
$config =  array(
    'server_mode' => 'Http',
    'project_name' => 'zchat',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'project' => array(
        'log_path' => 'http',
        'static_url' => '/static/',
        'tpl_path'=>'template'. DS .'zchat',
        'view_mode'=> 'Json',
        'app_host'=> $_SERVER['HTTP_HOST']
    ),
);
$publicConfig = array('pdo.php', 'cache.php', 'connection.php', 'route.php');
foreach($publicConfig as $file) {
    $file = ZPHP::getRootPath() . DS . 'config' . DS . 'public'. DS . $file;
    $config += include "{$file}";
}

return $config;
