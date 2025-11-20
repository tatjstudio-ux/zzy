<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Request;
use think\Session; 
use think\Cookie; 
class Index extends Controller
{

    public function logout()//退出登录
    { 	
        Session::delete('mainame');
        Session::delete('xpass');
        Session::delete('xphone');
        Cookie::delete('xphone');
        return $this->redirect('/');
    }
    
    public function index($id)
    { 
            $cnum1=$all=Db::name('bl_dd')->select();
            $cnumcg=$all=Db::name('bl_ddcg')->select();
            $cnumww=$all=Db::name('bl_wwdd')->select();
            $cnum2=$all=Db::name('bl_dd')->where('she',1)->where('zf',0)->select();
            $cnum3=$all=Db::name('bl_dd')->where('zf',1)->select();
            $cnum4=$all=Db::name('bl_wwdd')->where('she',0)->where('zf',0)->select();
            $cnum5=$all=Db::name('bl_wwdd')->where('she',1)->where('zf',0)->select();
            $cnum6=$all=Db::name('bl_wwdd')->where('zf',1)->select();
            $chkpass=Session::get('xpass');
			$all=Db::name('bl_dd')->order('id desc')->select();
			$idx=$all[0]["id"];
			$db2 = Db::name('bl_cp')->where('fid',$idx)->select();
			$db3 = Db::name('bl_user')->where('bm_phone',$id)->select();
			if($chkpass==$db3[0]['bm_pass']){
			    return $this->fetch('index',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$db3[0]['bm_phone'],'num1'=>count($cnum1),'numcg'=>count($cnumcg),'numww'=>count($cnumww),'num2'=>count($cnum2),'num3'=>count($cnum3),'num4'=>count($cnum4),'num5'=>count($cnum5),'num6'=>count($cnum6),'auth'=>$db3[0]['auth']]);
			}else{
                $this->success('登录超时，请重新登录！',"/admin/index/logout");
			}


    }
    public function list($id,$d,$u)
    {       if($d==0){
                $xd=date('Y-m-d');
            }else{
                $xd=$d;
            }
            $users=$u;
        	$db2 = Db::name('bl_cp')->select();
			$db3 = Db::name('bl_user')->select();
            if($id==1){
			    $all=Db::name('bl_dd')->where('she',0)->where('zf',0)->order('id desc')->select();
			    return $this->fetch('list',['all'=>$all,'db2'=>$db2,'db3'=>$db3,'id'=>$id,'xphone'=>$db3[0]['bm_phone'],'xname'=>$db3[0]['bm_name'],'d'=>$xd,'u'=>$users,'title'=>'加工单据管理']);
			}elseif($id==2){
			    $all=Db::name('bl_dd')->order('id desc')->select();
			    return $this->fetch('list',['all'=>$all,'db2'=>$db2,'db3'=>$db3,'id'=>$id,'xphone'=>$db3[0]['bm_phone'],'xname'=>$db3[0]['bm_name'],'d'=>$xd,'u'=>$users,'title'=>'加工单据管理']);
			}


	
    }
    
 public function wwlist($id,$d,$u)
    {       if($d==0){
                $xd=date('Y-m-d');
            }else{
                $xd=$d;
            }
            $users=$u;
        	$db2 = Db::name('bl_wwcp')->select();
			$db3 = Db::name('bl_user')->select();
            if($id==1){
			    $all=Db::name('bl_wwdd')->where('she',0)->where('zf',0)->order('id desc')->select();
			    return $this->fetch('wwlist',['all'=>$all,'db2'=>$db2,'db3'=>$db3,'id'=>$id,'xphone'=>$db3[0]['bm_phone'],'xname'=>$db3[0]['bm_name'],'d'=>$xd,'u'=>$users,'title'=>'外委单据管理']);
			}elseif($id==2){
			    $all=Db::name('bl_wwdd')->order('id desc')->select();
			    return $this->fetch('wwlist',['all'=>$all,'db2'=>$db2,'db3'=>$db3,'id'=>$id,'xphone'=>$db3[0]['bm_phone'],'xname'=>$db3[0]['bm_name'],'d'=>$xd,'u'=>$users,'title'=>'外委单据管理']);
			}


	
    }
    
    
    public function pass($idx,$id,$ck)//通过审核
    { 		
			
			$db = Db::name('bl_dd')->where('id',$idx);
				$idb = $db->update(['she'=>1,'cku'=>$ck]); 
				return $this->redirect("/admin/index?id=".$id);
	
    }
    
    public function wwpass($idx,$id,$ck)//外委审核
    { 		
			
			$db = Db::name('bl_dd')->where('id',$idx);
				$idb = $db->update(['she'=>1,'cku'=>$ck]); 
				return $this->redirect("/admin/index?id=".$id);
	
    }
    
    
    public function passx($idx,$d,$u,$ck)
    { 
			$db = Db::name('bl_dd')->where('id',$idx);
				$idb = $db->update(['she'=>1,'cku'=>$ck,'zf'=>0]); 
				return $this->redirect("/admin/index/list?id=2&d=0&u=".$u);
	
    }
    public function wwpassx($idx,$d,$u,$ck)//外委
    { 
			$db = Db::name('bl_wwdd')->where('id',$idx);
				$idb = $db->update(['she'=>1,'cku'=>$ck,'zf'=>0]); 
				return $this->redirect("/admin/index/wwlist?id=".$idx."&d=0&u=".$u);
	
    } 
    public function passx2($idx,$u,$ck,$id)
    { 
			$db = Db::name('bl_dd')->where('id',$idx);
				$idb = $db->update(['she'=>1,'cku'=>$ck]); 
				return $this->redirect("/admin/index/list?id=".$id."&d=0&u=".$u);
	
    }
    public function wwpassx2($idx,$u,$ck,$id)//外委
    { 
			$db = Db::name('bl_wwdd')->where('id',$idx);
				$idb = $db->update(['she'=>1,'cku'=>$ck]); 
				return $this->redirect("/admin/index/wwlist?id=".$id."&d=0&u=".$u);
	
    }
    public function del($idx,$id)
    { 
			$db = Db::name('bl_dd')->where('id',$idx);
			$idb = $db->update(['zf'=>1,'she'=>0]); 
		    return $this->redirect("/admin/index/list?id=2&d=0&u=".$id);
	
    }
    
    public function wwdel($idx,$id)//外委作废
    { 
			$db = Db::name('bl_wwdd')->where('id',$idx);
			$idb = $db->update(['zf'=>1,'she'=>0]); 
		    return $this->redirect("/admin/index/list?id=2&d=0&u=".$id);
	
    }
    
    public function reprint($idx,$id)
    { 
			$db = Db::name('bl_dd')->where('id',$idx);
			$idb = $db->update(['mprint'=>0]); 
		    return $this->redirect("/admin/index?id=".$id);
	
    }
    public function wwreprint($idx,$id)//外委补打
    { 
			$db = Db::name('bl_wwdd')->where('id',$idx);
			$idb = $db->update(['mprint'=>0]); 
		    return $this->redirect("/admin/index?id=".$id);
	
    }
    
    public function dtle($id)
    { 
		    $all=Db::name('bl_dd')->where('id',$id)->select();
			$idx=$all[0]["id"];
			$ux=$all[0]["userx"];
			$db2 = Db::name('bl_cp')->where('fid',$idx)->select();
			$db3 = Db::name('bl_user')->where('bm_id',$ux)->select();
			return $this->fetch('dtle',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$all[0]['myphone'],'xname'=>$all[0]['myname']]);
	
    }
    public function wwdtle($id)//外委详情
    { 
		    $all=Db::name('bl_wwdd')->where('id',$id)->select();
			$idx=$all[0]["id"];
			$ux=$all[0]["userx"];
			$db2 = Db::name('bl_wwcp')->where('fid',$idx)->select();
			$db3 = Db::name('bl_user')->where('bm_id',$ux)->select();
			return $this->fetch('wwdtle',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$all[0]['myphone'],'xname'=>$all[0]['myname']]);
	
    }
    
    
