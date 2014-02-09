<?php
    return array(
        'route'=>array(
            'static' => array(       //静态路由
                '/check' => array(
                    'main\\main',
                    'check'
                ),
                '/reg' => array(
                    'main\\main',
                    'reg'
                ),
                '/savereg' => array(
                    'main\\main',
                    'savereg'
                ),
            ),
            'dynamic' => array( //动态路由
                '/^\/(\d+)\/(.*?)$/iU' => array(  //匹配 http://host/uid/token
                    'main\\main',
                    'main',
                    array('uid', 'token'),   //对应的参数名
                    '/{uid}/{token}'        //反向返回的格式
                ),
            ),
        ),
    );
