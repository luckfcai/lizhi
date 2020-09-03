<?php

namespace app\index\controller;

use think\Db;
use think\Controller;
use think\facade\Cache;
use think\facade\Request;
use think\facade\Session;

class Index extends Controller
{

    /**
     * 首页
     */
    public function index()
    {
        !Session::has("userInfo") &&  $this->error('请先登录',URL.'/login');
        $data = Cache::remember('index',function(){ 
            return DB::table('question')->select();
        });
        $hits = DB::table('question')->order('hits desc')->limit(3)->select();
        $list = DB::table('column')->select();
        $this->assign('list',json_encode($list));
        $this->assign('hits',json_encode($hits));
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
                    $userInfo = examine('userInfo',input('data'));
                    $userInfo ? (Session::set('userInfo',$userInfo) && result($userInfo,200,'登陆成功')) : result('',0,'请输入正确的账号密码');
                    break;
                case 'register':
                    examine('userInfo',input('data')) && result('',100,'该手机已经注册过了');
                    $id = DB::table('userInfo')->insertGetId(input('data'));
                    if($id){
                        $userInfo = [
                            'id' => $id,
                            'phone' => input('data')['phone'],
                            'password' => input('data')['password'],
                        ];
                        result($userInfo,'200','注册成功点击确定为您跳转');
                    }else{
                        result('',100,' 服务器繁忙请稍后重试');
                    }
                break;
            }    
        }

        return $this->fetch('/login');
    }
    /* 
     * 短信验证码
     */
    public function send()
    {
        if (Request::has('phone','post')) {
            examine('userInfo',input('data')) && result('',100,'该手机已经注册过了');
            $code = rand(100000, 999999);
            $phone = input('phone');
            $result = send_sms($phone, $code);
            ($result['Message']=='OK') && result($code,200,'以发送6位数验证码');
            result('',0,'请输入正确手机号');
        }
    }
}