public function cpmg($id)//产品列表
	{ 
			$all = Db::name('bl_clscp')->order('myorder asc')->select();
			$cls = Db::name('bl_cls')->select();
			return $this->fetch('cplist',['all'=>$all,'id'=>$id,'cls'=>$cls,'title'=>'产品管理']);
	
	}
public function phaomg($id)//牌号列表
	{ 
			$all = Db::name('bl_paihao')->order('myorder asc')->select();
			$cls = Db::name('bl_clsphao')->select();
			return $this->fetch('phlist',['all'=>$all,'id'=>$id,'cls'=>$cls,'title'=>'牌号管理']);
	
	}
	public function addphao($id)//增加牌号
	{ 
			
			$all = Db::name('bl_paihao')->select();
			$cls = Db::name('bl_clsphao')->select();
			$cnum=count($all)+1;
			return $this->fetch('addphao',['cnum'=>$cnum,'id'=>$id,'cls'=>$cls,'title'=>'新增牌号']);
	
	}
	
	public function phaoedit($id,$xid)//编辑公司名
	{ 		
			$cls = Db::name('bl_clsphao')->select();
			$all = Db::name('bl_paihao')->where('id',$xid)->select();
			return $this->fetch('phaoedit',['all'=>$all,'id'=>$id,'cls'=>$cls,'xid'=>$xid,'title'=>'编辑牌号']);
	
	}
	
	public function upphaoadd()//提交产品
	{ 
			$myorder = $this->request->post('myorder');
			$myname = $this->request->post('myname');
			//$myjiag = $this->request->post('myjiag');
			//$myvip = $this->request->post('myvip');
			//$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$kcyj = $this->request->post('kcyj');
			$db2 = Db::name('bl_paihao');
			$id2 = $db2->insert(['kcyj'=>1,'name'=>$myname,'class'=>1,'jine'=>1,'vjine'=>1,'tdjg'=>1,'mypath'=>'','myorder'=>$myorder,'mytext'=>1]); 
			return $this->redirect("/admin/index/phaomg?id=".$myid);
	
	}
	
	public function upphaoedit()//保存编辑
	{ 
	        $myorder= $this->request->post('myorder');
			$myname = $this->request->post('myname');
			$myjiag = $this->request->post('myjiag');
			$myvip = $this->request->post('myvip');
			$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$xid = $this->request->post('xid');
			$kcyj = $this->request->post('kcyj');
			$mypath = $this->request->post('mypath');
			$mytext = $this->request->post('mytext');
			$db = Db::name('bl_paihao')->where('id',$xid);
			$id2 = $db->update(['name'=>$myname,'jine'=>$myjiag,'vjine'=>$myvip,'tdjg'=>$mytd,'class'=>$mytd,'kcyj'=>$kcyj,'myorder'=>$myorder,'mypath'=>$mypath,'mytext'=>$mytext]); 
			//echo $myname;
			return $this->redirect("/admin/index/phaomg?id=".$myid);
	
	}
	
	
	public function addcp($id)//产品列表
	{ 
			
			$all = Db::name('bl_clscp')->select();
			$cls = Db::name('bl_cls')->select();
			$cnum=count($all)+1;
			return $this->fetch('addcp',['cnum'=>$cnum,'id'=>$id,'cls'=>$cls,'title'=>'增加产品']);
	
	}
	public function upcpadd()//提交产品
	{ 
			$myorder = $this->request->post('myorder');
			$myname = $this->request->post('myname');
			$myjiag = $this->request->post('myjiag');
			$myvip = $this->request->post('myvip');
			$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$kcyj = $this->request->post('kcyj');
			$db2 = Db::name('bl_clscp');
			$id2 = $db2->insert(['name'=>$myname,'jine'=>$myjiag,'vjine'=>$myvip,'tdjg'=>$mytd,'class'=>$mytd,'kcyj'=>$kcyj,'myorder'=>$myorder]); 
			return $this->redirect("/admin/index/cpmg?id=".$myid);
	
	}
	public function cpedit($id,$xid)//编辑产品
	{ 		
			$cls = Db::name('bl_cls')->select();
			$all = Db::name('bl_clscp')->where('id',$xid)->select();
			return $this->fetch('cpedit',['all'=>$all,'id'=>$id,'cls'=>$cls,'title'=>'编辑产品']);
	
	}
	
	public function cpnmedit($id,$xid)//编辑公司名
	{ 		
			$cls = Db::name('bl_cls')->select();
			$all = Db::name('bl_cpnm')->where('id',$xid)->select();
			return $this->fetch('cpnmedit',['all'=>$all,'id'=>$id,'cls'=>$cls,'title'=>'编辑公司名']);
	
	}
	
	public function upcpedit()//保存编辑
	{ 
			$myorder = $this->request->post('myorder');
			$myname = $this->request->post('myname');
			$myjiag = $this->request->post('myjiag');
			$myvip = $this->request->post('myvip');
			$mytd =1;
			$myid = $this->request->post('myid');
			$xid = $this->request->post('xid');
			$kcyj = $this->request->post('kcyj');
			$mypath = $this->request->post('mypath');
			$mytext = $this->request->post('mytext');
			$db = Db::name('bl_clscp')->where('id',$xid);
			$id2 = $db->update(['name'=>$myname,'jine'=>$myjiag,'vjine'=>$myvip,'tdjg'=>$mytd,'class'=>$mytd,'kcyj'=>$kcyj,'myorder'=>$myorder,'mypath'=>$mypath,'mytext'=>$mytext]); 
			return $this->redirect("/admin/index/cpmg?id=".$myid);
	
	}
	
	public function upcpnmedit()//公司名编辑保存
	{ 
			$myorder = $this->request->post('myorder');
			$myname = $this->request->post('myname');
			$mynamev = $this->request->post('mynamev');
			$myjiag = $this->request->post('myjiag');
			$myvip = 0;
			$mytd = 1;
			$myid = $this->request->post('myid');
			$xid = $this->request->post('xid');
			$kcyj =0;
			$mypath =0;
			$mytext = $this->request->post('mytext');
			
			$db = Db::name('bl_cpnm')->where('id',$xid);
			
			$db1 = Db::name('bl_dd')->where('myname',$mynamev)->update(['myname'=>$myname]);
			$db2 = Db::name('bl_ddcg')->where('myname',$mynamev)->update(['myname'=>$myname]);
			$db3 = Db::name('bl_wwdd')->where('myname',$mynamev)->update(['myname'=>$myname]);
			
			$id2 = $db->update(['name'=>$myname,'jine'=>$myjiag,'vjine'=>$myvip,'tdjg'=>$mytd,'class'=>$mytd,'kcyj'=>$kcyj,'myorder'=>$myorder,'mypath'=>$mypath,'mytext'=>$mytext]); 
			return $this->redirect("/admin/index/cpnamemg?id=".$myid);
	
	}
	
    public function cpdel($id,$xid)//删除产品
    { 
			$db = Db::name('bl_clscp')->where('id',$xid);
			$idb = $db->delete(); 
		    return $this->redirect("/admin/index/cpmg?id=".$id);
	
    }
    
    public function memberup($idx,$id)
    {       $db1 = Db::name('bl_user')->where('bm_phone',$idx)->select();
            if($db1[0]['bm_vip']==0){
                $db = Db::name('bl_user')->where('bm_phone',$idx);
			    $idb = $db->update(['bm_vip'=>1]); 
            }else{
                $db = Db::name('bl_user')->where('bm_phone',$idx);
			    $idb = $db->update(['bm_vip'=>0]); 
            }
		    return $this->redirect("/admin/index/showmember?id=".$id."&idx=".$idx);
	
    }
    
    public function member($id)
    { 
			return $this->fetch('member',['id'=>$id]);
	
    }
    public function showmember($id,$idx)
    { 
		$db3 = Db::name('bl_user')->where('bm_phone',$idx)->select();
		return $this->fetch('showmember',['id'=>$id,'db3'=>$db3]);
	
    }
    
    public function eupload(){
        $picname = $_FILES['uploadfile']['name']; 
        $picsize = $_FILES['uploadfile']['size']; 
        if ($picname != "") { 
            if ($picsize > 201400000) { //限制上传大小 
                echo '{"status":0,"content":"图片大小不能超过2M"}';
                exit; 
            } 
            $type = strstr($picname, '.'); //限制上传格式 
            if ($type != ".gif" && $type != ".jpg" && $type != "png") {
                echo '{"status":2,"content":"文件格式不对！"}';
                exit; 
            }
            $rand = rand(100, 999); 
            $pics = uniqid() . $type; //命名图片名称 
            //上传路径 
            $pic_path = "/public/static/upload/". $pics; 
            move_uploaded_file($_FILES['uploadfile']['tmp_name'], $pic_path); 
    		$myfile = fopen("1/".date("His")."testfile.txt", "w");
        } 
        $size = round($picsize/1024,2); //转换成kb 
        echo '{"status":1,"name":"'.$picname.'","url":"'.$pic_path.'","size":"'.$size.'","content":"上传成功"}'; 
    }
    

    public function cpnamemg($id)//产品列表
	{ 
			$all = Db::name('bl_cpnm')->select();
			$cls = Db::name('bl_clsname')->select();
			return $this->fetch('cpnamelist',['all'=>$all,'id'=>$id,'cls'=>$cls,'title'=>'客户管理']);
	
	}
	public function addcpname($id)//产品列表
	{ 
			
			$all = Db::name('bl_cpnm')->select();
			$cls = Db::name('bl_clsname')->select();
			$cnum=count($all)+1;
			return $this->fetch('addcpname',['cnum'=>$cnum,'id'=>$id,'cls'=>$cls,'title'=>'新增客户']);
	
	}
	public function upcpnameadd()//提交产品
	{ 
			$myorder = $this->request->post('myorder');
			$myname = $this->request->post('myname');
			//$myjiag = $this->request->post('myjiag');
			//$myvip = $this->request->post('myvip');
			//$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$kcyj = $this->request->post('kcyj');
			$db2 = Db::name('bl_cpnm');
			$id2 = $db2->insert(['kcyj'=>1,'name'=>$myname,'class'=>1,'jine'=>1,'vjine'=>1,'tdjg'=>1,'mypath'=>1,'myorder'=>$myorder,'mytext'=>1]); 
			return $this->redirect("/admin/index/cpnamemg?id=".$myid);
	
	}
	public function cpnameedit($id,$xid)//编辑产品
	{ 		
			$cls = Db::name('bl_cls')->select();
			$all = Db::name('bl_clscp')->where('id',$xid)->select();
			return $this->fetch('cpedit',['all'=>$all,'id'=>$id,'cls'=>$cls,'title'=>'编辑公司名']);
	
	}
	
	public function upcpnameedit()//保存编辑
	{ 
			$myorder = $this->request->post('myorder');
			$myname = $this->request->post('myname');
			$myjiag = $this->request->post('myjiag');
			$myvip = $this->request->post('myvip');
			$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$xid = $this->request->post('xid');
			$kcyj = $this->request->post('kcyj');
			$mypath = $this->request->post('mypath');
			$mytext = $this->request->post('mytext');
			$db = Db::name('bl_clscp')->where('id',$xid);
			$id2 = $db->update(['name'=>$myname,'jine'=>$myjiag,'vjine'=>$myvip,'tdjg'=>$mytd,'class'=>$mytd,'kcyj'=>$kcyj,'myorder'=>$myorder,'mypath'=>$mypath,'mytext'=>$mytext]); 
			return $this->redirect("/admin/index/cpmg?id=".$myid);
	
	}
	
    public function cpnamedel($id,$xid)//删除产品
    { 
			$db = Db::name('bl_cpnm')->where('id',$xid);
			$idb = $db->delete(); 
		    return $this->redirect("/admin/index/cpnamemg?id=".$id);
	
    }
     public function phaodel($id,$xid)//删除牌号
    { 
			$db = Db::name('bl_paihao')->where('id',$xid);
			$idb = $db->delete(); 
		    return $this->redirect("/admin/index/phaomg?id=".$id);
	
    }   
