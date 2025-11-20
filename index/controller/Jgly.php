<?php
namespace app\index\controller;//采购单据
use think\Controller;
use think\Db;
use think\facade\Request;
use phpmailer\phpmailer;

class Jgly extends Controller
{
	
    public function myku($idx,$phone)//出入库显示
   { 
        $year=date('Y');
        $xdate=date('Y-m-d');
	   	$id = $idx;
	   	
	   	$jgfs=Db::name('bl_jgfs')->select();//加工方式
		$scpname=Db::name('bl_cpnm')->select();
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		$allcls=Db::name('bl_cls')->select();
		$xsdate=date("Y-m-d");
		$sdate=date("Ymd");
		$snum=Db::name('bl_ddcg')->where('xdate',$xsdate)->select();
		$ddnum=count($snum)+1;
		$all = Db::name('bl_kcsp')->order('myorder asc')->select();//库存商品
		$xstr="";
		$vstr="";
		for($i=0;$i<count($all);$i++){
		    if($all[$i]['jine']!=0){
			$xstr=$xstr."<option value='".$all[$i]['jine']."-".$all[$i]['name']."'>".$all[$i]['name']."</option>";
		    }
		}
		for($i=0;$i<count($all);$i++){
		    if($all[$i]['vjine']!=0){
			$vstr=$vstr."<option value='".$all[$i]['vjine']."-".$all[$i]['name']."'>".$all[$i]['name']."[会员:".$all[$i]['vjine']."]</option>";
		    }
		}
		//控制打烊时间---------
		/*$h=date('G');
		if($h>20 || $h<9){
		return $this->fetch('dayang');
		}*/
		//控制打烊时间---------
		if(empty($select)){
		    echo "权限不足！";
			//return $this->fetch('myview',['phone'=>$phone,'id'=>0,'sdate'=>$sdate,'ddnum'=>$ddnum,"id"=>$id,'all'=>$all,'xstr'=>$xstr,'allcls'=>$allcls,'xdate'=>$xdate]);//不是会员则进入此页面
		}elseif($select[0]['bm_vip']==0){
			return $this->fetch('jgly',['phone'=>$phone,'id'=>0,'sdate'=>$sdate,'ddnum'=>$ddnum,'select'=>$select,"id"=>$id,'all'=>$all,'xstr'=>$xstr,'allcls'=>$allcls,'xdate'=>$xdate,'scpname'=>$scpname,'year'=>$year,'jgfs'=>$jgfs]);//不是会员则进入此页面
		}
		elseif($select[0]['bm_vip']==1){
		    echo "权限不足！";
			//return $this->fetch('vip',['phone'=>$phone,'id'=>1,'sdate'=>$sdate,'ddnum'=>$ddnum,'select'=>$select,"id"=>$id,'all'=>$all,'xstr'=>$vstr,'allcls'=>$allcls,'xdate'=>$xdate]);//如果是会员则进入此页面
		}
	
	}
	
