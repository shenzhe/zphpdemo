###composer 安装
1.创建composer.json文件   
2.添加代码  
```javascript
{
    "require": {
        "zphp/zphp": "dev-master"
    }
}
```  
3.执行composer install  
4.然后参照demo_composer文件夹初始项目。

##运行demo

    http模式：
        1) 域名绑定到目录webroot
        2) 运行：http://域名/main.php?name=zphp&k1=v1
    socket模块:
        1) php 项目目录/webroot/main.php socket
        2) telnet 127.0.0.1 8991
        3) 输入: {"a":"main/main","name":"zphp","k1":"v1"} 发送
        4) 返回: zphp running\n
        