zhttpserver Demo运行：
======

	1) cd 程序目录

	2) php httpserver/webroot/main.php http 

	3) 浏览器运行：http://host:8992

	4) 静态文件还是走nginx。需要在config/http/config.php里配置

依赖两个扩展
====
	https://github.com/matyhtf/php-webserver/tree/master/ext   //用来解析http 
	https://github.com/laruence/yac                            //共享，如果采用fd按模分配，可不用此扩展，config/public/cache.php里的Yac改成Php

线上demo地址
====

	http://zchat.45117.com:8992


