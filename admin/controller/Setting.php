<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Request;
use think\Session; 

class Setting extends Controller
{
    
    public function main($id)
    { 
        $setting=Db::name('bl_setting')->select();
        if($id==1){
            return $this->fetch('index',['id'=>$id,'setting'=>$setting,'title'=>'系统设置']);
        }else{
            return $this->fetch('index',['id'=>$id,'setting'=>$setting,'title'=>'系统设置']);
        }

    }
    public function edit()
    { 
        // 接收原有参数
        $name = $this->request->post('cpname');
        $exdh = $this->request->post('exdh');
        $wwexdh = $this->request->post('wwexdh');
        $excg = $this->request->post('excg');
        $sphao = $this->request->post('sphao');
        $sguige = $this->request->post('sguige');
        $semail = $this->request->post('semail');
        $semail2 = $this->request->post('semail2');
        $semail3 = $this->request->post('semail3');
        $autogl = $this->request->post('autogl');
        $xiaoshou = $this->request->post('xiaoshou');
        $oldph = $this->request->post('oldph');
        $onlineprt = $this->request->post('onlineprt');
        $id = $this->request->post('id');
        
        // 处理新增的9个选项，生成shuliang字段值
        $shuliang = '';
        for ($i = 1; $i <= 9; $i++) {
            $value = $this->request->post('shuliang_' . $i, 0);
            $shuliang .= $value;
        }
        
        // 更新数据，新增shuliang字段
        $db = Db::name('bl_setting')->where('id', 1);
        $id2 = $db->update([
            'name'=>$name,
            'exdh'=>$exdh,
            'wwexdh'=>$wwexdh,
            'excg'=>$excg,
            'autoph'=>$sphao,
            'sguige'=>$sguige,
            'semail'=>$semail,
            'semail2'=>$semail2,
            'semail3'=>$semail3,
            'autogl'=>$autogl,
            'xiaoshou'=>$xiaoshou,
            'oldph'=>$oldph,
            'onlineprt'=>$onlineprt,
            'shuliang' => $shuliang // 新增字段
        ]); 
        return $this->redirect("/admin/setting/main?id=".$id);
    }
}