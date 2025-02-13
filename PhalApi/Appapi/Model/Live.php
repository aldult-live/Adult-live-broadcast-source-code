
<?php

class Model_Live extends PhalApi_Model_NotORM {
	/* 创建房间 */
	public function createRoom($uid,$data) {
        
        /* 获取主播 推荐、热门 */
        $data['ishot']='0';
        $data['isrecommend']='0';
        $userinfo=DI()->notorm->user
					->select("ishot,isrecommend")
					->where('id=?',$uid)
					->fetchOne();
        if($userinfo){
            $data['ishot']=$userinfo['ishot'];
            $data['isrecommend']=$userinfo['isrecommend'];
        }
		$isexist=DI()->notorm->live
					->select("uid")
					->where('uid=?',$uid)
					->fetchOne();


		if($isexist){
            /* 判断存在的记录是否为直播状态 */
            if($isexist['isvideo']==0 && $isexist['islive']==1){
                /* 若存在未关闭的直播 关闭直播 */
                $this->stopRoom($uid,$isexist['stream']);
                
                /* 加入 */
                $rs=DI()->notorm->live->insert($data);
				
            }else{

                /* 更新 */
                $rs=DI()->notorm->live->where('uid = ?', $uid)->update($data);
            }
		}else{
			/* 加入 */
			$rs=DI()->notorm->live->insert($data);
			
		}


		if(!$rs){
			return $rs;
		}

		$configpri=getConfigPri();
		$dailytask_switch=$configpri['dailytask_switch'];

		if($dailytask_switch){
			//开播直播计时---用于每日任务--记录主播开播
			$key='open_live_daily_tasks_'.$uid;
			$room_time=time();
			setcaches($key,$room_time);
		}
		return 1;
	}
	
	/* 主播粉丝 */
    public function getFansIds($touid) {

		$list=DI()->notorm->user_attention
			->select("uid")
			->where('touid=?',$touid)
			->fetchAll();
        return $list;
    }	
	
	/* 修改直播状态 */
	public function changeLive($uid,$stream,$status){

		if($status==1){
            $info=DI()->notorm->live
                    ->select("*")
					->where('uid=? and stream=?',$uid,$stream)
                    ->fetchOne();

            if($info){
                DI()->notorm->live
					->where('uid=? and stream=?',$uid,$stream)
					->update(array("islive"=>1));
            }
			return $info;
		}else{
			$this->stopRoom($uid,$stream);
			return 1;
		}
	}
	
	/* 修改直播状态 */
	public function changeLiveType($uid,$stream,$data){
		return DI()->notorm->live
				->where('uid=? and stream=?',$uid,$stream)
				->update( $data );
	}
	
	/* 关播 */
	public function stopRoom($uid,$stream) {

		$info=DI()->notorm->live
				->select("uid,showid,starttime,title,province,city,stream,lng,lat,type,type_val,liveclassid,live_type,deviceinfo")
				->where('uid=? and stream=? and islive="1"',$uid,$stream)
				->fetchOne();
//        file_put_contents(API_ROOT.'/../log/phalapi/live_stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 info:'.json_encode($info)."\r\n",FILE_APPEND);
		if($info){
			$isdel=DI()->notorm->live
				->where('uid=?',$uid)
				->delete();
            if(!$isdel){
                return 0;
            }
			$nowtime=time();
			$info['endtime']=$nowtime;
			$info['time']=date("Y-m-d",$info['showid']);
			$votes=DI()->notorm->user_voterecord
				->where('uid =? and showid=?',$uid,$info['showid'])
				->sum('total');
			$info['votes']=0;
			if($votes){
				$info['votes']=$votes;
			}
			$nums=DI()->redis->zCard('user_'.$stream);			
			DI()->redis->hDel("livelist",$uid);
			DI()->redis->del($uid.'_zombie');
			DI()->redis->del($uid.'_zombie_uid');
			DI()->redis->del('attention_'.$uid);
			DI()->redis->del('user_'.$stream);
			$info['nums']=$nums;			
			$result=DI()->notorm->live_record->insert($info);	
//            file_put_contents(API_ROOT.'/../log/phalapi/live_stopRoom_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result['id'])."\r\n",FILE_APPEND);

            /* 解除本场禁言 */
            $list2=DI()->notorm->live_shut
                ->select('uid')
                ->where('liveuid=? and showid!=0',$uid)
                ->fetchAll();
            DI()->notorm->live_shut->where('liveuid=? and showid!=0',$uid)->delete();
            
            foreach($list2 as $k=>$v){
                DI()->redis -> hDel($uid . 'shutup',$v['uid']);
            }
            
            /* 游戏处理 */
			$game=DI()->notorm->game
				->select("*")
				->where('stream=? and liveuid=? and state=?',$stream,$uid,"0")
				->fetchOne();
			$total=array();
			if($game){
				$total=DI()->notorm->gamerecord
					->select("uid,sum(coin_1 + coin_2 + coin_3 + coin_4 + coin_5 + coin_6) as total")
					->where('gameid=?',$game['id'])
					->group('uid')
					->fetchAll();
				foreach($total as $k=>$v){
					DI()->notorm->user
						->where('id = ?', $v['uid'])
						->update(array('coin' => new NotORM_Literal("coin + {$v['total']}")));
					
					$insert=array("type"=>'1',"action"=>'20',"uid"=>$v['uid'],"touid"=>$v['uid'],"giftid"=>$game['id'],"giftcount"=>1,"totalcoin"=>$v['total'],"showid"=>0,"addtime"=>$nowtime );
					DI()->notorm->user_coinrecord->insert($insert);
				}

				DI()->notorm->game
					->where('id = ?', $game['id'])
					->update(array('state' =>'3','endtime' => time() ) );
				$brandToken=$stream."_".$game["action"]."_".$game['starttime']."_Game";
				DI()->redis->del($brandToken);
			}

			/*主播直播奖励---每日任务*/
			$key='open_live_daily_tasks_'.$uid;
			$starttime=getcaches($key);
			if($starttime){ 
				$endtime=time();  //当前时间
				$data=[
					'type'=>'3',
					'starttime'=>$starttime,
					'endtime'=>$endtime,
				];
				dailyTasks($uid,$data);
				//删除当前存入的时间
				delcache($key);
			}

			//语音聊天室操作上麦相关信息
			if($info['live_type']==1){
				//删除上麦记录
				DI()->notorm->voicelive_mic->where("live_stream=?",$stream)->delete();
				//删除上麦申请记录
				DI()->notorm->voicelive_applymic->where("stream=?",$stream)->delete();
			}
            
		}
		return 1;
	}
	/* 关播信息 */
	public function stopInfo($stream){
		
		$rs=array(
			'nums'=>0,
			'length'=>0,
			'votes'=>0,
		);
		
		$stream2=explode('_',$stream);
		$liveuid=$stream2[0];
		$starttime=$stream2[1];
		$liveinfo=DI()->notorm->live_record
					->select("starttime,endtime,nums,votes")
					->where('uid=? and stream=?',$liveuid,$stream)
					->fetchOne();
		if($liveinfo){
            $cha=$liveinfo['endtime'] - $liveinfo['starttime'];
			$rs['length']=getSeconds($cha,1);
			$rs['nums']=$liveinfo['nums'];
		}
		if($liveinfo['votes']){
			$rs['votes']=$liveinfo['votes'];
		}
		return $rs;
	}
	
