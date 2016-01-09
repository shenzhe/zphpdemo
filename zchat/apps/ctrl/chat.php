<?php

namespace ctrl;

use common;

class chat extends Base
{
    public function check()
    {
        $uid = $this->getInteger($this->params, 'uid');
        $token = $this->getString($this->params, 'token');
        return common\loadClass::getService('Chat')->check($uid, $token);
    }

    public function msg()
    {
        $toId = $this->getInteger($this->params, 'toId');
        $msg = $this->getString($this->params, 'msg');
        common\loadClass::getService('Chat')->msg($toId, $msg);
    }

    public function online()
    {
        common\loadClass::getService('Chat')->getOnlineList();
    }

    public function offline()
    {
        common\loadClass::getService('Chat')->offline();
    }
}
