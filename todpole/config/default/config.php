<?php
use ZPHP\ZPHP;
define('PROJECT_NAME', 'todpole');
$config =  array(
    'server_mode' => 'Http',
    'project_name' => PROJECT_NAME,
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'project' => array(
        'log_path' => 'http',
        'default_ctrl_name'=>'main',
        'tpl_path' => ZPHP::getRootPath(). DS.'template'. DS . PROJECT_NAME.DS
    ),
);
return $config;
