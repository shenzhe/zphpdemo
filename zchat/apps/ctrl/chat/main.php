<?php

namespace ctrl\chat;

use common,
    ctrl\Base;

class main extends Base
{
    public function check()
    {
        $uid = $this->getInteger($this->params, 'uid');
        $token = $this->getString($this->params, 'token');
        if(common\Utils::checkToken($uid, $token)) {
            $uinfo =  common\loadClass::getService('User')->fetchById($uid);
            if(!empty($uinfo)) {
                return $uinfo->hash();
            }
            return false;
        }
        return false;
    }

    public function online()
    {
        $olUids = common\Utils::online();
        if(empty($olUids)) {
            return array();
        }
        $idsArr = \array_keys($olUids);
        $where = "id in (".implode(',', $idsArr).")";
        $userInfo = common\loadClass::getService('User')->fetchWhere($where);
        $result = array();
        foreach($userInfo as $user) {
            $result[$user->id] = $user->hash();
        }
        return $result;
    }
}
