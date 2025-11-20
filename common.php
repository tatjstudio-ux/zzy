<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

// application/common.php
use think\View;
use think\Db;
function registerGlobalVars(View $view)
{
        // 设置全局变量
        $all=Db::name('bl_setting')->where('id',1)->select();
        $mainame=$all[0]['name'];
        $this->view->assign('appName', $mainame);
}