public function phmg($id)//产品列表
	{ 
			$all = Db::name('bl_clsphao')->order('myorder asc')->select();
			$cls = Db::name('bl_paihao')->select();
			return $this->fetch('phlist',['all'=>$all,'id'=>$id,'cls'=>$cls]);
	
	}
	
public function jldw($id)//牌号列表
	{ 
			$all = Db::name('bl_jldw')->order('myorder asc')->select();
			$cls = Db::name('bl_clsjldw')->select();
			return $this->fetch('jldwlist',['all'=>$all,'id'=>$id,'cls'=>$cls,'title'=>'管理计量单位']);
	
	}


	public function addjldw($id)//增加牌号
	{ 
			
			$all = Db::name('bl_jldw')->select();
			$cls = Db::name('bl_clsjldw')->select();
			$cnum=count($all)+1;
			return $this->fetch('addjldw',['cnum'=>$cnum,'id'=>$id,'cls'=>$cls,'title'=>'新增计量单位']);
	
	}
	
	public function upjldwadd()//提交产品
	{ 
			$myorder = $this->request->post('myorder');
			$myname = $this->request->post('myname');
			//$myjiag = $this->request->post('myjiag');
			//$myvip = $this->request->post('myvip');
			//$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$kcyj = $this->request->post('kcyj');
			$db2 = Db::name('bl_jldw');
			$id2 = $db2->insert(['kcyj'=>1,'name'=>$myname,'class'=>1,'jine'=>1,'vjine'=>1,'tdjg'=>1,'mypath'=>'','myorder'=>$myorder,'mytext'=>1]); 
			return $this->redirect("/admin/index/jldw?id=".$myid);
	
	}
	
	

	public function upjldwedit()//保存编辑
	{ 
	        $myorder= $this->request->post('myorder');
			$myname = $this->request->post('myname');
			$myjiag = $this->request->post('myjiag');
			$myvip = $this->request->post('myvip');
			$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$xid = $this->request->post('xid');
			$kcyj = $this->request->post('kcyj');
			$mypath = $this->request->post('mypath');
			$mytext = $this->request->post('mytext');
			$db = Db::name('bl_jldw')->where('id',$xid);
			$id2 = $db->update(['name'=>$myname,'jine'=>$myjiag,'vjine'=>$myvip,'tdjg'=>$mytd,'class'=>$mytd,'kcyj'=>$kcyj,'myorder'=>$myorder,'mypath'=>$mypath,'mytext'=>$mytext]); 
			//echo $myname;
			return $this->redirect("/admin/index/jldw?id=".$myid);
	
	}
	
	///一下为加工方式操作模块
