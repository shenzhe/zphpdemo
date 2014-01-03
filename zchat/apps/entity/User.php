<?php

namespace entity;

/**
 * Class User
 * @package entity
 * 用户信息
 */
class User
{
    const TABLE_NAME = 'user';

    public $id;             //用户id
    public $username;       //用户名称
    public $password;       //用户密码
    public $icon;           //用户头像

    public function hash()
    {
        return array(
            $this->id,
            $this->username,
            $this->icon
        );
    }
}