   public function selshow()//显示选择的产品
    { 	
		$all = Db::name('bl_kcsp')->select();
		$symbol = Db::name('bl_symbol')->select();
		$allphao = Db::name('bl_paihao')->select();
		$alljldw = Db::name('bl_jldw')->select();
		$vx=0;
		$vpud=[];
		$id = $this->request->post("id");//id
		$phone = $this->request->post("phone");//手机
		$xdate = $this->request->post("xdate");//日期
		$name = $this->request->post("bm_name");//姓名
		$add = $this->request->post("bm_add");//地址
		$jgfs = $this->request->post("jgfs");//加工方式
		for($i=0;$i<count($all);$i++){
		$pnum=0;
		$jine=0;
		$pnum = $this->request->post($i+1);//重量
		$jine = $this->request->post(($i+1).'x');//金额
		$cid = $this->request->post(($i+1).'xv');//编号
		$v=$i+1;
		$mytext = $this->request->post('mytext'.$v);//备注
		if($pnum!=""){
			$allc= Db::name('bl_kcsp')->where('name',$pnum)->select();
			$vpud[$vx]['name']=$pnum;
			$vpud[$vx]['num']=$pnum;
			$vpud[$vx]['jine']=$jine;
			$vpud[$vx]['id']=$allc[0]['id'];
			$vpud[$vx]['mytext']=$mytext;
			//echo $cid;
			//$vpud[$vx]['mypath']=$allc[0]['mypath'];
			$vx=$vx+1;
			//print_r($allc);
		}
		}
		//print_r($vpud);
		if(count($vpud)==0){
		    echo "<script>alert('请至少选择一个项目！');window.history.go(-1)</script>";
		}
		$alljine=0;
		for($n=0;$n<count($vpud);$n++){
			//$alljine=$alljine+$vpud[$n]['jine']*$vpud[$n]['num'];
			$alljine=0;
		}
		
		return $this->fetch('selshow',['vpud'=>$vpud,'phone'=>$phone,"id"=>$id,'alljine'=>$alljine,'all'=>count($vpud),'name'=>$name,'add'=>$add,'xdate'=>$xdate,'allphao'=>$allphao,'alljldw'=>$alljldw,'jgfs'=>$jgfs,'symbol'=>$symbol]);//进入确认界面
	}
		
		
public function editshow($id)//显示选择的产品
    { 	
        
		$all = Db::name('bl_cpcg')->where('fid',$id)->select();
		$alljldw = Db::name('bl_jldw')->select();
		$vx=0;
		$vpud=[];
        for($i=0;$i<count($all);$i++){
			$allc= Db::name('bl_cpcg')->where('fid',$id)->select();
			$vpud[$vx]['name']=$allc[$i]['cpname'];
			$vpud[$vx]['num']=$allc[$i]['cpnum'];
			$vpud[$vx]['jine']=$allc[$i]['csl'];
			$vpud[$vx]['id']=$allc[$i]['id'];
			$vpud[$vx]['jldw']=$allc[$i]['jine'];
			$vpud[$vx]['mytext']=$allc[$i]['mytext'];
			$vpud[$vx]['danjias']=$allc[$i]['danjias'];
			$vpud[$vx]['jineall']=$allc[$i]['jineall'];
			//echo $cid;
			$vpud[$vx]['mypath']=$allc[$i]['djia'];
			$vpud[$vx]['hjzl']=$allc[$i]['hjzl'];
			$vx=$vx+1;
        }
		if(count($vpud)==0){
		    echo "<script>alert('请至少选择一个项目！');window.history.go(-1)</script>";
		}
		$alljine=0;
		for($n=0;$n<count($vpud);$n++){
			//$alljine=$alljine+$vpud[$n]['jine']*$vpud[$n]['num'];
			$alljine=0;
		}
		//print_r($vpud);
		$allcx= Db::name('bl_ddcg')->where('id',$id)->select();
		//echo $id;
		//print_r($allcx);
		return $this->fetch('editshow',['vpud'=>$vpud,'mytext'=>$allcx[0]['xtext'],"id"=>$id,'alljine'=>$alljine,'all'=>count($vpud),'alljldw'=>$alljldw]);//进入确认界面
	}	
		
		
		
