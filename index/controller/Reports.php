<?php
namespace app\index\controller;//报表数据
use think\Controller;
use think\Db;
use think\facade\Request;
use phpmailer\phpmailer;
use think\Session;  

class Reports extends Controller
{
    
public function index()
    { 
        $year=date('Y');
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		
		$scpname=Db::name('bl_cpnm')->select();
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$allcls=Db::name('bl_cls')->select();
		$xsdate=date("Y-m-d");
		$sdate=date("Ymd");
		$snum=Db::name('bl_dd')->where('xdate',$xsdate)->select();
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
		if(Session::get('xpass')=='')
    		if($select[0]['bm_pass']==md5($password)){
    		    Session::set('xpass', md5($password));
    		    return $this->fetch('main',['phone'=>$phone,'id'=>0,'sdate'=>$sdate,'ddnum'=>$ddnum,'select'=>$select,"id"=>$id,'all'=>$all,'xstr'=>$xstr,'allcls'=>$allcls,'xdate'=>$xdate,'scpname'=>$scpname,'year'=>$year,'myname'=>$select[0]['bm_name']]);//不是会员则进入此页面
    		}else{
    		    echo "<script>alert('用户名或者密码错误！');window.location.href = '/';</script>";

    		}
    	else{
    	    return $this->fetch('index',['phone'=>$phone,'id'=>0,'sdate'=>$sdate,'ddnum'=>$ddnum,'select'=>$select,"id"=>$id,'all'=>$all,'xstr'=>$xstr,'allcls'=>$allcls,'xdate'=>$xdate,'scpname'=>$scpname,'year'=>$year,'myname'=>$select[0]['bm_name']]);//不是会员则进入此页面
    	}

	
	} 
    

public function mainrk($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cp')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                    }
                                    
            		            $muser=Db::name('bl_cp')->where("fid",$xall[$x]['id'])->select();
                		            foreach ($muser as $musers) {
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
                                    
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('mainrk',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth]);//不是会员则进入此页面

	
	}
	
	
	
public function mainck($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cp')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                    }
                                    
            		            $muser=Db::name('bl_cp')->where("fid",$xall[$x]['id'])->select();
                		            foreach ($muser as $musers) {
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
                                    
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('mainck',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth]);//不是会员则进入此页面
	
	}	
public function mainwwrk($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_wwcp')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('mainwwrk',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth]);//不是会员则进入此页面

	
	
	}	
public function mainwwck($year)
    { 
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_wwcp')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('mainwwck',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth]);//不是会员则进入此页面
	}	


public function crjy($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cp')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
	$mcpname2=Db::name('bl_cpnm')->select();
	$scpname2=Db::name('bl_cpnm')->select();	
	$v2=0;	
		
foreach ($scpname2 as $scpname2) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg2=0;//总重量每月清零
    		    $alljine2=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname2['name']."-".$mydate."<br/>";
                    $sname2=$scpname2['name'];
                    $xall2=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$sname2)->where("zf",0)->select();
                    //print_r($xall2);
                    //echo "<br/><hr/>";
        		    $cont2=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$scpname2['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont2;$x++){
        		            
        		            if(strpos($xall2[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser2=Db::name('bl_cp')->where("fid",$xall2[$x]['id'])->where("hjzl",1)->select();
            		            //print_r($muser2);
                		            foreach ($muser2 as $muser2) {
                                        $allkg2=$allkg2+floatval($muser2['cpnum']);
                                        $alljine2=$alljine2+floatval($muser2['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg2."-".$alljine2."<br/>";
        		    $allcont2[$v2]['m']=$i;
        		    $allcont2[$v2]['c']=$cont2;
        		    $allcont2[$v2]['name']=$sname2;
        		    $allcont2[$v2]['allkg']=$allkg2;
        		    $allcont2[$v2]['alljine']=$alljine2;
        		    $v2=$v2+1;
                }
		}
		
		//print_r($allcont2);
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('crjy',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'scpname2'=>$mcpname2,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'allcont2'=>$allcont2,'m'=>$nowmoth]);//不是会员则进入此页面

	
	}


public function wwcrjy($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_wwcp')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
	$mcpname2=Db::name('bl_cpnm')->select();
	$scpname2=Db::name('bl_cpnm')->select();	
	$v2=0;	
		
foreach ($scpname2 as $scpname2) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg2=0;//总重量每月清零
    		    $alljine2=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname2['name']."-".$mydate."<br/>";
                    $sname2=$scpname2['name'];
                    $xall2=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$sname2)->where("zf",0)->select();
                    //print_r($xall2);
                    //echo "<br/><hr/>";
        		    $cont2=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$scpname2['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont2;$x++){
        		            
        		            if(strpos($xall2[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser2=Db::name('bl_wwcp')->where("fid",$xall2[$x]['id'])->where("hjzl",1)->select();
            		            //print_r($muser2);
                		            foreach ($muser2 as $muser2) {
                                        $allkg2=$allkg2+floatval($muser2['cpnum']);
                                        $alljine2=$alljine2+floatval($muser2['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg2."-".$alljine2."<br/>";
        		    $allcont2[$v2]['m']=$i;
        		    $allcont2[$v2]['c']=$cont2;
        		    $allcont2[$v2]['name']=$sname2;
        		    $allcont2[$v2]['allkg']=$allkg2;
        		    $allcont2[$v2]['alljine']=$alljine2;
        		    $v2=$v2+1;
                }
		}
		
		//print_r($allcont2);
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('wwcrjy',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'scpname2'=>$mcpname2,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'allcont2'=>$allcont2,'m'=>$nowmoth]);//不是会员则进入此页面

	
	}
	
	
public function wwrkxdyl($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        $cpnamex= $this->request->get('cpnamex');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}

        $ddall=Db::name('bl_wwdd')->where("xdate like '{$year}%'")->where("cku",1)->where("myname",$cpnamex)->where("zf",0)->order(['xdate'  => 'asc'])->select();
        $ddcp=Db::name('bl_wwcp')->where("hjzl",1)->select();
        //print_r($ddall);
        //print_r($ddcp);
        //--------------------------------------
        $v=0;

    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $xall=Db::name('bl_wwdd')->where("xdate like '{$year}%'")->where("cku",1)->where("myname",$cpnamex)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$cpnamex)->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_wwcp')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$cpnamex;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }

		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('wwrkxdyl',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth,'cpnamex'=>$cpnamex,'ddall'=>$ddall,'ddcp'=>$ddcp]);//不是会员则进入此页面

	
	
	}
	
public function clrkxdyl($year)//材料入库详单
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        $cpnamex= $this->request->get('cpnamex');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}

        $ddall=Db::name('bl_ddcg')->where("xdate like '{$year}%'")->where("cku",5)->where("myname",$cpnamex)->where("zf",0)->order(['xdate'  => 'asc'])->select();
        $ddcp=Db::name('bl_cpcg')->where("hjzl",1)->select();
        //print_r($ddall);
        //print_r($ddcp);
        //--------------------------------------
        $v=0;

    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $xall=Db::name('bl_ddcg')->where("xdate like '{$year}%'")->where("cku",5)->where("myname",$cpnamex)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",5)->where("myname",$cpnamex)->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cpcg')->where("fid",$xall[$x]['id'])->where("hjzl",5)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$cpnamex;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }

		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('clrkxdyl',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth,'cpnamex'=>$cpnamex,'ddall'=>$ddall,'ddcp'=>$ddcp]);//不是会员则进入此页面

	
	
	}
	
public function wwckxdyl($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        $cpnamex= $this->request->get('cpnamex');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}

