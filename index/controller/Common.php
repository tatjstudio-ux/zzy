<?php
namespace app\index\controller;
use think\Controller;
class Common extends Controller
{
    //检查是否登录
    public function _initialize()
    {
        if (!session('username')) {
            $this->error('请先登录！', url('/index/login/login'));
        }
    }
}
