<?php

namespace app\index\controller;

use think\Db;
use think\Controller;
use think\facade\Cache;
use think\facade\Request;

class Index extends Controller
{

    /**
     * 首页
     */
    public function index()
    {
        $data = Cache::remember('index',function(){ 
            return DB::table('question')->select();
        });
        $this->assign('data',json_encode($data));
        return $this->fetch('/index');
    }
    /**
     * 登陆
     */
    public function login()
    {
        if (Request::has('data','post')) {
            switch(input('type')){
                case 'login':
                    $ret = examine('userInfo',input('data'));
                    $ret ? result($ret,200,'登陆成功') : result($ret,0,'登陆失败');
                    break;
                case 'register':
                    examine('userInfo',input('data')) && result('',100,'该手机已经注册过了');
                    $id = DB::table('userInfo')->insertGetId(input('data'));
                    $id ? result($id,'200','注册成功') : result('',100,' 服务器繁忙请稍后重试');
                break;
            }



            
        }

        return $this->fetch('/login');
    }
    public function send()
    {
        if (Request::has('phone','post')) {
            $code = rand(100000, 999999);
            $phone = input('phone');
            $result = send_sms($phone, $code);
            ($result['Message']=='OK') && result($code,200,'以发送6位数验证码');
            result('',0,'请输入正确手机号');
        }
    }
}