	/* 直播状态 */
	public function checkLive($uid,$liveuid,$stream){
        
        /* 是否被踢出 */
        $isexist=DI()->notorm->live_kick
					->select("id")
					->where('uid=? and liveuid=?',$uid,$liveuid)
					->fetchOne();
        if($isexist){
            return 1008;
        }
        
		$islive=DI()->notorm->live
					->select("islive,type,type_val,starttime,live_type")
					->where('uid=? and stream=?',$liveuid,$stream)
					->fetchOne();

					
		if(!$islive || $islive['islive']==0){
			return 1005;
		}


		$rs['type']=$islive['type'];
		$rs['type_val']='0';
		$rs['type_msg']='';
		$rs['live_type']=$islive['live_type'];

		$model_user=new Model_User();
		$checkTeenager = $model_user->checkTeenager($uid);
		$teenager_status=$checkTeenager['info'][0]['status'];


		if($uid>0){

			$userinfo=DI()->notorm->user
				->select("issuper")
				->where('id=?',$uid)
				->fetchOne();

			if($userinfo && $userinfo['issuper']==1 && !$teenager_status){  //超管身份、非青少年模式下
            
	            if($islive['type']==6){
	                
	                return 1007;
	            }

				$rs['type']='0';
				$rs['type_val']='0';
				$rs['type_msg']='';
				
				return $rs;
			}
		}
		

		$configpub=getConfigPub();
		
		if($islive['type']==1){
			$rs['type_msg']=md5($islive['type_val']);
			
		}else if($islive['type']==2){

			$rs['type_msg']='本房间为收费房间，需支付'.$islive['type_val'].$configpub['name_coin'];
			$rs['type_val']=$islive['type_val'];

			
			//打开青少年模式
			if($teenager_status==1){
				return $rs;
			}

			$isexist=DI()->notorm->user_coinrecord
						->select('id')
						->where('uid=? and touid=? and showid=? and action=6 and type=0',$uid,$liveuid,$islive['starttime'])
						->fetchOne();
			if($isexist){
				$rs['type']='0';
				$rs['type_val']='0';
				$rs['type_msg']='';
			}
		}else if($islive['type']==3){

			$rs['type_val']=$islive['type_val'];
			$rs['type_msg']='本房间为计时房间，每分钟需支付'.$islive['type_val'].$configpub['name_coin'];
		}
		
		return $rs;
		
	}
	
	/* 用户余额 */
	public function getUserCoin($uid){
		$userinfo=DI()->notorm->user
					->select("coin")
					->where('id=?',$uid)
					->fetchOne();
		return $userinfo;
	}
	
	/* 房间扣费 */
	public function roomCharge($uid,$liveuid,$stream){
		$islive=DI()->notorm->live
					->select("islive,type,type_val,starttime")
					->where('uid=? and stream=?',$liveuid,$stream)
					->fetchOne();
		if(!$islive || $islive['islive']==0){
			return 1005;
		}
		
		if($islive['type']==0 || $islive['type']==1 ){
			return 1006;
		}
				
		$total=$islive['type_val'];
		if($total<=0){
			return 1007;
		}
        
        /* 更新用户余额 消费 */
		$ifok=DI()->notorm->user
				->where('id = ? and coin >= ?', $uid,$total)
				->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}")) );
        if(!$ifok){
            return 1008;
        }

		$action='6';
		if($islive['type']==3){
			$action='7';
		}
		
		$giftid=0;
		$giftcount=0;
		$showid=$islive['starttime'];
		$addtime=time();
		

		/* 更新直播 映票 累计映票 */
		DI()->notorm->user
				->where('id = ?', $liveuid)
				->update( array('votes' => new NotORM_Literal("votes + {$total}"),'votestotal' => new NotORM_Literal("votestotal + {$total}") ));
        
        $insert_votes=[
            'type'=>'1',
            'action'=>$action,
            'uid'=>$liveuid,
            'fromid'=>$uid,
            'actionid'=>$giftid,
            'nums'=>$giftcount,
            'total'=>$total,
            'showid'=>$showid,
            'votes'=>$total,
            'addtime'=>time(),
        ];
        DI()->notorm->user_voterecord->insert($insert_votes);

