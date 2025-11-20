<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Request;
use think\Session; 
use think\Cookie; 

class Product extends Controller
{
    // 分类列表
    public function index()
    {
        $list = Db::name('bl_kcsp')->order('sort asc')->select();
        $tree = $this->getTree($list);
        return view('', ['list' => $tree]);
    }
    
    // 获取树形结构
    private function getTree($data, $pid = 0, $level = 0)
    {
        static $tree = [];
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['level'] = $level;
                $tree[] = $v;
                $this->getTree($data, $v['id'], $level + 1);
            }
        }
        return $tree;
    }
    
    // 添加分类
    public function add()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data['sort'] = $data['sort'] ?? 0;
            $result = Db::name('bl_kcsp')->insert($data);
            if ($result) {
                return json(['code' => 1, 'msg' => '添加成功']);
            } else {
                return json(['code' => 0, 'msg' => '添加失败']);
            }
        }
        
        $pid = input('pid', 0);
        $list = Db::name('bl_kcsp')->select();
        $tree = $this->getTree($list);
        return view('', ['pid' => $pid, 'list' => $tree]);
    }
    
    // 编辑分类
    public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $result = Db::name('bl_kcsp')->where('id', $data['id'])->update($data);
            if ($result !== false) {
                return json(['code' => 1, 'msg' => '更新成功']);
            } else {
                return json(['code' => 0, 'msg' => '更新失败']);
            }
        }
        
        $id = input('id');
        $info = Db::name('bl_kcsp')->where('id', $id)->find();
        $list = Db::name('bl_kcsp')->select();
        $tree = $this->getTree($list);
        return view('', ['info' => $info, 'list' => $tree]);
    }
    
    // 删除分类
    public function del()
    {
        $id = input('id');
        // 检查是否有子分类
        $child = Db::name('bl_')->where('pid', $id)->find();
        if ($child) {
            return json(['code' => 0, 'msg' => '请先删除子分类']);
        }
        
        $result = Db::name('bl_kcsp')->where('id', $id)->delete();
        if ($result) {
            return json(['code' => 1, 'msg' => '删除成功']);
        } else {
            return json(['code' => 0, 'msg' => '删除失败']);
        }
    }
}