        $ddall=Db::name('bl_wwdd')->where("xdate like '{$year}%'")->where("cku",2)->where("myname",$cpnamex)->where("zf",0)->order(['xdate'  => 'asc'])->select();
        $ddcp=Db::name('bl_wwcp')->where("hjzl",1)->select();
        //print_r($ddall);
        //print_r($ddcp);
        //--------------------------------------
        $v=0;

    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $xall=Db::name('bl_wwdd')->where("xdate like '{$year}%'")->where("cku",2)->where("myname",$cpnamex)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$cpnamex)->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_wwcp')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$cpnamex;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }

		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('wwckxdyl',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth,'cpnamex'=>$cpnamex,'ddall'=>$ddall,'ddcp'=>$ddcp]);//不是会员则进入此页面

	}

public function ckxdyl($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        $cpnamex= $this->request->get('cpnamex');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}

        $ddall=Db::name('bl_dd')->where("xdate like '{$year}%'")->where("cku",2)->where("myname",$cpnamex)->where("zf",0)->order(['xdate'  => 'asc'])->select();
        $ddcp=Db::name('bl_cp')->where("hjzl",1)->select();
        //print_r($ddall);
        //print_r($ddcp);
        //--------------------------------------
        $v=0;

    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $xall=Db::name('bl_dd')->where("xdate like '{$year}%'")->where("cku",2)->where("myname",$cpnamex)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$cpnamex)->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cp')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$cpnamex;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }

		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('ckxdyl',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth,'cpnamex'=>$cpnamex,'ddall'=>$ddall,'ddcp'=>$ddcp]);//不是会员则进入此页面

	}



public function rkxdyl($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        $cpnamex= $this->request->get('cpnamex');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}

        $ddall=Db::name('bl_dd')->where("xdate like '{$year}%'")->where("cku",1)->where("myname",$cpnamex)->where("zf",0)->order(['xdate'  => 'asc'])->select();
        $ddcp=Db::name('bl_cp')->where("hjzl",1)->select();
        //print_r($ddall);
        //print_r($ddcp);
        //--------------------------------------
        $v=0;

    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $xall=Db::name('bl_dd')->where("xdate like '{$year}%'")->where("cku",1)->where("myname",$cpnamex)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$cpnamex)->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cp')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$cpnamex;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }

		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('rkxdyl',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth,'cpnamex'=>$cpnamex,'ddall'=>$ddall,'ddcp'=>$ddcp]);//不是会员则进入此页面

	}
//材料入库汇总
public function clrk($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",5)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",5)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cpcg')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('maincgrk',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth]);//不是会员则进入此页面

	
	
	}
//成品入库汇总
public function cprk($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",6)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",6)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cpcg')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('maincprk',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth]);//不是会员则进入此页面

	
	
	}
	

//退货入库汇总
public function thrk($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",7)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",7)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cpcg')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('maincgrk',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth]);//不是会员则进入此页面

	
	
	}
	
//销售出库汇总
public function xsck($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cpcg')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('maincgck',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth]);//不是会员则进入此页面

	
	
	}
//加工出库汇总
public function jgck($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",3)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",3)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cpcg')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('maincgck',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth]);//不是会员则进入此页面

	
	
	}
//加工出库汇总
public function wwck($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",4)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",4)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cpcg')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('maincgck',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth]);//不是会员则进入此页面

	
	
	}
	
//加工出库汇总
public function thck($year)
    { 
        //$year=date('Y');
        //echo $year;
        $xdate=date('Y-m-d');
        $phone=cookie('xphone');
        $password= $this->request->post('password');
        if($phone==""){
	   	$phone = $this->request->post('phone');
	   	$id = $this->request->post('idx');
	   	cookie('xphone',$phone);
	   	}else{
	   	    $id="rk";
	   	    
	   	}
		$select=Db::name('bl_user')->where('bm_phone',$phone)->select();
		if(count($select)==0){
		    Session::delete('xpass');
            return $this->redirect('/');
		}
		$mcpname=Db::name('bl_cpnm')->select();
		$scpname=Db::name('bl_cpnm')->select();
		if ($year!=date('Y')){
		    $nowmoth=13;//获取当前月份
		}else{
            $nowmoth=date('n')+1;//获取当前月份
		}


        //--------------------------------------
        $v=0;
        foreach ($scpname as $scpname) {
    		for($i=1;$i<$nowmoth;$i++){
    		    $allkg=0;//总重量每月清零
    		    $alljine=0;//总金额每月清零
    		  if($i<10){
                $mt="0".$i;
              }else{
                $mt=$i;
              }
                $mydate=$year."-".$mt;
                //echo $scpname['name']."-".$mydate."<br/>";
                    $sname=$scpname['name'];
                    $xall=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",8)->where("myname",$sname)->where("zf",0)->select();
                    //print_r($xall);
                    //echo "<br/><hr/>";
        		    $cont=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",8)->where("myname",$scpname['name'])->where("zf",0)->count();
        		        for($x=0;$x<$cont;$x++){
        		            
        		            if(strpos($xall[$x]['xdate'],$mydate) !== false){
        		                //echo $xall[$x]['xdate']."<br/>";
            		            $muser=Db::name('bl_cpcg')->where("fid",$xall[$x]['id'])->where("hjzl",1)->select();
                		            foreach ($muser as $musers) {
                                        $allkg=$allkg+floatval($musers['cpnum']);
                                        $alljine=$alljine+floatval($musers['jineall']);
                                    }
        		            }
        		          
        		        }
        		    //echo $allkg."<br/>";
        		    $allcont[$v]['m']=$i;
        		    $allcont[$v]['c']=$cont;
        		    $allcont[$v]['name']=$sname;
        		    $allcont[$v]['allkg']=$allkg;
        		    $allcont[$v]['alljine']=$alljine;
        		    $v=$v+1;
                }
		}
		
        //----------------------------------
		$all = Db::name('bl_clscp')->order('myorder asc')->select();
    	return $this->fetch('mainthck',['phone'=>$phone,'select'=>$select,'all'=>$all,'xdate'=>$xdate,'scpname'=>$mcpname,'year'=>$year,'myname'=>$select[0]['bm_name'],'allcont'=>$allcont,'m'=>$nowmoth]);//不是会员则进入此页面

	}
    /**
     * 订单报表页面
     * @return mixed
     */
