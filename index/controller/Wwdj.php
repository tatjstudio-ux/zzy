<?php
namespace app\index\controller;//外委
use think\Controller;
use think\Db;
use think\facade\Request;
use phpmailer\phpmailer;

class Wwdj extends Controller
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
		$snum=Db::name('bl_wwdd')->where('xdate',$xsdate)->select();
		$ddnum=count($snum)+1;
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
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
        $categories = $this->getAllCategories(0, 0);
        $setting=Db::name('bl_setting')->where('id',1)->select();
		if(empty($select)){
		    echo "权限不足！";
		}elseif($select[0]['bm_vip']==0){
			return $this->fetch('wwdj',['phone'=>$phone,'id'=>0,'sdate'=>$sdate,'ddnum'=>$ddnum,'select'=>$select,"id"=>$id,'all'=>$all,'xstr'=>$xstr,'allcls'=>$allcls,'xdate'=>$xdate,'scpname'=>$scpname,'year'=>$year,'jgfs'=>$jgfs,'categories'=>$categories,'setting'=>$setting]);//不是会员则进入此页面
		}
		elseif($select[0]['bm_vip']==1){
		    echo "权限不足！";
		}
	
	}
	
   public function selshow()//显示选择的产品
    { 	
		$all = Db::name('bl_clscp')->select();
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
			$allc= Db::name('bl_clscp')->where('id',$cid)->select();
			$vpud[$vx]['name']=$pnum;
			$vpud[$vx]['num']=$pnum;
			$vpud[$vx]['jine']=$jine;
			$vpud[$vx]['id']=$cid;
			$vpud[$vx]['mytext']=$mytext;
			//echo $cid;
			//$vpud[$vx]['mypath']=$allc[0]['mypath'];
			$vx=$vx+1;
		}
		}
		if(count($vpud)==0){
		    echo "<script>alert('请至少选择一个项目！');window.history.go(-1)</script>";
		}
		$alljine=0;
		for($n=0;$n<count($vpud);$n++){
			//$alljine=$alljine+$vpud[$n]['jine']*$vpud[$n]['num'];
			$alljine=0;
		}
		$setting=Db::name('bl_setting')->where('id',1)->select();
		$sdate=date("YmdHi");
		if($setting[0]['autoph']==1){
		    $phaox=$setting[0]['wwexdh'].$sdate;
		}else{
		    $phaox="";
		}
        $oldph=$setting[0]['oldph'];
        
        // 定义表名数组
        $tables = ['bl_cp', 'bl_wwcp', 'bl_cpcg'];
        $djiaValues = [];
        
        foreach ($tables as $table) {
            // 查询每个表的djia字段
            $data = Db::name($table)->field('djia')->select();
            
            // 处理每个djia值
            foreach ($data as $item) {
                $djia = $item['djia'];
                // 分割字符串并提取后半部分
                $parts = explode('&', $djia);
                if (count($parts) >= 2) {
                    $djiaValues[] = trim($parts[1]);
                }
            }
        }
        
        // 去重处理
        $uniqueValues = array_unique($djiaValues);
        // 重置数组索引
        $result = array_values($uniqueValues);
        
        // 定义表名数组规格
        $tablesgg = ['bl_cp', 'bl_wwcp', 'bl_cpcg'];
        $djiaValuesgg = [];
        
        foreach ($tablesgg as $tablegg) {
            // 查询每个表的djia字段
            $datagg = Db::name($tablegg)->field('csl')->select();
            
            // 处理每个djia值
            foreach ($datagg as $itemgg) {
                $csl = $itemgg['csl'];
                $csls[] =$csl;
            }
        }
        
        // 去重处理
        $uniqueValues = array_unique($csls);
        // 重置数组索引
        $resultgg = array_values($uniqueValues);
        
		//print_r($resultgg);
        
		return $this->fetch('selshow',['vpud'=>$vpud,'phone'=>$phone,"id"=>$id,'alljine'=>$alljine,'all'=>count($vpud),'name'=>$name,'add'=>$add,'xdate'=>$xdate,'allphao'=>$allphao,'alljldw'=>$alljldw,'jgfs'=>$jgfs,'symbol'=>$symbol,'phaox'=>$phaox,'oldph'=>$oldph,'result'=>$result,'resultgg'=>$resultgg]);//进入确认界面
	}
		
		