   public function upadd()
    { 
        $xsdate=$this->request->post('xdate');
        $mut=date("H-i");
        $snumx=Db::name('bl_ddcg')->where('xdate',$xsdate)->where('mut',$mut)->select();
		$bm_name = $this->request->post('name');
		$bm_add =$this->request->post('add');
		$bm_call =$this->request->post('jine');
		$bm_phone = $this->request->post('phone');
		$all_cp =$this->request->post('all');
		$sh_id = $this->request->post('id');
		$bm_text = $this->request->post('bm_text');
		$jgfs = $this->request->post('jgfs');
		if($sh_id=="jgly"){
		    $cku=3;
		    $ckuname="加工领用";
		}else{
		    $cku=4;
		    $ckuname="外委领用";
		}
		//防止重复下单--------
                $m_date=$xsdate;
        		$sdate=$xsdate;
        		$setting=Db::name('bl_setting')->where('id',1)->select();
        		$snum=Db::name('bl_ddcg')->where('xdate',$xsdate)->select();
        		$ddnum=count($snum)+1;
        		$bm_danhao=$setting[0]['excg'].$sdate."-".$ddnum;
        	    //if(count($snumx)==0){
        		$select=Db::name('bl_user')->where('bm_phone',$bm_phone)->select();//查询会员
        		if(empty($select)){//添加订单
        			$db1 = Db::name('bl_user'); 
        			$id1 = $db1->insert(['bm_name'=>$bm_name,'bm_phone'=>$bm_phone,'bm_add'=>$bm_add,'bm_vip'=>0,'bm_time'=>$m_date]);
        			$userId = Db::name('bl_user')->getLastInsID();
        		}else{
        			$userId=$select[0]['bm_id'];
        			$username=$select[0]['bm_name'];
        		}
        
        		$db2 = Db::name('bl_ddcg');
        		$id2 = $db2->insert(['name'=>$username,'userx'=>$userId,'dhao'=>$bm_danhao,'madd'=>$jgfs,'jine'=>$bm_call,'cku'=>$cku,'she'=>1,'mprint'=>0,'xdate'=>$m_date,'xtext'=>$bm_text,'m_share'=>$sh_id,'mut'=>$mut,'zf'=>0,'myname'=>$bm_name,'myphone'=>$bm_phone]);
        		
        		$fId = Db::name('bl_ddcg')->getLastInsID();//添加产品
        		$db3 = Db::name('bl_cpcg');
        		for($i=1;$i<$all_cp+1;$i++){
        			if(!empty( $this->request->post('name'.$i))){
        				$cpname = $this->request->post('name'.$i);
        				$cpnum = $this->request->post('num'.$i);
        				$csl = $this->request->post('csl'.$i);
        				$jineall = $this->request->post('jineall'.$i);
        				$phao = $this->request->post('phao'.$i);
        				$danjia = $this->request->post('danjia'.$i);
        				$mytext = $this->request->post('mytext'.$i);
        				$cpid = $this->request->post('id'.$i);
        				$jinec = $this->request->post('jldw'.$i);
        				if(!empty($this->request->post('hjzl'.$i))){
                            $hjzl = 1;
                        } else {
                            $hjzl = 0;
                        }
        				
        				if($sh_id=="jgly"){
                        	    $cp = Db::name('bl_kcsp')->where('id',$cpid)->setDec('kcl',$cpnum);//减少库存
                        	}else{
                        	    $cp = Db::name('bl_kcsp')->where('id',$cpid)->setInc('kcl',$cpnum);//增加库存
                        	}
        				$id3 = $db3->insert(['fid'=>$fId,'cpname'=>$cpname,'csl'=>$csl,'cpnum'=>$cpnum,'jine'=>$jinec,'djia'=>$phao,'mytext'=>$mytext,'cpid'=>$cpid,'danjias'=>$danjia,'jineall'=>$jineall,'hjzl'=>$hjzl]); 
        				
        			}
        
        		}
        	   $strall="";
               $strallx=Db::name('bl_cpcg')->where('fid',$fId)->select();//查询会员
               $strheji=0;
               for($ix=0;$ix<count($strallx);$ix++){
                   $strall=$strall."<tr><td>".$strallx[$ix]['cpname']."</td><td>".$strallx[$ix]['cpnum']."</td><td>".$strallx[$ix]['djia']."<br/></td>";
                   $strheji=$strheji+$strallx[$ix]['cpnum']; 
                   
               }
               $M1=$setting[0]['semail'];
               $M2=$setting[0]['semail2'];
               $M3=$setting[0]['semail3'];
               $mail = new PHPMailer();
        
               $mail->isSMTP();// 使用SMTP服务
               $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
               $mail->Host = "smtp.qq.com";// 发送方的SMTP服务器地址
               $mail->SMTPAuth = true;// 是否使用身份验证
               $mail->Username = "873355565@qq.com";// 发送方的163邮箱用户名
               $mail->Password = "wyehucxpisuibbge";// 客户端授权密码
               $mail->SMTPSecure = "ssl";// 使用ssl协议方式
               $mail->Port = 465;// 163邮箱的ssl协议方式端口号是465/994
        
               $mail->setFrom("873355565@qq.com","新订单");// 设置发件人信息
               $mail->addAddress($M1, 'zzy');  // 收件人
               $mail->addAddress($M2, 'zzy');  // 收件人
               $mail->addAddress($M3, 'zzy');  // 收件人
               $mail->addReplyTo('873355565@qq.com', 'info'); //回复的时候回复给哪个邮箱 建议和发件人一致 
               //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)
               //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)
               //$mail->addAttachment("bug0.jpg");// 添加附件
                $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容 
                $mail->Subject = '您有新的【' . $ckuname.'】用单据！'; 
                $mail->Body = "<table border='1' style='font-size:20px;border: solid 1px #666'><tr width='40%'><td style='text-align:right;'>单号：</td><td>".$bm_danhao."</td></tr><tr><td style='text-align:right;'>操作员:</td><td style='color:#ef4343;'>".$username."</td></tr><tr><td style='text-align:right;'>来源:</td><td>".$bm_name."</td></tr><tr><td style='text-align:right;'>类别:</td><td>".$ckuname."</td></tr><tr><td style='text-align:right;'>备注:</td><td>".$bm_text."</td></tr></table><table border='1' style='font-size:20px;border: solid 1px #666'><tr><td>产品</td><td>重量</td><td>
               批号</td></tr>".$strall."<tr><td style='text-align:right;'>合计：</td><td>".$strheji."</td><td></td></tr></table>"; 
                $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容'; 
        
               $mail->send();
        
                //}
                //return $this->redirect('/index/wwdj/mylist.html?id=rk');
                if($sh_id=="jgly"){
                    $this->success('提交成功，正在转向加工领用列表！',"/index/cgdj/mylist.html?id=jgly&y=0&m=0");
                }else{
                    $this->success('提交成功，正在转向外委领用列表！',"/index/cgdj/mylist.html?id=wwly&y=0&m=0");
                }
         
		      
		      
		  }
		  //防止重复下单-------------
    //}
	
	


