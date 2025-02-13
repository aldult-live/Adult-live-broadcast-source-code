<?php
if (!session_id()) session_start();
class Model_Home extends PhalApi_Model_NotORM {
    protected $live_fields='uid,title,city,stream,pull,thumb,isvideo,type,type_val,goodnum,anyway,starttime,isshop,game_action,isrecommend,live_type';
     
    
	/* 轮播 */
	public function getSlide($where){

		$rs=DI()->notorm->slide_item
			->select("image as slide_pic,url as slide_url")
			->where($where)
			->order("list_order asc")
			->fetchAll();
		foreach($rs as $k=>$v){
			$rs[$k]['slide_pic']=get_upload_path($v['slide_pic']);
		}				

		return $rs;
	}

	/* 热门主播 */
    public function getHot($p) {
        if($p<1){
            $p=1;
        }
		$pnum=50;
		$where=" islive= '1' and live_type=0";
        
        if($p==1){
			$_SESSION['hot_starttime']=time();
			$hotvotes=DI()->notorm->live->where("islive= '1' and live_type=0")->max('hotvotes');
			$_SESSION['hot_hotvotes']=$hotvotes;
		}
        
		if($p>1){
			$endtime=$_SESSION['hot_starttime'];
            if($endtime){
                $where.=" and starttime < {$endtime}";
            }

            $hotvotes=$_SESSION['hot_hotvotes'];

            if($hotvotes>0){
                $where.=" and hotvotes < {$hotvotes}";
            }else{
            	$where.=" and hotvotes = 0";
            }

		}


		/*var_dump($where);
		die;*/
	
		
		$result=DI()->notorm->live
                    ->select($this->live_fields.',hotvotes')
                    ->where($where)
                    ->order('isrecommend desc,recommend_time desc,hotvotes desc,starttime desc')
                    ->limit(0,$pnum)
                    ->fetchAll();
                    
		foreach($result as $k=>$v){
			$v=handleLive($v);     
            $result[$k]=$v;
		}	
		if($result){
			$last=end($result);
			$_SESSION['hot_starttime']=$last['starttime'];
			$_SESSION['hot_hotvotes']=$last['hotvotes'];
		}
		return $result;
    }
	
	
	/* 聊天室 */
    public function getRecommendVoiceLive() {

		$where=" islive= '1' and live_type=1";

		$result=DI()->notorm->live
                    ->select($this->live_fields.',hotvotes')
                    ->where($where)
                    ->order('isrecommend desc,recommend_time desc,hotvotes desc,starttime desc')
                    ->limit(0,2)
                    ->fetchAll();
                    
		foreach($result as $k=>$v){
			$v=handleLive($v);     
            $result[$k]=$v;
		}	

		return $result;
    }
	
	
	
	
		/* 关注列表 */
    public function getFollow($uid,$live_type,$p) {
        $rs=array(
            'title'=>'你关注的主播没有开播',
            'des'=>'赶快去看看其他主播的直播吧',
            'list'=>array(),
        );
        if($p<1){
            $p=1;
        }
		$result=array();
		$pnum=50;
		$start=($p-1)*$pnum;
		
		$touid=DI()->notorm->user_attention
				->select("touid")
				->where("uid=?",$uid)
				->fetchAll();
		
				
		if(!$touid){
            return $rs;
        }
        
        $rs['title']='你关注的主播没有开播';
        $rs['des']='赶快去看看其他主播的直播吧';
        $where=" islive='1' and live_type=".$live_type;		
        			
        if($p!=1){
            $endtime=$_SESSION['follow_starttime'];
            if($endtime){
                $start=0;
                $where.=" and starttime < {$endtime}";
            }
            
        }	
    
        $touids=array_column($touid,"touid");
        $touidss=implode(",",$touids);
        $where.=" and uid in ({$touidss})";
        $result=DI()->notorm->live
                ->select($this->live_fields)
                ->where($where)
                ->order("starttime desc")
                ->limit(0,$pnum)
                ->fetchAll();
	
		foreach($result as $k=>$v){
            
			$v=handleLive($v);
            
            $result[$k]=$v;
		}	

		if($result){
			$last=end($result);
			$_SESSION['follow_starttime']=$last['starttime'];
		}
        
        $rs['list']=$result;

		return $rs;					
    }
		
		
		/* 搜索 */
    public function search($uid,$key,$p) {
        if($p<1){
            $p=1;
        }
		$pnum=50;
		$start=($p-1)*$pnum;
		$where=' user_type="2" and ( id=? or user_nickname like ?  or goodnum like ? ) and id!=?';
		if($p!=1){
			$id=$_SESSION['search'];
            if($id){
                $where.=" and id < {$id}";
            }
		}
		
		$result=DI()->notorm->user
				->select("id,user_nickname,avatar,sex,signature,consumption,votestotal")
				->where($where,$key,'%'.$key.'%','%'.$key.'%',$uid)
				->order("id desc")
				->limit($start,$pnum)
				->fetchAll();
		foreach($result as $k=>$v){
			$v['level']=(string)getLevel($v['consumption']);
			$v['level_anchor']=(string)getLevelAnchor($v['votestotal']);
			$v['isattention']=(string)isAttention($uid,$v['id']);
			$v['avatar']=get_upload_path($v['avatar']);
			unset($v['consumption']);
            
            $result[$k]=$v;
		}				
		
		if($result){
			$last=end($result);
			$_SESSION['search']=$last['id'];
		}
		
		return $result;
    }
	
