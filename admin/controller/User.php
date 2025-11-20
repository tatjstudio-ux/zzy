<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Request;
use think\Session; 

class User extends Controller
{
    // 显示用户管理页面（新增权限列和选择功能）
    public function main($id,$user)
    { 
        $admin = Db::name('bl_user')->select();
        $userInfo = Db::name('bl_user')->where('bm_phone',$user)->select();
        $authList = Db::name('bm_auth')->select(); // 读取所有权限
        
        if($userInfo[0]['auth']==1){
            return $this->fetch('admin',[
                'id'=>$id,
                'auth'=>$userInfo[0]['auth'],
                'admin'=>$admin,
                'user'=>$userInfo,
                'authList'=>$authList, // 传递权限列表供下拉框使用
                'title'=>'用户管理'
            ]);
        }else{
            return $this->fetch('user',[
                'id'=>$id,
                'user'=>$userInfo,
                'authList'=>$authList,
                'title'=>'用户管理'
            ]);
        }
    }

    // 更新用户密码
    public function updata()
    {
        $id = $this->request->post('id');
        $user = $this->request->post('user');
        $oldpass = $this->request->post('oldpass');
        $pass = $this->request->post('pass');
        $repass = $this->request->post('repass');
        $mpass=md5($pass);
        $alluser=Db::name('bl_user')->where('bm_phone',$user)->select();
        
        if($pass==$repass){
            if(md5($oldpass)==$alluser[0]['bm_pass']){
                Db::name('bl_user')->where('bm_phone',$user)->update(['bm_pass'=>$mpass]);
                Session::delete('xpass');
                $this->success('密码修改成功，请重新登录',"/admin/index/logout");
            }else{
                $this->success('原密码错误！',"/admin/user/main?id=".$id.'&user='.$user);
            }
        }else{
            $this->success('新密码不一致!',"/admin/user/main?id=".$id.'&user='.$user);
        }
    }

    // 编辑用户信息（新增权限选择）
    public function adminedit($id,$uid)
    { 
        $user = Db::name('bl_user')->where('bm_id',$uid)->select();
        $authList = Db::name('bm_auth')->select(); // 读取所有权限
        return $this->fetch('adminuser',[
            'id'=>$id,
            'user'=>$user,
            'authList'=>$authList,
            'title'=>'编辑用户信息'
        ]);
    }

    // 更新用户信息（新增权限更新逻辑，使用 auth 字段）
    public function adminupdata()
    {
        $id = $this->request->post('id');
        $user = $this->request->post('user');
        $myname = $this->request->post('myname');
        $myphone = $this->request->post('myphone');
        $repass = $this->request->post('repass');
        $authId = $this->request->post('auth_id', 0, 'intval'); // 接收权限ID
        
        $data = ['bm_name'=>$myname, 'bm_phone'=>$myphone];
        if(!empty($repass)){
            $data['bm_pass'] = md5($repass);
        }
        $data['auth'] = $authId; // 直接更新 auth 字段（存储权限ID）
        
        Db::name('bl_user')->where('bm_phone',$user)->update($data);
        $this->success('修改成功！',"/admin/user/main?id=".$id.'&user='.$user);
    }

    // 增加用户（新增权限选择）
    public function adminadd($id,$user)
    { 
        $authList = Db::name('bm_auth')->select(); // 读取所有权限
        return $this->fetch('adminadd',[
            'id'=>$id,
            'user'=>$user,
            'authList'=>$authList,
            'title'=>'新增用户'
        ]);
    }

    // 提交新增用户（新增权限关联，使用 auth 字段）
    public function adminaddup()
    { 
        $myname = $this->request->post('myname');
        $myphone = $this->request->post('myphone');
        $repass = $this->request->post('repass');
        $user = $this->request->post('user');
        $id = $this->request->post('id');
        $authId = $this->request->post('auth_id', 0, 'intval'); // 接收权限ID
        
        $date = date('Y-m-d');
        Db::name('bl_user')->insert([
            'bm_name'=>$myname,
            'bm_phone'=>$myphone,
            'bm_pass'=>md5($repass),
            'bm_add'=>0,
            'auth'=>$authId, // 直接存储权限ID到 auth 字段
            'bm_vip'=>0,
            'bm_time'=>$date
        ]); 
        return $this->redirect("/admin/user/main?id=".$id."&user=".$user);
    }
}