public function dayrtp() { 
    // 获取日期参数：id=all 显示当天，否则显示指定日期（格式Y-m-d）
    $dateParam = input('id', 'all');
    $today = date('Y-m-d');
    $selectedDate = ($dateParam === 'all') ? $today : $dateParam;
    
    // 验证日期格式（避免非法日期参数）
    if ($dateParam !== 'all' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
        $selectedDate = $today; // 格式错误时默认显示当天
    }
    
    // 入库数据：按选择日期和公司名分组
    $rukuGroups = [];
    $rukuData = Db::name('bl_dd')
        ->alias('dd')
        ->join('bl_cp cp', 'dd.id = cp.fid')
        ->where('dd.xdate', $selectedDate)
        ->where('dd.zf', 0)
        ->where('dd.cku', 1)
        ->field('dd.myname, cp.cpname, cp.cpnum, cp.csl, cp.danjias, cp.jineall')
        ->order('dd.myname ASC')
        ->select();
    
    $rukuTotalCpnum = 0;
    $rukuTotalJine = 0;
    $currentCompany = '';
    foreach ($rukuData as $item) {
        $rukuTotalCpnum += $item['cpnum'];
        $rukuTotalJine += $item['jineall'];
        if ($item['myname'] != $currentCompany) {
            $currentCompany = $item['myname'];
            $rukuGroups[$currentCompany] = [
                'list' => [],
                'totalCpnum' => 0,
                'totalJine' => 0,
                'avgPrice' => 0
            ];
        }
        $rukuGroups[$currentCompany]['list'][] = $item;
        $rukuGroups[$currentCompany]['totalCpnum'] += $item['cpnum'];
        $rukuGroups[$currentCompany]['totalJine'] += $item['jineall'];
        $rukuGroups[$currentCompany]['avgPrice'] = $rukuGroups[$currentCompany]['totalJine'] > 0 
            ? round($rukuGroups[$currentCompany]['totalJine'] / $rukuGroups[$currentCompany]['totalCpnum'], 2) 
            : 0;
    }
    $rukuAvgPrice = $rukuTotalCpnum > 0 ? round($rukuTotalJine / $rukuTotalCpnum, 2) : 0;
    
    // 出库数据：按选择日期和公司名分组
    $chukuGroups = [];
    $chukuData = Db::name('bl_dd')
        ->alias('dd')
        ->join('bl_cp cp', 'dd.id = cp.fid')
        ->where('dd.xdate', $selectedDate)
        ->where('dd.zf', 0)
        ->where('dd.cku', 2)
        ->field('dd.myname, cp.cpname, cp.cpnum, cp.csl, cp.danjias, cp.jineall')
        ->order('dd.myname ASC')
        ->select();
    
    $chukuTotalCpnum = 0;
    $chukuTotalJine = 0;
    $currentChukuCompany = '';
    foreach ($chukuData as $item) {
        $chukuTotalCpnum += $item['cpnum'];
        $chukuTotalJine += $item['jineall'];
        if ($item['myname'] != $currentChukuCompany) {
            $currentChukuCompany = $item['myname'];
            $chukuGroups[$currentChukuCompany] = [
                'list' => [],
                'totalCpnum' => 0,
                'totalJine' => 0,
                'avgPrice' => 0
            ];
        }
        $chukuGroups[$currentChukuCompany]['list'][] = $item;
        $chukuGroups[$currentChukuCompany]['totalCpnum'] += $item['cpnum'];
        $chukuGroups[$currentChukuCompany]['totalJine'] += $item['jineall'];
        $chukuGroups[$currentChukuCompany]['avgPrice'] = $chukuGroups[$currentChukuCompany]['totalJine'] > 0 
            ? round($chukuGroups[$currentChukuCompany]['totalJine'] / $chukuGroups[$currentChukuCompany]['totalCpnum'], 2) 
            : 0;
    }
    $chukuAvgPrice = $chukuTotalCpnum > 0 ? round($chukuTotalJine / $chukuTotalCpnum, 2) : 0;
    
    $this->assign([
        'rukuGroups' => $rukuGroups,
        'rukuCount' => count($rukuData),
        'rukuTotalCpnum' => $rukuTotalCpnum,
        'rukuTotalJine' => $rukuTotalJine,
        'rukuAvgPrice' => $rukuAvgPrice,
        'chukuGroups' => $chukuGroups,
        'chukuCount' => count($chukuData),
        'chukuTotalCpnum' => $chukuTotalCpnum,
        'chukuTotalJine' => $chukuTotalJine,
        'chukuAvgPrice' => $chukuAvgPrice,
        'today' => $today,
        'selectedDate' => $selectedDate // 传递选中日期到视图
    ]);
    
    return $this->fetch();
} 
   
