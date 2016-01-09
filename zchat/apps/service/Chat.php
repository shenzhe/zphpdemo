<?php
namespace service;

use common,
    entity;
use ZPHP\Protocol\Request;
use ZPHP\Conn\Factory as ZConn;
use ZPHP\Core\Config as ZConfig;

class Chat extends Base
{

    private function getConn()
    {
        return ZConn::getInstance('Redis', ZConfig::get('connection'));
    }

    public function check($uid, $token)
    {
        $fd = Request::getFd();
        if(common\Utils::checkToken($uid, $token)) {
            $uinfo =  common\loadClass::getService('User')->fetchById($uid);
            if(!empty($uinfo)) {  //登录成功
                $oinfo = $this->getConn()->get($uinfo->id);
                if(!empty($oinfo)) {
                    $this->sendOne($oinfo['fd'], common\Cmd::RELOGIN, []);
                    $this->getConn()->delete($oinfo['fd'], $uid);
                    $this->close($oinfo['fd']);
                }
                $this->getConn()->add($uid, $fd);
                $this->getConn()->addFd($fd, $uid);
                $this->sendToChannel(common\Cmd::LOGIN_SUCC, $uinfo->hash());
                return null;
            }
        }
        $this->sendOne(Request::getFd(), common\Cmd::LOGIN_ERROR);
        Request::getSocket()->close($fd);
    }

    public function sendOne($fd, $cmd, $data=[])
    {
        if (empty($fd) || empty($cmd)) {
            return;
        }
        $data = json_encode(array($cmd, $data));
        return Request::getSocket()->send($fd, $data.CHAT_MSG_EOF);
    }

    public function sendToChannel($cmd, $data, $channel = 'ALL')
    {
        $list = $this->getConn()->getChannel($channel);
        if (empty($list)) {
            return;
        }
        foreach ($list as $fd) {
            $this->sendOne($fd, $cmd, $data);
        }
    }

    public function getOnlineList()
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
        $this->sendOne(Request::getFd(), common\Cmd::OLLIST, $result);
    }

    public function offline()
    {
        $fd = Request::getFd();
        $uid = $this->getConn()->getUid($fd);
        $this->getConn()->delete($fd, $uid);
        $this->sendToChannel(common\Cmd::LOGOUT, array($uid));
    }

    public function close($fd)
    {
        Request::getSocket()->close($fd);
    }

    public function msg($toId, $msg)
    {
        $fd = Request::getFd();
        $uid = $this->getConn()->getUid($fd);
        if(empty($toId)) {  //公共聊天
            $this->sendToChannel(common\Cmd::CHAT, array($uid, $msg, $toId));
        } else { //私聊
            $toInfo = $this->getConn()->get($toId);
            if(!empty($toInfo)) {
                $this->sendOne($toInfo['fd'], common\Cmd::CHAT, array($uid, $msg, $toId));
                $this->sendOne($fd, common\Cmd::CHAT, array($uid, $msg, $toId));
            }
        }
    }
} 