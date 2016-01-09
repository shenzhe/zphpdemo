zchat
====

@author: shenzhe (泽泽，半桶水)

@email: shenzhe163@gmail.com

zchat 是 基于 zphp实现的聊天室，着重于zphp在 socket, redis, mysql, swoole相结合

demo地址：
========
 http://zchat.45117.com


需求的扩展及服务：
=========

1) swoole: https://github.com/swoole/swoole-src

2:redis: http://redis.io

3: phpredis: http://github.com/phpredis/phpredis

运行：
======

1) cd 程序目录

2) php zchat/webroot/main.php sendbox   //sendbox服务

3) php zchat/webroot/main.php socket    //zchat socket服务

4) cache,conn需要redis支持 (可根据你启动的redis的host的port去更改config/public目录下cache.php和connedtion.php上相对应的配置)

5）导入database下的sql         (config/public目录下pdo是相对应的配置)

6) webserver绑定域名到 zchat/webroot ，运行 http://host


url route说明
============

如果需要支持url route

1)   web服务器要支持 pathinfo
2)   用rewrite规则，把请求rewrite到 main.php/下

附：nginx规则：

	server {
	        listen       80;
	        server_name  zchat.45117.com;
	        root   /home/www/zchat/webroot;     //这里改成你自己的目录
	        index  main.php main.html;


	        location / {                                      //这里是rewrire定义
	            if (!-e $request_filename) {
	                rewrite ^/(.*)$ /main.php/$1 last;
	            }
	        }

	        error_page   403 404 /404.html;

	        error_page   500 502 503 504  /50x.html;

	        location ~ [^/]\.php(/|$) {
	            fastcgi_split_path_info ^(.+?\.php)(/.*)$;                     //开始pathinfo
	            if (!-f $document_root$fastcgi_script_name) {
	                return 404;
	            }

	            fastcgi_pass   127.0.0.1:9000;
	            fastcgi_index  main.php;
	            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
	            include        fastcgi_params;
	            fastcgi_param  PATH_INFO $fastcgi_path_info;
	        }

	}