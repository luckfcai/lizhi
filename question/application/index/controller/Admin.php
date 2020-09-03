<?php
namespace app\index\controller;

use think\DB;
use think\Controller;
use think\facade\Request;

class Admin extends Controller
{
 
    /**
     * 首页
     */
    public function adminIndex()
    {
        return $this->fetch('/adminIndex');
    }


    /**
     * 管理员列表
     */
    public function adminList()
    {

        if (Request::has('list','get')) {
            $like = input('like') ? input('like') : '%';
            $data = DB::table('admin_info')
            ->where('nick_name','like','%'.$like.'%')
            ->order('id','asc')
            ->paginate(2);

            $count = $data->total();
            $data = $data->items();
            result($data,0,'',["count"=>$count]);
        }
        return $this->fetch('/admin/list');
    }

    /**
     * 删除
     */
    public function adminDel()
    {
        if (Request::has('id','post')) {
            $ret = DB::table('admin_info')->delete(input('id'));
            $ret ? result($ret,200,'删除成功') : result($ret,0,'删除失败');
        }
    }

    /**
     * 添加
     */
    public function adminAdd()
    {
        
        if (Request::has('data','post')) {
            $data =  input('data');
            examine('admin_info',['nick_name'=>$data['nick_name']]) && result('',100,'名称已存在！');
            $data['update_time'] = date('Y-m-d H:i:s',time());
            $ret =  DB::table('admin_info')->insertGetId($data);
            $ret ? result($ret,200,'添加成功') : result($ret,0,'添加失败');
        }
        return $this->fetch('/admin/add');
    }


    //修改/编辑
    public function adminEdit()
    {

        if (Request::has('data','post')) {
            $data =  input('data');
            $check = examine('admin_info',['nick_name'=>$data['nick_name']]);
           ($check && $check['id'] != $data['id']) && result('',100,'名称已存在！');
            $data['update_time'] = date('Y-m-d H:i:s',time());
            $ret =   DB::table('admin_info')->update($data);
            $ret ? result($ret,200,'修改成功') : result($ret,0,'修改失败');
        }
        return $this->fetch('/admin/edit');
    }

}