		/* 更新直播 映票 累计映票 */
		DI()->notorm->user_coinrecord
				->insert(array("type"=>'0',"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime ));	
				
		$userinfo2=DI()->notorm->user
					->select('coin,consumption')
					->where('id = ?', $uid)
					->fetchOne();

		$level=getLevel($userinfo2['consumption']);

		$rs['coin']=$userinfo2['coin'];
		$rs['level']=$level;
		return $rs;
		
	}
	
	/* 判断是否僵尸粉 */
	public function isZombie($uid) {
        $userinfo=DI()->notorm->user
					->select("iszombie")
					->where("id='{$uid}'")
					->fetchOne();
		
		return $userinfo['iszombie'];				
    }
	
	/* 僵尸粉 */
    public function getZombie($stream,$where) {
		$ids= DI()->notorm->user_zombie
            ->select('uid')
            ->where("uid not in ({$where})")
			->limit(0,10)
            ->fetchAll();	

		$info=array();

		if($ids){
            foreach($ids as $k=>$v){
                
                $userinfo=getUserInfo($v['uid'],1);
                if(!$userinfo){
                    DI()->notorm->user_zombie->where("uid={$v['uid']}")->delete();
                    continue;
                }
                
                $info[]=$userinfo;

                $score='0.'.($userinfo['level']+100).'1';
				DI()->redis -> zAdd('user_'.$stream,$score,$v['uid']);
            }	
		}
		return 	$info;		
    }
	
	/* 礼物列表 */
	public function getGiftList(){

		$rs=DI()->notorm->gift
			->select("id,type,mark,giftname,needcoin,gifticon,sticker_id,swftime,isplatgift")
            ->where('type!=2')
			->order("list_order asc,addtime desc")
			->fetchAll();


		return $rs;
	}
	
	/* 礼物：道具列表 */
	public function getPropgiftList(){

		$rs=DI()->notorm->gift
			->select("id,type,mark,giftname,needcoin,gifticon,sticker_id,swftime,isplatgift")
			->where("type=2")
			->order("list_order asc,addtime desc")
			->fetchAll();

		return $rs;
	}



	/* 赠送礼物 */
	public function sendGift($uid,$liveuid,$stream,$giftid,$giftcount,$ispack,$touids) {

        /* 礼物信息 */
		$giftinfo=DI()->notorm->gift
					->select("type,mark,giftname,gifticon,needcoin,swftype,swf,swftime,isplatgift,sticker_id")
					->where('id=?',$giftid)
					->fetchOne();
		if(!$giftinfo){
			/* 礼物信息不存在 */
			return 1002;
		}

		$touids_arr=explode(',', $touids);
		if(empty($touids_arr)){
			return 1004;
		}

		$touid_nums=count($touids_arr);
        $personal_total=$giftinfo['needcoin']*$giftcount;
		$total= $personal_total*$touid_nums;

		$total_giftcount=$giftcount*$touid_nums;
		 
		$addtime=time();
		$type='0';
		$action='1';
		
        $stream2=explode('_',$stream);
        $showid=$stream2[1];
            
        if($ispack==1){
            /* 背包礼物 */
            $ifok =DI()->notorm->backpack
                    ->where('uid=? and giftid=? and nums>=?',$uid,$giftid,$total_giftcount)
                ->update(array('nums'=> new NotORM_Literal("nums - {$total_giftcount} ")));
            if(!$ifok){
                /* 数量不足 */
                return 1003;
            }
        }else{
           /* 更新用户余额 消费 */
            $ifok =DI()->notorm->user
                    ->where('id = ? and coin >=?', $uid,$total)
                    ->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}") ) );
            if(!$ifok){
                /* 余额不足 */
                return 1001;
            }

        }

        $multi_arr=[];
        foreach ($touids_arr as $k => $v) {
        	$insert=array(
        		"type"=>$type,
        		"action"=>$action,
        		"uid"=>$uid,
        		"touid"=>$v,
        		"giftid"=>$giftid,
        		"giftcount"=>$giftcount,
        		"totalcoin"=>$personal_total,
        		"showid"=>$showid,
        		"mark"=>$giftinfo['mark'],
        		"addtime"=>$addtime,
        		"ispack"=>$ispack
        	);
        	$multi_arr[]=$insert;	
        }
            
        DI()->notorm->user_coinrecord->insert_multi($multi_arr); //批量写入

        $gifttoken=md5(md5($action.$uid.$liveuid.$giftid.$giftcount.$total.$showid.$addtime.rand(100,999)));

        $swf=$giftinfo['swf'] ? get_upload_path($giftinfo['swf']):'';

        $result=[];
        $result['gifttoken']=$gifttoken;
        $result['list']=[];

        $live_type=getLiveType($liveuid,$stream);

        //语音聊天室因为不显示幸运礼物，没有奖池功能，所以，要将幸运礼物标识改为普通礼物
        if($live_type==1 && $giftinfo['mark']==3){
        	$giftinfo['mark']=0;
        }

        foreach ($touids_arr as $k => $v) {

        	$anthor_total=$personal_total;
        	//获取直播信息


	        if($live_type==0){ //视频直播

	        	// 幸运礼物分成start
		        if($giftinfo['type']==0 && $giftinfo['mark']==3){ //幸运礼物
		            $jackpotset=getJackpotSet();
		            if($jackpotset['switch']=='1'){
		            	$anthor_total=floor($anthor_total*$jackpotset['luck_anchor']*0.01*100)/100;
		            }
		        }
		        // 幸运礼物分成end
	        }

	        // 家族分成之后的金额
	        if($anthor_total){
	        	$anthor_total=setFamilyDivide($v,$anthor_total);
	        }



	        // 更新用户 魅力值 累计魅力值
			$istouid =DI()->notorm->user
					->where('id = ?', $v)
					->update( array('votes' => new NotORM_Literal("votes + {$anthor_total}"),'votestotal' => new NotORM_Literal("votestotal + {$personal_total}") ));

			
            $insert_votes=[
                'type'=>'1',
                'action'=>$action,
                'uid'=>$v,
                'fromid'=>$uid,
                'actionid'=>$giftid,
                'nums'=>$giftcount,
                'total'=>$personal_total,
                'showid'=>$showid,
                'votes'=>$anthor_total,
                'addtime'=>time(),
            ];
            DI()->notorm->user_voterecord->insert($insert_votes);
	        

	        // 更新主播热门
	        if($giftinfo['mark']==1){
	            DI()->notorm->live
	                ->where('uid = ?', $v)
	                ->update( array('hotvotes' => new NotORM_Literal("hotvotes + {$personal_total}") ));
	        }


	        //幸运礼物start
	        $ifluck=0;
	        $ifup=0;
	        $ifwin=0;
	        
	        if($giftinfo['type']==0 && $giftinfo['mark']==3){
	            $ifup=1;
	            $ifwin=1;
	            $list=getLuckRate();
	            // 有中奖配置 才处理
	            if($list){
	                $rateinfo=[];
	                foreach($list as $k1=>$v1){
	                    if($v1['giftid']==$giftid && $v1['nums']==$giftcount){
	                        $rateinfo[]=$v1;
	                    }
	                }
	                // 有该礼物、该数量 中奖配置 才处理
	                if($rateinfo){
	                    $ifluck=1;
	                }
	            }
	            
	        }

//	        file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 ifluck:'.json_encode($ifluck)."\r\n",FILE_APPEND);
//	        file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 ifwin:'.json_encode($ifwin)."\r\n",FILE_APPEND);
//	        file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 ifup:'.json_encode($ifup)."\r\n",FILE_APPEND);
//	        file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 rateinfo:'.json_encode($rateinfo)."\r\n",FILE_APPEND);
	        
	        // 幸运礼物中奖start
	        $isluck='0';
	        $isluckall='0';
	        $luckcoin='0';
	        $lucktimes='0';
	        if($ifluck ==1 ){
	            $luckrate=rand(1,100000);
	            //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 luckrate:'.json_encode($luckrate)."\r\n",FILE_APPEND);
	            $rate=0;
	            foreach($rateinfo as $k2=>$v2){
	                $rate+=floor($v2['rate']*1000);
	                //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 rate:'.json_encode($rate)."\r\n",FILE_APPEND);
	                if($luckrate <= $rate){
	                    // 中奖
	                    $isluck='1';
	                    $isluckall=$v2['isall'];
	                    $lucktimes=$v2['times'];
	                    $luckcoin= $total * $lucktimes;
	                    
	                    // 用户加余额  写记录
	                    DI()->notorm->user
	                        ->where('id = ?', $uid)
	                        ->update( array('coin' => new NotORM_Literal("coin + {$luckcoin}") ));
	                    $insert=array(
	                        "type"=>'1',
	                        "action"=>'12',
	                        "uid"=>$uid,
	                        "touid"=>$uid,
	                        "giftid"=>$giftid,
	                        "giftcount"=>$lucktimes,
	                        "totalcoin"=>$luckcoin,
	                        "showid"=>$showid,
	                        "mark"=>$giftinfo['mark'],
	                        "addtime"=>$addtime 
	                    );
	                    DI()->notorm->user_coinrecord->insert($insert);
	                    break;
	                }
	            }
	        }
	        
	        // 幸运礼物中奖end

	        $isup='0';
	        $uplevel='0';
	        $upcoin='0';

		    $iswin='0';
		    $wincoin='0';

		    $ispk='0';
	        $pkuid1='0';
	        $pkuid2='0';
	        $pktotal1='0';
	        $pktotal2='0';
	        $pkuid='0';
		            
	        if($live_type==0){

	        	// 奖池升级start
		        
		        if($ifup == 1 ){
		            //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 ifup:'.json_encode($ifup)."\r\n",FILE_APPEND);
		            //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpotset:'.json_encode($jackpotset)."\r\n",FILE_APPEND);
		            if($jackpotset['switch']==1 && $jackpotset['luck_jackpot'] > 0){
		                // 开启奖池
		                $jackpot_up=floor($total * $jackpotset['luck_jackpot'] * 0.01);
		                
		                //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpot_up:'.json_encode($jackpot_up)."\r\n",FILE_APPEND);
		                if($jackpot_up){
		                    DI()->notorm->jackpot->where("id = 1 ") ->update( array('total' => new NotORM_Literal("total + {$jackpot_up}") ));
		                    
		                    $jackpotinfo=getJackpotInfo();
		                    
		                    $jackpot_level=getJackpotLevel($jackpotinfo['total']);
		                    //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpotinfo:'.json_encode($jackpotinfo)."\r\n",FILE_APPEND);
		                    //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpot_level:'.json_encode($jackpot_level)."\r\n",FILE_APPEND);
		                    if($jackpot_level>$jackpotinfo['level']){
		                        $isok=DI()->notorm->jackpot->where("id = 1 and level < {$jackpot_level}") ->update( array('level' => $jackpot_level ));
		                        //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 isok:'.json_encode($isok)."\r\n",FILE_APPEND);
		                        if($isok){
		                            //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 isup:'.json_encode($isup)."\r\n",FILE_APPEND);
		                            $isup='1';
		                            $uplevel=$jackpot_level;
		                        }
		                    }
		                }
		            }
		        }
		        // 奖池升级end
		        
		        // 奖池中奖start
		        
		        if($ifwin ==1 ){
		            if($jackpotset['switch']==1 ){
		               // 奖池开启
		               $jackpotinfo=getJackpotInfo();
		               //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 jackpotinfo:'.json_encode($jackpotinfo)."\r\n",FILE_APPEND);
		               if($jackpotinfo['level']>=1){
		                    // 至少达到第一阶段才能中奖/
		                    
		                    $list=getJackpotRate();
		                    //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 list:'.json_encode($list)."\r\n",FILE_APPEND);
		                    // 有奖池中奖配置 才处理/
		                    if($list){
		                        $rateinfo=[];
		                        foreach($list as $k3=>$v3){
		                            if($v3['giftid']==$giftid && $v3['nums']==$giftcount){
		                                $rateinfo=$v3;
		                                break;
		                            }
		                        }
		                        //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 rateinfo:'.json_encode($rateinfo)."\r\n",FILE_APPEND);
		                        // 有该礼物中奖配置 才处理/
		                        if($rateinfo){
		                            $winrate=rand(1,100000);
		                            
		                            $rate_jackpot=json_decode($rateinfo['rate_jackpot'],true);
		                            
		                            $rate=floor($rate_jackpot[$jackpotinfo['level']] * 1000);
		                            //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 winrate:'.json_encode($winrate)."\r\n",FILE_APPEND);
		                            //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 rate:'.json_encode($rate)."\r\n",FILE_APPEND);
		                            if($winrate <= $rate){
		                                // 中奖
		                                $wincoin2=$jackpotinfo['total'];
		                                $isok=DI()->notorm->jackpot->where("id = 1 and total >= {$wincoin2}") ->update( array('total' => new NotORM_Literal("total - {$wincoin2}"),'level'=>'0' ));
		                                if($isok){
		                                    //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 iswin:'.'1'."\r\n",FILE_APPEND);
		                                    $iswin='1';
		                                    $wincoin=(string)$wincoin2;
		                                    
		                                    // 用户加余额  写记录
		                                    DI()->notorm->user
		                                        ->where('id = ?', $uid)
		                                        ->update( array('coin' => new NotORM_Literal("coin + {$wincoin2}") ));
		                                    $insert=array(
		                                        "type"=>'1',
		                                        "action"=>'13',
		                                        "uid"=>$uid,
		                                        "touid"=>$uid,
		                                        "giftid"=>'0',
		                                        "giftcount"=>'1',
		                                        "totalcoin"=>$wincoin2,
		                                        //"showid"=>$showid,
		                                        "mark"=>$giftinfo['mark'],
		                                        "addtime"=>$addtime 
		                                    );
		                                    DI()->notorm->user_coinrecord->insert($insert);
		                                }
		                            }
		                        }
		                    }
		               }
		            }
		        }
		        // 奖池中奖end


		    	// PK处理start
		        $key1='LivePK';
		        $key2='LivePK_gift';
		        
		        
		        
		        $pkuid=DI()->redis -> hGet($key1,$liveuid);
		        if($pkuid){
		            $ispk='1';
		            DI()->redis -> hIncrBy($key2,$liveuid,$personal_total);
		            
		            $gift_uid=DI()->redis -> hGet($key2,$liveuid);
		            $gift_pkuid=DI()->redis -> hGet($key2,$pkuid);
		            
		            $pktotal1=$gift_uid;
		            $pktotal2=$gift_pkuid;
		            
		            $pkuid1=$liveuid;
		            $pkuid2=$pkuid;
		            
		        }

	        }

	        DI()->redis->zIncrBy('user_'.$stream,$personal_total,$uid);

	        $votestotal=$this->getVotes($liveuid);
	        $userinfo2 =DI()->notorm->user
				->select('consumption,coin')
				->where('id = ?', $uid)
				->fetchOne();

			$level=getLevel($userinfo2['consumption']);	

			if($giftinfo['type']!=1){
	            $giftinfo['isplatgift']='0';
	        }

	        $touserinfo=getUserInfo($v);

	        $user_sendgift_info=array(
	            "uid"=>$uid,
	            "touid"=>$v,
	            "to_username"=>$touserinfo['user_nickname'],
	            "giftid"=>$giftid,
	            "type"=>$giftinfo['type'],
	            "mark"=>$giftinfo['mark'],
	            "giftcount"=>$giftcount,
	            "totalcoin"=>$total,
	            "giftname"=>$giftinfo['giftname'],
	            "gifticon"=>get_upload_path($giftinfo['gifticon']),
	            "swftime"=>$giftinfo['swftime'],
	            "swftype"=>$giftinfo['swftype'],
	            "swf"=>$swf,
	            "level"=>$level,
	            "coin"=>$userinfo2['coin'],
	            "votestotal"=>$votestotal,
				"isplatgift"=>$giftinfo['isplatgift'],
				"sticker_id"=>$giftinfo['sticker_id'],
	            
	            "isluck"=>$isluck,
	            "isluckall"=>$isluckall,
	            "luckcoin"=>$luckcoin,
	            "lucktimes"=>$lucktimes,
	            
	            "isup"=>$isup,
	            "uplevel"=>$uplevel,
	            "upcoin"=>$upcoin,
	            
	            "iswin"=>$iswin,
	            "wincoin"=>$wincoin,
	            
	            "ispk"=>$ispk,
	            "pkuid"=>$pkuid,
	            "pkuid1"=>$pkuid1,
	            "pkuid2"=>$pkuid2,
	            "pktotal1"=>$pktotal1,
	            "pktotal2"=>$pktotal2,
	        );

	        $result['list'][]=$user_sendgift_info;
	        $result['level']=$level;
	        $result['coin']=$userinfo2['coin'];

        }
		

        // 清除缓存
		delCache("userinfo_".$uid); 
		delCache("userinfo_".$liveuid); 	

		$configpri=getConfigPri();
		$dailytask_switch=$configpri['dailytask_switch'];
		
		if($dailytask_switch){
			//打赏礼物---每日任务---针对于用户
			$data=[
				'type'=>'4',
				'total'=>$total,
			];
			dailyTasks($uid,$data);
		}
		
        //file_put_contents(API_ROOT.'/../log/phalapi/live_sendGift_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);
		return $result;
	}		
	
