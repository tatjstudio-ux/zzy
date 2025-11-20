<?php
namespace app\index\controller;//外围搜索
use think\Controller;
use think\Db;
use think\facade\Request;
use phpmailer\phpmailer;

class Wwserch extends Controller
{
    public function rksrch($id,$year)
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
		return $this->fetch('wwserch/rksrch',['id'=>$id,'m'=>$m,'year'=>$year,'xname'=>$xname,'allkg'=>$allkg]);
    }
   public function rkmlist($id,$m,$y,$bs)
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
			return $this->fetch('wwserch/rkmlist',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'bs'=>$bs]);
			}
	
    }
    
    
   public function serch($id,$kw)
    {
            $all=[];
            $myphone=cookie('xphone');
            $uxv=Db::name('bl_user')->where('bm_phone',$myphone)->select();
            if(count($uxv)==0){
			    echo "<script>alert('没有查到订单！');window.history.go(-1);</script>";
			}else{
            $ux=$uxv[0]['bm_id'];
            $where['djia'] = array('like', '%'.$kw.'%');
            $list = Db::name('bl_cp')->where($where)->distinct(true)->field('fid')->select();
            
            for($i=0;$i<count($list);$i++){
            $allv=Db::name('bl_dd')->where('id',$list[$i]['fid'])->order([
                 'xdate' => 'desc',
                 'id'  => 'desc'
             ])
             ->select();
             $all[]=$allv[0];
            }
            

             //print_r($all);
			$db2 = Db::name('bl_cp')->select();
			return $this->fetch('wwserch/serch',['all'=>$all,'db2'=>$db2,'id'=>$id,'xphone'=>$myphone,'kw'=>$kw]);
			}
	
    }
    
   public function rprint($idx,$id,$y,$m)
    { 
			$db = Db::name('bl_wwdd')->where('id',$idx);
			$idb = $db->update(['mprint'=>0]); 
			echo "<script>alert('补打成功！');window.history.back();</script>";
	
    }
    
}