public function wwdayrtp() { //外委日报表
    // 获取日期参数：id=all 显示当天，否则显示指定日期（格式Y-m-d）
    $dateParam = input('id', 'all');
    $today = date('Y-m-d');
    $selectedDate = ($dateParam === 'all') ? $today : $dateParam;
    
    // 验证日期格式（避免非法日期参数）
    if ($dateParam !== 'all' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
        $selectedDate = $today; // 格式错误时默认显示当天
    }
    
    // 入库数据：按选择日期和公司名分组
    $rukuGroups = [];
    $rukuData = Db::name('bl_wwdd')
        ->alias('dd')
        ->join('bl_wwcp cp', 'dd.id = cp.fid')
        ->where('dd.xdate', $selectedDate)
        ->where('dd.zf', 0)
        ->where('dd.cku', 1)
        ->field('dd.myname, cp.cpname, cp.cpnum, cp.csl, cp.danjias, cp.jineall')
        ->order('dd.myname ASC')
        ->select();
    
    $rukuTotalCpnum = 0;
    $rukuTotalJine = 0;
    $currentCompany = '';
    foreach ($rukuData as $item) {
        $rukuTotalCpnum += $item['cpnum'];
        $rukuTotalJine += $item['jineall'];
        if ($item['myname'] != $currentCompany) {
            $currentCompany = $item['myname'];
            $rukuGroups[$currentCompany] = [
                'list' => [],
                'totalCpnum' => 0,
                'totalJine' => 0,
                'avgPrice' => 0
            ];
        }
        $rukuGroups[$currentCompany]['list'][] = $item;
        $rukuGroups[$currentCompany]['totalCpnum'] += $item['cpnum'];
        $rukuGroups[$currentCompany]['totalJine'] += $item['jineall'];
        $rukuGroups[$currentCompany]['avgPrice'] = $rukuGroups[$currentCompany]['totalJine'] > 0 
            ? round($rukuGroups[$currentCompany]['totalJine'] / $rukuGroups[$currentCompany]['totalCpnum'], 2) 
            : 0;
    }
    $rukuAvgPrice = $rukuTotalCpnum > 0 ? round($rukuTotalJine / $rukuTotalCpnum, 2) : 0;
    
    // 出库数据：按选择日期和公司名分组
    $chukuGroups = [];
    $chukuData = Db::name('bl_wwdd')
        ->alias('dd')
        ->join('bl_wwcp cp', 'dd.id = cp.fid')
        ->where('dd.xdate', $selectedDate)
        ->where('dd.zf', 0)
        ->where('dd.cku', 2)
        ->field('dd.myname, cp.cpname, cp.cpnum, cp.csl, cp.danjias, cp.jineall')
        ->order('dd.myname ASC')
        ->select();
    
    $chukuTotalCpnum = 0;
    $chukuTotalJine = 0;
    $currentChukuCompany = '';
    foreach ($chukuData as $item) {
        $chukuTotalCpnum += $item['cpnum'];
        $chukuTotalJine += $item['jineall'];
        if ($item['myname'] != $currentChukuCompany) {
            $currentChukuCompany = $item['myname'];
            $chukuGroups[$currentChukuCompany] = [
                'list' => [],
                'totalCpnum' => 0,
                'totalJine' => 0,
                'avgPrice' => 0
            ];
        }
        $chukuGroups[$currentChukuCompany]['list'][] = $item;
        $chukuGroups[$currentChukuCompany]['totalCpnum'] += $item['cpnum'];
        $chukuGroups[$currentChukuCompany]['totalJine'] += $item['jineall'];
        $chukuGroups[$currentChukuCompany]['avgPrice'] = $chukuGroups[$currentChukuCompany]['totalJine'] > 0 
            ? round($chukuGroups[$currentChukuCompany]['totalJine'] / $chukuGroups[$currentChukuCompany]['totalCpnum'], 2) 
            : 0;
    }
    $chukuAvgPrice = $chukuTotalCpnum > 0 ? round($chukuTotalJine / $chukuTotalCpnum, 2) : 0;
    
    $this->assign([
        'rukuGroups' => $rukuGroups,
        'rukuCount' => count($rukuData),
        'rukuTotalCpnum' => $rukuTotalCpnum,
        'rukuTotalJine' => $rukuTotalJine,
        'rukuAvgPrice' => $rukuAvgPrice,
        'chukuGroups' => $chukuGroups,
        'chukuCount' => count($chukuData),
        'chukuTotalCpnum' => $chukuTotalCpnum,
        'chukuTotalJine' => $chukuTotalJine,
        'chukuAvgPrice' => $chukuAvgPrice,
        'today' => $today,
        'selectedDate' => $selectedDate // 传递选中日期到视图
    ]);
    
    return $this->fetch();
} 
    
    
public function cgdayrtp() { //采购日报表
    // 1. 获取参数：日期+出入库类型
    $dateParam = input('id', 'all'); // 日期参数（all=当天，否则Y-m-d）
    $typeParam = input('type', 'all'); // 类型筛选参数（all=全部，否则cku值）
    $today = date('Y-m-d');
    $selectedDate = ($dateParam === 'all') ? $today : $dateParam;
    
    // 2. 验证日期格式
    if ($dateParam !== 'all' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
        $selectedDate = $today;
    }

    // 3. 定义cku映射关系（与需求一致）
    $ckuConfig = [
        'all' => ['name' => '全部类型', 'list' => []], // 全部类型（空数组后续处理）
        2 => ['name' => '销售出库', 'type' => 'chuku'],
        3 => ['name' => '加工领用', 'type' => 'chuku'],
        4 => ['name' => '外委领用', 'type' => 'chuku'],
        5 => ['name' => '材料入库', 'type' => 'ruku'],
        6 => ['name' => '成品入库', 'type' => 'ruku'],
        7 => ['name' => '退回入库', 'type' => 'ruku'],
        8 => ['name' => '采购退回', 'type' => 'chuku'],
        9 => ['name' => '残料入库', 'type' => 'ruku'],
        10 => ['name' => '其他入库', 'type' => 'ruku'],
    ];
    
    // 4. 拆分入库/出库基础cku列表
    $rukuBaseCku = [5, 6, 7, 9, 10]; // 入库类基础cku
    $chukuBaseCku = [2, 3, 4, 8];    // 出库类基础cku
    
    // 5. 根据筛选类型，确定最终需要查询的cku列表
    $selectedRukuCku = $rukuBaseCku;
    $selectedChukuCku = $chukuBaseCku;
    
    if ($typeParam !== 'all' && isset($ckuConfig[$typeParam])) {
        // 筛选特定类型：只保留该类型对应的cku
        $targetType = $ckuConfig[$typeParam]['type'];
        if ($targetType === 'ruku') {
            $selectedRukuCku = [$typeParam];
            $selectedChukuCku = []; // 出库类置空，不查询
        } elseif ($targetType === 'chuku') {
            $selectedChukuCku = [$typeParam];
            $selectedRukuCku = []; // 入库类置空，不查询
        }
    }

    // 6. 入库数据查询（按筛选条件）
    $rukuGroups = [];
    $rukuData = [];
    if (!empty($selectedRukuCku)) {
        $rukuData = Db::name('bl_ddcg')
            ->alias('dd')
            ->join('bl_cpcg cp', 'dd.id = cp.fid')
            ->where('dd.xdate', $selectedDate)
            ->where('dd.zf', 0)
            ->whereIn('dd.cku', $selectedRukuCku)
            ->field('dd.myname, cp.cpname, cp.cpnum, cp.csl, cp.danjias, cp.jineall, dd.cku')
            ->order('dd.myname ASC')
            ->select();
    }
    
    // 入库数据分组+统计
    $rukuTotalCpnum = 0;
    $rukuTotalJine = 0;
    $currentCompany = '';
    foreach ($rukuData as $item) {
        $rukuTotalCpnum += $item['cpnum'];
        $rukuTotalJine += $item['jineall'];
        if ($item['myname'] != $currentCompany) {
            $currentCompany = $item['myname'];
            $rukuGroups[$currentCompany] = [
                'list' => [],
                'totalCpnum' => 0,
                'totalJine' => 0,
                'avgPrice' => 0
            ];
        }
        $rukuGroups[$currentCompany]['list'][] = $item;
        $rukuGroups[$currentCompany]['totalCpnum'] += $item['cpnum'];
        $rukuGroups[$currentCompany]['totalJine'] += $item['jineall'];
        $rukuGroups[$currentCompany]['avgPrice'] = $rukuGroups[$currentCompany]['totalJine'] > 0 
            ? round($rukuGroups[$currentCompany]['totalJine'] / $rukuGroups[$currentCompany]['totalCpnum'], 2) 
            : 0;
    }
    $rukuAvgPrice = $rukuTotalCpnum > 0 ? round($rukuTotalJine / $rukuTotalCpnum, 2) : 0;
    
    // 7. 出库数据查询（按筛选条件）
    $chukuGroups = [];
    $chukuData = [];
    if (!empty($selectedChukuCku)) {
        $chukuData = Db::name('bl_ddcg')
            ->alias('dd')
            ->join('bl_cpcg cp', 'dd.id = cp.fid')
            ->where('dd.xdate', $selectedDate)
            ->where('dd.zf', 0)
            ->whereIn('dd.cku', $selectedChukuCku)
            ->field('dd.myname, cp.cpname, cp.cpnum, cp.csl, cp.danjias, cp.jineall, dd.cku')
            ->order('dd.myname ASC')
            ->select();
    }
    
    // 出库数据分组+统计
    $chukuTotalCpnum = 0;
    $chukuTotalJine = 0;
    $currentChukuCompany = '';
    foreach ($chukuData as $item) {
        $chukuTotalCpnum += $item['cpnum'];
        $chukuTotalJine += $item['jineall'];
        if ($item['myname'] != $currentChukuCompany) {
            $currentChukuCompany = $item['myname'];
            $chukuGroups[$currentChukuCompany] = [
                'list' => [],
                'totalCpnum' => 0,
                'totalJine' => 0,
                'avgPrice' => 0
            ];
        }
        $chukuGroups[$currentChukuCompany]['list'][] = $item;
        $chukuGroups[$currentChukuCompany]['totalCpnum'] += $item['cpnum'];
        $chukuGroups[$currentChukuCompany]['totalJine'] += $item['jineall'];
        $chukuGroups[$currentChukuCompany]['avgPrice'] = $chukuGroups[$currentChukuCompany]['totalJine'] > 0 
            ? round($chukuGroups[$currentChukuCompany]['totalJine'] / $chukuGroups[$currentChukuCompany]['totalCpnum'], 2) 
            : 0;
    }
    $chukuAvgPrice = $chukuTotalCpnum > 0 ? round($chukuTotalJine / $chukuTotalCpnum, 2) : 0;

    // 8. 组装下拉框选项（入库/出库分类显示，便于用户选择）
    $typeOptions = [
        ['value' => 'all', 'name' => '全部类型'],
        ['value' => '', 'name' => '—— 入库类型 ——', 'disabled' => true], // 分隔符（不可选）
        ['value' => 5, 'name' => '材料入库'],
        ['value' => 6, 'name' => '成品入库'],
        ['value' => 7, 'name' => '退回入库'],
        ['value' => 9, 'name' => '残料入库'],
        ['value' => 10, 'name' => '其他入库'],
        ['value' => '', 'name' => '—— 出库类型 ——', 'disabled' => true], // 分隔符（不可选）
        ['value' => 2, 'name' => '销售出库'],
        ['value' => 3, 'name' => '加工领用'],
        ['value' => 4, 'name' => '外委领用'],
        ['value' => 8, 'name' => '采购退回'],
    ];
    
    // 9. 传递变量到视图
    $this->assign([
        'rukuGroups' => $rukuGroups,
        'rukuTotalCpnum' => $rukuTotalCpnum,
        'rukuTotalJine' => $rukuTotalJine,
        'rukuAvgPrice' => $rukuAvgPrice,
        'chukuGroups' => $chukuGroups,
        'chukuTotalCpnum' => $chukuTotalCpnum,
        'chukuTotalJine' => $chukuTotalJine,
        'chukuAvgPrice' => $chukuAvgPrice,
        'today' => $today,
        'selectedDate' => $selectedDate,
        'selectedType' => $typeParam, // 当前选中的筛选类型
        'typeOptions' => $typeOptions, // 筛选下拉框选项
        'ckuNameMap' => array_column($ckuConfig, 'name', 'key') // cku->名称映射
    ]);
    
    return $this->fetch();
}


