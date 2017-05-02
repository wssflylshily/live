<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
    public $_validate = array(
        array('username','6,16','用户名在6-16位之间',1,'length',3),
        array('password','6,16','密码在6-16位之间',1,'length',3),
        array('email','email','邮箱不合法',1,'regex',3),
        array('confirm_password','password','两次密码不一致啊……',1,'confirm',3),
    );

}