	/* 附近 */
    public function getNearby($lng,$lat,$live_type,$p) {
        if($p<1){
            $p=1;
        }
		$pnum=50;
		$start=($p-1)*$pnum;
		$where=" islive='1' and lng!='' and lat!='' and live_type='{$live_type}'";
		
		$result=DI()->notorm->live
				->select($this->live_fields.",getDistance('{$lat}','{$lng}',lat,lng) as distance,province")
				->where($where)
                ->order("distance asc")
                ->limit($start,$pnum)
				->fetchAll();	
		foreach($result as $k=>$v){
            
			$v=handleLive($v);
            
            if($v['distance']>1000){
                $v['distance']=1000;
            }
            $v['distance']=$v['distance'].'km';

            $result[$k]=$v;
		}
		
		return $result;
    }


	/* 推荐 */
	public function getRecommend(){

		$result=DI()->notorm->user
				->select("id,user_nickname,avatar,avatar_thumb")
				->where("isrecommend='1'")
				->order("recommend_time desc,votestotal desc")
				->limit(0,12)
				->fetchAll();
		foreach($result as $k=>$v){
			$v['avatar']=get_upload_path($v['avatar']);
			$v['avatar_thumb']=get_upload_path($v['avatar_thumb']);
			$fans=getFans($v['id']);
			$v['fans']='粉丝 · '.$fans;
            
            $result[$k]=$v;
		}
		return  $result;
	}
	/* 关注推荐 */
	public function attentRecommend($uid,$touids){

        $users=preg_split('/,|，/',$touids);
		foreach($users as $k=>$v){
			$touid=$v;

			if($touid && !isAttention($uid,$touid)){
				DI()->notorm->user_black
					->where('uid=? and touid=?',$uid,$touid)
					->delete();
				DI()->notorm->user_attention
					->insert(array("uid"=>$uid,"touid"=>$touid));
			}
			
		}
		return 1;
	}

	/*获取收益排行榜*/
	public function profitList($uid,$type,$p){
        if($p<1){
            $p=1;
        }
		$pnum=50;
		$start=($p-1)*$pnum;

		switch ($type) {
			case 'day':
				//获取今天开始结束时间
				$dayStart=strtotime(date("Y-m-d"));
				$dayEnd=strtotime(date("Y-m-d 23:59:59"));
				$where=" addtime >={$dayStart} and addtime<={$dayEnd} and ";

			break;

			case 'week':
                $w=date('w'); 
                //获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
                $first=1;
                //周一
                $week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
                $week_start=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

                //本周结束日期 
                //周天
                $week_end=strtotime("{$week} +1 week")-1;
                
				$where=" addtime >={$week_start} and addtime<={$week_end} and ";

			break;

			case 'month':
                //本月第一天
                $month=date('Y-m-d',strtotime(date("Ym").'01'));
                $month_start=strtotime(date("Ym").'01');

                //本月最后一天
                $month_end=strtotime("{$month} +1 month")-1;

				$where=" addtime >={$month_start} and addtime<={$month_end} and ";

			break;

			case 'total':
				$where=" ";
			break;
			
			default:
				//获取今天开始结束时间
				$dayStart=strtotime(date("Y-m-d"));
				$dayEnd=strtotime(date("Y-m-d 23:59:59"));
				$where=" addtime >={$dayStart} and addtime<={$dayEnd} and ";
			break;
		}




		$where.=" action in (1,2)";
		
		$result=DI()->notorm->user_voterecord
            ->select('sum(total) as totalcoin, uid')
            ->where($where)
            ->group('uid')
            ->order('totalcoin desc')
            ->limit($start,$pnum)
            ->fetchAll();

		foreach ($result as $k => $v) {
            $userinfo=getUserInfo($v['uid']);
            $v['avatar']=$userinfo['avatar'];
			$v['avatar_thumb']=$userinfo['avatar_thumb'];
			$v['user_nickname']=$userinfo['user_nickname'];
			$v['sex']=$userinfo['sex'];
			$v['level']=$userinfo['level'];
			$v['level_anchor']=$userinfo['level_anchor'];
			$v['totalcoin']=(string)intval($v['totalcoin']);
            
            $v['isAttention']=isAttention($uid,$v['uid']);//判断当前用户是否关注了该主播
            
            $result[$k]=$v;
		}

		return $result;
	}



