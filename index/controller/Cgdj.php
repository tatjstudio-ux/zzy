<?php
namespace app\index\controller;//采购单据
use think\Controller;
use think\Db;
use think\facade\Request;
use phpmailer\phpmailer;

class Cgdj extends Controller
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
		$xiaoshou=Db::name('bl_user')->where('auth',3)->select();
		$setting=Db::name('bl_setting')->where('id',1)->select();
		//echo $xiaoshou;
        $categories = $this->getAllCategories(0, 0);
		return $this->fetch('cgdj',['phone'=>$phone,'sdate'=>$sdate,'ddnum'=>$ddnum,'select'=>$select,"id"=>$id,'all'=>$all,'xstr'=>$xstr,'allcls'=>$allcls,'xdate'=>$xdate,'scpname'=>$scpname,'year'=>$year,'jgfs'=>$jgfs,'categories'=>$categories,'xiaoshou'=>$xiaoshou,'setting'=>$setting]);//不是会员则进入此页面
	}
	
   public function selshow()//显示选择的产品
    { 	
		$all = Db::name('bl_cls')->select();
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
		$xiaoshou = $this->request->post("xiaoshou");//销售编号
		$jgfs = $this->request->post("jgfs");//加工方式
		for($i=0;$i<count($all);$i++){
		$pnum=0;
		$jine=0;
		$pnum = $this->request->post($i+1);//产品编号
		$jine = $this->request->post(($i+1).'x');//金额
		$cid = $this->request->post(($i+1).'xv');//编号
		$v=$i+1;
		$mytext = $this->request->post('mytext'.$v);//备注
		if($pnum!=""){
			$allc= Db::name('bl_kcsp')->where('id',$pnum)->select();
			$vpud[$vx]['name']=$allc[0]['name'];
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
		$setting=Db::name('bl_setting')->where('id',1)->select();
		$sdate=date("YmdHi");
		if($setting[0]['autoph']==1){
		    $phaox=$setting[0]['excg'].$sdate;
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
		$setting=Db::name('bl_setting')->where('id',1)->select();
		return $this->fetch('selshow',['vpud'=>$vpud,'phone'=>$phone,"id"=>$id,'alljine'=>$alljine,'all'=>count($vpud),'name'=>$name,'add'=>$add,'xdate'=>$xdate,'allphao'=>$allphao,'alljldw'=>$alljldw,'jgfs'=>$jgfs,'symbol'=>$symbol,'phaox'=>$phaox,'oldph'=>$oldph,'result'=>$result,'resultgg'=>$resultgg,'xiaoshou'=>$xiaoshou,'setting'=>$setting]);//进入确认界面
	}
		
		
public function editshow($id)//显示选择的产品
    { 	
		$all = Db::name('bl_cpcg')->where('fid',$id)->select();
		$alljldw = Db::name('bl_jldw')->select();
		$scpname=Db::name('bl_cpnm')->select();//公司名称列表
		$jgfs=Db::name('bl_jgfs')->select();//加工方式
		$allphao = Db::name('bl_paihao')->select();//加载牌号
		$vx=0;
		$vpud=[];
        for($i=0;$i<count($all);$i++){
			$allc= Db::name('bl_cpcg')->where('fid',$id)->select();
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
		$allcx= Db::name('bl_ddcg')->where('id',$id)->select();
		$xdate=$allcx[0]['xdate']; 
		$dateArray = explode('-', $xdate);

		//echo $id;
		//print_r($allcx);
		$setting=Db::name('bl_setting')->where('id',1)->select();
		return $this->fetch('editshow',['vpud'=>$vpud,'mytext'=>$allcx[0]['xtext'],"id"=>$id,'alljine'=>$alljine,'all'=>count($vpud),'alljldw'=>$alljldw,'allcx'=>$allcx,'dateArray'=>$dateArray,'scpname'=>$scpname,'jgfs'=>$jgfs,'allphao'=>$allphao,'setting'=>$setting,'cku'=>$allcx[0]['cku']]);//进入确认界面
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
		$xiaoshou=$this->request->post('xiaoshou');
		$jgfs = $this->request->post('jgfs');
		if($sh_id=="ck"){
		    $cku=2;
		    $ckuname="销售出库";
		}
		if($sh_id=="jgly"){
		    $cku=3;
		    $ckuname="加工领用";
		}
		if($sh_id=="wwly"){
		    $cku=4;
		    $ckuname="外委领用";
		}
		if($sh_id=="cl"){
		    $cku=5;
		    $ckuname="材料入库";
		}
		if($sh_id=="cp"){
		    $cku=6;
		    $ckuname="成品入库";
		}
		if($sh_id=="th"){
		    $cku=7;
		    $ckuname="退回入库";
		}
		if($sh_id=="cgth"){
		    $cku=8;
		    $ckuname="采购退回";
		}
		if($sh_id=="cal"){
		    $cku=9;
		    $ckuname="残料入库";
		}
		if($sh_id=="qt"){
		    $cku=10;
		    $ckuname="其他入库";
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
        		$id2 = $db2->insert(['name'=>$username,'userx'=>$userId,'dhao'=>$bm_danhao,'madd'=>$jgfs,'jine'=>$bm_call,'cku'=>$cku,'she'=>1,'mprint'=>0,'xdate'=>$m_date,'xtext'=>$bm_text,'m_share'=>$sh_id,'mut'=>$mut,'zf'=>0,'myname'=>$bm_name,'myphone'=>$bm_phone,'xiaoshou'=>$xiaoshou]);
        		
        		$fId = Db::name('bl_ddcg')->getLastInsID();//添加产品
        		$db3 = Db::name('bl_cpcg');
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
        				
        				if($sh_id=="ck" or $sh_id=="jgly" or $sh_id=="wwly"  or $sh_id=="cgth"){
        				        if($cpnum!=0){
                        	    $cp = Db::name('bl_kcsp')->where('id',$cpid)->setDec('kcl',$cpnum);//减少库存
        				        }
                        	}else{
                        	    if($cpnum!=0){
                        	    $cp = Db::name('bl_kcsp')->where('id',$cpid)->setInc('kcl',$cpnum);//增加库存
                        	    }
                        	}
        				$id3 = $db3->insert(['fid'=>$fId,'cpname'=>$cpname,'csl'=>$csl,'cpnum'=>$cpnum,'jine'=>$jinec,'djia'=>$phao,'mytext'=>$mytext,'cpid'=>$cpid,'danjias'=>$danjia,'jineall'=>$jineall,'hjzl'=>$hjzl,'oldph'=>$oldph]); 
        				
        			}
        
        		}
        	   $strall="";
               $strallx=Db::name('bl_cpcg')->where('fid',$fId)->select();//查询会员
               $strheji=0;
               for($ix=0;$ix<count($strallx);$ix++){
                   $strall=$strall."<tr><td>".$strallx[$ix]['cpname']."[".$oldph."]</td><td>".$strallx[$ix]['cpnum']."</td>
                   <td>".$strallx[$ix]['csl']."</td>
                   <td>".$strallx[$ix]['djia']."<br/></td>";
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
                $mail->Subject = '您有新采购单据！【' . $ckuname.'】'; 
                $mail->Body = "<table border='1' style='font-size:20px;border: solid 1px #666'>
                <tr width='40%'>
                <td style='text-align:right;'>单号：</td>
                <td>".$bm_danhao."</td></tr>
                <tr>
                <td style='text-align:right;'>操作员:</td>
                <td style='color:#3b82f6;'>".$username."</td>
                </tr>
                <tr>
                <td style='text-align:right;'>来源:</td>
                <td>".$bm_name."</td>
                </tr><tr>
                <td style='text-align:right;'>类别:</td>
                <td>".$ckuname."</td></tr><tr><td style='text-align:right;'>备注:</td><td>".$bm_text."</td></tr></table><table border='1' style='font-size:20px;border: solid 1px #666'><tr><td>产品</td><td>重量</td><td>
               规格</td><td>
               批号</td></tr>".$strall."<tr><td style='text-align:right;'>合计：</td><td>".$strheji."</td><td></td><td></td></tr></table>"; 
                $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容'; 
        
               $mail->send();
               
               
//外委领用
if($sh_id=="wwly" and $setting[0]['autogl']==1){
		    $cku=2;
		    $ckuname="外委出库";

		//防止重复下单--------
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
        				$id3 = $db3->insert(['fid'=>$fId,'cpname'=>$cpname,'csl'=>$csl,'cpnum'=>$cpnum,'jine'=>$jinec,'djia'=>$phao,'mytext'=>$mytext,'cpid'=>$cpid,'danjias'=>$danjia,'jineall'=>$jineall,'hjzl'=>$hjzl]); 
        				
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
                $mail->Subject = '您有新采购单据！【外委领用出库】'; 
                $mail->Body = "<table border='1' style='font-size:20px;border: solid 1px #666'><tr width='40%'><td style='text-align:right;'>单号：</td><td>".$bm_danhao."</td></tr><tr><td style='text-align:right;'>操作员:</td><td style='color:#3b82f6;;'>".$username."</td></tr><tr><td style='text-align:right;'>来源:</td><td>".$bm_name."</td></tr><tr><td style='text-align:right;'>类别:</td><td>".$ckuname."</td></tr><tr><td style='text-align:right;'>备注:</td><td>".$bm_text."</td></tr></table><table border='1' style='font-size:20px;border: solid 1px #666'><tr><td>产品</td><td>重量</td><td>
               批号</td></tr>".$strall."<tr><td style='text-align:right;'>合计：</td><td>".$strheji."</td><td></td></tr></table>"; 
                $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容'; 
        
               $mail->send();
}
//外委领用
        
                //}
                //return $this->redirect('/index/wwdj/mylist.html?id=rk');
                if($sh_id=="ck"){
                    $this->success('提交成功，正在转向销售出库列表！',"/index/cgdj/cklist?id=ck&y=0&m=0");
                }
                if($sh_id=="jgly"){
                    $this->success('提交成功，正在转向加工领用列表！',"/index/cgdj/jglylist?id=jgly&y=0&m=0");
                }
                if($sh_id=="wwly"){
                    $this->success('提交成功，正在转向外委领用列表！',"/index/cgdj/wwlylist?id=wwly&y=0&m=0");
                }
                if($sh_id=="cl"){
                $this->success('提交成功，正在转向材料列表！',"/index/cgdj/clrklist.html?id=cl&y=0&m=0");
                }
                if($sh_id=="cp"){
                $this->success('提交成功，正在转向成品列表！',"/index/cgdj/cprklist.html?id=cp&y=0&m=0");
                }
                if($sh_id=="th"){
                $this->success('提交成功，正在转向退货列表！',"/index/cgdj/thrklist.html?id=th&y=0&m=0");
                }
                if($sh_id=="cgth"){
                $this->success('提交成功，正在转向退货列表！',"/index/cgdj/cgthrklist.html?id=cgth&y=0&m=0");
                }
                if($sh_id=="cal"){
                $this->success('提交成功，正在转向残料列表！',"/index/cgdj/calrklist.html?id=cal&y=0&m=0");
                }
                if($sh_id=="qt"){
                $this->success('提交成功，正在转向其他列表！',"/index/cgdj/qtrklist.html?id=qt&y=0&m=0");
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
		$sh_id = $this->request->post('shid');
		$bm_text = $this->request->post('bm_text');
		if($sh_id==2){
		    $cku=2;
		    $ckuname="销售出库";
		}
		if($sh_id==3){
		    $cku=3;
		    $ckuname="加工领用";
		}
		if($sh_id==4){
		    $cku=4;
		    $ckuname="外委领用";
		}
		if($sh_id==5){
		    $cku=5;
		    $ckuname="材料入库";
		}
		if($sh_id==6){
		    $cku=6;
		    $ckuname="成品入库";
		}
		if($sh_id==7){
		    $cku=7;
		    $ckuname="退货入库";
		}
		if($sh_id==8){
		    $cku=8;
		    $ckuname="退回出库";
		}
		if($sh_id==9){
		    $cku=9;
		    $ckuname="残料入库";
		}
		if($sh_id==10){
		    $cku=10;
		    $ckuname="其他入库";
		}
		//echo $id;
		$ida=Db::name('bl_ddcg')->where('id',$id)->update(['xtext'=>$bm_text,'myname'=>$bm_name]); 
		$db3=Db::name('bl_cpcg')->where('fid',$id)->select();//查询产品
		//print_r($db3);
		$mynum=count($db3);
		for($i=1;$i<$mynum+1;$i++){
			if(!empty( $this->request->post('name'.$i))){
				$cpname = $this->request->post('name'.$i);
				$cpnumx = $this->request->post('num'.$i);
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
				
				$oldcpnum=$db3[$i-1]['cpnum'];
                $cpnum2=0;
                //echo $cpnumx;
                $sid=$db3[$i-1]['cpid'];
                $idx=$db3[$i-1]['id'];
				if($sh_id==2 or $sh_id==3 or $sh_id==4 ){
    				    if($cpnumx>$oldcpnum){
    				        $cpnum2=$cpnumx-$oldcpnum;
    				        $cp = Db::name('bl_kcsp')->where('id',$sid)->setDec('kcl',$cpnum2);//减少库存
    				    }
    				    if($cpnumx<$oldcpnum){
    				        $cpnum2=$oldcpnum-$cpnumx;
    				        $cp = Db::name('bl_kcsp')->where('id',$sid)->setInc('kcl',$cpnum2);//增加库存
    				    }  
                	}else{
    				    if($cpnumx>$oldcpnum){
    				        $cpnum2=$cpnumx-$oldcpnum;
    				        $cp = Db::name('bl_kcsp')->where('id',$sid)->setInc('kcl',$cpnum2);//增加库存
    				    }
    				    if($cpnumx<$oldcpnum){
    				        $cpnum2=$oldcpnum-$cpnumx;
    				        $cp = Db::name('bl_kcsp')->where('id',$sid)->setDec('kcl',$cpnum2);//减少库存
    				    }
                	}
				//echo $cpnumx;
				$id3=Db::name('bl_cpcg')->where('id',$idx)->update(['cpname'=>$cpname,'csl'=>$csl,'cpnum'=>$cpnumx,'danjias'=>$danjias,'jineall'=>$jineall,'jine'=>$jinec,'djia'=>$danjia,'mytext'=>$mytext,'hjzl'=>$hjzl,'oldph'=>$oldph]);
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
    
   public function mylist($id,$y,$m)
    { 
            if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$yd}%'")->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'cku'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y]);
			}
	
    }

   public function mysku($id)
    { 
                $y=date('Y');
                $m=date('m');

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
            $categories = $this->getAllCategories(0, 0);
			$db2 = Db::name('bl_kcsp')->select();
			return $this->fetch('mysku',['all'=>$all,'categories'=>$categories,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y]);
			}
	
    }

   public function skulist($id,$y,$m)
    { 
        
          if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $kcsp = Db::name('bl_kcsp')->where('id',$id)->select();
            $kcspname=$kcsp[0]['name'];
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where('zf',0)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('skulist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y,'kcspname'=>$kcsp[0]['name']]);
			}

	
    }



   public function cklist($id,$y,$m)
    { 
        
          if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$yd}%'")->where('cku',2)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y,'cku'=>2]);
			}

	
    }
    
   public function rklist($id,$y,$m)
    { 
          if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$yd}%'")->where('cku',1)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y]);
			}
	
    }
    
   public function clrklist($id,$y,$m)
    { 
          if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$yd}%'")->where('cku',5)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y,'cku'=>5]);
			}
	
    }
    
   public function cprklist($id,$y,$m)
    { 
          if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$yd}%'")->where('cku',6)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y,'cku'=>6]);
			}
	
    }

   public function thrklist($id,$y,$m)
    { 
          if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$yd}%'")->where('cku',7)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y,'cku'=>7]);
			}
	
    }

   public function cgthrklist($id,$y,$m)
    { 
          if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$yd}%'")->where('cku',8)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y,'cku'=>8]);
			}
	
    }
    
    
   public function jglylist($id,$y,$m)
    { 
          if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$yd}%'")->where('cku',3)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y,'cku'=>3]);
			}
	
    }
   public function wwlylist($id,$y,$m)
    { 
          if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$yd}%'")->where('cku',4)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y,'cku'=>4]);
			}
	
    }
    
   public function calrklist($id,$y,$m)
    { 
          if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$yd}%'")->where('cku',9)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y,'cku'=>9]);
			}
	
    }    
    
   public function qtrklist($id,$y,$m)
    { 
          if($y==0){
                $y=date('Y');
                $m=date('m');
                $yd=$y."-".$m;
            }else{
                $yd=$y."-".$m;
            }
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$yd}%'")->where('cku',10)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('mylist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'m'=>$m,'y'=>$y,'cku'=>10]);
			}
	
    }    
    
   public function del($idx,$id)
     { 
 			// 标记bl_cgdd的数据
 			$db = Db::name('bl_ddcg')->where('id',$idx);
 			$idb = $db->update(['zf'=>1]); 
 			$db2=$db->find();

            //echo $idx;
 			// 获取bl_cpcg中fid对应的bl_cgdd的id值和cpnum
 			$cpcgInfo = Db::name('bl_cpcg')->where('fid',$idx)->select();
			//print_r($cpcgInfo);
			
            $cpnumx=0;
 			    for($i=0;$i<count($cpcgInfo);$i++){
     				$reduceAmount = $cpcgInfo[$i]['cpnum'];
    				$xname=$cpcgInfo[$i]['cpname'];
    				//echo $reduceAmount."-".$xname;
     				// 更新bl_kcsp的kcl值
    				if($id==5 or $id==6 or $id==7 or $id==9 or $id==10){
    					$u=Db::name('bl_kcsp')->where('name',$xname)->setDec('kcl',$reduceAmount);
    				}else{
    					$u=Db::name('bl_kcsp')->where('name',$xname)->setInc('kcl',$reduceAmount);
    				}
    				$cpnumx=$cpnumx+$reduceAmount;
 			    }

 			if($db2['zkid']==0){
 			    
 			}else{
 			    //echo "正确";
 			    $res=Db::name('bl_cpcg')->where('id',$db2['zkid'])->setInc('cpnum',$cpnumx);
 			    $cpcgInfo2 = Db::name('bl_cpcg')->where('id',$db2['zkid']);
 			    $res2=$cpcgInfo2->find();
 			    $u=Db::name('bl_kcsp')->where('name',$res2['cpname'])->setInc('kcl',$cpnumx);
 			}
 			//echo $id.'----'.$reduceAmount.'----'.$xname;
 			//print_r($res2);
 		    return $this->redirect("/index/cgdj/mylist.html?id=".$id."&y=0&m=0");
     }
   public function rprint($idx,$id)
    { 
			$db = Db::name('bl_ddcg')->where('id',$idx);
			$idb = $db->update(['mprint'=>0]); 
			echo "<script>alert('补打成功！');</script>";
		    return $this->redirect("/index/cgdj/mylist.html?id=".$id."&y=0&m=0");
	
    }
    public function dtle($id)
    { 
		    $all=Db::name('bl_ddcg')->where('id',$id)->select();
		    $sh_id=$all[0]['cku'];
		if($sh_id==2){
		    $cku=2;
		    $ckuname="销售出库";
		}
		if($sh_id==3){
		    $cku=3;
		    $ckuname="加工领用";
		}
		if($sh_id==4){
		    $cku=4;
		    $ckuname="外委领用";
		}
		if($sh_id==5){
		    $cku=5;
		    $ckuname="材料入库";
		}
		if($sh_id==6){
		    $cku=6;
		    $ckuname="成品入库";
		}
		if($sh_id==7){
		    $cku=7;
		    $ckuname="退回入库";
		}
		if($sh_id==8){
		    $cku=8;
		    $ckuname="采购退回";
		}
		if($sh_id==9){
		    $cku=9;
		    $ckuname="残料入库";
		}
		if($sh_id==10){
		    $cku=10;
		    $ckuname="其他入库";
		}
			$idx=$all[0]["id"];
			$ux=$all[0]["userx"];
			$db2 = Db::name('bl_cpcg')->where('fid',$idx)->select();
			$db3 = Db::name('bl_user')->where('bm_id',$ux)->select();
			$setting=Db::name('bl_setting')->where('id',1)->select();
			return $this->fetch('dtle',['all'=>$all,'ckuname'=>$ckuname,'db2'=>$db2,'id'=>$id,'xphone'=>$db3[0]['bm_phone'],'xname'=>$db3[0]['bm_name'],'setting'=>$setting]);
	
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
    
//转库分配到子目录显示******************************************************************************
    public function zhuanku($id,$phone,$fid,$cku,$sid)//出入库显示
   { 
        $year=date('Y');
        $xdate=date('Y-m-d');
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
        $categories = $this->getAllCategories(0, 0);
		if(empty($select)){
		    echo "权限不足！";
		}elseif($select[0]['bm_vip']==0){
			return $this->fetch('zhuanku',['phone'=>$phone,'sdate'=>$sdate,'ddnum'=>$ddnum,'select'=>$select,"id"=>$id,'all'=>$all,'xstr'=>$xstr,'allcls'=>$allcls,'xdate'=>$xdate,'scpname'=>$scpname,'year'=>$year,'jgfs'=>$jgfs,'categories'=>$categories,'fid'=>$fid,'xcku'=>$cku,'sid'=>$sid]);//不是会员则进入此页面
		}
		elseif($select[0]['bm_vip']==1){
		    echo "权限不足！";
		}
	
	}



 public function rezhuanku()
    {
	$all = Db::name('bl_cls')->select();
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
		$fid = $this->request->post("fid");//需要分配的产品ID
		$xcku = $this->request->post("xcku");//需要分配的产品ID
		$sid = $this->request->post("sid");//需要分配的产品ID
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
		$setting=Db::name('bl_setting')->where('id',1)->select();
		$sdate=date("Ymd-Hi");
		if($setting[0]['autoph']==1){
		    $phaox=$setting[0]['excg'].$sdate;
		}else{
		    $phaox="";
		}
		
		return $this->fetch('rezhuanku',['vpud'=>$vpud,'phone'=>$phone,"id"=>$id,'alljine'=>$alljine,'all'=>count($vpud),'name'=>$name,'add'=>$add,'xdate'=>$xdate,'allphao'=>$allphao,'alljldw'=>$alljldw,'jgfs'=>$jgfs,'symbol'=>$symbol,'phaox'=>$phaox,'fid'=>$fid,'xcku'=>$xcku,'sid'=>$sid]);//进入确认界面 
        
    }
//转库分配到子目录显示******************************************************************************   
//转库分配到子目录提交******************************************************************************
 public function rezhuankuup()
    { 
        $xsdate=$this->request->post('xdate');
        $mut=date("H-i");
        $snumx=Db::name('bl_ddcg')->where('xdate',$xsdate)->where('mut',$mut)->select();
		$bm_name = $this->request->post('name');
		$bm_add =$this->request->post('add');
		$bm_call =$this->request->post('jine');
		$bm_phone = $this->request->post('phone');
		$all_cp =$this->request->post('all');
		$sh_id = $this->request->post('xcku');
		$bm_text = $this->request->post('bm_text');
		$jgfs = $this->request->post('jgfs');
		$fid = $this->request->post('fid');
		$sid = $this->request->post('sid');
		if($sh_id==5){
		    $cku=5;
		    $ckuname="转库分配-材料入库";
		}
		if($sh_id==6){
		    $cku=6;
		    $ckuname="转库分配-成品入库";
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
        		$id2 = $db2->insert(['name'=>$username,'userx'=>$userId,'dhao'=>$bm_danhao,'madd'=>$jgfs,'jine'=>$bm_call,'cku'=>$cku,'she'=>1,'mprint'=>0,'xdate'=>$m_date,'xtext'=>$bm_text,'m_share'=>$sh_id,'mut'=>$mut,'zf'=>0,'myname'=>$bm_name,'myphone'=>$bm_phone,'zkid'=>$sid]);
        		
        		$fId = Db::name('bl_ddcg')->getLastInsID();//添加产品
        		$db3 = Db::name('bl_cpcg');
        		$allst=0;
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
        				$xid=$this->request->post('xid'.$i);
        				if(!empty($this->request->post('hjzl'.$i))){
                            $hjzl = 1;
                        } else {
                            $hjzl = 0;
                        }
                        
                        $cp2 = Db::name('bl_kcsp')->where('id',$cpid)->setInc('kcl',$cpnum);//增加库存
                        $cp1 = Db::name('bl_kcsp')->where('id',$fid)->setDec('kcl',$cpnum);//减少库存
                        // 查询当前库存
                        $allst=$allst+$cpnum;
        				$id3 = $db3->insert(['fid'=>$fId,'cpname'=>$cpname,'csl'=>$csl,'cpnum'=>$cpnum,'jine'=>$jinec,'djia'=>$phao,'mytext'=>$mytext,'cpid'=>$cpid,'danjias'=>$danjia,'jineall'=>$jineall,'hjzl'=>$hjzl]); 
        				
        			}
        
        		}
        		
                $currentStock = Db::name('bl_cpcg')->where('id', $sid)->value('cpnum');
                // 将 text 类型转换为数值类型
                $currentStock = (int)$currentStock;
                // 计算新的库存数量
                $newStock = $currentStock - $allst;
                // 更新数据库
                $cp3 = Db::name('bl_cpcg')->where('id', $sid)->setField('cpnum', $newStock);
                //}
                //return $this->redirect('/index/wwdj/mylist.html?id=rk');
                if($sh_id==5){
                $this->success('提交成功，正在转向材料列表！',"/index/cgdj/clrklist.html?id=ck&y=0&m=0");
                }
                if($sh_id==6){
                $this->success('提交成功，正在转向成品列表！',"/index/cgdj/clrklist.html?id=ck&y=0&m=0");
                }

                
		  }
//转库分配到子目录******************************************************************************

}
