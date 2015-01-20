<?php
namespace service;

use common,
    entity;

class User extends Base
{

    public function __construct()
    {
        $this->dao = common\loadClass::getDao('User');
    }

    public function checkUser($username, $password)
    {
        $userInfo = $this->fetchAll(array(
                "username"=>"'{$username}'",
                "password"=>"'{$password}'",
            )
        );
        if(empty($userInfo)) {
            return false;
        } else {
            return $userInfo[0];
        }
    }

    public function addUser($username, $password)
    {
        if($this->checkUser($username, $password)) {
            return false;
        }
        $entity = new entity\User();
        $entity->username = $username;
        $entity->password = $password;
        return $this->add($entity);
    }
} 