<?php
use ZPHP\ZPHP;
$config =  array(
    'server_mode' => 'Http',
    'project_name' => 'todpole',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'project' => array(
        'log_path' => 'http',
        'default_ctrl_name'=>'main'
    ),
);
return $config;
