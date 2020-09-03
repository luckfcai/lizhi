<?php
namespace app\index\controller;

use think\Controller;
use think\facade\Request;
use think\facade\Cookie;
use think\DB;

class Column extends Controller
{

    /**
     * 列表
     */
    public function columnlist()
    {
        $this->assign('arr',$this->getData());
        return $this->fetch('/column/list');
    }

    public function getData()
    {
        return columnTree(DB::table('column')
        ->alias('a')
        ->leftjoin(['file_dir' => 'f'], 'a.file_id=f.target_id and file_type= 1')
        ->field('a.*,f.file_dir')
        ->select());
    }

    public function columnDel()
    {
        $delId = columnTree($this->getData(),input('id'),0,false);
        $delId[] = ['id'=> input('id'),'file_id'=>input('file_id')];
        foreach($delId as $val){
           DB::table('column')->delete($val['id']);
           DB::table('file_dir')->where(['target_id'=>$val['file_id']])->delete();
        }
        result('',200,'删除成功');
    }

    /**
     * 修改
     */
    public function columnEdit()
    {
        if (input('id')){
            $data = examine('column',['id'=>input('id')]);
            $img  = DB::table('file_dir')-> where('target_id',$data['file_id'])->column('file_dir');
            $imgData = $img ? $img : [];
            $this->assign('data', $data); 
            $this->assign('img' , $imgData); 
            //选择父栏目列表 
            $getData = $this->getData();
            foreach($getData as $val){
                $val['id'] == input('id') && ($level = $val['level']);
            }
            $tree = [['name'=>  '无父级','id'=>0]];
            foreach($getData as  $val){
                ($val['level'] <= $level && $val['id'] != input('id')) &&  $tree[] = $val;
            }                 
            $this->assign('tree', $tree);  
            return $this->fetch('/column/edit');             
        }
       
        if (Request::has('data','post')) {
            $editData =  input('data');
            if(input('img')) {
                $check = examine('column',['name'=>$editData['name']]);
                ($check && $check['id'] != $editData['id']) && result('',100,'名称已存在！');
                $img  = DB::table('file_dir')-> where('target_id',$editData['file_id'])->column('file_dir');
                if($img && count($img)>0){
                    DB::table('file_dir')->delete(['target_id'=>$editData['file_id']]);
                }
                $editData['file_id'] = addImg(input('img'));
            }
            $editData['update_time'] = date('Y-m-d H:i:s',time());
            $ret =  DB::table('column')->update($editData);
            $ret ? result($ret,200,'修改成功') : result($ret,0,'修改失败');
        }
                    
    }

    /**
     * 添加
     */
    public function columnAdd()
    {
        if (Request::has('pid','get')) {
            $this->assign('pid',input('pid'));
            return $this->fetch('/column/add');
        }
        if (Request::has('data','post')) {
            $data =  input('data');
            examine('column',['name'=>$data['name']]) && result('',100,'名称已存在');
            $data['file_id'] = addImg(input('img'));
            $data['update_time'] = date('Y-m-d H:i:s',time());
            $ret =  DB::table('column')->insertGetId($data);
            $ret ? result($ret,200,'添加成功') : result($ret,0,'添加失败');
        }  
    }

    /**
     * 上传图片
     */
    public function UploadImg()
    {
       $imgInfo = uploadImg($this->request->file('file'));
       $imgInfo ? $this->result($imgInfo, 0, '上传成功') :  $this->result('', 200, '上传失败');
    }

     /**
     * 删除图片
     */
    public function delImg()
    {
        delFileImg(input('delImg'));
        exit;
    }

}
