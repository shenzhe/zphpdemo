<?php

namespace ctrl;

use common,
    ZPHP\Core\Config as ZConfig;

class main extends Base
{

    public function main()
    {
        $token = $this->getString($this->params, 'token', '');
        $uid = $this->getString($this->params, 'uid', '');
        if(common\Utils::checkToken($uid, $token)) {
            return array(
                'uid'=>$uid,
                'token'=>$token,
                'static_url'=>ZConfig::getField('project', 'static_url'),
                'app_host'=>ZConfig::getField('project', 'app_host'),
            );
        }

        return common\Utils::jump("main", "login", array(
            "msg"=>"需要登录"
        ));
    }

    public function login()
    {
        return array(
            'static_url'=>ZConfig::getField('project', 'static_url'),
        );
    }

    public function check()
    {
        $username = $this->getString($this->params, 'username');
        $password = $this->getString($this->params, 'password');
        $service = common\loadClass::getService('User');
        $userInfo = $service->checkUser($username, $password);

        if(!empty($userInfo)) {
            $token = common\Utils::setToken($userInfo->id);
            return common\Utils::jump("main", "main", array(
                'uid'=>$userInfo->id,
                'token'=>$token,
            ));
        }

        return common\Utils::showMsg("登录失败，请重试");
    }

    public function reg()
    {
        return array(
            'static_url'=>ZConfig::getField('project', 'static_url'),
        );
    }

    public function savereg()
    {
        $username = $this->getString($this->params, 'username');
        $password = $this->getString($this->params, 'password');
        $icon = $this->getString($this->params, 'icon', 'noface.jpg');
        $service = common\loadClass::getService('User');
        $result = $service->addUser($username, $password, $icon);
        if($result) {
            return common\Utils::jump("main", "main", array(
                "msg"=>"注册成功"
            ));
        }

        return common\Utils::showMsg("注册失败");
    }
}
