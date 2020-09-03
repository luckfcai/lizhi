<?php
namespace app\index\controller\Validate;

use think\Validate;

class AdminInfo extends Validate
{

    protected $rule = [
        "__token__" => 'token',
        "captcha"   => 'require|captcha',
        "nick_name" => '/^[a-zA-Z_]\w{4,31}$/',
        "password"  => '/^\d{6}$/',
        "statuc"    => 'require',
        "phone"     => 'require|phone',
        "email"     => 'require|email',
    ];

    protected $message = [
        "nick_name" => '用户名不合法',
        "password"  => '密码不合法',
        "email.require"  => '邮箱不能为空',
        "email.email"    => '请输入正确的邮箱',
        "phone.require"  => '电话号码不能为空',
        "phone.phone"    => '请输入正确的电话号码',
        "captcha" => '请输入正确的验证码',
        "__token__" => '页面过期请刷新页面',
    ];


    protected $scene = [
        "login"     => ['nick_name','password','captcha','__token__'],
    ];
}