	/* 发送弹幕 */
	public function sendBarrage($uid,$liveuid,$stream,$giftid,$giftcount,$content) {

		$configpri=getConfigPri();
					 
		$giftinfo=array(
			"giftname"=>'弹幕',
			"gifticon"=>'',
			"needcoin"=>$configpri['barrage_fee'],
		);		
		
		$total= $giftinfo['needcoin']*$giftcount;
		if($total<0){
            return 1002;
        }

        $addtime=time();
        $action='2';

        if($total>0){

        	$type='0';
        	// 更新用户余额 消费
	        $ifok =DI()->notorm->user
	                ->where('id = ? and coin >=?', $uid,$total)
	                ->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}") ) );
	        if(!$ifok){
	            // 余额不足
	            return 1001;
	        }

	        // 更新直播 魅力值 累计魅力值
	        $istouid =DI()->notorm->user
	                ->where('id = ?', $liveuid)
	                ->update( array('votes' => new NotORM_Literal("votes + {$total}"),'votestotal' => new NotORM_Literal("votestotal + {$total}") ));
	                
	        $stream2=explode('_',$stream);
	        $showid=$stream2[1];
	        if(!$showid){
	            $showid=0;
	        }
	        
	        $insert_votes=[
	            'type'=>'1',
	            'action'=>$action,
	            'uid'=>$liveuid,
	            'fromid'=>$uid,
	            'actionid'=>$giftid,
	            'nums'=>$giftcount,
	            'total'=>$total,
	            'showid'=>$showid,
	            'votes'=>$total,
	            'addtime'=>time(),
	        ];
	        DI()->notorm->user_voterecord->insert($insert_votes);

	        // 写入记录 或更新
	        $insert=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime );
	        $isup=DI()->notorm->user_coinrecord->insert($insert);


        }

        

		$userinfo2 =DI()->notorm->user
				->select('consumption,coin')
				->where('id = ?', $uid)
				->fetchOne();	
			 
		$level=getLevel($userinfo2['consumption']);			
		
		/* 清除缓存 */
		delCache("userinfo_".$uid); 
		delCache("userinfo_".$liveuid); 
		
		$votestotal=$this->getVotes($liveuid);
		
		$barragetoken=md5(md5($action.$uid.$liveuid.$giftid.$giftcount.$total.$showid.$addtime.rand(100,999)));
		 
		$result=array("uid"=>$uid,"content"=>$content,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"giftname"=>$giftinfo['giftname'],"gifticon"=>$giftinfo['gifticon'],"level"=>$level,"coin"=>$userinfo2['coin'],"votestotal"=>$votestotal,"barragetoken"=>$barragetoken);
		
		return $result;
	}			
	
	/* 设置/取消 管理员 */
	public function setAdmin($liveuid,$touid){
					
		$isexist=DI()->notorm->live_manager
					->select("*")
					->where('uid=? and  liveuid=?',$touid,$liveuid)
					->fetchOne();			
		if(!$isexist){
			$count =DI()->notorm->live_manager
						->where('liveuid=?',$liveuid)
						->count();	
			if($count>=5){
				return 1004;
			}		
			$rs=DI()->notorm->live_manager
					->insert(array("uid"=>$touid,"liveuid"=>$liveuid) );	
			if($rs!==false){
				return 1;
			}else{
				return 1003;
			}				
			
		}else{
			$rs=DI()->notorm->live_manager
				->where('uid=? and  liveuid=?',$touid,$liveuid)
				->delete();		
			if($rs!==false){
				return 0;
			}else{
				return 1003;
			}						
		}
	}
	
	/* 管理员列表 */
	public function getAdminList($liveuid){
		$rs=DI()->notorm->live_manager
						->select("uid")
						->where('liveuid=?',$liveuid)
						->fetchAll();	
		foreach($rs as $k=>$v){
			$rs[$k]=getUserInfo($v['uid']);
		}	

        $info['list']=$rs;
        $info['nums']=(string)count($rs);
        $info['total']='5';
		return $info;
	}
    
	/* 举报类型 */
	public function getReportClass(){
		return  DI()->notorm->report_classify
                    ->select("*")
					->order("list_order asc")
					->fetchAll();
	}
	
	/* 举报 */
	public function setReport($uid,$touid,$content){
		return  DI()->notorm->report
				->insert(array("uid"=>$uid,"touid"=>$touid,'content'=>$content,'addtime'=>time() ) );	
	}
	
	/* 主播总映票 */
	public function getVotes($liveuid){
		$userinfo=DI()->notorm->user
					->select("votestotal")
					->where('id=?',$liveuid)
					->fetchOne();	
		return $userinfo['votestotal'];					
	}
    
    /* 是否禁言 */
	public function checkShut($uid,$liveuid){
        
        $isexist=DI()->notorm->live_shut
                ->where('uid=? and liveuid=? ',$uid,$liveuid)
                ->fetchOne();
        if($isexist){
            DI()->redis -> hSet($liveuid . 'shutup',$uid,1);
        }else{
            DI()->redis -> hDel($liveuid . 'shutup',$uid);
            return 0;
        }
		return 1;			
	}

    /* 禁言 */
	public function setShutUp($uid,$liveuid,$touid,$showid){
        
        $isexist=DI()->notorm->live_shut
                ->where('uid=? and liveuid=? ',$touid,$liveuid)
                ->fetchOne();
        if($isexist){
            if($isexist['showid']==$showid){
                return 1002;
            }
            
            
            if($isexist['showid']==0 && $showid!=0){
                return 1002;
            }
            
            $rs=DI()->notorm->live_shut->where('id=?',$isexist['id'])->update([ 'uid'=>$touid,'liveuid'=>$liveuid,'actionid'=>$uid,'showid'=>$showid,'addtime'=>time() ]);
            
        }else{
            $rs=DI()->notorm->live_shut->insert([ 'uid'=>$touid,'liveuid'=>$liveuid,'actionid'=>$uid,'showid'=>$showid,'addtime'=>time() ]);
        }
        
        
        
		return $rs;			
	}
    
    /* 踢人 */
	public function kicking($uid,$liveuid,$touid){
        
        $isexist=DI()->notorm->live_kick
                ->where('uid=? and liveuid=? ',$touid,$liveuid)
                ->fetchOne();
        if($isexist){
            return 1002;
        }
        
        $rs=DI()->notorm->live_kick->insert([ 'uid'=>$touid,'liveuid'=>$liveuid,'actionid'=>$uid,'addtime'=>time() ]);
        
        
		return $rs;
	}
      
	
	/* 超管关闭直播间 */
	public function superStopRoom($uid,$liveuid,$type,$banruleid){
		
		$userinfo=DI()->notorm->user
					->select("issuper")
					->where('id=? ',$uid)
					->fetchOne();
		
		if($userinfo['issuper']==0){
			return 1001;
		}
		
		if($type==1){

			$nowtime=time();
			
            // 禁播列表
            $isexist=DI()->notorm->live_ban->where('liveuid=? ',$liveuid)->fetchOne();
            if($isexist){
                return 1002;
            }

            $rules=getLiveBanRules();

            $current_rules=[];

            foreach ($rules as $k => $v) {
            	if($banruleid==$v['id']){
            		$current_rules=$v;
            		break;
            	}
            }

            $ban_type=$current_rules['type'];

            switch ($ban_type) {
            	case '30min':
            		$endtime=$nowtime+30*60;
            		break;

            	case '1day':
        			$endtime=strtotime("+1 day");
        		break;

	        	case '7day':
	        		$endtime=strtotime("+7 day");
	        		break;

	        	case '15day':
	        		$endtime=strtotime("+15 day");
	        		break;

	        	case '30day':
	        		$endtime=strtotime("+30 day");
	        		break;

	        	case '90day':
	        		$endtime=strtotime("+90 day");
	        		break;

	        	case '180day':
	        		$endtime=strtotime("+180 day");
	        		break;

	        	case 'all':
	        		$endtime=0;
	        		break;
	        	
	        	default:
	        		$endtime=0;
	        		break;

            }

            DI()->notorm->live_ban->insert(
            	[
            		'liveuid'=>$liveuid,
            		'superid'=>$uid,
            		'type'=>$ban_type,
            		'addtime'=>$nowtime,
            		'endtime'=>$endtime
            	]);
		}
        
        if($type==2){
            //关闭并禁用
			DI()->notorm->user->where('id=? ',$liveuid)->update(array('user_status'=>0));
        }
		
	
		$info=DI()->notorm->live
				->select("stream")
				->where('uid=? and islive="1"',$liveuid)
				->fetchOne();
		if($info){
            $this->stopRoom($liveuid,$info['stream']);
		}

		
		return 0;
		
	}
    
    /* 获取用户本场贡献 */
    public function getContribut($uid,$liveuid,$showid){
        $sum=DI()->notorm->user_coinrecord
				->where('action=1 and uid=? and touid=? and showid=? ',$uid,$liveuid,$showid)
				->sum('totalcoin');
        if(!$sum){
            $sum=0;
        }
        
        return (string)$sum;
    }

    /* 检测房间状态 */
    public function checkLiveing($uid,$stream){
        $info=DI()->notorm->live
                ->select('uid')
				->where('uid=? and stream=? ',$uid,$stream)
				->fetchOne();
        if($info){
            return '1';
        }
        
        return '0';
    }
    
    /* 获取直播信息 */
    public function getLiveInfo($liveuid){
        
        $info=DI()->notorm->live
					->select("uid,title,city,stream,pull,thumb,isvideo,type,type_val,goodnum,anyway,starttime,isshop,game_action")
					->where('uid=? and islive=1',$liveuid)
					->fetchOne();
        if($info){
            
            $info=handleLive($info);
            
        }
        
        return $info;
    }

    //直播间在售商品列表是否正在展示状态
    public function setLiveGoodsIsShow($uid,$goodsid){

    	$rs=array('status'=>'0'); //商品展示状态 0不显示 1 展示

    	//获取商品信息
    	$model_shop=new Model_Shop();
    	$where=array('uid'=>$uid,'id'=>$goodsid);
    	$goods_info=$model_shop->getGoods($where);

    	if(!$goods_info){ //非本人发布的商品
    		
    		//判断是否为该用户代售的商品
    		
    		$where1=[];
    		$where1['uid']=$uid;
    		$where1['goodsid']=$goodsid;
    		$where1['status']=1;

    		$is_sale=checkUserSalePlatformGoods($where1);

    		if(!$is_sale){
    			return 1001;
    		}

    		$sale_info=getOnsalePlatformInfo($where1);

    		if($sale_info['live_isshow']){ //在售
    			setOnsalePlatformInfo($where1,['live_isshow'=>0]);
    		}else{
    			setOnsalePlatformInfo($where1,['live_isshow'=>1,'issale'=>1]);
    			$rs['status']='1';

    			//将自己发布的商品在售状态改为0
    			DI()->notorm->shop_goods->where("uid={$uid} and status=1 and live_isshow=1")->update(array("live_isshow"=>0));

    			//将其他代售商品的在售状态改为0
    			$where2="uid={$uid} and goodsid !={$goodsid}";
    			setOnsalePlatformInfo($where2,['live_isshow'=>0]);
    		}



    		
    	}else{ //自己发布的商品

    		if($goods_info['status']!=1){
	    		return 1002;
	    	}

	    	if($goods_info['live_isshow']==1){ //取消展示
	    		$data=array(
	    			'live_isshow'=>0
	    		);

	    		$res=$model_shop->upGoods($where,$data);
	    		if(!$res){
	    			return 1003;
	    		}


	    	}else{ //设置展示

	    		
	    		$data=array(
	    			'live_isshow'=>1
	    		);

	    		$res=$model_shop->upGoods($where,$data);
	    		if(!$res){
	    			return 1004;
	    		}
	    		//将其他展示状态的商品改为非展示状态
	    		$where1="uid={$uid} and id !={$goodsid} and live_isshow=1";
	    		$data1=array(
	    			'live_isshow'=>0
	    		);

	    		$model_shop->upGoods($where1,$data1);

	    		$rs['status']='1';

	    		//将其他代售商品的在售状态改为0
    			$where2="uid={$uid} and goodsid !={$goodsid}";
    			setOnsalePlatformInfo($where2,['live_isshow'=>0]);

	    	}


    	}

    	

    	


    	return $rs;
    }

    //获取直播间在售商品中正在展示的商品
    public function getLiveShowGoods($liveuid){

    	$res=array('goodsid'=>'0','goods_name'=>'','goods_thumb'=>'','goods_price'=>'','goods_type'=>'0');

    	//判断直播间是否开启购物车
    	$isshop=DI()->notorm->live->where("uid=?",$liveuid)->fetchOne('isshop');
    	if(!$isshop){
    		return $res;
    	}

    	$where=array(
    		'uid'=>$liveuid,
    		'status'=>1,
    		'issale'=>1,
    		'live_isshow'=>1,
    	);

    	$model_shop=new Model_Shop();
    	$goods_info=$model_shop->getGoods($where);

    	if($goods_info){
    		$goods_info=handleGoods($goods_info);
    		$res['goodsid']=$goods_info['id'];
    		$res['goods_name']=$goods_info['name'];
    		$res['goods_thumb']=$goods_info['thumbs_format'][0];
    		if($goods_info['type']==1){ //外链商品
    			$res['goods_price']=$goods_info['present_price'];
    		}else{
    			$res['goods_price']=$goods_info['specs_format'][0]['price'];
    		}
    		
    		$res['goods_type']=$goods_info['type'];

    	}else{ //代售平台商品
    		$where1=array(
    			'uid'=>$liveuid,
    			'status'=>1,
    			'issale'=>1,
    			'live_isshow'=>1
    		);
    		$onsale_platfrom_goods=getOnsalePlatformInfo($where1);

    		if($onsale_platfrom_goods){
    			$where2=array(
    				'id'=>$onsale_platfrom_goods['goodsid'],
    				'status'=>1
    			);
    			$goods_info=$model_shop->getGoods($where2);

    			if($goods_info){
		    		$goods_info=handleGoods($goods_info);
		    		$res['goodsid']=$goods_info['id'];
		    		$res['goods_name']=$goods_info['name'];
		    		$res['goods_thumb']=$goods_info['thumbs_format'][0];
		    		if($goods_info['type']==1){ //外链商品
		    			$res['goods_price']=$goods_info['present_price'];
		    		}else{
		    			$res['goods_price']=$goods_info['specs_format'][0]['price'];
		    		}
		    		
		    		$res['goods_type']=$goods_info['type'];

		    	}


    		}

    	}

    	return $res;

    }

    //语音聊天室用户申请上麦
    public function applyVoiceLiveMic($uid,$stream){
    	//判断直播间
    	$liveinfo=DI()->notorm->live
    	->where("stream=? and islive=1",$stream)
    	->fetchOne();

    	if(!$liveinfo){
    		return 1001;
    	}

    	if($liveinfo['live_type']!=1){
    		return 1002;
    	}

    	//判断用户是否已经申请上麦
    	$mic_apply=DI()->notorm->voicelive_applymic
    	->where("uid=? and stream=?",$uid,$stream)
    	->fetchOne();
    	if($mic_apply){
    		return 1003;
    	}

    	$count=DI()->notorm->voicelive_applymic
    	->where("stream=?",$stream)
    	->count();

    	if($count>=100){ //申请上麦上限100人
    		return 1005;
    	}

    	$stream_arr=explode('_', $stream);
    	$data=array(
    		'uid'=>$uid,
    		'liveuid'=>$stream_arr[0],
    		'stream'=>$stream,
    		'addtime'=>time()
    	);

    	$res=DI()->notorm->voicelive_applymic->insert($data);
    	if(!$res){
    		return 1004;
    	}
    	return 1;
    }

    //用户取消语音聊天室上麦申请
    public function cancelVoiceLiveMicApply($uid,$stream){
    	//判断用户是否已经申请上麦
    	$mic_apply=DI()->notorm->voicelive_applymic
    	->where("uid=? and stream=?",$uid,$stream)
    	->fetchOne();

    	if(!$mic_apply){
    		return 1001;
    	}

    	$res=DI()->notorm->voicelive_applymic
    	->where("uid=? and stream=?",$uid,$stream)
    	->delete();

    	if(!$res){
    		return 1002;
    	}

    	return 1;
    }

    //主播处理语音聊天室用户上麦申请
    public function handleVoiceMicApply($uid,$stream,$apply_uid,$status){

    	//判断语音聊天室主播是否开播
    	$voice_islive=checkVoiceIsLive($uid,$stream);

    	if(!$voice_islive){
    		return 1001;
    	}

    	//获取用户申请上麦记录
    	$mic_apply=DI()->notorm->voicelive_applymic
    	->where("uid=? and liveuid=? and stream=?",$apply_uid,$uid,$stream)
    	->fetchOne();
    	if(!$mic_apply){
    		return 1002;
    	}

    	$user_stream='';
    	$now=time();

    	if($status==0){ //主播拒绝用户上麦
    		$res=DI()->notorm->voicelive_applymic
    		->where("uid=? and liveuid=? and stream=?",$apply_uid,$uid,$stream)
    		->delete();

    		if(!$res){
    			return 1003;
    		}

    		return array('position'=>'-1');

    	}else{ //主播同意用户上麦

    		//判断用户是否已经上麦
    		$where="liveuid={$uid} and live_stream='{$stream}' and uid={$apply_uid}";
    		$mic_info=getVoiceMicInfo($where);
    		
    		if($mic_info){
    			return 1006;
    		}

    		//判断有没有麦位可以上麦
    		$num=DI()->notorm->voicelive_mic
    		->where("liveuid=? and live_stream=? and status !=0",$uid,$stream)
    		->order("position")
    		->count();

    		if($num>=8){
    			return 1004;
    		}

    		$mic_list=DI()->notorm->voicelive_mic
    		->where("liveuid=? and live_stream=?",$uid,$stream)
    		->order("position")
    		->fetchAll();

    		$curr_position=0;
    		$is_empty=0;
    		$mic_id=0;
    		foreach ($mic_list as $k => $v) {
    			$position=$v['position'];

    			if($curr_position<$position){
    				break;
    			}else{
    				//判断该麦位是否是空麦位且未禁麦
    				if($v['uid']==0 && $v['status']==0){
    					$is_empty=1;
    					$mic_id=$v['id'];
    					break;
    				}
    				
    			}

    			$curr_position++;

    			//var_dump($curr_position.'--');

    		}


    		if($is_empty==0){
    			$data=array(
	    			'uid'=>$apply_uid,
	    			'liveuid'=>$uid,
	    			'live_stream'=>$stream,
	    			'position'=>$curr_position,
	    			'status'=>1, //开麦状态
	    			'addtime'=>$now
	    		);

	    		

	    		$res=DI()->notorm->voicelive_mic->insert($data);


    		}else{
    			//更新空麦位的信息
    			$update_data=array(
    				'uid'=>$apply_uid,
	    			'status'=>1, //开麦状态
	    			'addtime'=>$now
    			);

    			$res=DI()->notorm->voicelive_mic->where("id={$mic_id}")->update($update_data);
    		}


    		
    		
    		if(!$res){
    			return 1005;
    		}

    		//删除上麦申请记录
    		DI()->notorm->voicelive_applymic
    		->where("uid=? and liveuid=? and stream=?",$apply_uid,$uid,$stream)
    		->delete();

    		return array('position'=>(string)$curr_position); //返回用户当前的麦位
    	}


    }

    //获取当前语音聊天室内正在申请连麦的用户列表
    public function getVoiceMicApplyList($uid,$stream){

    	$mic_apply=DI()->notorm->voicelive_applymic
    	->select("uid")
    	->where("stream=?",$stream)
    	->order("addtime")
    	->fetchAll();

    	$position='-1';
    	foreach ($mic_apply as $k => $v) {
    		$userinfo=getUserInfo($v['uid']);
    		$mic_apply[$k]=$userinfo;
    		if($uid==$v['uid']){
    			$position=(string)($k+1);
    		}
    	}

    	$apply_list=$mic_apply;

    	return array("apply_list"=>$apply_list,"position"=>$position);
    }

    //主播对空麦位设置禁麦或取消禁麦
    public function changeVoiceEmptyMicStatus($uid,$stream,$position,$status){
    	//判断语音聊天室主播是否开播
    	$voice_islive=checkVoiceIsLive($uid,$stream);

    	if(!$voice_islive){
    		return 1003;
    	}

    	//获取麦位信息
    	$where="liveuid={$uid} and live_stream='{$stream}' and position={$position}";
    	$mic_info=getVoiceMicInfo($where);

    	$now=time();
    	//判断麦位上是否有用户上麦
    	if($mic_info){
    		if($mic_info['uid']>0){ //麦上有用户
    			return 1001;
    		}

    		//更新麦位信息

    		if($status==0&&$mic_info['status']==2){ //主播禁麦操作
    			return 1004; //已经禁麦,不可重复处理
    		}

    		if($status==1&&$mic_info['status']==0){ //主播取消禁麦
    			return 1005; //已经取消禁麦,不可重复处理
    		}
    		
    		if($status==0){
    			$data=array(
	    			'status'=>2 // -1 关麦；  0无人； 1开麦 ； 2 禁麦；
	    		);
    		}

    		if($status==1){
    			$data=array(
	    			'status'=>0 // -1 关麦；  0无人； 1开麦 ； 2 禁麦；
	    		);
    		}
    		
    		$res=DI()->notorm->voicelive_mic
    		->where("liveuid=? and live_stream=? and position=?",$uid,$stream,$position)
    		->update($data);

    		if($res==false){
    			return 1002;
    		}

    	}else{

    		if($status==0){ //禁麦
    			$mic_status=2;
    		}else{ //取消禁麦
    			$mic_status=0; 
    		}

    		$data=array(
    			'uid'=>0,
    			'liveuid'=>$uid,
    			'live_stream'=>$stream,
    			'status'=>$mic_status,
    			'position'=>$position,
    			'addtime'=>$now
    		);

    		$res=DI()->notorm->voicelive_mic->insert($data);
    		if($res==false){
    			return 1002;
    		}
    	}

    	return 1;
    } 

    //主播获取语音聊天室麦位列表信息
    public function anchorGetVoiceMicList($uid,$stream){

    	//判断语音聊天室主播是否开播
    	$voice_islive=checkVoiceIsLive($uid,$stream);

    	if(!$voice_islive){
    		return 1001;
    	}

    	$mic_list=DI()->notorm->voicelive_mic
    	->where("liveuid=? and live_stream=?",$uid,$stream)
    	->order("position")
    	->fetchAll();

    	$new_mic_list=getVoiceLiveMicList($mic_list);

    	return $new_mic_list;
    }

    //语音聊天室中主播对麦上用户设置闭麦/开麦
    public function changeVoiceMicStatus($uid,$stream,$position,$status){

    	$stream_arr=explode("_", $stream);
    	$liveuid=$stream_arr[0];

    	//判断语音聊天室主播是否开播
    	$voice_islive=checkVoiceIsLive($liveuid,$stream);

    	if(!$voice_islive){
    		return 1001;
    	}

    	

    	//获取麦位信息
    	$where="live_stream='{$stream}' and position={$position} ";
    	$mic_info=getVoiceMicInfo($where);
    	if(!$mic_info){
    		return 1002;
    	}

    	if(!$mic_info['uid']){
    		return 1002;
    	}	

    	if($mic_info['status']==2){ //麦位已禁麦
    		return 1003;
    	}

    	if($status==0 && $mic_info['status']==-1){ //麦位已关麦
    		return 1004;
    	}

    	if($status==1 && $mic_info['status']==1){ //麦位已开麦
    		return 1005;
    	}

    	if($mic_info['status']==-1 && $status==1){ //麦位已关，且是开麦操作

    		//判断操作者是否是主播或麦位用户本人
    		if($uid!=$liveuid && $uid!=$mic_info['uid']){
    			return 1010;
    		}
    		
    		if($mic_info['mic_closeuid']!=$uid){ //非关麦用户操作

    			if($mic_info['mic_closeuid']==$liveuid){ //主播关，用户开
    				return 1009;
    			}

    			if($mic_info['mic_closeuid']==$mic_info['uid']){ //麦上用户自己关，主播开
    				return 1007;
    			}
    		}
    		
    	}

    	if($mic_info['status']==1 && $status==0){ //麦位已开,且是关麦操作
    		if($uid!=$liveuid && $uid !=$mic_info['uid']){
    			return 1008;
    		}
    	}

    	//更新麦位信息
    	if($status==0){ //关麦
    		$data=array(
    			'status'=>-1,
    			'mic_closeuid'=>$uid
    		);
    	}else{ //开麦

    		$data=array(
    			'status'=>1,
    			'mic_closeuid'=>0
    		);
    	}
    	$res=DI()->notorm->voicelive_mic
    	->where($where)
    	->update($data);

    	if($res==false){
    		return 1006;
    	}

    	return 1;

    } 

    //语音聊天室用户主动下麦
    public function userCloseVoiceMic($uid,$stream){
    	//判断语音聊天室主播是否开播
    	$stream_arr=explode('_', $stream);
    	$liveuid=$stream_arr[0];
    	$voice_islive=checkVoiceIsLive($liveuid,$stream);

    	if(!$voice_islive){
    		return 1001;
    	}

    	//删除上麦申请
    	DI()->notorm->voicelive_applymic
    	->where("uid=? and stream=?",$uid,$stream)
    	->delete();

    	//获取麦位信息
    	$where="live_stream='{$stream}' and uid={$uid} ";
    	$mic_info=getVoiceMicInfo($where);
    	if(!$mic_info){
    		return 1002;
    	}

    	if($mic_info['status']==2){
    		return 1003;
    	}

    	$res=DI()->notorm->voicelive_mic
    	->where($where)
    	->delete();
    	if($res==false){
    		return 1004;
    	}

    	return 1;
    }
    //语音聊天室主播或管理员将连麦用户下麦
    public function closeUserVoiceMic($uid,$liveuid,$stream,$touid){
    	
    	//获取麦位信息
    	$where="liveuid={$liveuid} and live_stream='{$stream}' and uid={$touid}";
    	$mic_info=getVoiceMicInfo($where);
    	if(!$mic_info){
    		return 1001;
    	}

    	if($mic_info['status']==2){
    		return 1002;
    	}

    	//删除连麦信息
    	$res=DI()->notorm->voicelive_mic
    	->where($where)
    	->delete();

    	if(!$res){
    		return 1003;
    	}

    	return 1;
    }

    //语音聊天室上麦用户获取推拉流地址【低延迟流】
    public function getVoiceMicStream($uid,$liveuid,$stream){
    	//获取麦位信息
    	$where="liveuid={$liveuid} and live_stream='{$stream}' and uid={$uid}";
    	$mic_info=getVoiceMicInfo($where);
    	if(!$mic_info){
    		return 1001;
    	}

    	if($mic_info['status']==2){
    		return 1002;
    	}

        $nowtime=time();
        $user_stream=$uid.'_'.$nowtime;

        //$lowlatency_stream=getLowLatencyStream($user_stream);
		//$push_url=$lowlatency_stream['pushurl'];
		//$play_url=$lowlatency_stream['playurl'];
		
		$push_url=getTxTrtcUrl($uid,$user_stream,1);

		//更新麦位信息
		DI()->notorm->voicelive_mic
		->where($where)
		->update(
			array(				
				'stream'=>$user_stream
			)
		);

		return array(
			'push'=>$push_url,
			//'pull'=>$play_url,
			'user_stream'=>$user_stream
		);
    }

    //获取语音聊天室主播和麦上用户的低延迟播流地址
    public function getVoiceLivePullStreams($uid,$stream){
    	$stream_arr=explode("_",$stream);
    	$liveuid=$stream_arr[0];
    	$voice_islive=checkVoiceIsLive($liveuid,$stream);
    	if(!$voice_islive){
    		return 1001;
    	}

    	//获取语音聊天室麦位列表
    	$mic_list=DI()->notorm->voicelive_mic
    	->where("liveuid=? and live_stream=? and uid>0 and uid !=?",$liveuid,$stream,$uid)
    	->select("uid,stream")
    	->order('position')
    	->fetchAll();

    	$new_mic_list=[];
    	$live_arr=[];
    	$live_arr['uid']=$liveuid;
    	//$low_latencystream=getLowLatencyStream($stream);
    	//$live_arr['pull']=$low_latencystream['playurl'];
    	$play_url=getTxTrtcUrl($uid,$stream,0);
    	$live_arr['pull']=$play_url;
    	$live_arr['isanchor']='1';

    	$new_mic_list[]=$live_arr;

    	foreach ($mic_list as $k => $v) {
    		$arr=array();
    		$arr['uid']=$v['uid'];
    		$arr['isanchor']='0';
    		//$low_latencystream=getLowLatencyStream($v['stream']);
    		//$arr['pull']=$low_latencystream['playurl'];
    		$arr['pull']=getTxTrtcUrl($uid,$v['stream'],0);
    		$new_mic_list[]=$arr;
    	}

    	return $new_mic_list;

    }

    //用户获取语音聊天室内所有麦位信息
    public function userGetVoiceMicList($liveuid,$stream){
    	$mic_list=DI()->notorm->voicelive_mic
    	->where("liveuid=? and live_stream=?",$liveuid,$stream)
    	->order("position")
    	->fetchAll();

    	$new_mic_list=getVoiceLiveMicList($mic_list);

    	return $new_mic_list;
    }
    //获取主播封禁信息
    public function getLiveBanInfo($uid){

    	$rs=array(
    		'ban_num'=>'0',
    		'ban_msg'=>'该直播间被强制关播'
    	);

    	$ban_info = DI()->notorm->live_ban
    		->where(['liveuid'=>$uid])
    		->fetchOne();

    	if(!$ban_info){
    		return $rs;
    	}

    	$ban_type = $ban_info['type'];

    	if($ban_type=='all'){

	    	$rs['ban_msg'] = "您的直播间涉嫌违规,平台将永久封禁";

    	}else{

    		$now=time();
    		if($ban_info['endtime']<=$now){

    			DI()->notorm->live_ban
    			->where(['liveuid'=>$uid])
    			->delete();

    			return $rs;
    		}

    		$ban_rules=getLiveBanRules();

	    	$current_name='';

	        foreach ($ban_rules as $k => $v) {
	        	if($ban_info['type']==$v['type']){
	        		$current_name=$v['name'];
	        		break;
	        	}
	        }

	        if($current_name){

	        	$rs['ban_num']=number_format($current_name);
	        	$rs['ban_msg'] = '您的直播间涉嫌违规,平台将封禁'.$current_name;
	        }

	        


    	}

    	return $rs;

    }
}
