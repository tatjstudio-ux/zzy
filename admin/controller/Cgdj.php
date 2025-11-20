<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Request;
use think\Session; 
use think\Cookie; 
class Cgdj extends Controller
{

    public function list($id,$d,$u)
    {       if($d==0){
                $xd=date('Y-m-d');
            }else{
                $xd=$d;
            }
            $users=$u;
        	$db2 = Db::name('bl_cpcg')->select();
			$db3 = Db::name('bl_user')->select();
            if($id==1){
			    $all=Db::name('bl_ddcg')->where('she',0)->where('zf',0)->order('id desc')->select();
			    return $this->fetch('list',['all'=>$all,'db2'=>$db2,'db3'=>$db3,'id'=>$id,'xphone'=>$db3[0]['bm_phone'],'xname'=>$db3[0]['bm_name'],'d'=>$xd,'u'=>$users,'title'=>'采购单据管理']);
			}elseif($id==2){
			    $all=Db::name('bl_ddcg')->order('id desc')->select();
			    return $this->fetch('list',['all'=>$all,'db2'=>$db2,'db3'=>$db3,'id'=>$id,'xphone'=>$db3[0]['bm_phone'],'xname'=>$db3[0]['bm_name'],'d'=>$xd,'u'=>$users,'title'=>'采购单据管理']);
			}


	
    }
    
    
    public function pass($idx,$id,$ck)//通过审核
    { 		
			
			$db = Db::name('bl_ddcg')->where('id',$idx);
				$idb = $db->update(['she'=>1,'cku'=>$ck]); 
				return $this->redirect("/admin/index?id=".$id);
	
    }

    
    
    public function passx($idx,$d,$u,$ck)
    { 
			$db = Db::name('bl_ddcg')->where('id',$idx);
				$idb = $db->update(['she'=>1,'cku'=>$ck,'zf'=>0]); 
				return $this->redirect("/admin/cgdj/list?id=2&d=0&u=".$u);
	
    }

    public function passx2($idx,$u,$ck,$id)
    { 
			$db = Db::name('bl_ddcg')->where('id',$idx);
				$idb = $db->update(['she'=>1,'cku'=>$ck]); 
				return $this->redirect("/admin/cgdj/list?id=".$id."&d=0&u=".$u);
	
    }

    public function del($idx,$id)
    { 
			$db = Db::name('bl_ddcg')->where('id',$idx);
			$idb = $db->update(['zf'=>1,'she'=>0]); 
		    return $this->redirect("/admin/cgdj/list?id=2&d=0&u=".$id);
	
    }
    

    public function reprint($idx,$id)
    { 
			$db = Db::name('bl_ddcg')->where('id',$idx);
			$idb = $db->update(['mprint'=>0]); 
		    return $this->redirect("/admin/index?id=".$id);
	
    }

    
    public function dtle($id)
    { 
		    $all=Db::name('bl_ddcg')->where('id',$id)->select();
			$idx=$all[0]["id"];
			$ux=$all[0]["userx"];
			$db2 = Db::name('bl_cpcg')->where('fid',$idx)->select();
			$db3 = Db::name('bl_user')->where('bm_id',$ux)->select();
			return $this->fetch('dtle',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$all[0]['myphone'],'xname'=>$all[0]['myname']]);
	
    }


//库存商品管理模块结束
    
}



