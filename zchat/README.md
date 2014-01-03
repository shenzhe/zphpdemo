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

1) swoole: https://github.com/matyhtf/swoole

2:redis-storage: http://github.com/shenzhe/redis-storage

3: phpredis: http://github.com/shenzhe/phpredis

运行：
======

1) cd 程序目录

2) php zchat/webroot/main.php sendbox   //sendbox服务

3) php zchat/webroot/main.php socket    //zchat socket服务

4) 运行cache,conn两个redis服务 (config/public目录下cache和conn上相对应的配置)

5）导入database下的sql         (config/public目录下pdo是相对应的配置)

6) webserver绑定域名到 zchat/webroot ，运行 http://host


