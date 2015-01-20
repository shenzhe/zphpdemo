<?php
namespace ctrl;
use service\Base;
use ZPHP\Controller\IController;
use ZPHP\Core\Config as ZConfig;
use common;

class login extends Base
{

    public function check()
    {
        print_r($this->_server->getParams());
    }

    public function reg()
    {
        return [];
    }

    public function savereg()
    {
        $username = $this->getString($this->params, 'username');
        $password = $this->getString($this->params, 'password');
        $icon = $this->getString($this->params, 'icon', 'noface.jpg');
        $service = common\loadClass::getService('User');
        $result = $service->addUser($username, $password, $icon);
        if($result) {
            return common\Utils::jump("main/main", "main", array(
                "msg"=>"注册成功"
            ));
        }

        return common\Utils::showMsg("注册失败");
    }
}

