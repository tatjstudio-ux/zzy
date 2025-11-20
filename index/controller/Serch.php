<?php
namespace app\index\controller;//搜索
use think\Controller;
use think\Db;
use think\facade\Request;
use phpmailer\phpmailer;

class Serch extends Controller
{
    public function rksrch($id,$year)//入库汇总
    { 
        $allkg=0;
        $cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        $xname=$cpnm[0]["name"];
        //$cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        //echo $cpnm[0]["name"];
		//$all=Db::name('bl_clscp')->where('id',$id)->select();
		//print_r($all);
		for($i=12;$i>0;$i--){
		  if($i<10){
            $mt="0".$i;
          }else{
            $mt=$i;
          }
            $mydate=$year."-".$mt;
            $xall=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$xname)->where("zf",0)->select();
		    $cont=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$xname)->where("zf",0)->count();
		        for($x=0;$x<$cont;$x++){
		            $muser=Db::name('bl_cp')->where("fid",$xall[$x]['id'])->select();
		            foreach ($muser as $musers) {
                        $allkg=$allkg+intval($musers['cpnum']);
                    }
		          
		        }
		    $m[$i]['m']=$i;
		    $m[$i]['c']=$cont;
		}
		//print_r($m);
		return $this->fetch('serch/rksrch',['id'=>$id,'m'=>$m,'year'=>$year,'xname'=>$xname,'allkg'=>$allkg]);
    }
   public function rkmlist($id,$m,$y,$bs)//入库汇总详细
    {
        $cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        $xname=$cpnm[0]["name"];
            if($m<10){
            $m="0".$m;
            }
            $mydate=$y."-".$m;
            //echo $mydate;
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$xname)->where("zf",0)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();

			$db2 = Db::name('bl_cp')->select();
			return $this->fetch('serch/rkmlist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'bs'=>$bs,'y'=>$y,'m'=>$m]);
			}
	
    }
    
    public function cksrch($id,$year)//出库汇总
    { 
        $allkg=0;
        $cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        $xname=$cpnm[0]["name"];
        //$cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        //echo $cpnm[0]["name"];
		//$all=Db::name('bl_clscp')->where('id',$id)->select();
		//print_r($all);
		for($i=12;$i>0;$i--){
		  if($i<10){
            $mt="0".$i;
          }else{
            $mt=$i;
          }
            $mydate=$year."-".$mt;
            $xall=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$xname)->where("zf",0)->select();
		    $cont=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$xname)->where("zf",0)->count();
		        for($x=0;$x<$cont;$x++){
		            $muser=Db::name('bl_cp')->where("fid",$xall[$x]['id'])->select();
		            foreach ($muser as $musers) {
                        $allkg=$allkg+intval($musers['cpnum']);
                    }
		          
		        }
		    $m[$i]['m']=$i;
		    $m[$i]['c']=$cont;
		}
		//print_r($m);
		return $this->fetch('serch/cksrch',['id'=>$id,'m'=>$m,'year'=>$year,'xname'=>$xname,'allkg'=>$allkg]);
    }
    
    
   public function ckmlist($id,$m,$y,$bs)//出库汇总明细
    {
        $cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        $xname=$cpnm[0]["name"];
            if($m<10){
            $m="0".$m;
            }
            $mydate=$y."-".$m;
            //echo $mydate;
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_dd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$xname)->where("zf",0)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();

			$db2 = Db::name('bl_cp')->select();
			return $this->fetch('serch/ckmlist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'bs'=>$bs,'y'=>$y,'m'=>$m]);
			}
	
    }
    
    public function wwcksrch($id,$year)//外委出库汇总
    { 
        $allkg=0;
        $cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        $xname=$cpnm[0]["name"];
        //$cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        //echo $cpnm[0]["name"];
		//$all=Db::name('bl_clscp')->where('id',$id)->select();
		//print_r($all);
		for($i=12;$i>0;$i--){
		  if($i<10){
            $mt="0".$i;
          }else{
            $mt=$i;
          }
            $mydate=$year."-".$mt;
            $xall=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$xname)->where("zf",0)->select();
		    $cont=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$xname)->where("zf",0)->count();
		        for($x=0;$x<$cont;$x++){
		            $muser=Db::name('bl_wwcp')->where("fid",$xall[$x]['id'])->select();
		            foreach ($muser as $musers) {
                        $allkg=$allkg+intval($musers['cpnum']);
                    }
		          
		        }
		    $m[$i]['m']=$i;
		    $m[$i]['c']=$cont;
		}
		//print_r($m);
		return $this->fetch('serch/wwcksrch',['id'=>$id,'m'=>$m,'year'=>$year,'xname'=>$xname,'allkg'=>$allkg]);
    }
    
    
   public function wwckmlist($id,$m,$y,$bs)//外委出库汇总明细
    {
        $cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        $xname=$cpnm[0]["name"];
            if($m<10){
            $m="0".$m;
            }
            $mydate=$y."-".$m;
            //echo $mydate;
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",2)->where("myname",$xname)->where("zf",0)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();

			$db2 = Db::name('bl_wwcp')->select();
			return $this->fetch('serch/wwckmlist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'bs'=>$bs,'y'=>$y,'m'=>$m]);
			}
	
    }


    public function wwrksrch($id,$year)//外委出库汇总
    { 
        $allkg=0;
        $cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        $xname=$cpnm[0]["name"];
        //$cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        //echo $cpnm[0]["name"];
		//$all=Db::name('bl_clscp')->where('id',$id)->select();
		//print_r($all);
		for($i=12;$i>0;$i--){
		  if($i<10){
            $mt="0".$i;
          }else{
            $mt=$i;
          }
            $mydate=$year."-".$mt;
            $xall=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$xname)->where("zf",0)->select();
		    $cont=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$xname)->where("zf",0)->count();
		        for($x=0;$x<$cont;$x++){
		            $muser=Db::name('bl_wwcp')->where("fid",$xall[$x]['id'])->select();
		            foreach ($muser as $musers) {
                        $allkg=$allkg+intval($musers['cpnum']);
                    }
		          
		        }
		    $m[$i]['m']=$i;
		    $m[$i]['c']=$cont;
		}
		//print_r($m);
		return $this->fetch('serch/wwrksrch',['id'=>$id,'m'=>$m,'year'=>$year,'xname'=>$xname,'allkg'=>$allkg]);
    }
    
    
   public function wwrkmlist($id,$m,$y,$bs)//外委入库汇总明细
    {
        $cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        $xname=$cpnm[0]["name"];
            if($m<10){
            $m="0".$m;
            }
            $mydate=$y."-".$m;
            //echo $mydate;
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_wwdd')->where("xdate like '{$mydate}%'")->where("cku",1)->where("myname",$xname)->where("zf",0)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();

			$db2 = Db::name('bl_wwcp')->select();
			return $this->fetch('serch/wwrkmlist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'bs'=>$bs,'y'=>$y,'m'=>$m]);
			}
	
    }

   public function serch($id,$kw)
    {
            $all=[];
            $allww=[];
            $myphone=cookie('xphone');
            $cpcls=Db::name('bl_clscp')->select();
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
            $where['djia'] = array('like', '%'.$kw.'%');
            $list = Db::name('bl_cp')->where($where)->distinct(true)->field('fid')->select();
            $listww = Db::name('bl_wwcp')->where($where)->distinct(true)->field('fid')->select();
            
            for($i=0;$i<count($list);$i++){
            $allv=Db::name('bl_dd')->where('id',$list[$i]['fid'])->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
             $all[]=$allv[0];
            }
            
            for($j=0;$j<count($listww);$j++){
            $allvww=Db::name('bl_wwdd')->where('id',$listww[$j]['fid'])->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
             $allww[]=$allvww[0];
            }

             //print_r($all);
			$db2 = Db::name('bl_cp')->select();
			$db2ww = Db::name('bl_wwcp')->select();
			return $this->fetch('serch/serch',['all'=>$all,'allww'=>$allww,'db2'=>$db2,'db2ww'=>$db2ww,'id'=>$id,'xphone'=>$myphone,'kw'=>$kw,'cpcls'=>$cpcls]);
			}
	
    }
    
   public function rprint($idx,$id,$y,$m)
    { 
			$db = Db::name('bl_dd')->where('id',$idx);
			$idb = $db->update(['mprint'=>0]); 
			echo "<script>alert('补打成功！');window.history.back();</script>";
	
    }
    
   public function cgrkmlist($id,$m,$y,$bs)//材料入库
    {
        $cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        $xname=$cpnm[0]["name"];
            if($m<10){
            $m="0".$m;
            }
            $mydate=$y."-".$m;
            //echo $mydate;
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",5)->where("myname",$xname)->where("zf",0)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();

			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('serch/cgrkmlist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'bs'=>$bs,'y'=>$y,'m'=>$m]);
			}
	
    }
    
   public function cprkmlist($id,$m,$y,$bs)//材料入库
    {
        $cpnm=Db::name('bl_cpnm')->where('id',$id)->select();
        $xname=$cpnm[0]["name"];
            if($m<10){
            $m="0".$m;
            }
            $mydate=$y."-".$m;
            //echo $mydate;
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
			$all=Db::name('bl_ddcg')->where("xdate like '{$mydate}%'")->where("cku",6)->where("myname",$xname)->where("zf",0)->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();

			$db2 = Db::name('bl_cpcg')->select();
			return $this->fetch('serch/cprkmlist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'bs'=>$bs,'y'=>$y,'m'=>$m]);
			}
	
    }
    
}