public function sguige()
{
    // 关联父表bl_ddcg并筛选cku不等于5的数据
    $dataggx = Db::name('bl_cpcg')
        ->alias('c')
        // 关联父表（子表fid对应父表id）
        ->join('bl_ddcg d', 'c.fid = d.id', 'INNER')
        ->field('c.csl, c.cpname, c.id, c.djia')
        // 筛选父表cku不等于5的数据
        ->where('d.cku', '<>', 3)//加工领用
        ->where('d.cku', '<>', 4)//外委领用
        ->where('d.cku', '<>', 5)//材料
        ->where('d.cku', '<>', 9)//废料
        ->where('c.csl', '<>', '')  // 排除空字符串
        // 按原始csl和cpname分组去重（保持用户原始代码逻辑）
        ->group('c.csl, c.cpname')
        ->select();
    
    // 渲染视图
    return $this->fetch('sguige', ['dataggx' => $dataggx]);
}
    
public function sguigecl() { 
    // 1. 获取原始数据（关联父表过滤作废单据，排除空规格）
    $rawData = Db::name('bl_cpcg') 
        ->alias('c')
        ->join('bl_ddcg d', 'c.fid = d.id', 'LEFT')
        ->field('c.id, c.cpname, c.csl, c.djia, c.cpnum, d.cku')
        ->where('c.csl', '<>', '')
        ->where('c.csl', 'EXP', 'IS NOT NULL')
        ->where('d.zf', '<>', 1)
        ->where('d.cku', '<>', 6)
        ->select(); 

    // 2. 处理数据：计算各规格库存总量
    $processedData = [];
    $uniqueKeys = [];
    $stockTotals = []; // 存储各规格库存总量

    // 2.1 先计算每个规格的库存总量
    foreach ($rawData as $item) {
        // 处理csl字段（保留第二个*前内容）
        $cslParts = explode('*', $item['csl']);
        $processedCsl = count($cslParts) >= 2 ? implode('*', array_slice($cslParts, 0, 2)) : $item['csl'];
        
        // 生成规格唯一键
        $specKey = $item['cpname'] . '|' . $processedCsl;
        
        // 计算库存数量（入库为正，出库为负）
        $isInStock = in_array($item['cku'], [5, 7]);
        $cpnum = $isInStock ? (int)$item['cpnum'] : -(int)$item['cpnum'];
        
        // 累加到规格总量
        if (!isset($stockTotals[$specKey])) {
            $stockTotals[$specKey] = 0;
        }
        $stockTotals[$specKey] += $cpnum;
    }

    // 2.2 处理数据去重和排序
    foreach ($rawData as $item) {
        $cslParts = explode('*', $item['csl']);
        $processedCsl = count($cslParts) >= 2 ? implode('*', array_slice($cslParts, 0, 2)) : $item['csl'];
        $uniqueKey = $item['cpname'] . '|' . $processedCsl;
        
        if (!in_array($uniqueKey, $uniqueKeys)) {
            $uniqueKeys[] = $uniqueKey;
            $sortKey = floatval(explode('*', $processedCsl)[0]);
            
            // 获取当前规格的库存总量
            $totalStock = $stockTotals[$uniqueKey] ?? 0;
            
            $processedData[] = [ 
                'id' => $item['id'], 
                'cpname' => $item['cpname'], 
                'csl' => $processedCsl, 
                'djia' => $item['djia'],
                'sort_key' => $sortKey,
                'total_stock' => $totalStock // 新增库存总量字段
            ]; 
        } 
    } 

    // 3. 按第一个*前的数字排序
    usort($processedData, function($a, $b) {
        return $a['sort_key'] <=> $b['sort_key'];
    });

    // 4. 移除临时排序字段
    foreach ($processedData as &$item) {
        unset($item['sort_key']);
    }

    // 5. 传递数据到视图
    return $this->fetch('sguigecl', ['dataggx' => $processedData]); 
}
    
    public function ggdetail(){
 // 获取URL参数
        $csl = input('get.csl');
        $cpname = input('get.cpname');
        // 查询相同csl和cpname的所有记录
// 主从表关联查询（增加zf<>1过滤条件）
        $stockData = Db::name('bl_cpcg')
            ->alias('c')
            ->join('bl_ddcg d', 'c.fid = d.id', 'LEFT')
            ->where('c.csl', $csl)
            ->where('c.cpname', $cpname)
            ->where('d.zf', '<>', 1) // 过滤主表zf=1的数据
            ->field('c.cpnum, c.csl, c.danjias, c.jineall, d.cku, d.zf')
            ->select();
        
        // 处理数据（金额入库值为负数，出库为正数）
        $totalCpnum = 0;
        $totalJineall = 0;
        $processedData = [];
        
        foreach ($stockData as $item) {
            $isInStock = in_array($item['cku'], [5, 6, 7]);
            
            // 质量：入库正数，出库负数（原规则不变）
            $cpnum = $isInStock ? (int)$item['cpnum'] : -(int)$item['cpnum'];
            
            // 金额：入库负数，出库正数（新规则）
            $jineall = $isInStock ? -(int)$item['jineall'] : (int)$item['jineall'];
            
            // 累加合计
            $totalCpnum += $cpnum;
            $totalJineall += $jineall;
            
            $processedData[] = [
                'cpnum' => $cpnum,
                'csl' => $item['csl'],
                'danjias' => $item['danjias'],
                'jineall' => $jineall,
                'stock_type' => $isInStock ? '入库' : '出库',
                'cku' => $item['cku']
            ];
        }

        // 计算加权平均单价（金额合计 / 质量合计）
        $weightedAvg = 0;
        if ($totalCpnum != 0) {
            $weightedAvg = round($totalJineall / $totalCpnum, 2); // 保留两位小数
        }
        
        $this->assign([
            'stockData' => $processedData,
            'totalCpnum' => $totalCpnum,
            'totalJineall' => $totalJineall,
            'cpname' => $cpname,
            'csl' => $csl,
            'weightedAvg' => $weightedAvg // 新增加权平均单价参数
        ]);
        
        return $this->fetch('ggdetail');
    }