public function jgfs($id)//牌号列表
	{ 
			$all = Db::name('bl_jgfs')->order('myorder asc')->select();
			$cls = Db::name('bl_clsjgfs')->select();
			return $this->fetch('jgfslist',['all'=>$all,'id'=>$id,'cls'=>$cls,'title'=>'管理加工方式']);
	
	}

	public function addjgfs($id)//增加牌号
	{ 
			
			$all = Db::name('bl_jgfs')->select();
			$cls = Db::name('bl_clsjgfs')->select();
			$cnum=count($all)+1;
			return $this->fetch('addjgfs',['cnum'=>$cnum,'id'=>$id,'cls'=>$cls,'title'=>'新增加工方式']);
	
	}
		public function upjgfsadd()//提交产品
	{ 
			$myorder = $this->request->post('myorder');
			$myname = $this->request->post('myname');
			//$myjiag = $this->request->post('myjiag');
			//$myvip = $this->request->post('myvip');
			//$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$kcyj = $this->request->post('kcyj');
			$db2 = Db::name('bl_jgfs');
			$id2 = $db2->insert(['kcyj'=>1,'name'=>$myname,'class'=>1,'jine'=>1,'vjine'=>1,'tdjg'=>1,'mypath'=>'','myorder'=>$myorder,'mytext'=>1]); 
			return $this->redirect("/admin/index/jgfs?id=".$myid);
	
	}
	
	
	public function jgfsedit($id,$xid)//编辑公司名
	{ 		
			$cls = Db::name('bl_clsjgfs')->select();
			$all = Db::name('bl_jgfs')->where('id',$xid)->select();
			return $this->fetch('jgfsedit',['all'=>$all,'id'=>$id,'cls'=>$cls,'xid'=>$xid,'title'=>'编辑加工方式']);
	
	}	
	
	public function jldwedit($id,$xid)//编辑公司名
	{ 		
			$cls = Db::name('bl_clsjldw')->select();
			$all = Db::name('bl_jldw')->where('id',$xid)->select();
			return $this->fetch('jldwedit',['all'=>$all,'id'=>$id,'cls'=>$cls,'xid'=>$xid,'title'=>'编辑计量单位']);
	
	}
	
	public function upjgfsedit()//保存编辑
	{ 
	        $myorder= $this->request->post('myorder');
			$myname = $this->request->post('myname');
			$myjiag = $this->request->post('myjiag');
			$myvip = $this->request->post('myvip');
			$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$xid = $this->request->post('xid');
			$kcyj = $this->request->post('kcyj');
			$mypath = $this->request->post('mypath');
			$mytext = $this->request->post('mytext');
			$db = Db::name('bl_jgfs')->where('id',$xid);
			$id2 = $db->update(['name'=>$myname,'jine'=>$myjiag,'vjine'=>$myvip,'tdjg'=>$mytd,'class'=>$mytd,'kcyj'=>$kcyj,'myorder'=>$myorder,'mypath'=>$mypath,'mytext'=>$mytext]); 
			//echo $myname;
			return $this->redirect("/admin/index/jgfs?id=".$myid);
	
	}
	
    public function deljgfs($id,$xid)//删除产品
    { 
			$db = Db::name('bl_jgfs')->where('id',$xid);
			$idb = $db->delete(); 
		    return $this->redirect("/admin/index/jgfs?id=".$id);
	
    }
	
