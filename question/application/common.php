<?php
use think\DB;
use think\facade\Cookie;

//地址
define('URL','http://'.$_SERVER['HTTP_HOST'].'/index.php');

/**
 * 检查数据库是否存在
 * @param string 表名
 * @param array  条件
 * @return bool/array 
 */
function examine($table,$condition)
{
    return  DB::table($table)->where($condition)->find();
}


/**
 * 返回数据
 * @param array/string 返回数据 
 * @param init    验证码
 * @param string  提示
 * @return json
 */
function result($data,$code = 200, $msg = 'ok',$other=false)
{
    $ret = ["code"=>$code,"msg"=>$msg,'data'=>$data];
    $other && $ret = array_merge_recursive($ret,$other);
    exit(json_encode($ret));
}


/**
 * 递归找子集
 * @param array 范围数组
 * @param int   需找子集的id
 */
function getTree($data, $pid = 0)
{
    static $arr = array();
    foreach($data as $val){
        if($val['pid'] ==$pid){
            $arr[] = $val['id'];
            getTree($data, $val['id']);
        }
    }
    return $arr;
}

/**
 * 递归找子集
 * @param array 范围数组
 * @param int   需找子集的id
 */
function columnTree($data, $pid = 0, $level = 0 , $static=true)
{

    if($static){
        static $str = array();
    }else{
        $str = array();
    }
   
    foreach($data as $val){
        if($val['pid'] ==$pid){
            $val['level'] = $level;
            $str[] = $val;
            columnTree($data, $val['id'], $level + 1);
        }
    }
    return $str;
}



/**
 * 上传图片
 * @param string 图片地址
 */
function uploadImg($file)
{
    $info =  $file->validate(['size' => '5242880*1024', 'ext' => 'jpg,gif,png'])
    ->move('static/upload/admin');  
    if($info){
        $data['src'] = '/static/upload/admin/' . $info->getSaveName();
        //存入cookie方便删除
        $img = !Cookie::has('img')?[]:Cookie::get("img");
        $img[] = $data['src'];
        Cookie::set("img", $img, 30 * 24 * 60 * 60);
        $file = 'static/upload/admin/'.date('Ymd',time());
        //给文件权限
        file_exists($file) ? (is_writable($file) ? chmod($file, 0777):''):'';
        return $data;
    }
     return false;
}


/**
* 判断图片是否存在并删除
* @param string 图片地址
*/
function delFileImg($imgs)
{
    !is_array($imgs) && ($imgs = [$imgs]);
    foreach($imgs as $val){
        $img = substr($val,1);
        file_exists($img) && unlink($img);
    }
  
}


/**
* 图片表添加
* @param array 图片地址
* @param int   图片表外键标识
* @param int   图片类型1为默认图片2为其他 
*/
function addImg($file,$target_id='',$type='')
{
    $target_id = $target_id =='' ? time() : $target_id;
    if(count($file) == 0) return  $target_id;
    for ($i = 0; $i < count($file); $i++) {
        $img['file_type']   = $type==''? ($i==0? 1:2) : ($i==0? $type:2);
        $img['file_dir']    = $file[$i];
        $img['update_time'] = date('Y-m-d H:i:s', time());
        $img['target_id']   = $target_id;
        Db::table('file_dir')->insertGetId($img);
    }
    return  $target_id;
}

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
/**
 * 短信发送
 * @param $to    接收人
 * @param $code   短信验证码
 * @return json
 */
// 我的登录名称 luckfcai@1130499112462469.onaliyun.com
// 登录密码 luckfeicai123
// AccessKey ID LTAI4G7eEry1NAkk5WqVNsVK
// AccessKey Secret FzoEDOECYdJ7V6pKbtGhhYYVGBIhLG
function send_sms($to, $code){
    require_once '../extend/alisms/api_sdk/vendor/autoload.php';
    Config::load(); //加载区域结点配置
    $accessKeyId = 'LTAI4FzuEPQBmhFW49KmnPUM';
    $accessKeySecret = 'bX1Dzlk9K6xgOte11ZbQkEJq9kgFb1';
    $templateParam = $code;
    //短信API产品名（短信产品名固定，无需修改）
    $product = "Dysmsapi";
    //短信API产品域名（接口地址固定，无需修改）
    $domain = "dysmsapi.aliyuncs.com";
    //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
    $region = "cn-hangzhou";
    // 初始化用户Profile实例
    $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
    // 增加服务结点
    DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
    // 初始化AcsClient用于发起请求
    $acsClient= new DefaultAcsClient($profile);
    // 初始化SendSmsRequest实例用于设置发送短信的参数
    $request = new SendSmsRequest();
    // 必填，设置雉短信接收号码
    $request->setPhoneNumbers($to);
    // 必填，设置签名名称
    $request->setSignName("注册验证");
    // 必填，设置模板CODE
    $request->setTemplateCode("SMS_199910018");
    // 可选，设置模板参数
    if($templateParam) {
        $request->setTemplateParam(json_encode(['code'=>$templateParam]));//由于我的模板里需要传递我的短信验证码
    }
    //发起访问请求
    $acsResponse = $acsClient->getAcsResponse($request);
    //返回请求结果
    $result = json_decode(json_encode($acsResponse),true);
    // 具体返回值参考文档：https://help.aliyun.com/document_detail/55451.html?spm=a2c4g.11186623.6.563.YSe8FK
    return $result;
}