public function ggcldetail() { 
    // 获取URL参数 
    $csl = input('get.csl'); 
    $cpname = input('get.cpname'); 
    $batch = input('get.batch'); // 搜索参数变量
    
    // 处理特殊值"all"显示全部
    if($batch === 'all') {
        $batch = '';
    }

    // 处理csl匹配规则
    $query = Db::name('bl_cpcg')->alias('c')
        ->join('bl_ddcg d', 'c.fid = d.id', 'LEFT')
        ->where('d.zf', '<>', 1)
        ->where('c.cpname', $cpname)
        ->field('c.cpnum, c.csl, c.danjias, c.jineall, d.cku, c.djia, d.xdate, c.fid');

    $query->whereRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(c.csl, '*', 2), '*', -2) = ?", [$csl]);
    
    // 批号模糊搜索条件
    if (!empty($batch)) {
        $query->where('c.djia', 'like', "%&%{$batch}%");
    }

    $stockData = $query->select();
    
    // 按批号分组计算剩余量（变量重命名为$itemBatch）
    $batchSummary = [];
    foreach ($stockData as $index => $item) {
        $djia = $item['djia'] ?? '';
        $djiaParts = explode('&', $djia);
        
        $brand = $djiaParts[0] ?? '';
        $itemBatch = $djiaParts[1] ?? ("UNKNOWN_" . uniqid()); // 关键修改：使用$itemBatch避免覆盖
        
        $isInStock = in_array($item['cku'], [5,6,7,9,10]);
        $qty = $isInStock ? (int)$item['cpnum'] : -(int)$item['cpnum'];
        
        if (!isset($batchSummary[$itemBatch])) { // 使用$itemBatch
            $batchSummary[$itemBatch] = [
                'total_in' => 0,
                'total_out' => 0,
                'remaining' => 0,
                'brand' => $brand
            ];
        }
        
        if ($isInStock) {
            $batchSummary[$itemBatch]['total_in'] += $qty;
        } else {
            $batchSummary[$itemBatch]['total_out'] += abs($qty);
        }
        
        $batchSummary[$itemBatch]['remaining'] = $batchSummary[$itemBatch]['total_in'] - $batchSummary[$itemBatch]['total_out'];
    }
    
    // 数据处理（变量重命名为$itemBatch）
    $totalCpnum = 0; 
    $totalJineall = 0; 
    $totalInStock = 0; 
    $totalOutStock = 0; 
    $processedData = []; 
    
    foreach ($stockData as $item) { 
        $djia = $item['djia'] ?? '';
        $djiaParts = explode('&', $djia);
        $brand = $djiaParts[0] ?? '未知牌号';
        $itemBatch = $djiaParts[1] ?? '未知批号'; // 关键修改：使用$itemBatch避免覆盖
        
        $isInStock = in_array($item['cku'], [5,6,7,9,10]); 
        $cpnum = $isInStock ? (int)$item['cpnum'] : -(int)$item['cpnum']; 
        $jineall = $isInStock ? -(int)$item['jineall'] : (int)$item['jineall']; 
        
        $totalCpnum += $cpnum; 
        $totalJineall += $jineall; 
        
        if ($isInStock) {
            $totalInStock += $cpnum;
            $inStock = $cpnum;
            $outStock = 0;
        } else {
            $totalOutStock += abs($cpnum);
            $inStock = 0;
            $outStock = abs($cpnum);
        }

        $pihaoall = $itemBatch ? ($batchSummary[$itemBatch]['remaining'] ?? 0) : 0; // 使用$itemBatch

        $processedData[] = [ 
            'cpnum' => $cpnum, 
            'csl' => $item['csl'], 
            'danjias' => $item['danjias'], 
            'jineall' => $jineall, 
            'stock_type' => $isInStock ? '入库' : '出库', 
            'cku' => $item['cku'],
            'brand' => $brand,
            'batch' => $itemBatch, // 保持前端显示正确
            'in_stock' => $inStock,    
            'out_stock' => $outStock,  
            'pihaoall' => $pihaoall,   
            'xdate' => $item['xdate'],
            'fid'=>$item['fid']
        ]; 
    } 

    $weightedAvg = 0; 
    if ($totalCpnum != 0) { 
        $weightedAvg = round($totalJineall / $totalCpnum, 2); 
    } 

    $this->assign([ 
        'stockData' => $processedData, 
        'totalCpnum' => $totalCpnum, 
        'totalJineall' => $totalJineall, 
        'totalInStock' => $totalInStock,    
        'totalOutStock' => $totalOutStock,  
        'cpname' => $cpname, 
        'csl' => $csl, 
        'weightedAvg' => $weightedAvg,
        'batch' => $batch // 此时$batch仍为搜索参数值（空）
    ]); 

    return $this->fetch('ggcldetail'); 
}