//库存商品管理模块
	///一下为加工方式操作模块
public function kcsp($id)//牌号列表
	{ 
			$all = Db::name('bl_kcsp')->order('myorder asc')->select();
			$cls = Db::name('bl_clskcsp')->select();
			return $this->fetch('kcsplist',['all'=>$all,'id'=>$id,'cls'=>$cls]);
	
	}

	public function addkcsp($id)//增加牌号
	{ 
			
			$all = Db::name('bl_kcsp')->select();
			$cls = Db::name('bl_clskcsp')->select();
			$cnum=count($all)+1;
			return $this->fetch('addkcsp',['cnum'=>$cnum,'id'=>$id,'cls'=>$cls]);
	
	}
		public function upkcspadd()//提交产品
	{ 
			$myorder = $this->request->post('myorder');
			$myname = $this->request->post('myname');
			//$myjiag = $this->request->post('myjiag');
			//$myvip = $this->request->post('myvip');
			//$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$kcyj = $this->request->post('kcyj');
			$db2 = Db::name('bl_kcsp');
			$id2 = $db2->insert(['kcyj'=>1,'name'=>$myname,'class'=>1,'jine'=>1,'vjine'=>1,'tdjg'=>1,'mypath'=>'','myorder'=>$myorder,'mytext'=>1]); 
			return $this->redirect("/admin/index/kcsp?id=".$myid);
	
	}
	
	
	public function kcspedit($id,$xid)//编辑公司名
	{ 		
			$cls = Db::name('bl_clskcsp')->select();
			$all = Db::name('bl_kcsp')->where('id',$xid)->select();
			return $this->fetch('kcspedit',['all'=>$all,'id'=>$id,'cls'=>$cls,'xid'=>$xid]);
	
	}	
	
	public function upkcspedit()//保存编辑
	{ 
	        $myorder= $this->request->post('myorder');
			$myname = $this->request->post('myname');
			$myjiag = $this->request->post('myjiag');
			$myvip = $this->request->post('myvip');
			$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$xid = $this->request->post('xid');
			$kcyj = $this->request->post('kcyj');
			$kcl = $this->request->post('kcl');
			$mypath = $this->request->post('mypath');
			$mytext = $this->request->post('mytext');
			$db = Db::name('bl_kcsp')->where('id',$xid);
			$id2 = $db->update(['name'=>$myname,'jine'=>$myjiag,'vjine'=>$myvip,'tdjg'=>$mytd,'class'=>$mytd,'kcyj'=>$kcyj,'myorder'=>$myorder,'mypath'=>$mypath,'mytext'=>$mytext,'kcl'=>$kcl]); 
			//echo $myname;
			return $this->redirect("/admin/index/kcsp?id=".$myid);
	
	}	
