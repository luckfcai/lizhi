<?php
namespace app\index\controller;
use think\Db;
use think\Controller;
use think\facade\Cache;
class Question extends Controller
{
    
     /**
     * 后台页面
     */
    public function index()
    {      
        $this->assign('data',json_encode($this->tree()));
        return $this->fetch('/question');
    }

     /**
     * 添加 
     */
    public function add()
    {   
        $id = DB::table('question')->insertGetId(input('data'));
        $id ? result($this->tree(true),200,'添加成功') :  result('',0,'添加失败');
    }

     /**
     * 修改 
     */
    public function edit()
    {   
        $ret = DB::table('question')->update(input('data'));
        $ret ? result($this->tree(true),200,'修改成功') :  result('',0,'修改失败');
        exit;
    }

    /**
     * 删除 
     */
    public function del()
    {   
        //递归删子集 
        $delId = getTree(DB::table('question')->select(),input('data'));
        count($delId) > 0 && DB::table('question')->delete($delId);
        $ret = DB::table('question')->delete(input('data'));
        $ret ? result($this->tree(true),200,'删除成功') :  result('',0,'删除失败');
    }


    /**
     * 树状图数据列表
     * @param bool 是否更新
     * @return array
     */
    public function tree($update=false)
    {
        $data = Cache::get('question');
        if($update || !$data){
            $info = DB::table('question')->select();
            $data = [];
            foreach($info as $val){
                $str = $val;
                $tree = [];
                foreach($info as $vals){
                   $vals['pid'] == $val['id'] && $tree[] = $vals['id'];
                }
                count($tree) >= 1 ? $str['tree'] = [$tree[0],end($tree)] : $str['tree'] = 1 ;
                $data[] = $str;
            }
            
            Cache::clear();
            Cache::set('question',$data,60*60*24*30);
            Cache::set('index',$info,60*60*24*30);
            return Cache::get('question');
        }
        return $data;
    }
}
