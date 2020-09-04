<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::rule('administrationLogin',  'index/login/administrationLogin');          //后台管理员登录
Route::rule('code',                 'index/login/code');                         //后台管理员登录验证码

Route::rule('login',                'index/index/login');                        //客户登录\注册 
Route::rule('send',                 'index/index/send');                         //手机验证码获取

Route::group('index', function () { 
    Route::rule('adminindex',       'index/admin/adminindex');                   //后台首页
    Route::rule('adminlist',        'index/admin/adminlist');                    //后台管理员列表
    Route::rule('adminadd',         'index/admin/adminadd');                     //后台管理员添加
    Route::rule('admindel',         'index/admin/admindel');                     //后台管理员删除
    Route::rule('adminedit',        'index/admin/adminedit');                    //后台管理员修改
    Route::rule('administrationOut','index/login/administrationOut');            //后台管理员退出登录

    Route::rule('columnlist',       'index/column/columnlist');                  //栏目首页
    Route::rule('columnadd',        'index/column/columnadd');                   //后台栏目添加
    Route::rule('uploadImg',        'index/column/uploadImg');                   //图片添加
    Route::rule('delImg',           'index/column/delImg');                      //图片删除 
    Route::rule('columnDel',        'index/column/columnDel');                   //后台栏目删除
    Route::rule('columnedit',       'index/column/columnedit');                  //后台栏目修改
    
    Route::rule('questionlist',     'index/question/index');                     //问答后台页面
    

})->middleware('Check');





return [

];