public function phcldetail() { 
    // 获取URL参数 
    $csl = input('get.csl'); 
    $cpname = input('get.cpname'); 

    // 处理csl匹配规则
    //$hasSecondAsterisk = substr_count($csl, '*') >= 2;
    $query = Db::name('bl_cpcg')->alias('c')
        ->join('bl_ddcg d', 'c.fid = d.id', 'LEFT')
        ->where('d.zf', '<>', 1)
        ->where('c.cpname', $cpname)
        ->field('c.cpnum, c.csl, c.danjias, c.jineall, d.cku, c.djia');

    //if ($hasSecondAsterisk) {
        $query->whereRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(c.csl, '*', 2), '*', -2) = ?", [$csl]);
    //} else {
        //$query->where('c.csl', $csl);
    //}

    $stockData = $query->select();

    // 按批号分组处理数据
    $batchGroups = [];
    foreach ($stockData as $item) {
        // 拆分djia字段获取批号
        $djiaParts = explode('&', $item['djia']);
        $brand = $djiaParts[0] ?? '';
        $batch = $djiaParts[1] ?? '';
        
        // 确定分组键（批号）
        $groupKey = $batch ?: 'unknown';
        
        // 初始化分组
        if (!isset($batchGroups[$groupKey])) {
            $batchGroups[$groupKey] = [
                'brand' => $brand,
                'batch' => $batch,
                'items' => [],
                'totalCpnum' => 0,
                'totalJineall' => 0,
                'weightedAvg' => 0
            ];
        }
        
        // 处理单条数据
        $isInStock = in_array($item['cku'], [5,6,7,9,10]);
        $cpnum = $isInStock ? (int)$item['cpnum'] : -(int)$item['cpnum'];
        $jineall = $isInStock ? -(int)$item['jineall'] : (int)$item['jineall'];
        
        // 添加到分组
        $batchGroups[$groupKey]['items'][] = [
            'cpnum' => $cpnum,
            'csl' => $item['csl'],
            'danjias' => $item['danjias'],
            'jineall' => $jineall,
            'stock_type' => $isInStock ? '入库' : '出库',
            'cku' => $item['cku'],
            'brand' => $brand
        ];
        
        // 累加分组合计
        $batchGroups[$groupKey]['totalCpnum'] += $cpnum;
        $batchGroups[$groupKey]['totalJineall'] += $jineall;
    }
    
    // 计算每组加权平均单价
    foreach ($batchGroups as &$group) {
        if ($group['totalCpnum'] != 0) {
            $group['weightedAvg'] = round($group['totalJineall'] / $group['totalCpnum'], 2);
        }
    }
    
    // 准备页面数据
    $this->assign([
        'batchGroups' => array_values($batchGroups),  // 转换为数组便于模板遍历
        'cpname' => $cpname,
        'csl' => $csl
    ]);
    
    return $this->fetch('phcldetail');
}