public function editshow($id)//显示选择的产品
    { 	
        $all = Db::name('bl_wwcp')->where('fid',$id)->select();
		$alljldw = Db::name('bl_jldw')->select();
		$scpname=Db::name('bl_cpnm')->select();//公司名称列表
		$jgfs=Db::name('bl_jgfs')->select();//加工方式
		$allphao = Db::name('bl_paihao')->select();//加载牌号
		$vx=0;
		$vpud=[];
        for($i=0;$i<count($all);$i++){
			$allc= Db::name('bl_wwcp')->where('fid',$id)->select();
			$vpud[$vx]['name']=$allc[$i]['cpname'];
			$vpud[$vx]['num']=$allc[$i]['cpnum'];
			$vpud[$vx]['oldph']=$allc[$i]['oldph'];
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
		$allcx= Db::name('bl_wwdd')->where('id',$id)->select();
		$xdate=$allcx[0]['xdate']; 
		$dateArray = explode('-', $xdate);
		//echo $id;
		//print_r($allcx);
		$setting=Db::name('bl_setting')->where('id',1)->select();
		return $this->fetch('editshow',['vpud'=>$vpud,'mytext'=>$allcx[0]['xtext'],"id"=>$id,'alljine'=>$alljine,'all'=>count($vpud),'alljldw'=>$alljldw,'allcx'=>$allcx,'dateArray'=>$dateArray,'scpname'=>$scpname,'jgfs'=>$jgfs,'allphao'=>$allphao,'setting'=>$setting]);//进入确认界面
	}	
		
		
		
   public function upadd()
    { 
        $xsdate=$this->request->post('xdate');
        $mut=date("H-i");
        $snumx=Db::name('bl_wwdd')->where('xdate',$xsdate)->where('mut',$mut)->select();
		$bm_name = $this->request->post('name');
		$bm_add =$this->request->post('add');
		$bm_call =$this->request->post('jine');
		$bm_phone = $this->request->post('phone');
		$all_cp =$this->request->post('all');
		$sh_id = $this->request->post('id');
		$bm_text = $this->request->post('bm_text');
		$jgfs = $this->request->post('jgfs');
		if($sh_id=="ck"){
		    $cku=2;
		    $ckuname="外委出库";
		}else{
		    $cku=1;
		    $ckuname="外委入库";
		}
		//防止重复下单--------
		  //$sfxd=Db::name('bl_wwdd')->where('xdate',$xsdate)->where('myphone',$bm_phone)->where('zf',0)->where('she',0)->select();//判断今日是否已经下单
		 // if(count($sfxd)>0){
		  //echo "<script>alert('您今日已经下单，即将转到订单列表...');window.location.href='/index/index/mylist?id=".$sh_id."'</script>";
		  //return $this->redirect('/index/index/mylist?id='.$sh_id);
		  //}else{
		      
                $m_date=$xsdate;
        		$sdate=$xsdate;
        		$setting=Db::name('bl_setting')->where('id',1)->select();
        		$snum=Db::name('bl_wwdd')->where('xdate',$xsdate)->select();
        		$ddnum=count($snum)+1;
        		$bm_danhao=$setting[0]['wwexdh'].$sdate."-".$ddnum;
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
        
        		$db2 = Db::name('bl_wwdd');
        		$id2 = $db2->insert(['name'=>$username,'userx'=>$userId,'dhao'=>$bm_danhao,'madd'=>$jgfs,'jine'=>$bm_call,'cku'=>$cku,'she'=>1,'mprint'=>0,'xdate'=>$m_date,'xtext'=>$bm_text,'m_share'=>$sh_id,'mut'=>$mut,'zf'=>0,'myname'=>$bm_name,'myphone'=>$bm_phone]);
        		
        		$fId = Db::name('bl_wwdd')->getLastInsID();//添加产品
        		$db3 = Db::name('bl_wwcp');
        		for($i=1;$i<$all_cp+1;$i++){
        			if(!empty( $this->request->post('name'.$i))){
        				$cpname = $this->request->post('name'.$i);
        				$cpnum = $this->request->post('num'.$i);
        				$oldph = $this->request->post('oldph'.$i);
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
        				
        				$cp = Db::name('bl_clscp')->where('id',$cpid)->setDec('kcyj',$cpnum);//较少库存
        				$id3 = $db3->insert(['fid'=>$fId,'cpname'=>$cpname,'csl'=>$csl,'cpnum'=>$cpnum,'jine'=>$jinec,'djia'=>$phao,'mytext'=>$mytext,'cpid'=>$cpid,'danjias'=>$danjia,'jineall'=>$jineall,'hjzl'=>$hjzl,'sfck'=>1,'rkid'=>0,'oldph'=>$oldph]); 
        			}
        
        		}
        	   $strall="";
               $strallx=Db::name('bl_wwcp')->where('fid',$fId)->select();//查询会员
               $strheji=0;
               for($ix=0;$ix<count($strallx);$ix++){
                   $strall=$strall."<tr><td>".$strallx[$ix]['cpname']."[".$oldph."]</td><td>".$strallx[$ix]['cpnum']."</td><td>".$strallx[$ix]['csl']."</td><td>".$strallx[$ix]['djia']."<br/></td>";
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
                $mail->Subject = '新单据外委！【' . $ckuname.'】'; 
                $mail->Body = "<table border='1' style='font-size:20px;border: solid 1px #666'><tr width='40%'><td style='text-align:right;'>单号：</td><td>".$bm_danhao."</td></tr><tr><td style='text-align:right;'>操作员:</td><td style='color:#3b82f6;'>".$username."</td></tr><tr><td style='text-align:right;'>来源:</td><td>".$bm_name."</td></tr><tr><td style='text-align:right;'>类别:</td><td>".$ckuname."</td></tr><tr><td style='text-align:right;'>备注:</td><td>".$bm_text."</td></tr></table><table border='1' style='font-size:20px;border: solid 1px #666'><tr><td>产品</td><td>重量</td><td>规格</td><td>
               批号</td></tr>".$strall."<tr><td style='text-align:right;'>合计：</td><td>".$strheji."</td><td></td><td></td></tr></table>"; 
                $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容'; 
        
               $mail->send();
        
                //}
                //return $this->redirect('/index/wwdj/mylist.html?id=rk');
                $this->success('提交成功，正在转向外委列表！',"/index/wwdj/mylist.html?id=rk&y=0&m=0");
         
		      
		      
		  }
		  //防止重复下单-------------
    //}
	
	


   public function upaddedit()//编辑订单
    { 
        $id=$this->request->post('id');
        $xsdate=$this->request->post('xdate');
        $jgfs=$this->request->post('jgfs');
        $dhaox=$this->request->post('dhaox');
        $bm_name=$this->request->post('bm_name');
        $vdate = Db::name('bl_wwdd')->where('id',$id)->select();
		$setting=Db::name('bl_setting')->where('id',1)->select();
		//$snum=Db::name('bl_dd')->where('xdate',$xsdate)->select();
		
        $data = Db::name('bl_wwdd')->where('xdate',$xsdate)->order("dhao","desc")->limit(1)->select();

        if(count($data)==0){
            $snum=Db::name('bl_wwdd')->where('xdate',$xsdate)->select();
            $ddnum=count($snum)+1;
        }else{
        $dhao = $data[0]['dhao'];  // 内容为"ZZY2025-04-02-18"
        // 按"-"分割字符串并取末尾元素
        $parts = explode('-', $dhao);
        $snum = end($parts);  // 输出"18"
        $ddnum=$snum+1;
        }
		$bm_danhao=$setting[0]['exdh'].$xsdate."-".$ddnum;
        $mut=date("H-i");
        //echo $xsdate."---";
        //echo $vdate[0]['xdate']."---";
        //echo $dhaox."---";
        //echo $vdate[0]['dhao'];
        if($vdate[0]['dhao']==$dhaox && $xsdate==$vdate[0]['xdate']){
            //echo "ok";
        $strall=Db::name('bl_wwdd')->where('id',$id)->update(['xdate'=>$xsdate,'madd'=>$jgfs,'myname'=>$bm_name]); 
        }else{
            //echo "no";
        $strall=Db::name('bl_wwdd')->where('id',$id)->update(['xdate'=>$xsdate,'madd'=>$jgfs,'myname'=>$bm_name,'dhao'=>$bm_danhao]); 
        }
		$bm_name = $this->request->post('name');
		$bm_add =$this->request->post('add');
		$bm_call =$this->request->post('jine');
		$bm_phone = $this->request->post('phone');
		$all_cp =$this->request->post('all');
		$sh_id = $this->request->post('id');
		$bm_text = $this->request->post('bm_text');
		if($sh_id=="ck"){
		    $cku=2;
		    $ckuname="出库";
		}else{
		    $cku=1;
		    $ckuname="入库";
		}
		//echo $id;
		
		
		$ida=Db::name('bl_wwdd')->where('id',$id)->update(['xtext'=>$bm_text]); 
		$db3=Db::name('bl_wwcp')->where('fid',$id)->select();//查询产品
		$mynum=count($db3);
		for($i=1;$i<$mynum+1;$i++){
			if(!empty( $this->request->post('name'.$i))){
				$cpname = $this->request->post('name'.$i);
				$cpnum = $this->request->post('num'.$i);
				$oldph = $this->request->post('oldph'.$i);
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
				$id3=Db::name('bl_wwcp')->where('id',$db3[$i-1]['id'])->update(['cpname'=>$cpname,'csl'=>$csl,'cpnum'=>$cpnum,'danjias'=>$danjias,'jineall'=>$jineall,'jine'=>$jinec,'djia'=>$danjia,'mytext'=>$mytext,'cpid'=>0,'hjzl'=>$hjzl,'oldph'=>$oldph]); 
			}

		}

                return $this->redirect("/index/wwdj/dtle?id=".$id);
         
		      
		      
		  }
	
	
   public function pay($phone,$call,$dd,$id)//输入手机号码
    { 
		//$all=Db::name('bl_baoming')->select();
        //$this->view->assign('all',$all);
		return $this->fetch('pay',['phone'=>$phone,'id'=>$id,'call'=>$call,'dd'=>$dd]);//如果是会员则进入此页面
    }
    
   public function mylist($id,$y,$m)
    {       if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            //echo $m;
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_wwdd')->where("xdate like '{$yd}%'")->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_wwcp')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y]);
			}
	
    }

