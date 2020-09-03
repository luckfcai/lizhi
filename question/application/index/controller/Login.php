<?php
namespace app\index\controller;

use think\Controller;
use think\facade\Request;
use think\captcha\Captcha;
use think\facade\Session;
use app\index\controller\Validate\AdminInfo;

class Login extends Controller
{
  
    /**
     * 管理员登录
     */
    public function administrationLogin()
    {

        if (Request::has("nick_name")) {

            $data = [                   
                "nick_name" => input('nick_name'),
                "password"  => input('password'),
                "captcha"   => input('captcha'),
                "__token__" => input('__token__'),
            ];  
            $conition = [
                'nick_name' => $data['nick_name'],
                'password'  => $data['password']
            ];
            $validate = new AdminInfo(); 

            if ($validate->scene('login')->check($data)) {
                $adminInfo = examine('admin_info',$conition);
                if ($adminInfo) { 
                    $adminInfo['status'] == 0 && result($adminInfo,0,'账号被禁用');
                    Session::set('adminInfo',$adminInfo);
                    result($adminInfo,200,'登录成功');
                } else {
                    result($conition,0,'帐号不存在');
                }
            } else {
                $erroMsg = $validate->getError();
                result($erroMsg,100,$erroMsg);
            }
            exit;
        }
        return $this->fetch(('/administrationLogin'));
    }


     /**
      * 验证码
      */
    public function code()
    {
        $captcha = new Captcha();
        return $captcha->entry();
    }

    /**
     * 退出登录
     */
    public function administrationOut()
    {
        Session::delete('adminInfo');
        header('Location:'.URL.'/administrationLogin');
        exit;
    }

}