	/*获取消费排行榜*/
	public function consumeList($uid,$type,$p){
        if($p<1){
            $p=1;
        }
		$pnum=50;
		$start=($p-1)*$pnum;

		switch ($type) {
			case 'day':
				//获取今天开始结束时间
				$dayStart=strtotime(date("Y-m-d"));
				$dayEnd=strtotime(date("Y-m-d 23:59:59"));
				$where=" addtime >={$dayStart} and addtime<={$dayEnd} and ";

			break;
            
            case 'week':
                $w=date('w'); 
                //获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
                $first=1;
                //周一
                $week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
                $week_start=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

                //本周结束日期 
                //周天
                $week_end=strtotime("{$week} +1 week")-1;
                
				$where=" addtime >={$week_start} and addtime<={$week_end} and ";

			break;

			case 'month':
                //本月第一天
                $month=date('Y-m-d',strtotime(date("Ym").'01'));
                $month_start=strtotime(date("Ym").'01');

                //本月最后一天
                $month_end=strtotime("{$month} +1 month")-1;

				$where=" addtime >={$month_start} and addtime<={$month_end} and ";

			break;

			case 'total':
				$where=" ";
			break;
			
			default:
				//获取今天开始结束时间
				$dayStart=strtotime(date("Y-m-d"));
				$dayEnd=strtotime(date("Y-m-d 23:59:59"));
				$where=" addtime >={$dayStart} and addtime<={$dayEnd} and ";
			break;
		}

		$where.=" type=0 and action in ('1','2')";
		
        $result=DI()->notorm->user_coinrecord
            ->select('sum(totalcoin) as totalcoin, uid')
            ->where($where)
            ->group('uid')
            ->order('totalcoin desc')
            ->limit($start,$pnum)
            ->fetchAll();

		foreach ($result as $k => $v) {
            $userinfo=getUserInfo($v['uid']);
            $v['avatar']=$userinfo['avatar'];
			$v['avatar_thumb']=$userinfo['avatar_thumb'];
			$v['user_nickname']=$userinfo['user_nickname'];
			$v['sex']=$userinfo['sex'];
			$v['level']=$userinfo['level'];
			$v['level_anchor']=$userinfo['level_anchor'];
            
            $v['isAttention']=isAttention($uid,$v['uid']);//判断当前用户是否关注了该主播
            
            $result[$k]=$v;
		}

		return $result;
	}
    
    /* 分类下直播 */
    public function getClassLive($liveclassid,$p) {
        if($p<1){
            $p=1;
        }
		$pnum=50;
		//$start=($p-1)*$pnum;
		$start=0;
		$where=" islive='1' and liveclassid={$liveclassid} and live_type=0";
        
		if($p!=1){
			$endtime=$_SESSION['getClassLive_starttime'];
            if($endtime){
                $where.=" and starttime < {$endtime}";
            }
			
		}
		$last_starttime=0;
		$result=DI()->notorm->live
				->select($this->live_fields)
				->where($where)
				->order("starttime desc")
				->limit(0,$pnum)
				->fetchAll();	
		foreach($result as $k=>$v){
			$v=handleLive($v);
            $result[$k]=$v;
		}		
		if($result){
            $last=end($result);
			$_SESSION['getClassLive_starttime']=$last['starttime'];
		}

		return $result;
    }
	