// 显示库存商品（含规格）
// 显示库存商品（含规格搜索筛选）
public function mySku($id) { 
    $y = date('Y'); 
    $m = date('m'); 
    $myphone = cookie('xphone'); 
    $search = input('get.search', ''); // 获取搜索参数
    
    $uxv = Db::name('bl_user')->where('bm_phone', $myphone)->select(); 
    if (count($uxv) == 0) { 
        echo "alert('没有查到订单！');window.history.go(-1);"; 
        exit; 
    } 
    
    $ux = $uxv[0]['bm_id']; 
    $all = Db::name('bl_kcsp')->order('id', 'desc')->select(); 
    $categories = $this->getAllCategories(0, 0, $search); // 传入搜索参数
    
    $this->debugSpecsBalance($categories);
    
    return $this->fetch('mysku', [ 
        'all' => $all, 
        'categories' => $categories, 
        'id' => $id, 
        'search' => $search, // 传递搜索值到视图
        'xphone' => $myphone, 
        'm' => $m, 
        'y' => $y 
    ]); 
} 

private function getAllCategories($parentId = 0, $level = 0, $search = '') { 
    $indent = str_repeat(' ', $level); 
    $categories = Db::name('bl_kcsp')
        ->where('pid', $parentId)
        ->order('myorder', 'asc')
        ->select();
        
    $result = [];
    foreach ($categories as $category) {
        $category['indent'] = $indent;
        $category['level'] = $level;
        
        // 获取规格（含余量和搜索过滤）
        $category['specs'] = $this->getSpecsWithBalance($category['id'], $search);
        
        // 递归子分类
        $children = $this->getAllCategories($category['id'], $level + 1, $search);
        if (!empty($children)) {
            $category['children'] = $children;
        }
        
        // 如果有搜索值且没有规格和子分类，则不添加到结果
        if ($search && empty($category['specs']) && empty($category['children'])) {
            continue;
        }
        
        $result[] = $category;
    }
    
    return $result;
}

private function getSpecsWithBalance($categoryId, $search = '') {
    // 1. 规格查询（去重+过滤zf=1+搜索筛选）
    $query = Db::name('bl_cpcg')
        ->alias('c')
        ->join('bl_ddcg d', 'c.fid = d.id', 'left')
        ->where('c.cpid', $categoryId)
        ->where(function($query) {
            $query->where('d.zf', '<>', 1)->whereOr('d.zf', 'exp', 'IS NULL');
        });
    
    // 添加搜索过滤条件
    if ($search) {
        $query->where('c.csl', 'like', "%{$search}%");
    }
    
    $specs = $query->field('c.csl, SUM(c.cpnum) as total_cpnum')
                   ->group('c.csl')  // 按规格字段csl物理去重
                   ->select();
    
    // 2. 计算余量
    $validSpecs = [];
    foreach ($specs as $spec) {
        $spec['balance'] = 0;
        
        // 3. 计算余量（同样过滤zf=1）
        try {
            // 入库总量
            $inStock = Db::name('bl_cpcg')
                ->alias('c')
                ->join('bl_ddcg d', 'c.fid = d.id', 'left')
                ->where('c.csl', $spec['csl'])
                ->where('c.cpid', $categoryId)
                ->where('d.cku', 'in', [5,6,7,9,10])
                ->where(function($query) {
                    $query->where('d.zf', '<>', 1)->whereOr('d.zf', 'exp', 'IS NULL');
                })
                ->sum('c.cpnum');
                
            // 出库总量
            $outStock = Db::name('bl_cpcg')
                ->alias('c')
                ->join('bl_ddcg d', 'c.fid = d.id', 'left')
                ->where('c.csl', $spec['csl'])
                ->where('c.cpid', $categoryId)
                ->where('d.cku', 'not in', [5,6,7,9,10])
                ->where(function($query) {
                    $query->where('d.zf', '<>', 1)->whereOr('d.zf', 'exp', 'IS NULL');
                })
                ->sum('c.cpnum');
                
            $spec['balance'] = (float)($inStock ?: 0) - (float)($outStock ?: 0);
        } catch (\Exception $e) {
            $spec['balance'] = (float)$spec['total_cpnum'];
        }
        
        $validSpecs[] = $spec;
    }
    
    return $validSpecs;
}

// 调试：验证所有规格是否包含balance字段
private function debugSpecsBalance($categories) {
    foreach ($categories as $category) {
        if (!empty($category['specs'])) {
            foreach ($category['specs'] as $spec) {
                if (!isset($spec['balance'])) {
                    file_put_contents('balance_debug.log', "Missing balance in category {$category['id']}, spec: {$spec['csl']}\n", FILE_APPEND);
                }
            }
        }
        
        if (!empty($category['children'])) {
            $this->debugSpecsBalance($category['children']);
        }
    }
}
    
    
    
}
