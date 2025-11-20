<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Request;
use think\Session; 
use think\Cookie; 
use think\View;


class Category extends Controller
{
    // 分类列表页面
    public function index()
    {
        $categories = $this->getAllCategories(0, 0);
        return $this->fetch('list', ['categories' => $categories,'title'=>'库存商品']);
    }

    // 添加分类页面
    public function add()
    {
        $categoryList = $this->getCategoryList(0, 0);
        return $this->fetch('add', ['categoryList' => $categoryList,'title'=>'新增库存商品']);
    }

    // 处理添加分类
    public function doAdd()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $result = Db::name('bl_kcsp')->insert([
                'name'     => $data['name'],
                'pid'      => $data['parent_id'] ?? 0,
                'class'    => $data['class'] ?? 1,
                'myorder'  => $data['myorder'] ?? 0,
                'jine'     => $data['jine'] ?? 0,
                'vjine'    => $data['vjine'] ?? 0,
                'tdjg'     => $data['tdjg'] ?? 0,
                'kcyj'     => $data['kcyj'] ?? 0
            ]);
            
            if ($result) {
                $this->success('添加成功', url('index'));
            } else {
                $this->error('添加失败');
            }
        }
    }

    // 编辑分类页面
    public function edit()
    {
        $id = $this->request->param('id/d', 0);
        if (!$id) {
            $this->error('参数错误', url('index'));
        }
        
        $category = Db::name('bl_kcsp')->find($id);
        $categoryList = $this->getCategoryList(0, 0);
        
        return $this->fetch('edit', [
            'category' => $category,
            'categoryList' => $categoryList,'title'=>'编辑库存商品'
        ]);
    }

    // 处理编辑分类
    public function doEdit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $result = Db::name('bl_kcsp')->update([
                'id'       => $data['id'],
                'name'     => $data['name'],
                'pid'      => $data['parent_id'],
                'class'    => $data['class'],
                'myorder'  => $data['myorder'],
                'jine'     => $data['jine'] ?? 0,
                'vjine'    => $data['vjine'] ?? 0,
                'tdjg'     => $data['tdjg'] ?? 0,
                'kcyj'     => $data['kcyj'] ?? 0,
                'kcl'      => $data['kcl']
            ]);
            
            if ($result !== false) {
                $this->success('更新成功', url('index'));
            } else {
                $this->error('更新失败');
            }
        }
    }

    // 处理删除分类
    public function delete()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id/d', 0);
            if (!$id) {
                return json(['code' => 0, 'msg' => '参数错误']);
            }
            
            if ($this->hasChildren($id)) {
                return json(['code' => 0, 'msg' => '该分类存在子分类，无法删除']);
            }
            
            $result = Db::name('bl_kcsp')->delete($id);
            if ($result) {
                return json(['code' => 1, 'msg' => '删除成功']);
            } else {
                return json(['code' => 0, 'msg' => '删除失败']);
            }
        }
    }

    // 递归获取所有分类
    private function getAllCategories($parentId = 0, $level = 0)
    {
        $categories = Db::name('bl_kcsp')
            ->where('pid', $parentId)
            ->order('myorder', 'asc')
            ->select()
            ->toArray();
            
        $result = [];
        foreach ($categories as $category) {
            $category['indent'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
            $category['children'] = $this->getAllCategories($category['id'], $level + 1);
            $result[] = $category;
        }
        
        return $result;
    }

    // 获取分类列表（用于下拉菜单）
    private function getCategoryList($parentId = 0, $level = 0)
    {
        $categories = Db::name('bl_kcsp')
            ->where('pid', $parentId)
            ->order('myorder', 'asc')
            ->select()
            ->toArray();
            
        $result = [];
        foreach ($categories as $category) {
            $category['name'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . $category['name'];
            $result[] = $category;
            $result = array_merge($result, $this->getCategoryList($category['id'], $level + 1));
        }
        
        return $result;
    }

    // 检查分类是否有子分类
    private function hasChildren($id)
    {
        $count = Db::name('bl_kcsp')
            ->where('pid', $id)
            ->count();
        return $count > 0;
    }
}