public function cklist($id,$y,$m)
    {       if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            //echo $m;
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_wwdd')->where("xdate like '{$yd}%'")->where('cku',2)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_wwcp')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y]);
			}
	
    }
  


public function rklist($id,$y,$m)
    {       if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            //echo $m;
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_wwdd')->where("xdate like '{$yd}%'")->where('cku',1)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_wwcp')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y]);
			}
	
    }
    
   public function del($idx,$id,$y,$m)
    { 
			$db = Db::name('bl_wwdd')->where('id',$idx);
			$idb = $db->update(['zf'=>1]); 
		    return $this->redirect("/index/wwdj/mylist.html?id=".$id."&y=".$y."&m=".$m);
	
    }
    
   public function rprint($idx,$id,$y,$m)
    { 
			$db = Db::name('bl_wwdd')->where('id',$idx);
			$idb = $db->update(['mprint'=>0]); 
			echo "<script>alert('补打成功！');</script>";
		    return $this->redirect("/index/wwdj/mylist.html?id=".$id."&y=".$y."&m=".$m);
	
    }
    public function dtle($id)
    { 
            $y = $this->request->get('y');
            $m = $this->request->get('m');
		    $all=Db::name('bl_wwdd')->where('id',$id)->select();
			$idx=$all[0]["id"];
			$ux=$all[0]["userx"];
			$db2 = Db::name('bl_wwcp')->where('fid',$idx)->select();
			$db3 = Db::name('bl_user')->where('bm_id',$ux)->select();
			$setting=Db::name('bl_setting')->where('id',1)->select();
			return $this->fetch('dtle',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$db3[0]['bm_phone'],'xname'=>$db3[0]['bm_name'],'y'=>$y,'m'=>$m,'setting'=>$setting]);
	
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
    
   public function tdmy()
    { 
	   	$phone = $this->request->post('phone');
		$id = $this->request->post('idx');
		cookie('xphone',$phone);
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		$xsdate=date("Y-m-d");
		$sdate=date("Ymd");
		$snum=Db::name('bl_td')->where('xdate',$xsdate)->select();
		$ddnum=count($snum)+1;
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
		$xstr="";
		$vstr="";
		for($i=0;$i<count($all);$i++){
			$xstr=$xstr."<option value='".$all[$i]['jine']."-".$all[$i]['name']."'>".$all[$i]['name']."</option>";
		}
		for($i=0;$i<count($all);$i++){
		    if($all[$i]['tdjg']!=0){
			$vstr=$vstr."<option value='".$all[$i]['tdjg']."-".$all[$i]['name']."'>".$all[$i]['name']."[退单:".$all[$i]['tdjg']."]</option>";
		    }
		}
		return $this->fetch('tdmy',['phone'=>$phone,'id'=>0,'sdate'=>$sdate,'ddnum'=>$ddnum,'select'=>$select,"id"=>$id,'all'=>$all,'xstr'=>$xstr,'vstr'=>$vstr]);//不是会员则进入此页面

	
    }
   public function uptd()
    { 
        $xsdate=date("Y-m-d");
        $mut=date("H-i");
        $snumx=Db::name('bl_td')->where('xdate',$xsdate)->where('mut',$mut)->select();
		$bm_name = $this->request->post('bm_name');
		//$bm_danhao = $this->request->post('bm_danhao');
		$bm_add = $this->request->post('bm_add');
		$bm_call = $this->request->post('bm_call');
		$bm_phone = $this->request->post('bm_phone');
		$all_cp = $this->request->post('all_cp');
		$sh_id = $this->request->post('sh_id');
		$bm_text = $this->request->post('bm_text');
	    $m_date=date('Y-m-d');
		$sdate=date("Ymd");
		$snum=Db::name('bl_td')->where('xdate',$xsdate)->select();
		$ddnum=count($snum)+1;
		$bm_danhao="TD".$sdate."-".$ddnum;
	    //if(count($snumx)==0){
		$select=Db::name('bl_user')->where('bm_phone',$bm_phone)->select();//查询会员
		if(empty($select)){
			$db1 = Db::name('bl_user'); 
			$id1 = $db1->insert(['bm_name'=>$bm_name,'bm_phone'=>$bm_phone,'bm_add'=>$bm_add,'bm_vip'=>0,'bm_time'=>$m_date]);
			$userId = Db::name('bl_user')->getLastInsID();
		}else{
			$userId=$select[0]['bm_id'];
		}

		$db2 = Db::name('bl_td');
		$id2 = $db2->insert(['userx'=>$userId,'dhao'=>$bm_danhao,'madd'=>$bm_add,'jine'=>$bm_call,'cku'=>1,'she'=>0,'mprint'=>0,'xdate'=>$m_date,'xtext'=>$bm_text,'m_share'=>$sh_id,'mut'=>$mut,'zf'=>0,'myname'=>$bm_name,'myphone'=>$bm_phone]); 
		$fId = Db::name('bl_td')->getLastInsID();
		$db3 = Db::name('bl_tdcp');
		for($i=0;$i<$all_cp;$i++){
			if(!empty( $this->request->post('product'.$i))){
				$cpname = $this->request->post('product'.$i);
				$pro=(explode("-",$cpname));
				$cpnum = $this->request->post('num'.$i);
				$jine=$pro[0]*$cpnum;
				$id3 = $db3->insert(['fid'=>$fId,'cpname'=>$pro[1],'cpnum'=>$cpnum,'jine'=>$jine,'djia'=>$pro[0]]); 
			}

		}
	   $strall="";
       $strallx=Db::name('bl_tdcp')->where('fid',$fId)->select();//查询会员
       for($ix=0;$ix<count($strallx);$ix++){
           $strall=$strall."<tr><td>".$strallx[$ix]['cpname']."</td><td>".$strallx[$ix]['cpnum']."</td><td>".$strallx[$ix]['jine']."<br/></td>";
       }
       $mail = new PHPMailer();

       $mail->isSMTP();// 使用SMTP服务
       $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
       $mail->Host = "smtp.qq.com";// 发送方的SMTP服务器地址
       $mail->SMTPAuth = true;// 是否使用身份验证
       $mail->Username = "873355565@qq.com";// 发送方的163邮箱用户名
       $mail->Password = "wyehucxpisuibbge";// 客户端授权密码
       $mail->SMTPSecure = "ssl";// 使用ssl协议方式
       $mail->Port = 465;// 163邮箱的ssl协议方式端口号是465/994

       $mail->setFrom("873355565@qq.com","退单信息");// 设置发件人信息
       $mail->addAddress('2441063316@qq.com', 'Joe');  // 收件人
       $mail->addAddress('2889383@qq.com', 'Joe');  // 收件人
       $mail->addReplyTo('873355565@qq.com', 'info'); //回复的时候回复给哪个邮箱 建议和发件人一致 
       //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)
       //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)
       //$mail->addAttachment("bug0.jpg");// 添加附件


        $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容 
        $mail->Subject = '退单信息！' .  date('Y-m-d H:i:s'); 
        $mail->Body = "<table border='1' style='font-size:20px;border: solid 1px #666'><tr width='40%'><td style='text-align:right;'>姓名：</td><td>".$bm_name."</td></tr><tr><td style='text-align:right;'>手机号：</td><td>".$bm_phone."</td></tr><tr><td style='text-align:right;'>配送地址:</td><td>".$bm_add."</td></tr><tr><td style='text-align:right;'>单号:</td><td>".$bm_danhao."</td></tr><tr><td style='text-align:right;'>备注:</td><td>".$bm_text."</td></tr><tr><td style='text-align:right;'>推广人:</td><td>".$sh_id."</td></tr></table><table border='1' style='font-size:20px;border: solid 1px #666'><tr><td>产品</td><td>数量</td><td>合计</td></tr>".$strall."<tr><td style='text-align:right;'>合计：</td><td></td><td>".$bm_call."</td></tr></table>"; 
        $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容'; 

       $mail->send();
        return $this->redirect('/index/index/tdmylist?id='.$sh_id);
    }
    
    //外委选择出库
   public function wwselectlist($idx,$phone){
			$db2=Db::name('bl_wwcp')->where("sfck",1)->order([
                 'id'  => 'desc'
             ])
             ->select();
			return $this->fetch('wwselectlist',['db2'=>$db2,'id'=>$idx,'xphone'=>$phone]);
    }
    
    public function selectout(){
        $ckval = $this->request->post('ckval/a'); 
        $ckvalnum=count($ckval);
        if($ckvalnum==0){
            echo "<script>alert('请至少选择一个项目！');window.history.go(-1);</script>";
        }
        $id = $this->request->post('id');
        $phone = $this->request->post('phone');
        for($v=0;$v<$ckvalnum;$v++){
        $allcp[]= Db::name('bl_wwcp')->where('id',$ckval[$v])->select();
        }
        $ffid= $allcp[0][0]['fid'];//第一个产品所对应的公司名
        $cpallnum=count($allcp);
        $allcx= Db::name('bl_wwdd')->where('id',$ffid)->select();
        $xdate=$allcx[0]['xdate']; 
		$dateArray = explode('-', $xdate);
        $jgfs=Db::name('bl_jgfs')->select();//加工方式
        $scpname=Db::name('bl_cpnm')->select();//公司名称列表

        $symbol = Db::name('bl_symbol')->select();
        $all = Db::name('bl_cls')->select();
		$allphao = Db::name('bl_paihao')->select();
		$alljldw = Db::name('bl_jldw')->select();
		$vpud=[];
		
		for($i=0;$i<$cpallnum;$i++){
			$vpud[$i]['name']=$allcp[$i][0]['cpname'];
			$array = explode('&', $allcp[$i][0]['djia']);
        	$phao=$array[1];
        	$vpud[$i]['paihao']=$array[0];
			$vpud[$i]['num']=$phao;
			$vpud[$i]['guige']=$allcp[$i][0]['csl'];
			$vpud[$i]['jine']=0;
			$vpud[$i]['id']=$allcp[$i][0]['id'];
			$vpud[$i]['mytext']=0;
		}
		
		return $this->fetch('selectout',['vpud'=>$vpud,"id"=>$id,'all'=>count($vpud),'xdate'=>$xdate,'allphao'=>$allphao,'alljldw'=>$alljldw,'jgfs'=>$jgfs,'symbol'=>$symbol,'allcx'=>$allcx,'dateArray'=>$dateArray,'jgfs'=>$jgfs,'scpname'=>$scpname,'allcp'=>$allcp,'phone'=>$phone,'cpallnum'=>$cpallnum]);//进入确认界面
        
    }
    
 public function upaddout()//提交单据信息
    { 
        $xsdate=$this->request->post('xdate');
        $mut=date("H-i");
        $snumx=Db::name('bl_wwdd')->where('xdate',$xsdate)->where('mut',$mut)->select();
		$bm_name =$this->request->post('name');
		$bm_add =0;
		$bm_phone =$this->request->post('phone');
		$all_cp =$this->request->post('all');
		$sh_id = $this->request->post('id');
		//$jldw = $this->request->post('jldw');
		$bm_text = $this->request->post('bm_text');
		$jgfs = $this->request->post('jgfs');
		if($sh_id=="ck"){
		    $cku=2;
		    $ckuname="外委出库";
		}else{
		    $cku=1;
		    $ckuname="外委入库";
		}
		//防止重复下单--------
		  //$sfxd=Db::name('bl_dd')->where('xdate',$xsdate)->where('myphone',$bm_phone)->where('zf',0)->where('she',0)->select();//判断今日是否已经下单
		 // if(count($sfxd)>0){
		  //echo "<script>alert('您今日已经下单，即将转到订单列表...');window.location.href='/index/index/mylist?id=".$sh_id."'</script>";
		  //return $this->redirect('/index/index/mylist?id='.$sh_id);
		  //}else{
		      
		      
                $m_date=$xsdate;
        		$sdate=$xsdate;
        		$setting=Db::name('bl_setting')->where('id',1)->select();

                $data = Db::name('bl_wwdd')->where('xdate',$sdate)->order("id","desc")->limit(1)->select();
                $snum=Db::name('bl_wwdd')->where('xdate',$xsdate)->select();
                if(count($data)==0){
                    $ddnum=count($snum)+1;
                }else{
                $dhao = $data[0]['dhao'];  // 内容为"ZZY2025-04-02-18"
                // 按"-"分割字符串并取末尾元素
                $parts = explode('-', $dhao);
                $snum = end($parts);  // 输出"18"
                    if(count($snum)>$snum){
                        $ddnum=count($snum)+1;
                    }else{
                        $ddnum=$snum+1;
                    }
                }
                
                                
        		
        		$bm_danhao=$setting[0]['exdh'].$sdate."-".$ddnum;
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
        
        		$db2 = Db::name('bl_wwdd');
        		$id2 = $db2->insert(['name'=>$username,'userx'=>$userId,'dhao'=>$bm_danhao,'madd'=>$jgfs,'jine'=>0,'cku'=>$cku,'she'=>1,'mprint'=>0,'xdate'=>$m_date,'xtext'=>$bm_text,'m_share'=>$sh_id,'mut'=>$mut,'zf'=>0,'myname'=>$bm_name,'myphone'=>$bm_phone]);
        		
        		$fId = Db::name('bl_wwdd')->getLastInsID();//添加产品
        		$db3 = Db::name('bl_wwcp');
        		for($i=1;$i<$all_cp+1;$i++){
        			if(!empty( $this->request->post('name'.$i))){
        				$cpname = $this->request->post('name'.$i);
        				$cpnum = $this->request->post('num'.$i);
        				$phao = $this->request->post('phao'.$i);
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
                        
        				$cp = Db::name('bl_clscp')->where('id',$cpid)->setDec('kcyj',$cpnum);//较少库存
        				$id3 = $db3->insert(['fid'=>$fId,'cpname'=>$cpname,'csl'=>$csl,'cpnum'=>$cpnum,'jine'=>$jinec,'djia'=>$phao,'mytext'=>$mytext,'cpid'=>$cpid,'danjias'=>$danjia,'jineall'=>$jineall,'hjzl'=>$hjzl,'sfck'=>0,'rkid'=>$cpid]); 
        			}
        
        		}
        		$db4 = Db::name('bl_wwcp');
        		for($i=1;$i<$all_cp+1;$i++){
        			if(!empty( $this->request->post('id'.$i))){
        				$cpid = $this->request->post('id'.$i);
        				$cp = Db::name('bl_clscp')->where('id',$cpid)->setDec('kcyj',$cpnum);//较少库存
        				$id4=$db4->where('id',$cpid)->update(['sfck'=>0]); 
        			}
        		}
        		    
        	   $strall="";
               $strallx=Db::name('bl_wwcp')->where('fid',$fId)->select();//查询会员
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
                $mail->Subject = '您有新单据！【' . $ckuname.'】'; 
                $mail->Body = "<table border='1' style='font-size:20px;border: solid 1px #666'><tr width='40%'><td style='text-align:right;'>单号：</td><td>".$bm_danhao."</td></tr><tr><td style='text-align:right;'>操作员:</td><td style='color:#ef4343;'>".$username."</td></tr><tr><td style='text-align:right;'>来源:</td><td>".$bm_name."</td></tr><tr><td style='text-align:right;'>类别:</td><td>".$ckuname."</td></tr><tr><td style='text-align:right;'>备注:</td><td>".$bm_text."</td></tr></table><table border='1' style='font-size:20px;border: solid 1px #666'><tr><td>产品</td><td>重量</td><td>
               批号</td></tr>".$strall."<tr><td style='text-align:right;'>合计：</td><td>".$strheji."</td><td></td></tr></table>"; 
                $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容'; 
        
               $mail->send();
        
                //}
                //return $this->redirect('/index/index/mylist.html?id=rk');
                $this->success('提交成功，正在转向单据列表！',"/index/wwdj/mylist.html?id=rk&y=0&m=0");
         
		      
		      
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
