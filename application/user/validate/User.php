<?php


namespace app\user\validate;


use think\Validate;

class User extends Validate
{
    protected  $rule = [
      'email'  => 'require|email',
      'email_code'  => 'require|max:5',
      'nickname'  => 'require|max:20',
      'passwd'  => 'require|max:16',
      'user_type'  => 'number',
    ];

    protected  $message = [
        'email'  => '注意邮箱格式！',
        'email_code'  => '注意填写验证码！',
        'nickname'  => '注意填写昵称！',
        'passwd'  => '注意密码格式！',
        'user_type'  => '注意类型选择！',
    ];

}