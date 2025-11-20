<?php
namespace app\admin\controller;
use app\common\controller\Base;
use think\Db;
use think\Request;

class Auth extends Base
{
    // 权限列表（带统计）
    public function index()
    {
        $authList = Db::name('bm_auth')
            ->order('id desc')
            ->select();

        // 统计数据
        $totalAuth = count($authList);
        $userCount = Db::name('bl_user')->where('auth_id', '>', 0)->count();
        $monthStart = date('Y-m-01 00:00:00');
        $newAuthCount = Db::name('bm_auth')->where('create_time', '>=', $monthStart)->count();

        // 菜单名称映射（用于列表显示）
        $menuMap = [
            '1'=>'首页', '2'=>'工作流', '3'=>'车间加工', '4'=>'外委加工', '5'=>'库房入', 
            '6'=>'库房出', '7'=>'报表', '8'=>'期初设置', '9'=>'单据管理', '10'=>'用户设置', 
            '11'=>'系统设置', '12'=>'帮助中心', '13'=>'退出登录'
        ];
        $this->assign([
            'authList' => $authList,
            'totalAuth' => $totalAuth,
            'userCount' => $userCount,
            'newAuthCount' => $newAuthCount,
            'menuMap' => $menuMap
        ]);
        return $this->fetch();
    }

    // 添加权限页面
    public function add()
    {
        return $this->fetch();
    }

    // 执行添加
    public function doAdd()
    {
        $request = Request::instance();
        if ($request->isPost()) {
            $data = [
                'auth_name' => trim($request->post('auth_name')),
                'auth_rule' => trim($request->post('auth_rule')),
                'menu_ids' => trim($request->post('menu_ids')),
                'auth_desc' => trim($request->post('auth_desc')),
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ];

            // 数据验证
            $validate = \think\Validate::make([
                'auth_name' => 'require|unique:bm_auth|max:50',
                'auth_rule' => 'require|unique:bm_auth|max:200',
                'menu_ids' => 'require|regex:^[\d,]+$',
                'auth_desc' => 'max:255',
            ], [
                'auth_name.require' => '权限名称不能为空',
                'auth_name.unique' => '该权限名称已存在',
                'auth_name.max' => '权限名称不能超过50个字符',
                'auth_rule.require' => '权限标识不能为空',
                'auth_rule.unique' => '该权限标识已存在',
                'auth_rule.max' => '权限标识不能超过200个字符',
                'menu_ids.require' => '请选择可访问菜单',
                'menu_ids.regex' => '菜单权限格式错误（仅允许数字和逗号）',
                'auth_desc.max' => '权限描述不能超过255个字符',
            ]);

            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }

            $result = Db::name('bm_auth')->insert($data);
            if ($result) {
                return $this->success('新增成功', 'auth/index');
            } else {
                return $this->error('新增失败');
            }
        }
        return $this->fetch();
    }

    // 编辑权限页面
    public function edit()
    {
        $id = request()->param('id/d');
        if (!$id) {
            return $this->error('参数错误：缺少权限ID');
        }

        $auth = Db::name('bm_auth')->find($id);
        if (!$auth) {
            return $this->error('该权限不存在或已被删除');
        }

        $this->assign('auth', $auth);
        return $this->fetch();
    }

    // 执行编辑
    public function doEdit()
    {
        $request = Request::instance();
        if ($request->isPost()) {
            $data = [
                'id' => $request->post('id/d'),
                'auth_name' => trim($request->post('auth_name')),
                'auth_rule' => trim($request->post('auth_rule')),
                'menu_ids' => trim($request->post('menu_ids')),
                'auth_desc' => trim($request->post('auth_desc')),
                'update_time' => date('Y-m-d H:i:s'),
            ];

            // 数据验证（编辑时排除自身）
            $validate = \think\Validate::make([
                'auth_name' => "require|unique:bm_auth,auth_name,{$data['id']}|max:50",
                'auth_rule' => "require|unique:bm_auth,auth_rule,{$data['id']}|max:200",
                'menu_ids' => 'require|regex:^[\d,]+$',
                'auth_desc' => 'max:255',
            ], [
                'auth_name.require' => '权限名称不能为空',
                'auth_name.unique' => '该权限名称已存在',
                'auth_name.max' => '权限名称不能超过50个字符',
                'auth_rule.require' => '权限标识不能为空',
                'auth_rule.unique' => '该权限标识已存在',
                'auth_rule.max' => '权限标识不能超过200个字符',
                'menu_ids.require' => '请选择可访问菜单',
                'menu_ids.regex' => '菜单权限格式错误（仅允许数字和逗号）',
                'auth_desc.max' => '权限描述不能超过255个字符',
            ]);

            if (!$validate->check($data)) {
                return $this->error($validate->getError());
            }

            $result = Db::name('bm_auth')->where('id', $data['id'])->update($data);
            if ($result !== false) {
                return $this->success('编辑成功', 'auth/index');
            } else {
                return $this->error('编辑失败');
            }
        }
        return $this->fetch();
    }

    // 删除权限
    public function del()
    {
        $id = request()->param('id/d');
        if (!$id) {
            return $this->error('参数错误：缺少权限ID');
        }

        // 校验是否有用户关联
        $userCount = Db::name('bl_user')->where('auth_id', $id)->count();
        if ($userCount > 0) {
            return $this->error("该权限已被 $userCount 个用户使用，无法删除！");
        }

        $result = Db::name('bm_auth')->delete($id);
        if ($result) {
            return $this->success('删除成功', 'auth/index');
        } else {
            return $this->error('删除失败');
        }
    }
}