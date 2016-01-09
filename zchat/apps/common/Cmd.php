<?php

namespace common;


class Cmd
{

    const LOGIN = 1; //登录
    const LOGIN_SUCC = 2; //登录成功
    const RELOGIN = 3;      //重复登录
    const NEED_LOGIN = 4; //需要登录
    const LOGIN_ERROR = 5;  //登录失败
    const HB = 6;           //心跳
    const CHAT = 7;         //聊天
    const OLLIST = 8;       //获取在线列表
    const LOGOUT = 9;       //退出登录
    const TOKEN_ERROR = 10;       //校验失败
    const ERROR = -1;

    public static function parseData($data)
    {
        switch ($data[0]) {
            case self::LOGIN:
                return [
                    'a'=>'chat',
                    'm'=>'check',
                    'uid'=>$data[1][0],
                    'token'=>$data[1][1],
                ];
                break;
            case self::OLLIST:
                return [
                    'a'=>'chat',
                    'm'=>'online'
                ];
                break;
            case self::CHAT:
                return [
                    'a'=>'chat',
                    'm'=>'msg',
                    'toId'=>$data[1][0],
                    'msg'=>$data[1][1],
                ];
                break;
            default:
                return null;
        }
    }

} 