//库存商品管理模块结束

    public function symbollist($id)//牌号列表
	{ 
			$all = Db::name('bl_symbol')->order('myorder asc')->select();
			$cls = Db::name('bl_clssymbol')->select();
			return $this->fetch('symbollist',['all'=>$all,'id'=>$id,'cls'=>$cls,'title'=>'管理特殊符号']);
	
	}

	public function symbol($id)//增加牌号
	{ 
			
			$all = Db::name('bl_symbol')->select();
			$cls = Db::name('bl_clssymbol')->select();
			$cnum=count($all)+1;
			return $this->fetch('symbol',['cnum'=>$cnum,'id'=>$id,'cls'=>$cls,'title'=>'新增特殊符号']);
	
	}
	
	public function addsymbol()//增加牌号addsymbolsymboledit
	{ 
			
			$myorder = $this->request->post('myorder');
			$myname = $this->request->post('myname');
			//$myjiag = $this->request->post('myjiag');
			//$myvip = $this->request->post('myvip');
			//$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$kcyj = $this->request->post('kcyj');
			$db2 = Db::name('bl_symbol');
			$id2 = $db2->insert(['kcyj'=>1,'name'=>$myname,'class'=>1,'jine'=>1,'vjine'=>1,'tdjg'=>1,'mypath'=>'','myorder'=>$myorder,'mytext'=>1]); 
			return $this->redirect("/admin/index/symbollist?id=".$myid);
	
	}
	
	public function symboledit($id,$xid)//增加牌号addsymbol
	{ 
			
			$cls = Db::name('bl_clssymbol')->select();
			$all = Db::name('bl_symbol')->where('id',$xid)->select();
			//print_r($all);
			return $this->fetch('symboledit',['all'=>$all,'id'=>$id,'cls'=>$cls,'xid'=>$xid,'title'=>'编辑特殊符号']);
	
	}
	
	
	public function upsymboledit()//增加牌号addsymbol
	{ 
			
	        $myorder= $this->request->post('myorder');
			$myname = $this->request->post('myname');
			$myjiag = $this->request->post('myjiag');
			$myvip = $this->request->post('myvip');
			$mytd = $this->request->post('mytd');
			$myid = $this->request->post('myid');
			$xid = $this->request->post('xid');
			$kcyj = $this->request->post('kcyj');
			$kcl = $this->request->post('kcl');
			$mypath = $this->request->post('mypath');
			$mytext = $this->request->post('mytext');
			$db = Db::name('bl_symbol')->where('id',$xid);
			$id2 = $db->update(['name'=>$myname,'jine'=>$myjiag,'vjine'=>$myvip,'tdjg'=>$mytd,'class'=>$mytd,'kcyj'=>$kcyj,'myorder'=>$myorder,'mypath'=>$mypath,'mytext'=>$mytext]); 

			return $this->redirect("/admin/index/symbollist?id=".$myid);
	
	}
    
}



