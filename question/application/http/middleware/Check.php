<?php
namespace app\http\middleware;

use think\facade\Session;
use think\Controller;


class Check extends Controller
{
    public function handle($request, \Closure $next)
    {
        !Session::has("adminInfo") &&  $this->error('请先登录',URL.'/administrationLogin');
        return $next($request);
    }
}
