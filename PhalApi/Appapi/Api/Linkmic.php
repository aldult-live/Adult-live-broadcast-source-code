<?php
/**
 * 用户连麦
 */


class Api_Linkmic extends PhalApi_Api {

	public function getRules() {
		return array(
            'setMic' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string','require' => true, 'desc' => '用户Token'),
				'ismic' => array('name' => 'ismic', 'type' => 'int', 'require' => true, 'desc' => '连麦开关，0关1开'),
			),
            
            'isMic' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
			),
            
			'RequestLVBAddrForLinkMic' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '连麦用户ID'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方主播用户ID'),
			),
			'RequestPlayUrlWithSignForLinkMic' => array( 
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '主播流名'),
			),
			
		);
	}
    
	/**
	 * 设置连麦开关
	 * @desc 用于 用户设置当前直播的连麦开关
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].pushurl 推流地址
	 * @return string info[].timestamp 当前时间
	 * @return string info[].playurl 播流地址
	 * @return string msg 提示信息
	 */
	public function setMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

		$uid=checkNull($this->uid);        
        $token=checkNull($this->token);
        $ismic=checkNull($this->ismic);

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$result = $domain->setMic($uid,$ismic);


		$rs['msg']='设置成功';
		return $rs;			
	}		

	/**
	 * 判断主播是否开启连麦
	 * @desc 用于 判断主播是否开启连麦
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function isMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

      
        $uid=checkNull($this->uid);
        $liveuid=checkNull($this->liveuid);
        
        $configpri=getConfigPri();
        $mic_limit=$configpri['mic_limit'];
        
        $userinfo=getUserinfo($uid);
        
        if($mic_limit && $userinfo['level']<$mic_limit){
            $rs['code'] = 1002;
			$rs['msg'] = "用户等级达到{$mic_limit}级才可与主播连麦哦~";
			return $rs;
        }
        
        $domain = new Domain_Linkmic();
		$result = $domain->isMic($liveuid);

        if(!$result){
            $rs['code'] = 1001;
			$rs['msg'] = '主播未开启连麦功能哦~';
			return $rs;
        }

		return $rs;	
	}
	
	/**
	 * 连麦观众获取自己的推拉流地址
	 * @desc 用于连麦观众获取自己的推拉流地址
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].pushurl 推流地址
	 * @return string info[].timestamp 当前时间
	 * @return string info[].playurl 播流地址
	 * @return string msg 提示信息
	 */
	public function RequestLVBAddrForLinkMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

		$uid=checkNull($this->uid);
		$liveuid=checkNull($this->liveuid);
        $nowtime=time();
        $stream=$uid.'_'.$nowtime;

        
        $configpri=getConfigPri();
        $live_sdk=$configpri['live_sdk'];
        if($live_sdk==1){
        	//trtc流
			$pushurl=getTxTrtcUrl($uid,$stream,1);
			$playurl=getTxTrtcUrl($liveuid,$stream,0); //给对方播放

			$arr=array(
				'pushurl'=>$pushurl,
				'playurl'=>$playurl,
				'timestamp'=>$nowtime,
				'stream'=>$stream
			);

			$rs['info'][0]=$arr;

        }else{
        	//rtmp流
        	$lowlatency_stream=getLowLatencyStream($stream);
        	$rs['info'][0]=$lowlatency_stream;
        }
        

		return $rs;			
	}		

	/**
	 * 获取鉴权流地址
	 * @desc 用于鉴权流地址
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].streamUrlWithSignature 鉴权地址
	 * @return string info[].timestamp 当前时间
	 * @return string msg 提示信息
	 */
	public function RequestPlayUrlWithSignForLinkMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$liveuid=checkNull($this->liveuid);
		$stream=checkNull($this->stream);


		//rtmp将普通播流转换为低延时流
		/*$originalUrl=checkNull($this->originStreamUrl);
		
		$configpri = getConfigPri(); 

		$bizid = $configpri['tx_bizid'];
		$push_url_key = $configpri['tx_push_key'];
		$tx_acc_key = $configpri['tx_acc_key'];
		
		$list1 = preg_split ('/\?/', $originalUrl);
        $originalUrl=$list1[0];
        
        $list = preg_split ('/\//', $originalUrl);
        $url = preg_split ('/\./', end($list));
		
        $now_time = time();
		$now_time = $now_time + 3*60*60;
		$txTime = dechex($now_time);
		
		$txSecret = md5($tx_acc_key . $url[0] . $txTime);
		
        $safe_url = $originalUrl."?txSecret=" . $txSecret ."&txTime=" .$txTime ."&bizid=".$bizid;

        $safe_url=str_replace(".flv",'',$safe_url);
        $safe_url=str_replace("http://",'rtmp://',$safe_url);

		$info=array(
			"streamUrlWithSignature" => $safe_url,
			"timestamp" => $now_time, 
		);

        */

		//获取主播trtc低延时播流地址
		$playurl=getTxTrtcUrl($uid,$stream,0);

        $info=array(
			"streamUrlWithSignature" => $playurl,
			"timestamp" => time(), 
		);


		$rs['info'][0]=$info;
		return $rs;			
	}		
	


}