   public function upaddedit()//编辑订单
    { 
        $id=$this->request->post('id');
        $xsdate=$this->request->post('xdate');
        $mut=date("H-i");
        $snumx=Db::name('bl_ddcg')->where('xdate',$xsdate)->where('mut',$mut)->select();
		$bm_name = $this->request->post('name');
		$bm_add =$this->request->post('add');
		$bm_call =$this->request->post('jine');
		$bm_phone = $this->request->post('phone');
		$all_cp =$this->request->post('all');
		$sh_id = $this->request->post('id');
		$bm_text = $this->request->post('bm_text');
		if($sh_id=="jgly"){
		    $cku=3;
		    $ckuname="加工领用";
		}else{
		    $cku=4;
		    $ckuname="外委领用";
		}
		//echo $id;
		$ida=Db::name('bl_ddcg')->where('id',$id)->update(['xtext'=>$bm_text]); 
		$db3=Db::name('bl_cpcg')->where('fid',$id)->select();//查询产品
		$mynum=count($db3);
		for($i=1;$i<$mynum+1;$i++){
			if(!empty( $this->request->post('name'.$i))){
				$cpname = $this->request->post('name'.$i);
				$cpnum = $this->request->post('num'.$i);
				$danjias = $this->request->post('danjias'.$i);
				$jineall = $this->request->post('jineall'.$i);
				$csl = $this->request->post('csl'.$i);
				$danjia = $this->request->post('danjia'.$i);
				$mytext = $this->request->post('mytext'.$i);
				$cpid = $this->request->post('id'.$i);
				$jinec = $this->request->post('jldw'.$i);
				if(!empty($this->request->post('hjzl'.$i))){
                            $hjzl = 1;
                        } else {
                            $hjzl = 0;
                }
				//echo $jinec;
				$id3=Db::name('bl_cpcg')->where('id',$db3[$i-1]['id'])->update(['cpname'=>$cpname,'csl'=>$csl,'cpnum'=>$cpnum,'danjias'=>$danjias,'jineall'=>$jineall,'jine'=>$jinec,'djia'=>$danjia,'mytext'=>$mytext,'cpid'=>0,'hjzl'=>$hjzl]); 
			}

		}

                return $this->redirect("/index/cgdj/dtle?id=".$id);
         
		      
		      
		  }
	
	
   public function pay($phone,$call,$dd,$id)//输入手机号码
    { 
		//$all=Db::name('bl_baoming')->select();
        //$this->view->assign('all',$all);
		return $this->fetch('pay',['phone'=>$phone,'id'=>$id,'call'=>$call,'dd'=>$dd]);//如果是会员则进入此页面
    }
    
   public function mylist($id)
    { 
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone]);
			}
	
    }

   public function mysku($id)
    { 
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_kcsp')->order([
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_kcsp')->select();
			return $this->fetch('mysku',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone]);
			}
	
    }


   public function cklist($id)
    { 
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where('cku',2)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone]);
			}
	
    }
    
   public function rklist($id)
    { 
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where('cku',1)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone]);
			}
	
    }
    
    
   public function del($idx,$id)
    { 
			$db = Db::name('bl_cgdd')->where('id',$idx);
			$idb = $db->update(['zf'=>1]); 
		    return $this->redirect("/index/index?id=".$id);
	
    }
   public function rprint($idx,$id)
    { 
			$db = Db::name('bl_wwdd')->where('id',$idx);
			$idb = $db->update(['mprint'=>0]); 
			echo "<script>alert('补打成功！');</script>";
		    return $this->redirect("/index/cgdj/mylist.html?id=".$id);
	
    }
    public function dtle($id)
    { 
		    $all=Db::name('bl_ddcg')->where('id',$id)->select();
			$idx=$all[0]["id"];
			$ux=$all[0]["userx"];
			$db2 = Db::name('bl_cpcg')->where('fid',$idx)->select();
			$db3 = Db::name('bl_user')->where('bm_id',$ux)->select();
			return $this->fetch('dtle',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$db3[0]['bm_phone'],'xname'=>$db3[0]['bm_name']]);
	
    }
	
    public function res()
    { 
		$id = $this->request->post('id');
		return $this->redirect("/index/index?id=".$id);
    }
    
    //退单模块
 //退单-------------------------------------------------------------------------------
      public function tdphone($id)//输入手机号码
    { 
		//$all=Db::name('bl_baoming')->select();
        //$this->view->assign('all',$all);
		return $this->fetch('tdphone',['id'=>$id,'xphone'=>cookie('xphone')]);
    }
   public function tdmylist($id)
    { 
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_td')->where('userx',$ux)->where('zf',0)->order('id desc')->select();
			$db2 = Db::name('bl_tdcp')->select();
			return $this->fetch('tdmylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone]);
	
    }
    

}
