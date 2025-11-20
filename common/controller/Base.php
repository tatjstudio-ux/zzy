<?php
namespace app\common\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Base extends Controller
{
    protected function initialize()
    {
        parent::initialize();
        $this->checkLogin(); // 验证登录
        $this->checkMenuAuth(); // 验证菜单权限
    }

    // 验证登录状态
    private function checkLogin()
    {
        $user = Session::get('user');
        if (empty($user) || empty($user['bm_id'])) {
            $this->redirect('/login'); // 未登录跳登录页
            exit;
        }
    }

    // 菜单权限验证：读取 bl_user.auth 字段（存储权限ID）
    private function checkMenuAuth()
    {
        $user = Session::get('user');
        $authId = $user['auth'] ?? 0; // 直接使用 auth 字段作为权限ID

        // 获取该权限可访问的菜单序号（menu_ids）
        $auth = Db::name('bm_auth')->where('id', $authId)->find();
        if (empty($auth)) {
            $this->error('无有效权限，请联系管理员', '/login');
            exit;
        }
        $allowMenuIds = explode(',', $auth['menu_ids']); // 如 [1,3,6]

        // 菜单URL与序号映射
        $menuUrlMap = [
            '/index' => 1,          // 首页
            '/workflow' => 2,       // 工作流
            '/workshop' => 3,       // 车间加工
            '/outsourcing' => 4,    // 外委加工
            '/warehouse-in' => 5,   // 库房入
            '/warehouse-out' => 6,  // 库房出
            '/report' => 7,         // 报表及统计数据
            '/setting' => 8,        // 期初数据及设置
            '/document' => 9,       // 单据管理
            '/user' => 10,          // 用户设置
            '/system' => 11,        // 系统设置
            '/help' => 12,          // 帮助中心
            '/logout' => 13         // 退出登录
        ];

        // 获取当前访问URL
        $currentUrl = Request::instance()->pathinfo();
        $currentMenuId = $menuUrlMap['/' . $currentUrl] ?? 0;

        // 拦截无权限访问（退出登录默认允许）
        if ($currentMenuId != 13 && !in_array((string)$currentMenuId, $allowMenuIds)) {
            $this->error('无权限访问该菜单！', '/index');
            exit;
        }
    }

    // 获取用户可访问的菜单（用于页面渲染）
    protected function getAllowMenus()
    {
        $user = Session::get('user');
        $authId = $user['auth'] ?? 0; // 使用 auth 字段
        $auth = Db::name('bm_auth')->where('id', $authId)->find();
        $allowMenuIds = explode(',', $auth['menu_ids'] ?? '1');

        // 完整菜单列表
        $allMenus = [
            ['id'=>1, 'name'=>'首页', 'url'=>'/index', 'icon'=>'fa-home'],
            ['id'=>2, 'name'=>'工作流', 'url'=>'/workflow', 'icon'=>'fa-random'],
            ['id'=>3, 'name'=>'车间加工', 'url'=>'/workshop', 'icon'=>'fa-cogs'],
            ['id'=>4, 'name'=>'外委加工', 'url'=>'/outsourcing', 'icon'=>'fa-truck'],
            ['id'=>5, 'name'=>'库房入', 'url'=>'/warehouse-in', 'icon'=>'fa-archive'],
            ['id'=>6, 'name'=>'库房出', 'url'=>'/warehouse-out', 'icon'=>'fa-archive'],
            ['id'=>7, 'name'=>'报表及统计数据', 'url'=>'/report', 'icon'=>'fa-bar-chart'],
            ['id'=>8, 'name'=>'期初数据及设置', 'url'=>'/setting', 'icon'=>'fa-cog'],
            ['id'=>9, 'name'=>'单据管理', 'url'=>'/document', 'icon'=>'fa-file-text'],
            ['id'=>10, 'name'=>'用户设置', 'url'=>'/user', 'icon'=>'fa-user'],
            ['id'=>11, 'name'=>'系统设置', 'url'=>'/system', 'icon'=>'fa-cog'],
            ['id'=>12, 'name'=>'帮助中心', 'url'=>'/help', 'icon'=>'fa-question-circle'],
            ['id'=>13, 'name'=>'退出登录', 'url'=>'/logout', 'icon'=>'fa-sign-out'],
        ];

        // 筛选可访问菜单
        $allowMenus = [];
        foreach ($allMenus as $menu) {
            if (in_array((string)$menu['id'], $allowMenuIds)) {
                $allowMenus[] = $menu;
            }
        }
        return $allowMenus;
    }
}