	/*商城-商品列表*/
	public function getShopList($p){
		$order="isrecom desc,sale_nums desc,id desc";
		
		$where=[];
        $where['status']=1;

		$list=handleGoodsList($where,$p,$order);
        foreach ($list as $k => $v) {
           unset($list[$k]['specs']);
        }

        return $list;
	}
    
	
	/*商城-获取分类下的商品*/
	public function getShopClassList($shopclassid,$sell,$price,$isnew,$p){
		$order="";  //排序
		$where="status=1 and three_classid={$shopclassid} ";
		if($isnew){
			//获取今天开始结束时间
			$dayStart=strtotime(date('Y-m-d',strtotime('-2 day')));
			$dayEnd=strtotime(date("Y-m-d 23:59:59"));
			$where.="and addtime >={$dayStart} and addtime<={$dayEnd}";

		}
		
		
		
		if($sell!=''){
			$order.="sale_nums {$sell},";
		}else if($price!=''){
			$order.="low_price {$price},";
		}
		
		
		$order.="id desc";
		$list=handleGoodsList($where,$p,$order);
        foreach ($list as $k => $v) {
           unset($list[$k]['specs']);
        }

        return $list;
	}
	
	
	public function searchShop($key,$sell,$price,$isnew,$p) {
		
		$order="";  //排序
		$where="status=1 and name like '%{$key}%' ";
		if($isnew){
			//获取今天开始结束时间
			$dayStart=strtotime(date('Y-m-d',strtotime('-2 day')));
			$dayEnd=strtotime(date("Y-m-d 23:59:59"));
			$where.="and addtime >={$dayStart} and addtime<={$dayEnd}";

		}

		if($sell!=''){
			$order.="sale_nums {$sell},";
		}else if($price!=''){
			$order.="low_price {$price},";
		}
		
		
		$order.="id desc";
		$list=handleGoodsList($where,$p,$order);
        foreach ($list as $k => $v) {
           unset($list[$k]['specs']);
        }

        return $list;
    }

    //获取语音聊天室列表
    public function getVoiceLiveList($p){
    	if($p<1){
            $p=1;
        }
		$pnum=50;
		$where=" islive= '1' and ishot='1' and live_type=1";
        
        if($p==1){
			$_SESSION['voicelive_starttime']=time();
		}
        
		if($p>1){
			$recommend_time=$_SESSION['recommend_time'];

			/*var_dump($recommend_time);
			die;*/

            if($recommend_time){
                $where.=" and recommend_time < {$recommend_time}";
            }else{
				$hotvotes=$_SESSION['hotvotes'];
				if($hotvotes){
					$where.=" and hotvotes < {$hotvotes}";
				}else{
					$endtime=$_SESSION['voicelive_starttime'];
					if($endtime){
						$where.=" and starttime < {$endtime}";
					}
				}
			}

			$where.=" and isvideo=0";
			
		}


		/*var_dump($where);
		die;*/
		
		$result=DI()->notorm->live
            ->select($this->live_fields.',hotvotes,recommend_time')
            ->where($where)
            ->order('isrecommend desc,recommend_time desc,hotvotes desc,starttime desc')
            ->limit(0,$pnum)
            ->fetchAll();
                    
		foreach($result as $k=>$v){
			$v=handleLive($v);     
            $result[$k]=$v;
		}

		/*var_dump($result);
		die;*/


		if($result){
			$last=end($result);

			/*var_dump($last);
			die;*/

			$_SESSION['recommend_time']=$last['recommend_time'];
			$_SESSION['hotvotes']=$last['hotvotes'];
			$_SESSION['voicelive_starttime']=$last['starttime'];
		}

		return $result;
    }

    //获取首页推荐的我的关注主播列表
    public function getRecommendAttentLive($uid){

    	$rs=array(
    		'list'=>[],
    		'nums'=>'0'
    	);

    	$touid=DI()->notorm->user_attention
				->select("touid")
				->where("uid=?",$uid)
				->fetchAll();
		
				
		if(!$touid){
            return $rs;
        }
        
        $where=" islive='1'";
    
        $touids=array_column($touid,"touid");
        $touidss=implode(",",$touids);
        $where.=" and uid in ({$touidss})";
        $result=DI()->notorm->live
            ->select($this->live_fields)
            ->where($where)
            ->order("starttime desc")
            ->fetchAll();

        $list=[];

        if(!empty($result)){
        	foreach($result as $k=>$v){
            
				$v=handleLive($v);
	            
	            $result[$k]=$v;
			}
        	$list = array_slice($result,0,3);
        }


        $rs['list']=$list;
        $rs['nums'] =(string)count($result);

		return $rs;
    }
}
