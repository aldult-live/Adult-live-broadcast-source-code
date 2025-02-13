<?php

	/* Redis链接 */
	function connectionRedis(){
		$REDIS_HOST= DI()->config->get('app.REDIS_HOST');
		$REDIS_AUTH= DI()->config->get('app.REDIS_AUTH');
		$REDIS_PORT= DI()->config->get('app.REDIS_PORT');
		$redis = new Redis();
		$redis -> pconnect($REDIS_HOST,$REDIS_PORT);
		$redis -> auth($REDIS_AUTH);

		return $redis;
	}
	/* 设置缓存 */
	function setcache($key,$info){
		$config=getConfigPri();
		if($config['cache_switch']!=1){
			return 1;
		}

		DI()->redis->set($key,json_encode($info));
		DI()->redis->expire($key, $config['cache_time']); 

		return 1;
	}	
	/* 设置缓存 可自定义时间*/
	function setcaches($key,$info,$time=0){
		DI()->redis->set($key,json_encode($info));
        if($time > 0){
            DI()->redis->expire($key, $time); 
        }
		
		return 1;
	}
	/* 获取缓存 */
	function getcache($key){
		$config=getConfigPri();

		if($config['cache_switch']!=1){
			$isexist=false;
		}else{
			$isexist=DI()->redis->Get($key);
		}

		return json_decode($isexist,true);
	}		
	/* 获取缓存 不判断后台设置 */
	function getcaches($key){

		$isexist=DI()->redis->Get($key);
		
		return json_decode($isexist,true);
	}
	
	/* 删除缓存 */
	function delcache($key){
		$isexist=DI()->redis->del($key);
		return 1;
	}	
    
    /* 密码检查 */
	function passcheck($user_pass) {
        /* 必须包含字母、数字 */
        $preg='/^(?=.*[A-Za-z])(?=.*[0-9])[a-zA-Z0-9~!@&%#_]{6,20}$/';
        $isok=preg_match($preg,$user_pass);
        if($isok){
            return 1;
        }
        return 0;
	}	
	/* 检验手机号 */
	function checkMobile($mobile){
		$ismobile = preg_match("/^1[3|4|5|6|7|8|9]\d{9}$/",$mobile);
		if($ismobile){
			return 1;
		}else{
			return 0;
		}
	}
	/* 随机数 */
	function random($length = 6 , $numeric = 0) {
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		if($numeric) {
			$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}
	/* 发送验证码--互译无线 */
	function sendCode_huiyi($mobile,$code){
		$rs=array();
		$config = getConfigPri();
        
        if(!$config['sendcode_switch']){
            $rs['code']=667;
			$rs['msg']='123456';
            return $rs;
        }
        
		/* 互亿无线 */
		$target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
		$content="您的验证码是：".$code."。请不要把验证码泄露给其他人。";
		$post_data = "account=".$config['ihuyi_account']."&password=".$config['ihuyi_ps']."&mobile=".$mobile."&content=".rawurlencode($content);
		//密码可以使用明文密码或使用32位MD5加密
		$gets = xml_to_array(Post($post_data, $target));
//        file_put_contents(API_ROOT.'/../log/phalapi/function_sendcode_huiyi_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 post_data:'.$post_data."\r\n",FILE_APPEND);
//        file_put_contents(API_ROOT.'/../log/phalapi/function_sendcode_huiyi_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'返回结果 gets:'.json_encode($gets)."\r\n\r\n",FILE_APPEND);
		if($gets['SubmitResult']['code']==2){
            setSendcode(array('type'=>'1','account'=>$mobile,'content'=>$content));
			$rs['code']=0;
		}else{
			$rs['code']=1002;
			//$rs['msg']=$gets['SubmitResult']['msg'];
			$rs['msg']="获取失败";
		} 
		return $rs;
	}

	function Post($curlPost,$url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
	}
	
	function xml_to_array($xml){
		$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
			$subxml= $matches[2][$i];
			$key = $matches[1][$i];
				if(preg_match( $reg, $subxml )){
					$arr[$key] = xml_to_array( $subxml );
				}else{
					$arr[$key] = $subxml;
				}
			}
		}
		return $arr;
	}
	/* 发送验证码 */
    
    	/* 发送验证码 -- 容联云 */
	function sendCode_ronglianyun($mobile,$code){
        
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
		$config = getConfigPri();
        
        if(!$config['sendcode_switch']){
            $rs['code']=667;
			$rs['msg']='123456';
            return $rs;
        }
        
        require_once API_ROOT.'/../sdk/ronglianyun/CCPRestSDK.php';
        
        //主帐号
        $accountSid= $config['ccp_sid'];
        //主帐号Token
        $accountToken= $config['ccp_token'];
        //应用Id
        $appId=$config['ccp_appid'];
        //请求地址，格式如下，不需要写https://
        $serverIP='app.cloopen.com';
        //请求端口 
        $serverPort='8883';
        //REST版本号
        $softVersion='2013-12-26';
        
        $tempId=$config['ccp_tempid'];
        
//        file_put_contents(API_ROOT.'/../log/phalapi/function_sendcode_ronglianyun_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 post_data: accountSid:'.$accountSid.";accountToken:{$accountToken};appId:{$appId};tempId:{$tempId}\r\n",FILE_APPEND);

        $rest = new REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);
        
        $datas=[];
        $datas[]=$code;
        
        $result = $rest->sendTemplateSMS($mobile,$datas,$tempId);
//        file_put_contents(API_ROOT.'/../log/phalapi/function_sendcode_rly_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n\r\n",FILE_APPEND);
        
         if($result == NULL ) {
            $rs['code']=1002;
			$rs['msg']="获取失败";
            return $rs;
         }
         if($result->statusCode!='000000') {
            //echo "error code :" . $result->statusCode . "<br>";
            //echo "error msg :" . $result->statusMsg . "<br>";
            //TODO 添加错误处理逻辑
            $rs['code']=1002;
			//$rs['msg']=$gets['SubmitResult']['msg'];
			$rs['msg']="获取失败";
            return $rs;
         }
        $content=$code;
        setSendcode(array('type'=>'1','account'=>$mobile,'content'=>$content));

		return $rs;
	}

	/* 发送验证码*/
	function sendCode($country_code,$mobile,$code){
        
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
		$config = getConfigPri();
        
        if(!$config['sendcode_switch']){
            $rs['code']=667;
			$rs['msg']='123456';
            return $rs;
        }

        $typecode_switch=$config['typecode_switch'];

		if($typecode_switch=='1'){//阿里云
			$res=sendCodeByAli($country_code,$mobile,$code);
		}else if($typecode_switch=='2'){ //容联云
			$res=sendCodeByRonglian($mobile,$code);
		}else if($typecode_switch=='3'){ //腾讯云
			$res=sendCodeByTencentSms($country_code,$mobile,$code);//腾讯云
		}

        $content=$code;
        setSendcode(array('type'=>'1','account'=>'+'.$country_code.'-'.$mobile,'content'=>$content,'send_type'=>$config['typecode_switch']));

		return $res;
	}

	function sendCodeByRonglian($mobile,$code){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
		$config = getConfigPri();
       
        require_once API_ROOT.'/../sdk/ronglianyun/CCPRestSDK.php';
        
        //主帐号
        $accountSid= $config['ccp_sid'];
        //主帐号Token
        $accountToken= $config['ccp_token'];
        //应用Id
        $appId=$config['ccp_appid'];
        //请求地址，格式如下，不需要写https://
        $serverIP='app.cloopen.com';
        //请求端口 
        $serverPort='8883';
        //REST版本号
        $softVersion='2013-12-26';
        
        $tempId=$config['ccp_tempid'];
        
        //file_put_contents(API_ROOT.'/../data/sendCode_rly_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 post_data: accountSid:'.$accountSid.";accountToken:{$accountToken};appId:{$appId};tempId:{$tempId}\r\n",FILE_APPEND);

        $rest = new REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);
        
        $datas=[];
        $datas[]=$code;
        
        $result = $rest->sendTemplateSMS($mobile,$datas,$tempId);
        //file_put_contents(API_ROOT.'/../data/sendCode_rly_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);
        
         if($result == NULL ) {
            $rs['code']=1002;
			$rs['msg']="获取失败";
            return $rs;
         }
         if($result->statusCode!='000000') {
            //echo "error code :" . $result->statusCode . "<br>";
            //echo "error msg :" . $result->statusMsg . "<br>";
            //TODO 添加错误处理逻辑
            $rs['code']=1002;
			//$rs['msg']=$gets['SubmitResult']['msg'];
			$rs['msg']="获取失败";
            return $rs;
         }
        

		return $rs;
	}
	//阿里云短信
	function sendCodeByAli($country_code,$mobile,$code){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $config = getConfigPri();

        //判断是否是国外
        $aly_sendcode_type=$config['aly_sendcode_type'];
        if($aly_sendcode_type==1 && $country_code!=86){ //国内
        	$rs['code']=1002;
			$rs['msg']="平台短信仅支持中国大陆地区";
            return $rs;
        }

        if($aly_sendcode_type==2 && $country_code==86){
        	$rs['code']=1002;
			$rs['msg']='平台短信仅支持国际/港澳台地区';
			return $rs;
        }
		
		require_once API_ROOT.'/../sdk/aliyunsms/AliSmsApi.php';

		$config_dl  = array(
            'accessKeyId' => $config['aly_keyid'], 
            'accessKeySecret' => $config['aly_secret'], 
            'PhoneNumbers' => $mobile, 
            'SignName' => $config['aly_signName'], //国内短信签名 
            'TemplateCode' => $config['aly_templateCode'], //国内短信模板ID
            'TemplateParam' => array("code"=>$code) 
        );

        $config_hw  = array(
            'accessKeyId' => $config['aly_keyid'], 
            'accessKeySecret' => $config['aly_secret'], 
            'PhoneNumbers' => $country_code.$mobile, 
            'SignName' => $config['aly_hw_signName'], //港澳台/国外短信签名 
            'TemplateCode' => $config['aly_hw_templateCode'], //港澳台/国外短信模板ID
            'TemplateParam' => array("code"=>$code) 
        );
        
        if($aly_sendcode_type==1){ //国内
            $config=$config_dl;
        }else if($aly_sendcode_type==2){ //国际/港澳台地区
            $config=$config_hw;
        }else{

            if($country_code==86){
                $config=$config_dl;
            }else{
                $config=$config_hw;
            }
        }
		 
		$go = new \AliSmsApi($config);
		$result = $go->send_sms();
//        file_put_contents(API_ROOT.'/../log/phalapi/function_sendCodeByAli_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);
		
        if($result == NULL ) {
            $rs['code']=1002;
			$rs['msg']="发送失败";
            return $rs;
        }
		if($result['Code']!='OK') {
            //TODO 添加错误处理逻辑
            $rs['code']=1002;
			//$rs['msg']=$result['Code'];
			$rs['msg']="获取失败";
            return $rs;
        }
		return $rs;
	}

	//腾讯云短信
	function sendCodeByTencentSms($nationCode,$mobile,$code){
		require_once API_ROOT."/../sdk/tencentSms/index.php";
		$rs=array();
		$configpri = getConfigPri();
        
        $appid=$configpri['tencent_sms_appid'];
        $appkey=$configpri['tencent_sms_appkey'];


		$smsSign_dl = $configpri['tencent_sms_signName'];
        $smsSign_hw = $configpri['tencent_sms_hw_signName'];
        $templateId_dl=$configpri['tencent_sms_templateCode'];
        $templateId_hw=$configpri['tencent_sms_hw_templateCode'];

		$tencent_sendcode_type=$configpri['tencent_sendcode_type'];

		if($tencent_sendcode_type==1){ //中国大陆
            $smsSign = $smsSign_dl;
            $templateId = $templateId_dl;

        }else if($tencent_sendcode_type==2){//港澳台/国际

            $smsSign=$smsSign_hw;
            $templateId = $templateId_hw;

        }else{ //全球

            if($nationCode==86){
                $smsSign = $smsSign_dl;
                $templateId = $templateId_dl;
            }else{
                $smsSign=$smsSign_hw;
                $templateId = $templateId_hw;
            }
        }

	
		$sender = new \Qcloud\Sms\SmsSingleSender($appid,$appkey);

		$params = [$code]; //参数列表与腾讯云后台创建模板时加的参数列表保持一致
		$result = $sender->sendWithParam($nationCode, $mobile, $templateId, $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
				
//		file_put_contents(API_ROOT.'/../log/phalapi/function_sendCodeByTencentSms_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);
		$arr=json_decode($result,TRUE);

		if($arr['result']==0 && $arr['errmsg']=='OK'){
            //setSendcode(array('type'=>'1','account'=>$mobile,'content'=>"验证码:".$code."---国家区号:".$nationCode));
			$rs['code']=0;
		}else{
			$rs['code']=1002;
			$rs['msg']=$arr['errmsg'];
			// $rs['msg']='验证码发送失败';
		} 
		return $rs;		
				
	}
    
    /* curl get请求 */
    function curl_get($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // 从证书中检查SSL加密算法是否存在
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
	}
    
	/* 检测文件后缀 */
	function checkExt($filename){
		$config=array("jpg","png","jpeg");
		$ext   =   pathinfo(strip_tags($filename), PATHINFO_EXTENSION);
		 
		return empty($config) ? true : in_array(strtolower($ext), $config);
	}	    
	/* 密码加密 */
	function setPass($pass){
		$authcode='rCt52pF2cnnKNB3Hkp';
		$pass="###".md5(md5($authcode.$pass));
		return $pass;
	}	
    /* 去除NULL 判断空处理 主要针对字符串类型*/
	function checkNull($checkstr){

		$checkstr=urldecode($checkstr);
        $checkstr=htmlspecialchars($checkstr);
        $checkstr=trim($checkstr);

		//$checkstr=filterEmoji($checkstr);
		if( strstr($checkstr,'null') || (!$checkstr && $checkstr!=0 ) ){
			$str='';
		}else{
			$str=$checkstr;
		}

		$str=htmlspecialchars($str);
		return $str;	
	}
	/* 去除emoji表情 */
	function filterEmoji($str){
		$str = preg_replace_callback(
			'/./u',
			function (array $match) {
				return strlen($match[0]) >= 4 ? '' : $match[0];
			},
			$str);
		return $str;
	}	
	/* 公共配置 */
	function getConfigPub() {
		$key='getConfigPub';
		$config=getcaches($key);
		$config=false;
		if(!$config){
			$config= DI()->notorm->option
					->select('option_value')
					->where("option_name='site_info'")
					->fetchOne();

            $config=json_decode($config['option_value'],true);
            
            if($config){
                setcaches($key,$config);
            }
			
		}
        if(isset($config['live_time_coin'])){
            if(is_array($config['live_time_coin'])){
                
            }else if($config['live_time_coin']){
                $config['live_time_coin']=preg_split('/,|，/',$config['live_time_coin']);
            }else{
                $config['live_time_coin']=array();
            }
        }else{
            $config['live_time_coin']=array();
        }
        
        if(isset($config['login_type'])){
            if(is_array($config['login_type'])){
                
            }else if($config['login_type']){
                $config['login_type']=preg_split('/,|，/',$config['login_type']);
            }else{
                $config['login_type']=array();
            }
        }else{
            $config['login_type']=array();
        }
        
        if(isset($config['share_type'])){
            if(is_array($config['share_type'])){
                
            }else if($config['share_type']){
                $config['share_type']=preg_split('/,|，/',$config['share_type']);
            }else{
                $config['share_type']=array();
            }
        }else{
            $config['share_type']=array();
        }
        
        if(isset($config['live_type'])){
            if(is_array($config['live_type'])){
                
            }else if($config['live_type']){
                $live_type=preg_split('/,|，/',$config['live_type']);
                foreach($live_type as $k=>$v){
                    $live_type[$k]=preg_split('/;|；/',$v);
                }
                $config['live_type']=$live_type;
            }else{
                $config['live_type']=array();
            }
        }else{
            $config['live_type']=array();
        }
        
		return 	$config;
	}		
	
	/* 私密配置 */
	function getConfigPri() {
		$key='getConfigPri';
		$config=getcaches($key);
		if(!$config){
			$config= DI()->notorm->option
					->select('option_value')
					->where("option_name='configpri'")
					->fetchOne();
            $config=json_decode($config['option_value'],true);
            if($config){
                setcaches($key,$config);
            }
			
		}
        
        if(isset($config['game_switch'])){
            if(is_array($config['game_switch'])){
                
            }else if($config['game_switch']){
                $config['game_switch']=preg_split('/,|，/',$config['game_switch']);
            }else{
                $config['game_switch']=array();
            }
        }else{
            $config['game_switch']=array();
        }

        
		return 	$config;
	}		
	
	/**
	 * 返回带协议的域名
	 */
	function get_host(){
		$config=getConfigPub();
		return $config['site'];
	}	
	
	/**
	 * 转化数据库保存的文件路径，为可以访问的url
	 */
	function get_upload_path($file){
        if($file==''){
            return $file;
        }
		if(strpos($file,"http")===0){
			return html_entity_decode(htmlspecialchars_decode($file));
		}else if(strpos($file,"/")===0){
			$filepath= get_host().$file;
			return html_entity_decode(htmlspecialchars_decode($filepath));
		}else{

			$fileinfo=explode("_",$file);//上传云存储标识：qiniu：七牛云；aws：亚马逊

			$storage_type=$fileinfo[0];
			$start=strlen($storage_type)+1;

			if($storage_type=='qiniu'){ //七牛云

				$space_host= DI()->config->get('app.Qiniu.space_host');
				$file=substr($file,$start);
				$filepath=$space_host."/".$file;
	            return html_entity_decode(htmlspecialchars_decode($filepath));

			}else if($storage_type=='aws'){ //亚马逊
				$configpri=getConfigPri();
				$space_host= $configpri['aws_hosturl'];
				$file=substr($file,$start);
				return html_entity_decode(htmlspecialchars_decode($space_host."/".$file));
			}else{

				$uptype=DI()->config->get('app.uptype');
	            if($uptype==1){
	                $space_host= DI()->config->get('app.Qiniu.space_host');
	                $filepath=$space_host."/".$file;
	            }else{
	                $filepath= get_host().'/upload/'.$file;
	            }

	            return html_entity_decode(htmlspecialchars_decode($filepath));

			}

            
			
			
		}
	}
	
	/* 判断是否关注 */
	function isAttention($uid,$touid) {

		if($uid<0 || $touid<0){
			return '0';
		}
		$isexist=DI()->notorm->user_attention
					->select("*")
					->where('uid=? and touid=?',$uid,$touid)
					->fetchOne();
		if($isexist){
			return  '1';
		}
        return  '0';
	}
	/* 是否黑名单 */
	function isBlack($uid,$touid) {

		if($uid<0 || $touid<0){
			return '0';
		}	
		$isexist=DI()->notorm->user_black
				->select("*")
				->where('uid=? and touid=?',$uid,$touid)
				->fetchOne();
		if($isexist){
			return '1';
		}
        return '0';
	}	
	
	/* 判断权限 */
	function isAdmin($uid,$liveuid) {

		if($uid<0){
			return 30;
		}
		if($uid==$liveuid){
			return 50;
		}
		$isuper=isSuper($uid);
		if($isuper){
			return 60;
		}
		$isexist=DI()->notorm->live_manager
					->select("*")
					->where('uid=? and liveuid=?',$uid,$liveuid)
					->fetchOne();
		if($isexist){
			return  40;
		}
		
		return  30;
			
	}	
	/* 判断账号是否超管 */
	function isSuper($uid){
		$isexist=DI()->notorm->user_super
					->select("*")
					->where('uid=?',$uid)
					->fetchOne();
		if($isexist){
			return 1;
		}			
		return 0;
	}
	/* 判断token */
	function checkToken($uid,$token) {

		//return 0;
		$userinfo=getcaches("token_".$uid);

		if(!$userinfo){
			$userinfo=DI()->notorm->user_token
						->select('token,expire_time')
						->where(['user_id'=>$uid])
						->fetchOne();
            if($userinfo){
                setcaches("token_".$uid,$userinfo);
            }
		}

		if((!$userinfo) || ($userinfo['token']!=$token) || ($userinfo['expire_time']<time())){
            return 700;
		}
        
        /* 是否禁用、拉黑 */
        $info=DI()->notorm->user
					->select('user_status,end_bantime')
					->where('id=? and user_type="2"',$uid)
					->fetchOne();

        if(!$info || $info['user_status']==0  || $info['end_bantime']>time()){
            return 700;	
        }
        
        return 	0;				
				
	}	
	
	/* 用户基本信息 */
	function getUserInfo($uid,$type=0) {

		if(!is_numeric($uid)){

			if($uid==='goodsorder_admin'){

				$configpub=getConfigPub(); 

				$info['user_nickname']="订单消息";
				$info['avatar']=get_upload_path('/orderMsg.png');
				$info['avatar_thumb']=get_upload_path('/orderMsg.png');
				$info['id']="goodsorder_admin";

			}

			$info['coin']="0";
			$info['sex']="1";
			$info['signature']='';
			$info['province']='';
			$info['city']='城市未填写';
			$info['birthday']='';
			$info['issuper']="0";
			$info['votestotal']="0";
			$info['consumption']="0";
			$info['location']='';
			$info['user_status']='1';
			$info['praise_num']='0';
			$info['bg_img']=$info['avatar'];

		}else{

			$info=getcaches("userinfo_".$uid);
			if($uid>0){
				$info=false;
			}
			
			
			if(!$info){
				$info=DI()->notorm->user
						->select('id,user_nickname,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,birthday,user_status,issuper,location,praise_num,bg_img')
						->where('id=? and user_type="2"',$uid)
						->fetchOne();


				if($info){
					
				}else if($type==1){
	                return 	$info;
	                
	            }else{
	                $info['id']=$uid;
	                $info['user_nickname']='用户不存在';
	                $info['avatar']='/default.jpg';
	                $info['avatar_thumb']='/default_thumb.jpg';
	                $info['sex']='0';
	                $info['signature']='';
	                $info['consumption']='0';
	                $info['votestotal']='0';
	                $info['province']='';
	                $info['city']='';
	                $info['birthday']='';
	                $info['issuper']='0';
	                $info['user_status']='1';
	                $info['location']='';
	                $info['praise_num']='0';
	                $info['bg_img']=$info['avatar'];

	                if($uid==1){
	                	$info['user_nickname']='直播系统小店';
	                }
	            }

	            if($uid<0){
	            	$info['user_nickname']='游客';
	            }


	            if($info){
	                setcaches("userinfo_".$uid,$info);
	            }
				
			}
	        if($info){
	            $info['level']=getLevel($info['consumption']);
	            $info['level_anchor']=getLevelAnchor($info['votestotal']);
	            $info['avatar']=get_upload_path($info['avatar']);
	            $info['avatar_thumb']=get_upload_path($info['avatar_thumb']);
	            $info['bg_img']=get_upload_path($info['bg_img']);
	                
	            $info['vip']=getUserVip($uid);
	            $info['liang']=getUserLiang($uid);
	            if($info['birthday']){
	                $info['birthday']=date('Y-m-d',$info['birthday']);   
	            }else{
	                $info['birthday']='';
	            }
	            
	        }


		}

		
		return 	$info;		
	}
	
	/* 会员等级 */
	function getLevelList(){
        $key='level';
		$level=getcaches($key);
		if(!$level){
			$level=DI()->notorm->level
					->select("*")
					->order("level_up asc")
					->fetchAll();
            if($level){
                setcaches($key,$level);	
            }
					 
		}
        
        foreach($level as $k=>$v){
            $v['thumb']=get_upload_path($v['thumb']);
            $v['thumb_mark']=get_upload_path($v['thumb_mark']);
            $v['bg']=get_upload_path($v['bg']);
            if($v['colour']){
                $v['colour']='#'.$v['colour'];
            }else{
                $v['colour']='#ffdd00';
            }
            $level[$k]=$v;
        }
        
        return $level;
    }
	function getLevel($experience){
		$levelid=1;
        $level_a=1;
		$level=getLevelList();

		foreach($level as $k=>$v){
			if( $v['level_up']>=$experience){
				$levelid=$v['levelid'];
				break;
			}else{
				$level_a = $v['levelid'];
			}
		}
		$levelid = $levelid < $level_a ? $level_a:$levelid;
		return (string)$levelid;
	}
	/* 主播等级 */
	function getLevelAnchorList(){
		$key='levelanchor';
		$level=getcaches($key);
		if(!$level){
			$level=DI()->notorm->level_anchor
					->select("*")
					->order("level_up asc")
					->fetchAll();
            if($level){
                setcaches($key,$level);
            }
            
		}
        
        foreach($level as $k=>$v){
            $v['thumb']=get_upload_path($v['thumb']);
            $v['thumb_mark']=get_upload_path($v['thumb_mark']);
            $v['bg']=get_upload_path($v['bg']);
            $level[$k]=$v;
        }
        
        return $level;
    }
	function getLevelAnchor($experience){
		$levelid=1;
		$level_a=1;
        $level=getLevelAnchorList();

		foreach($level as $k=>$v){
			if( $v['level_up']>=$experience){
				$levelid=$v['levelid'];
				break;
			}else{
				$level_a = $v['levelid'];
			}
		}
		$levelid = $levelid < $level_a ? $level_a:$levelid;
		return (string)$levelid;
	}

	/* 统计 直播 */
	function getLives($uid) {
		/* 直播中 */
		$count1=DI()->notorm->live
				->where('uid=? and islive="1"',$uid)
				->count();
		/* 回放 */
		$count2=DI()->notorm->live_record
					->where('uid=? ',$uid)
					->count();
		return 	$count1+$count2;
	}		
	
	/* 统计 关注 */
	function getFollows($uid) {
		$count=DI()->notorm->user_attention
				->where('uid=? ',$uid)
				->count();
		return 	$count;
	}			
	
	/* 统计 粉丝 */
	function getFans($uid) {
		$count=DI()->notorm->user_attention
				->where('touid=? ',$uid)
				->count();
		return 	$count;
	}		
	/**
	*  @desc 根据两点间的经纬度计算距离
	*  @param float $lat 纬度值
	*  @param float $lng 经度值
	*/
	function getDistance($lat1, $lng1, $lat2, $lng2){
		$earthRadius = 6371000; //近似地球半径 单位 米
		 /*
		   Convert these degrees to radians
		   to work with the formula
		 */

		$lat1 = ($lat1 * pi() ) / 180;
		$lng1 = ($lng1 * pi() ) / 180;

		$lat2 = ($lat2 * pi() ) / 180;
		$lng2 = ($lng2 * pi() ) / 180;


		$calcLongitude = $lng2 - $lng1;
		$calcLatitude = $lat2 - $lat1;
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
		$calculatedDistance = $earthRadius * $stepTwo;
		
		$distance=$calculatedDistance/1000;
		if($distance<10){
			$rs=round($distance,2);
		}else if($distance > 1000){
			$rs='1000';
		}else{
			$rs=round($distance);
		}
		return $rs.'km';
	}
	/* 判断账号是否禁用 */
	function isBanBF($uid){
		$status=DI()->notorm->user
					->select("user_status")
					->where('id=?',$uid)
					->fetchOne();
		if(!$status || $status['user_status']==0){
			return '0';
		}
		return '1';
	}
	/* 是否认证 */
	function isAuth($uid){
		$status=DI()->notorm->user_auth
					->select("status")
					->where('uid=?',$uid)
					->fetchOne();
		if($status && $status['status']==1){
			return '1';
		}

		return '0';
	}
	/* 过滤字符 */
	function filterField($field){
		$configpri=getConfigPri();
		
		$sensitive_field=$configpri['sensitive_field'];
		
		$sensitive=explode(",",$sensitive_field);
		$replace=array();
		$preg=array();
		foreach($sensitive as $k=>$v){
			if($v!=''){
				$re='';
				$num=mb_strlen($v);
				for($i=0;$i<$num;$i++){
					$re.='*';
				}
				$replace[$k]=$re;
				$preg[$k]='/'.$v.'/';
			}else{
				unset($sensitive[$k]);
			}
		}
		
		return preg_replace($preg,$replace,$field);
	}
	/* 时间差计算 */
	function datetime($time){
		$cha=time()-$time;
		$iz=floor($cha/60);
		$hz=floor($iz/60);
		$dz=floor($hz/24);
		/* 秒 */
		$s=$cha%60;
		/* 分 */
		$i=floor($iz%60);
		/* 时 */
		$h=floor($hz/24);
		/* 天 */
		
		if($cha<60){
			return $cha.'秒前';
		}else if($iz<60){
			return $iz.'分钟前';
		}else if($hz<24){
			return $hz.'小时'.$i.'分钟前';
		}else if($dz<30){
			return $dz.'天前';
		}else{
			return date("Y-m-d",$time);
		}
	}		
	/* 时长格式化 */
	/*function getSeconds($cha,$type=0){

		if($cha<0){
			return '0秒';
		}	 
		$iz=floor($cha/60);
		$hz=floor($iz/60);
		$dz=floor($hz/24);
		// 秒
		$s=$cha%60;
		// 分
		$i=floor($iz%60);
		// 时
		$h=floor($hz/24);
		// 天
		
        if($type==1){
            if($s<10){
                $s='0'.$s;
            }
            if($i<10){
                $i='0'.$i;
            }

            if($h<10){
                $h='0'.$h;
            }
            
            if($hz<10){
                $hz='0'.$hz;
            }
            return $hz.':'.$i.':'.$s;
        }
        
        
		if($cha<60){
			return $cha.'秒';
		}else if($iz<60){
			return $iz.'分钟'.$s.'秒';
		}else if($hz<24){
			return $hz.'小时'.$i.'分钟'.$s.'秒';
		}else{
			return $dz.'天'.$h.'小时'.$i.'分钟'.$s.'秒';
		}
	}*/	

	/* 时长格式化 */
	function getSeconds($time,$type=0){

			if(!$time){
				return (string)$time;
			}

		    $value = array(
		      "years"   => 0,
		      "days"    => 0,
		      "hours"   => 0,
		      "minutes" => 0,
		      "seconds" => 0
		    );
		    
		    if($time >= 31556926){
		      $value["years"] = floor($time/31556926);
		      $time = ($time%31556926);
		    }
		    if($time >= 86400){
		      $value["days"] = floor($time/86400);
		      $time = ($time%86400);
		    }
		    if($time >= 3600){
		      $value["hours"] = floor($time/3600);
		      $time = ($time%3600);
		    }
		    if($time >= 60){
		      $value["minutes"] = floor($time/60);
		      $time = ($time%60);
		    }
		    $value["seconds"] = floor($time);

		    if($value['years']){
		    	if($type==1&&$value['years']<10){
		    		$value['years']='0'.$value['years'];
		    	}
		    }

		    if($value['days']){
		    	if($type==1&&$value['days']<10){
		    		$value['days']='0'.$value['days'];
		    	}
		    }

		    if($value['hours']){
		    	if($type==1&&$value['hours']<10){
		    		$value['hours']='0'.$value['hours'];
		    	}
		    }

		    if($value['minutes']){
		    	if($type==1&&$value['minutes']<10){
		    		$value['minutes']='0'.$value['minutes'];
		    	}
		    }

		    if($value['seconds']){
		    	if($type==1&&$value['seconds']<10){
		    		$value['seconds']='0'.$value['seconds'];
		    	}
		    }

		    if($value['years']){
		    	$t=$value["years"] ."年".$value["days"] ."天". $value["hours"] ."小时". $value["minutes"] ."分".$value["seconds"]."秒";
		    }else if($value['days']){
		    	$t=$value["days"] ."天". $value["hours"] ."小时". $value["minutes"] ."分".$value["seconds"]."秒";
		    }else if($value['hours']){
		    	$t=$value["hours"] ."小时". $value["minutes"] ."分".$value["seconds"]."秒";
		    }else if($value['minutes']){
		    	$t=$value["minutes"] ."分".$value["seconds"]."秒";
		    }else if($value['seconds']){
		    	$t=$value["seconds"]."秒";
		    }
		    
		    return $t;

	}

	/* 数字格式化 */
	function NumberFormat($num){
		if($num<10000){

		}else if($num<1000000){
			$num=round($num/10000,2).'万';
		}else if($num<100000000){
			$num=round($num/10000,1).'万';
		}else if($num<10000000000){
			$num=round($num/100000000,2).'亿';
		}else{
			$num=round($num/100000000,1).'亿';
		}
		return $num;
	}

	/**
	*  @desc 获取推拉流地址
	*  @param string $host 协议，如:http、rtmp、trtc
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKeyA($host,$stream,$type){
		$configpri=getConfigPri();
		$cdn_switch=$configpri['cdn_switch'];
		//$cdn_switch=3;
		switch($cdn_switch){
			case '1':
				$url=PrivateKey_ali($host,$stream,$type);
				break;
			case '2':
				$url=PrivateKey_tx($host,$stream,$type);
				break;
			case '3':
				$url=PrivateKey_qn($host,$stream,$type);
				break;
			case '4':
				$url=PrivateKey_ws($host,$stream,$type);
				break;
			case '5':
				$url=PrivateKey_wy($host,$stream,$type);
				break;
			case '6':
				$url=PrivateKey_ady($host,$stream,$type);
				break;
		}

		
		return $url;
	}
	
	/**
	*  @desc 阿里云直播A类鉴权
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_ali($host,$stream,$type){
		$configpri=getConfigPri();
		$push=$configpri['push_url'];
		$pull=$configpri['pull_url'];
		$key_push=$configpri['auth_key_push'];
		$length_push=$configpri['auth_length_push'];
		$key_pull=$configpri['auth_key_pull'];
		$length_pull=$configpri['auth_length_pull'];
        
		if($type==1){
			$domain=$host.'://'.$push;
			$time=time() + $length_push;
		}else{
			$domain=$host.'://'.$pull;
			$time=time() + $length_pull;
		}
		
		$filename="/5showcam/".$stream;

		if($type==1){
            if($key_push!=''){
                $sstring = $filename."-".$time."-0-0-".$key_push;
                $md5=md5($sstring);
                $auth_key="auth_key=".$time."-0-0-".$md5;
            }
			if($auth_key){
				$auth_key='?'.$auth_key;
			}
			$url=$domain.$filename.$auth_key;
		}else{
            if($key_pull!=''){
                $sstring = $filename."-".$time."-0-0-".$key_pull;
                $md5=md5($sstring);
                $auth_key="auth_key=".$time."-0-0-".$md5;
            }
			if($auth_key){
				$auth_key='?'.$auth_key;
			}
			$url=$domain.$filename.$auth_key;
		}
		
		return $url;
	}
	
	/**
	*  @desc 腾讯云推拉流地址
	*  @param string $host 协议，如:http、rtmp、trtc
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_txBF($host,$stream,$type){
		$configpri=getConfigPri();
		$bizid=$configpri['tx_bizid'];
		$push_url_key=$configpri['tx_push_key'];
		$play_url_key=$configpri['tx_play_key'];
		$push=$configpri['tx_push'];
		$pull=$configpri['tx_pull'];
		$stream_a=explode('.',$stream);
		$streamKey = $stream_a[0];
		$ext = $stream_a[1];
		
		//$live_code = $bizid . "_" .$streamKey;      	
		$live_code = $streamKey; 

		$now=time();     	
		$now_time = $now + 3*60*60;
		$txTime = dechex($now_time);

		$txSecret = md5($push_url_key . $live_code . $txTime);
		$safe_url = "?txSecret=" .$txSecret."&txTime=" .$txTime;

		$play_safe_url='';
		//后台开启了播流鉴权
		if($configpri['tx_play_key_switch']){
			//播流鉴权时间

			$play_auth_time=$now+(int)$configpri['tx_play_time'];
			$txPlayTime = dechex($play_auth_time);
			$txPlaySecret = md5($play_url_key . $live_code . $txPlayTime);
			$play_safe_url = "?txSecret=" .$txPlaySecret."&txTime=" .$txPlayTime;

		}

		if($type==1){
			//$push_url = "rtmp://" . $bizid . ".livepush2.myqcloud.com/live/" .  $live_code . "?bizid=" . $bizid . "&record=flv" .$safe_url;	可录像
			$url = "rtmp://{$push}/live/" . $live_code . $safe_url;	
		}else{
			$url = "http://{$pull}/live/" . $live_code . ".flv".$play_safe_url;
			//$url = "http://{$pull}/live/" . $live_code . ".".$ext.$play_safe_url;（废弃）
		}
		
		return $url;
	}


	/**
	*  @desc 腾讯云推拉流地址
	*  @param string $host 协议，如:http、rtmp、trtc
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_tx($host,$stream,$type){

		$configpri=getConfigPri();

		$stream_arr=explode('.',$stream);
		$streamKey = isset($stream_arr[0])? $stream_arr[0] : '';
        $ext = isset($stream_arr[1])? $stream_arr[1] : '';

        $streamkey_arr=explode('_',$streamKey);

		$uid = $streamkey_arr[0];
		$showid = $streamkey_arr[1];

		$now=time();

		if($type==1){

			$live_sdk=$configpri['live_sdk'];
			if($live_sdk==1){
				//TRTC推流
				$url = getTxTrtcUrl($uid,$streamKey,1);
			}else{
				//rtmp推流
				$push=$configpri['tx_push'];
				$push_url_key=$configpri['tx_push_key'];
				$live_code=$streamKey;   	
				$now_time = $now + 3*60*60;
				$txTime = dechex($now_time);
				$txSecret = md5($push_url_key . $live_code . $txTime);
				$safe_url = "?txSecret=" .$txSecret."&txTime=" .$txTime;
				$url = "rtmp://{$push}/live/" . $live_code . $safe_url;
			}
			
			

		}else{

			//rtmp播流
			
			$pull=$configpri['tx_pull'];
			$play_url_key=$configpri['tx_play_key'];
			$play_safe_url='';
			$live_code=$streamKey;

			//后台开启了播流鉴权
			if($configpri['tx_play_key_switch']){
				//播流鉴权时间
				
				$play_auth_time=$now+(int)$configpri['tx_play_time'];
				$txPlayTime = dechex($play_auth_time);
				$txPlaySecret = md5($play_url_key . $live_code . $txPlayTime);
				$play_safe_url = "?txSecret=" .$txPlaySecret."&txTime=" .$txPlayTime;

			}

			$url = "http://{$pull}/live/" . $live_code . ".flv".$play_safe_url;
			
			if($ext){
                $url = "http://{$pull}/live/" . $live_code . ".".$ext.$play_safe_url;
            }
			
			$configpub=getConfigPub();
			if(strstr($configpub['site'],'https')){
                $url=str_replace('http:','https:',$url);
            }

            //TRTC播流
			//$url=getTxTrtcUrl($uid,$streamKey,0);

		}
		
		return $url;
	}

	/**
	*  @desc 获取腾讯云trtc推流/播流地址
	*  @param int $uid 观看用户id
	*  @param string $stream 流名 如:31258_1675238014
	*  @param int $type 流类型 0 播流 1 推流
	*/
	function getTxTrtcUrl($uid,$stream,$type=0){

		$configpri=getConfigPri();
		$txim_appid=$configpri['tencentIM_appid'];

        $user_sign=txImUserSign($uid);

        if($type==0){
        	$stream_type='play';
        }else{
        	$stream_type='push';
        }
		
        $url = 'trtc://cloud.tencent.com/'.$stream_type.'/'.$stream.'?sdkappid='.$txim_appid.'&userId='.$uid.'&usersig='.$user_sign.'&appscene=live';

		return $url;
	}

	/**
	*  @desc 七牛云直播
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_qn($host,$stream,$type){
		
        require_once API_ROOT.'/../sdk/qiniucdn/Pili_v2.php';
        
		$configpri=getConfigPri();
		$ak=$configpri['qn_ak'];
		$sk=$configpri['qn_sk'];
		$hubName=$configpri['qn_hname'];
		$push=$configpri['qn_push'];
		$pull=$configpri['qn_pull'];
		$stream_a=explode('.',$stream);
		$streamKey = $stream_a[0];
		$ext = $stream_a[1];

		if($type==1){
			$time=time() +60*60*10;
			
			//初始对象 创建流名,然后在进行推流
			//用于解决 Obs:无法访问指定的频道或串流秘钥的问题
			/*$mac = new \Qiniu\Pili\Mac($ak, $sk);
			$client = new \Qiniu\Pili\Client($mac);
			$hub = $client->hub($hubName);

			$stream_res = $hub->stream($streamKey);
			$resp = $hub->create($streamKey);*/
			
			//RTMP 推流地址
			$url = \Qiniu\Pili\RTMPPublishURL($push, $hubName, $streamKey, $time, $ak, $sk);
		}else{
			if($ext=='flv'){
				$pull=str_replace('pili-live-rtmp','pili-live-hdl',$pull);
				//HDL 直播地址
				$url = \Qiniu\Pili\HDLPlayURL($pull, $hubName, $streamKey);
			}else if($ext=='m3u8'){
				$pull=str_replace('pili-live-rtmp','pili-live-hls',$pull);
				//HLS 直播地址
				$url = \Qiniu\Pili\HLSPlayURL($pull, $hubName, $streamKey);
			}else{
				//RTMP 直播放址
				$url = \Qiniu\Pili\RTMPPlayURL($pull, $hubName, $streamKey);
			}
		}
				
		return $url;
	}
	
	/**
	*  @desc 网宿推拉流
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_ws($host,$stream,$type){
		$configpri=getConfigPri();
		if($type==1){
			$domain=$host.'://'.$configpri['ws_push'];
			//$time=time() +60*60*10;
		}else{
			$domain=$host.'://'.$configpri['ws_pull'];
			//$time=time() - 60*30 + $configpri['auth_length'];
		}
		
		$filename="/".$configpri['ws_apn']."/".$stream;

		$url=$domain.$filename;
		
		return $url;
	}
	
	/**网易cdn获取拉流地址**/
	function PrivateKey_wy($host,$stream,$type){
		$configpri=getConfigPri();
		$appkey=$configpri['wy_appkey'];
		$appSecret=$configpri['wy_appsecret'];
		$nonce =rand(1000,9999);
		$curTime=time();
		$var=$appSecret.$nonce.$curTime;
		$checkSum=sha1($appSecret.$nonce.$curTime);
		
		$header =array(
			"Content-Type:application/json;charset=utf-8",
			"AppKey:".$appkey,
			"Nonce:" .$nonce,
			"CurTime:".$curTime,
			"CheckSum:".$checkSum,
		);
        if($type==1){
            $url='https://vcloud.163.com/app/channel/create';
            $paramarr = array(
                "name"  =>$stream,
                "type"  =>0,
            );
        }else{
            $url='https://vcloud.163.com/app/address';
            $paramarr = array(
                "cid"  =>$stream,
            );
        }
        $paramarr=json_encode($paramarr);

		$curl=curl_init();
		curl_setopt($curl,CURLOPT_URL, $url);
		curl_setopt($curl,CURLOPT_HEADER, 0);
		curl_setopt($curl,CURLOPT_HTTPHEADER, $header); 
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_POST, 1);
		curl_setopt($curl,CURLOPT_POSTFIELDS, $paramarr);
		$data = curl_exec($curl);
		curl_close($curl);
		$rs=json_decode($data,1);
		return $rs;
	}
	
	/**
	*  @desc 奥点云推拉流
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_ady($host,$stream,$type){
		$configpri=getConfigPri();
		$stream_a=explode('.',$stream);
		$streamKey = $stream_a[0];
		$ext = $stream_a[1];

		if($type==1){
			$domain=$host.'://'.$configpri['ady_push'];
			//$time=time() +60*60*10;
			$filename="/".$configpri['ady_apn'].'/'.$stream;
			$url=$domain.$filename;
		}else{
			if($ext=='m3u8'){
				$domain=$host.'://'.$configpri['ady_hls_pull'];
				//$time=time() - 60*30 + $configpri['auth_length'];
				$filename="/".$configpri['ady_apn']."/".$stream;
				$url=$domain.$filename;
			}else{
				$domain=$host.'://'.$configpri['ady_pull'];
				//$time=time() - 60*30 + $configpri['auth_length'];
				$filename="/".$configpri['ady_apn']."/".$stream;
				$url=$domain.$filename;
			}
		}
				
		return $url;
	}

    /* 游戏类型 */
    function getGame($action){
        $game_action=array(
            '0'=>'',
            '1'=>'智勇三张',
            '2'=>'海盗船长',
            '3'=>'转盘',
            '4'=>'开心牛仔',
            '5'=>'二八贝',
        );
        
        return isset($game_action[$action])?$game_action[$action]:'';
    }
    
	/* 获取用户VIP */
	function getUserVip($uid){
		$rs=array(
			'type'=>'0',
			'endtime'=>''
		);

		if($uid<0){
			return $rs;
		}
		$nowtime=time();
		$key='vip_'.$uid;
		$isexist=getcaches($key);
		if(!$isexist){
			$isexist=DI()->notorm->vip_user
						->select("*")
						->where('uid=?',$uid)
						->fetchOne();			
			if($isexist){
				setcaches($key,$isexist);
			}
		}

		if($isexist){
			if($isexist['endtime'] <= $nowtime){
				return $rs;
            }
			$rs['type']='1';
			$rs['endtime'] = date("Y-m-d",$isexist['endtime']);
		}
		
		return $rs;
	}

	/* 获取用户坐骑 */
	function getUserCar($uid){
		$rs=array(
			'id'=>'0',
			'swf'=>'',
			'swftime'=>'0',
			'words'=>'',
		);

		if($uid<0){
			return $rs;
		}

		$nowtime=time();
		
		$key='car_'.$uid;
		$isexist=getcaches($key);
		if(!$isexist){
			$isexist=DI()->notorm->car_user
						->select("*")
						->where('uid=? and status=1',$uid)
						->fetchOne();
			if($isexist){
				setcaches($key,$isexist);
			}
        }
		if($isexist){
			if($isexist['endtime']<= $nowtime){
				return $rs;
			}
			$key2='carinfo';
			$car_list=getcaches($key2);
			if(!$car_list){
				$car_list=DI()->notorm->car
					->select("*")
                    ->order("list_order asc")
					->fetchAll();
				if($car_list){
					setcaches($key2,$car_list);
				}
			}
			$info=array();
			if($car_list){
				foreach($car_list as $k=>$v){
					if($v['id']==$isexist['carid']){
						$info=$v;
					}	
				}
				
				if($info){
					$rs['id']=$info['id'];
					$rs['swf']=get_upload_path($info['swf']) ;
					$rs['swftime']=$info['swftime'];
					$rs['words']=$info['words'];
                }
			}
			
		}
		
		return $rs;
	}

	/* 获取用户靓号 */
	function getUserLiang($uid){
		$rs=array(
			'name'=>'0',
		);

		if($uid<0){
			return $rs;
		}

		$nowtime=time();
		$key='liang_'.$uid;
		$isexist=getcaches($key);
		if(!$isexist){
			$isexist=DI()->notorm->liang
						->select("*")
						->where('uid=? and status=1 and state=1',$uid)
						->fetchOne();	
			if($isexist){
				setcaches($key,$isexist);
			}
		}
		if($isexist){
			$rs['name']=$isexist['name'];
		}
		
		return $rs;
	}
	
	/* 邀请奖励 */
	function setAgentProfit($uid,$total){

		$distribut1=0;
		$configpri=getConfigPri();
		if($configpri['agent_switch']==1){
			$agent=DI()->notorm->agent
				->select("*")
				->where('uid=?',$uid)
				->fetchOne();
			$isinsert=0;
			/* 一级 */
			if($agent['one_uid'] && $configpri['distribut1']){
				$distribut1=$total*$configpri['distribut1']*0.01;
                if($distribut1>0){
                    $profit=DI()->notorm->agent_profit
                        ->select("*")
                        ->where('uid=?',$agent['one_uid'])
                        ->fetchOne();
                    if($profit){
                        DI()->notorm->agent_profit
                            ->where('uid=?',$agent['one_uid'])
                            ->update(array('one_profit' => new NotORM_Literal("one_profit + {$distribut1}")));
                    }else{
                        DI()->notorm->agent_profit
                            ->insert(array('uid'=>$agent['one_uid'],'one_profit' =>$distribut1 ));
                    }
                    DI()->notorm->user
                            ->where('id=?',$agent['one_uid'])
                            ->update(array('votes' => new NotORM_Literal("votes + {$distribut1}")));
                    $isinsert=1;
                    $insert_votes=[
                        'type'=>'1',
                        'action'=>'3',
                        'uid'=>$agent['one_uid'],
                        'fromid'=>$uid,
                        'total'=>$distribut1,
                        'votes'=>$distribut1,
                        'addtime'=>time(),
                    ];
                    DI()->notorm->user_voterecord->insert($insert_votes);
                }
			}
			
			if($isinsert==1){
				$data=array(
					'uid'=>$uid,
					'total'=>$total,
					'one_uid'=>$agent['one_uid'],
					'one_profit'=>$distribut1,
					'addtime'=>time(),
				);
				
				DI()->notorm->agent_profit_recode->insert( $data );
				
			}
		}
		return 1;
		
	}
    
    /* 家族分成 */
    function setFamilyDivide($liveuid,$total){
        $configpri=getConfigPri();
	
		$anthor_total=$total;
		/* 家族 */
		if($configpri['family_switch']==1){
			$users_family=DI()->notorm->family_user
							->select("familyid,divide_family")
							->where('uid=? and state=2',$liveuid)
							->fetchOne();

			if($users_family){
				$familyinfo=DI()->notorm->family
							->select("uid,divide_family")
							->where('id=?',$users_family['familyid'])
							->fetchOne();
                if($familyinfo){
                    $divide_family=$familyinfo['divide_family'];

                    /* 主播 */
                    if( $users_family['divide_family']>=0){
                        $divide_family=$users_family['divide_family'];
                        
                    }
                    $family_total=$total * $divide_family * 0.01;
                    
                        $anthor_total=floor(($total - $family_total)*100)/100;
                        $addtime=time();
                        $time=date('Y-m-d',$addtime);
                        DI()->notorm->family_profit
                               ->insert(array("uid"=>$liveuid,"time"=>$time,"addtime"=>$addtime,"profit"=>$family_total,"profit_anthor"=>$anthor_total,"total"=>$total,"familyid"=>$users_family['familyid']));

                    if($family_total){
                        
                        DI()->notorm->user
                                ->where('id = ?', $familyinfo['uid'])
                                ->update( array( 'votes' => new NotORM_Literal("votes + {$family_total}")  ));
                                
                        $insert_votes=[
                            'type'=>'1',
                            'action'=>'4',
                            'uid'=>$familyinfo['uid'],
                            'fromid'=>$liveuid,
                            'total'=>$family_total,
                            'votes'=>$family_total,
                            'addtime'=>time(),
                        ];
                        DI()->notorm->user_voterecord->insert($insert_votes);
                    }
                }
			}	
		}
        return $anthor_total;
    }
	
	/* ip限定 */
	function ip_limit(){
		$configpri=getConfigPri();
		if($configpri['iplimit_switch']==0){
			return 0;
		}
		$date = date("Ymd");
		$ip= ip2long($_SERVER["REMOTE_ADDR"]) ; 
		
		$isexist=DI()->notorm->getcode_limit_ip
				->select('ip,date,times')
				->where(' ip=? ',$ip) 
				->fetchOne();
		if(!$isexist){
			$data=array(
				"ip" => $ip,
				"date" => $date,
				"times" => 1,
			);
			$isexist=DI()->notorm->getcode_limit_ip->insert($data);
			return 0;
		}elseif($date == $isexist['date'] && $isexist['times'] >= $configpri['iplimit_times'] ){
			return 1;
		}else{
			if($date == $isexist['date']){
				$isexist=DI()->notorm->getcode_limit_ip
						->where(' ip=? ',$ip) 
						->update(array('times'=> new NotORM_Literal("times + 1 ")));
				return 0;
			}else{
				$isexist=DI()->notorm->getcode_limit_ip
						->where(' ip=? ',$ip) 
						->update(array('date'=> $date ,'times'=>1));
				return 0;
			}
		}	
	}	
    
    /* 验证码记录 */
    function setSendcode($data){
        if($data){
            $data['addtime']=time();
            DI()->notorm->sendcode->insert($data);
        }
    }

    /* 检测用户是否存在 */
    function checkUser($where){
        if($where==''){
            return 0;
        }

        $isexist=DI()->notorm->user->where($where)->fetchOne();
        
        if($isexist){
            return 1;
        }
        
        return 0;
    }
    
    /* 直播分类 */
    function getLiveClass(){
        $key="getLiveClass";
		$list=getcaches($key);
		if(!$list){
            $list=DI()->notorm->live_class
					->select("*")
                    ->order("list_order asc,id desc")
					->fetchAll();
            if($list){
                setcaches($key,$list);
            }
			
		}
        
        foreach($list as $k=>$v){
            $v['thumb']=get_upload_path($v['thumb']);
            $list[$k]=$v;
        }
        return $list;        
        
    }
    /* 校验签名 */
    function checkSign($data,$sign){
        $key=DI()->config->get('app.sign_key');
        $str='';
        ksort($data);
        foreach($data as $k=>$v){
            $str.=$k.'='.$v.'&';
        }
        
        $str.=$key;
        $newsign=md5($str);

        if($sign==$newsign){
            return 1;
        }
        return 0;
    }

    
	/*获取音乐信息*/
	function getMusicInfo($user_nickname,$musicid){

		$res=DI()->notorm->music->select("id,title,author,img_url,length,file_url,use_nums")->where("id=?",$musicid)->fetchOne();

		if(!$res){
			$res=array();
			$res['id']='0';
			$res['title']='';
			$res['author']='';
			$res['img_url']='';
			$res['length']='00:00';
			$res['file_url']='';
			$res['use_nums']='0';
			$res['music_format']='@'.$user_nickname.'创作的原声';

		}else{
			$res['music_format']=$res['title'].'--'.$res['anchor'];
			$res['img_url']=get_upload_path($res['img_url']);
			$res['file_url']=get_upload_path($res['file_url']);
		}

		

		return $res;

	}

	/*距离格式化*/
	function distanceFormat($distance){
		if($distance<1000){
			return $distance.'米';
		}else{

			if(floor($distance/10)<10){
				return number_format($distance/10,1);  //保留一位小数，会四舍五入
			}else{
				return ">10千米";
			}
		}
	}

	/* 视频是否点赞 */
	function ifLike($uid,$videoid){
		$like=DI()->notorm->video_like
				->select("id")
				->where("uid='{$uid}' and videoid='{$videoid}'")
				->fetchOne();
		if($like){
			return 1;
		}else{
			return 0;
		}	
	}

	/* 视频是否踩 */
	function ifStep($uid,$videoid){
		$like=DI()->notorm->video_step
				->select("id")
				->where("uid='{$uid}' and videoid='{$videoid}'")
				->fetchOne();
		if($like){
			return 1;
		}else{
			return 0;
		}	
	}
    
    /* 拉黑视频名单 */
	function getVideoBlack($uid){
		$videoids=array('0');
		$list=DI()->notorm->video_black
						->select("videoid")
						->where("uid='{$uid}'")
						->fetchAll();
		if($list){
			$videoids=array_column($list,'videoid');
		}
		
		$videoids_s=implode(",",$videoids);
		
		return $videoids_s;
	}

    /* 生成二维码 */
    
    function scerweima($url='',$type=0,$uid=0){

    	if($type==1){
    		$key=$uid;
    	}else{
    		$key=md5($url);
    	}
        
        //生成二维码图片
        $filename2 = '/upload/qr/'.$key.'.png';
        $filename = API_ROOT.'/../public/upload/qr/'.$key.'.png';
        
        //if(!file_exists($filename)){
            require_once API_ROOT.'/../sdk/phpqrcode/phpqrcode.php';
            
            $value = $url;					//二维码内容
            
            $errorCorrectionLevel = 'H';	//容错级别 
            $matrixPointSize = 6.2068965517241379310344827586207;			//生成图片大小  
            
            //生成二维码图片
            \QRcode::png($value,$filename , $errorCorrectionLevel, $matrixPointSize, 2); 
       // }
      
        return $filename2;
    }
    
    /* 奖池信息 */
    function getJackpotInfo(){
        $jackpotinfo = DI()->notorm->jackpot->where("id = 1 ") -> fetchOne();
        return $jackpotinfo;
    }
    
    /* 奖池配置 */
    function getJackpotSet(){
        $key='jackpotset';
		$config=getcaches($key);
		if(!$config){
			$config= DI()->notorm->option
					->select('option_value')
					->where("option_name='jackpot'")
					->fetchOne();
            $config=json_decode($config['option_value'],true);
            if($config){
                setcaches($key,$config);
            }
			
		}
		return 	$config;
    }
    
    /* 奖池等级设置 */
    function getJackpotLevelList(){
        $key='jackpot_level';
        $list=getcaches($key);
        if(!$list){
            $list= DI()->notorm->jackpot_level->order("level_up asc")->fetchAll();
            if($list){
                setcaches($key,$list);
            }
        }
        return $list;
    }

    /* 奖池等级 */
    function getJackpotLevel($experience){
        $levelid='0';

		$level=getJackpotLevelList();

		foreach($level as $k=>$v){
			if( $v['level_up']<=$experience){
				$levelid=$v['levelid'];
			}
		}

		return (string)$levelid;
    }
    /* 奖池中奖配置 */
    function getJackpotRate(){
        $key='jackpot_rate';
        $list=getcaches($key);
        if(!$list){
            $list= DI()->notorm->jackpot_rate->order("id desc")->fetchAll();
            if($list){
                setcaches($key,$list);
            }
        }
        return $list;
    }

    /* 幸运礼物中奖配置 */
    function getLuckRate(){
        $key='gift_luck_rate';
        $list=getcaches($key);
        if(!$list){
            $list= DI()->notorm->gift_luck_rate->order("id desc")->fetchAll();
            if($list){
                setcaches($key,$list);
            }
        }
        return $list;
    }
    
    /* 视频数据处理 */
    function handleVideo($uid,$v){
        
			$userinfo=getUserInfo($v['uid']);
			if(!$userinfo){
				$userinfo['user_nickname']="已删除";
			}

			//防止uid为0时因为找不到用户信息而出现头像昵称为null的问题
			$v['user_nickname']=$userinfo['user_nickname'];
			$v['avatar']=$userinfo['avatar'];
			
			$v['userinfo']=$userinfo;
			$v['datetime']=datetime($v['addtime']);	
			$v['addtime']=date('Y-m-d H:i:s',$v['addtime']);	
			$v['comments']=NumberFormat($v['comments']);	
			$v['likes']=NumberFormat($v['likes']);	
			$v['steps']=NumberFormat($v['steps']);	
            
            $v['islike']='0';	
            $v['isstep']='0';	
            $v['isattent']='0';
            
			if($uid>0){
				$v['islike']=(string)ifLike($uid,$v['id']);	
				$v['isstep']=(string)ifStep($uid,$v['id']);	
			}
            
            if($uid>0 && $uid!=$v['uid']){
                $v['isattent']=(string)isAttention($uid,$v['uid']);	
            }
            
			$v['thumb']=get_upload_path($v['thumb']);
			$v['thumb_s']=get_upload_path($v['thumb_s']);
			$v['href']=get_upload_path($v['href']);
			$v['href_w']=get_upload_path($v['href_w']);
            
            $v['ad_url']=get_upload_path($v['ad_url']);

            if($v['ad_endtime']>0 &&($v['ad_endtime']<time())){
                $v['ad_url']='';
            }

            $goods_type='0';

            if($v['type']==1){ //视频绑定商品
            	$goodsid=$v['goodsid'];
            	//获取商品的类型是站内商品还是外链商品
            	$goods_type=DI()->notorm->shop_goods->where("id=?",$goodsid)->fetchOne('type');
            }

            $v['goods_type']=(String)$goods_type;
            
            /* 绑定的内容【废弃】 */
            /*$goodsinfo=(object)[];


            if($v['goodsid']>0){
                $goodsinfo=DI()->notorm->shop_goods
                            ->select("type,name,href,thumb,old_price,price,des")
                            ->where('id=? and status=1',$v['goodsid'])
                            ->fetchOne();
                if($goodsinfo){
                    $goodsinfo['thumb']=get_upload_path($goodsinfo['thumb']);
                }else{
                    $v['goodsid']='0';
                    $goodsinfo=(object)[];
                }
            }
            $v['goodsinfo']=$goodsinfo;*/
            
			unset($v['ad_endtime']);
			unset($v['orderno']);
			unset($v['isdel']);
			unset($v['show_val']);
			unset($v['xiajia_reason']);
			unset($v['nopass_time']);
			unset($v['watch_ok']);

        return $v;
    }
    //账号是否禁用
	function  isBan($uid){

		$result= DI()->notorm->user->where("end_bantime>? and id=?",time(),$uid)->fetchOne();
		if($result){
			return 0;
		}
		
		return 1;
	}
	/* 时长格式化 */
	function getBanSeconds($cha,$type=0){		 
		$iz=floor($cha/60);
		$hz=floor($iz/60);
		$dz=floor($hz/24);
		/* 秒 */
		$s=$cha%60;
		/* 分 */
		$i=floor($iz%60);
		/* 时 */
		$h=floor($hz/24);
		/* 天 */
        
        if($type==1){
            if($s<10){
                $s='0'.$s;
            }
            if($i<10){
                $i='0'.$i;
            }

            if($h<10){
                $h='0'.$h;
            }
            
            if($hz<10){
                $hz='0'.$hz;
            }
            return $hz.':'.$i.':'.$s; 
        }
        
		
		if($cha<60){
			return $cha.'秒';
		}else if($iz<60){
			return $iz.'分钟'.$s.'秒';
		}else if($hz<24){
			return $hz.'小时'.$i.'分钟';
		}else if($dz<30){
			return $dz.'天'.$h.'小时';
		}
	}	
	
	/* 过滤：敏感词 */
	function sensitiveField($field){
		if($field){
			$configpri=getConfigPri();
			
			$sensitive_words=$configpri['sensitive_words'];
			
			$sensitive=explode(",",$sensitive_words);
			$replace=array();
			$preg=array();
			
			foreach($sensitive as $k=>$v){
				if($v!=''){
					if(strstr($field, $v)!==false){
						return 1001;
					}
				}else{
					unset($sensitive[$k]);
				}
			}
		}
		return 1;
	}
	 /* 视频分类 */
    function getVideoClass(){
        $key="getVideoClass";
		$list=getcaches($key);
		if(!$list){
            $list=DI()->notorm->video_class
					->select("*")
                    ->order("list_order asc,id desc")
					->fetchAll();
			setcaches($key,$list); 
		}
        return $list;        
        
    }
	 /* 动态数据处理 */
    function handleDynamic($uid,$v){
     
			$v['datetime']=datetime($v['addtime']);
				if(!$v['city']){
					$v['city']="好像在火星";
				}
				if($v['thumb']){
					$thumbs=explode(";",$v['thumb']);
					foreach($thumbs as $kk=>$vv){
					
						$thumbs[$kk]=get_upload_path($vv);
					}
					$v['thumbs']=$thumbs;
				}else{
					$v['thumbs']=array();
				}
				
				if($v['video_thumb']){
					$v['video_thumb']=get_upload_path($v['video_thumb']);
				}
			   
				if($v['voice']){
					$v['voice']=get_upload_path($v['voice']);
				}
				if($v['href']){
					$v['href']=get_upload_path($v['href']);
				}
				
				$v['likes']=NumberFormat($v['likes']);
				$v['comments']=NumberFormat($v['comments']);
				
				if($uid<0){
					$v['islike']='0';
				}else{
					if($v['uid']==$uid){
						$v['islike']='0';
					}else{
						$v['islike']=isdynamiclike($uid,$v['id']);
					}
				}
				
				$userinfo=getUserInfo($v['uid']);
				$user['id']=$userinfo['id'];
				$user['user_nickname']=$userinfo['user_nickname'];
				$user['avatar']=$userinfo['avatar'];
				$user['avatar_thumb']=$userinfo['avatar_thumb'];
				$user['sex']=$userinfo['sex'];
				$user['isAttention']=isAttention($uid,$v['uid']);
				
				
				$v['userinfo']=$user;
				
				/* 标签 */
				$label_name='';
				if($v['labelid']>0){
					$labelinfo=getLabelInfo($v['labelid']);
					if($labelinfo){
						$label_name='#'.$labelinfo['name'];
					}else{
						$v['labelid']='0';
					}
				}
				$v['label_name']=$label_name;

				$v['goodsinfo']=(object)[];
				if($v['goodsid']){
					$model_shop=new Model_Shop();
					$where['id']=$v['goodsid'];
					$where['status']='1';

					$goodsinfo=$model_shop->getGoods($where);

					$v['goodsinfo']=handleGoods($goodsinfo);
				}

			return $v;
    }
	
	
	
	/* 标签信息 */
    function getLabelInfo($labelid){
        $key='LabelInfo_'.$labelid;
        $info=getcaches($key);
        if(!$info){
            $info=DI()->notorm->dynamic_label
                ->select("id,name,thumb")
                ->where('id=?',$labelid)
                ->fetchOne();
            if($info){
                setcaches($key,$info);
            }
        }
        if($info){
            $info['thumb']=get_upload_path($info['thumb']);
        }
        
        return $info;
    }
	
	 /* 动态：是否点赞 */
	function isdynamiclike($uid,$dynamicid) {
        
		$isexist=DI()->notorm->dynamic_like
						->select("id")
						->where("uid='{$uid}' and dynamicid='{$dynamicid}'")
						->fetchOne();
        if($isexist){
            return '1';
        }
        
		return '0';
	}
    
    /* 处理直播信息 */
    function handleLive($v){
        
        $configpri=getConfigPri();
        
        $nums=DI()->redis->zCard('user_'.$v['stream']);
        $v['nums']=(string)$nums;
        
        $userinfo=getUserInfo($v['uid']);
        $v['avatar']=$userinfo['avatar'];
        $v['avatar_thumb']=$userinfo['avatar_thumb'];
        $v['user_nickname']=$userinfo['user_nickname'];
        $v['sex']=$userinfo['sex'];
        $v['level']=$userinfo['level'];
        $v['level_anchor']=$userinfo['level_anchor'];
        
        if(!$v['thumb']){
            $v['thumb']=$v['avatar'];
        }
        if($v['isvideo']==0 && $configpri['cdn_switch']!=5){
            $v['pull']=PrivateKeyA('rtmp',$v['stream'],0);
        }
        
        if($v['type']==1){
            $v['type_val']='';
        }
		$v['thumb']=get_upload_path($v['thumb']);
        $v['game']=getGame($v['game_action']);
        
        return $v;
    }

    //身份证检测
	/*function checkCardNoBF($cardno){
		$preg='/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/';
		$isok=preg_match($preg, $cardno);
		if($isok){
			return 1;
		}else{
			return 0;
		}
	}*/

    /**
	 * 判断是否为合法的身份证号码
	 * @param $mobile
	 * @return int
	 */
	function isCreditNo($vStr){
		
		$vCity = array(
		  	'11','12','13','14','15','21','22',
		  	'23','31','32','33','34','35','36',
		  	'37','41','42','43','44','45','46',
		  	'50','51','52','53','54','61','62',
		  	'63','64','65','71','81','82','91'
		);
		
		if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)){
		 	return false;
		}

	 	if (!in_array(substr($vStr, 0, 2), $vCity)){
	 		return false;
	 	}
	 
	 	$vStr = preg_replace('/[xX]$/i', 'a', $vStr);
	 	$vLength = strlen($vStr);

	 	if($vLength == 18){
	  		$vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
	 	}else{
	  		$vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
	 	}

		if(date('Y-m-d', strtotime($vBirthday)) != $vBirthday){
		 	return false;
		}

	 	if ($vLength == 18) {
	  		$vSum = 0;
	  		for ($i = 17 ; $i >= 0 ; $i--) {
	   			$vSubStr = substr($vStr, 17 - $i, 1);
	   			$vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
	  		}
	  		if($vSum % 11 != 1){
	  			return false;
	  		}
	 	}

	 	return true;
	}

	/*判断店铺是否审核通过*/
	function checkShopIsPass($uid){
		$info=DI()->notorm->shop_apply->select("status")->where("uid=?",$uid)->fetchOne();
		if(!$info){
			return '0';
		}

		$status=$info['status'];
		if($status!=1){
			return '0';
		}

		return '1';
	}

	/*获取店铺申请状态*/
	function getShopApplyStatus($uid){
		$info=DI()->notorm->shop_apply
				->select("status")
                ->where("uid=?",$uid)
                ->fetchOne();

        if(!$info){
        	return '-1';
        }

        return $info['status'];
	}

	//获取商品分类信息
	function getGoodsClassInfo($classid){
		$info=DI()->notorm->shop_goods_class->where("gc_id=?",$classid)->fetchOne();
		if(!$info){
			return '';
		}
		return $info;
	}

	// 获取用户的余额
	function getUserBalance($uid){
		$res=array(
			'balance'=>'0.00',
			'balance_total'=>'0.00'
		);

		$info=DI()->notorm->user->where("id=?",$uid)->select("balance,balance_total")->fetchOne();

		if($info){
			$res['balance']=$info['balance'];
			$res['balance_total']=$info['balance_total'];
		}

		return $res;
	}

	//商品列表格式化处理
	function handleGoodsList($where,$p,$order="id desc"){

		if($p<1){
            $p=1;
        }
        
        $nums=50;
        $start=($p-1)*$nums;
		
        $list=DI()->notorm->shop_goods
                ->select("id,name,thumbs,sale_nums,specs,hits,issale,type,original_price,present_price,status,live_isshow,commission")
                ->where($where)
                ->order($order)
                ->limit($start,$nums)
                ->fetchAll();
		

		if(!$list){
			return [];
		}

		foreach ($list as $k => $v) {
            $thumb_arr=explode(',',$v['thumbs']);
            $list[$k]['thumb']=get_upload_path($thumb_arr[0]);

            
            if($v['type']==1){ //外链商品
            	$list[$k]['price']=(string)$v['present_price'];
            	$list[$k]['specs']=[];
            }else{
            	$spec_arr=json_decode($v['specs'],true);
            	$list[$k]['price']=(string)$spec_arr[0]['price'];
            	$list[$k]['specs']=$spec_arr;
            }
 

            unset($list[$k]['thumbs']);
            unset($list[$k]['present_price']);
        }

        return $list;
	}

	//单个商品信息格式化处理
	function handleGoods($goodsinfo){


		//获取商品的分类名称
        $one_classinfo=getGoodsClassInfo($goodsinfo['one_classid']);
        $two_classinfo=getGoodsClassInfo($goodsinfo['two_classid']);
        $three_classinfo=getGoodsClassInfo($goodsinfo['three_classid']);

        $goodsinfo['one_class_name']=isset($one_classinfo['gc_name'])?$one_classinfo['gc_name']:'分类不存在';
        $goodsinfo['two_class_name']=isset($two_classinfo['gc_name'])?$two_classinfo['gc_name']:'分类不存在';
        $goodsinfo['three_class_name']=isset($three_classinfo['gc_name'])?$three_classinfo['gc_name']:'分类不存在';

        $goodsinfo['hits']=isset($goodsinfo['hits'])?NumberFormat($goodsinfo['hits']):'0';
        $goodsinfo['sale_nums']=isset($goodsinfo['sale_nums'])?NumberFormat($goodsinfo['sale_nums']):'0';
        $goodsinfo['video_url_format']=isset($goodsinfo['video_url'])?get_upload_path($goodsinfo['video_url']):'';
        $goodsinfo['video_thumb_format']=isset($goodsinfo['video_thumb'])?get_upload_path($goodsinfo['video_thumb']):'';

        if($goodsinfo['thumbs']){
        	$thumb_arr=explode(',',$goodsinfo['thumbs']);
	        foreach ($thumb_arr as $k => $v) {
	        	$thumb_arr[$k]=get_upload_path($v);
	        }
        }else{
        	$thumb_arr=[];
        }

        $goodsinfo['thumbs_format']=$thumb_arr;

        if($goodsinfo['type']==1){ //外链商品
        	$goodsinfo['specs_format']=[];
        }else{

        	$spec_arr=(array)json_decode($goodsinfo['specs'],true);
	        foreach ($spec_arr as $k => $v) {
	        	$spec_arr[$k]['thumb']=get_upload_path($v['thumb']);
	        	$spec_arr[$k]['price']=(string)$v['price'];
	        }
	        $goodsinfo['specs_format']=$spec_arr;
        }

        

        if($goodsinfo['pictures']){
        	$picture_arr=explode(',', $goodsinfo['pictures']);
	        foreach ($picture_arr as $k => $v) {
	        	$picture_arr[$k]=get_upload_path($v);
	        }
        }else{
        	$picture_arr=[];
        }

        

        $goodsinfo['pictures_format']=$picture_arr;

        if($goodsinfo['postage']==0){
        	$goodsinfo['postage']='0.0';
        }

        if($goodsinfo['share_income']==0){
        	$goodsinfo['share_income']='0.0';
        }

        $goodsinfo['original_price']=(string)$goodsinfo['original_price'];
        $goodsinfo['present_price']=(string)$goodsinfo['present_price'];
        $goodsinfo['low_price']=(string)$goodsinfo['low_price'];
        $goodsinfo['postage']=(string)$goodsinfo['postage'];
        $goodsinfo['commission']=(string)$goodsinfo['commission'];
        $goodsinfo['share_income']=(string)$goodsinfo['share_income'];

        unset($goodsinfo['addtime']);
        unset($goodsinfo['uptime']);

        return $goodsinfo;
	}

	// 获取用户店铺余额
	function getUserShopBalance($uid){
		$info=DI()->notorm->user
			->select("balance,balance_total")
			->where("id=?",$uid)
			->fetchOne();

		return $info;
	}

	// 获取店铺商品订单详情
	function getShopOrderInfo($where,$files='*'){

		$info=DI()->notorm->shop_order
			->select($files)
			->where($where)
			->fetchOne();

		
		return $info;
		
	}

	//修改用户的余额 type:0 扣除余额 1 增加余额
	function setUserBalance($uid,$type,$balance){

		$res=0;

		if($type==0){ //扣除用户余额，增加用户余额消费总额
			$res=DI()->notorm->user
				->where("id=? and balance>=?",$uid,$balance)
				->update(array('balance' => new NotORM_Literal("balance - {$balance}"),'balance_consumption'=>new NotORM_Literal("balance_consumption + {$balance}")) );

		}else if($type==1){ //增加用户余额

			$res=DI()->notorm->user
				->where("id=?",$uid)
				->update(array('balance' => new NotORM_Literal("balance + {$balance}"),'balance_total'=>new NotORM_Literal("balance_total + {$balance}")) );
		}

		return $res;
		
	}



	// 修改店铺商品订单状态【 -1 已关闭  0 待付款 1 待发货 2 待收货 3 待评价 4 已评价 5 退款】
	function changeShopOrderStatus($uid,$orderid,$data){

		$res=DI()->notorm->shop_order
			->where("id=?",$orderid)
			->update($data);

		return $res;
	}


	// 根据不同条件获取订单总数
	function getOrderNums($where){
		
		$count=DI()->notorm->shop_order->where($where)->count();
		return $count;
	}

	// 根据不同条件获取物流列表信息
	function getExpressInfo($where){
		$info=DI()->notorm->shop_express
				->where($where)
				->fetchOne();

		return $info;
	}

	//添加余额操作记录
	function addBalanceRecord($data){
		$res=DI()->notorm->user_balance_record->insert($data);
		return $res;
	}

	//获取店铺设置的有效时间
	function getShopEffectiveTime(){


		$configpri=getConfigPri();
		$shop_payment_time=$configpri['shop_payment_time']; //付款有效时间（单位：分钟）
		$shop_shipment_time=$configpri['shop_shipment_time']; //发货有效时间（单位：天）
		$shop_receive_time=$configpri['shop_receive_time']; //自动确认收货时间（单位：天）
		$shop_refund_time=$configpri['shop_refund_time']; //买家发起退款,卖家不做处理自动退款时间（单位：天）
		$shop_refund_finish_time=$configpri['shop_refund_finish_time']; //卖家拒绝买家退款后,买家不做任何操作,退款自动完成时间（单位：天）
		$shop_receive_refund_time=$configpri['shop_receive_refund_time']; //订单确认收货后,指定天内可以发起退货退款（单位：天）
		$shop_settlement_time=$configpri['shop_settlement_time']; //订单确认收货后,货款自动打到卖家的时间（单位：天）

		$data['shop_payment_time']=$shop_payment_time;
		$data['shop_shipment_time']=$shop_shipment_time;
		$data['shop_receive_time']=$shop_receive_time;
		$data['shop_refund_time']=$shop_refund_time;
		$data['shop_refund_finish_time']=$shop_refund_finish_time;
		$data['shop_receive_refund_time']=$shop_receive_refund_time;
		$data['shop_settlement_time']=$shop_settlement_time;

		return $data;
	}

	//订单自动处理【用于买家/卖家获取订单列表时自动处理】
	function goodsOrderAutoProcess($uid,$where){

        $list=DI()->notorm->shop_order
            ->select("*")
            ->where($where)
            ->where("status !=-1") //待付款、待发货、待收货、待评价、已评价、退款
            ->order("addtime desc")
            ->fetchAll();

        $effective_time=getShopEffectiveTime();

        foreach ($list as $k => $v) {

        	$now=time();

            if($v['status']==0){ //待付款要判断是否付款超时

                $pay_end=$v['addtime']+$effective_time['shop_payment_time']*60;
                if($pay_end<=$now){
                    $data=array(
                        'status'=>-1,
                        'cancel_time'=>$now
                    );
                    changeShopOrderStatus($v['uid'],$v['id'],$data); //将订单关闭

                    //商品规格库存回增
                    changeShopGoodsSpecNum($v['goodsid'],$v['spec_id'],$v['nums'],1);

                    //给买家发消息
                    $title="你购买的“".$v['goods_name']."”订单由于超时未付款,已自动关闭";
                    $data1=array(
			            'uid'=>$v['uid'],
			            'orderid'=>$v['id'],
			            'title'=>$title,
			            'addtime'=>$now,
			            'type'=>'0'

			        );

			        addShopGoodsOrderMessage($data1);
			        //发送腾讯IM
        			txMessageIM($title,$v['uid'],'goodsorder_admin');

                }
            }

            if($v['status']==1){ //买家已付款 判断卖家发货是否超时

            	//如果买家没有申请退款
            	if($v['refund_status']==0){

            		$shipment_end=$v['paytime']+$effective_time['shop_shipment_time']*60*60*24;
	                
            	}else{ //买家申请了退款，判断时间超时，要根据退款最终的处理时间
            	
            		$shipment_end=$v['refund_endtime']+$effective_time['shop_shipment_time']*60*60*24;
            	}

            	if($shipment_end<=$now){
                    $data=array(
                        'status'=>-1,
                        'cancel_time'=>$now
                    );
                    changeShopOrderStatus($v['uid'],$v['id'],$data); //将订单关闭

                    //退还买家货款
                    setUserBalance($v['uid'],1,$v['total']);

                    //添加余额操作记录
                    $data1=array(
                        'uid'=>$v['uid'],
                        'touid'=>$v['shop_uid'],
                        'balance'=>$v['total'],
                        'type'=>1,
                        'action'=>3, //卖家超时未发货,退款给买家
                        'orderid'=>$v['id'],
                        'addtime'=>$now

                    );

                    addBalanceRecord($data1);

                    //店铺逾期发货记录+1
                    DI()->notorm->shop_apply
                    	->where("uid=?",$v['shop_uid'])
                    	->update(
                    		array('shipment_overdue_num' => new NotORM_Literal("shipment_overdue_num + 1"))
                    	);

                    //减去商品销量
            		changeShopGoodsSaleNums($v['goodsid'],0,$v['nums']);

                   	//减去店铺销量
        			changeShopSaleNums($v['shop_uid'],0,$v['nums']);

                    //给买家发消息
                    $title="你购买的“".$v['goods_name']."”订单由于卖家超时未发货已自动关闭,货款已退还到余额账户中";
                    $data2=array(
			            'uid'=>$v['uid'],
			            'orderid'=>$v['id'],
			            'title'=>$title,
			            'addtime'=>$now,
			            'type'=>'0'

			        );

			        addShopGoodsOrderMessage($data2);
			        //发送腾讯IM
        			txMessageIM($title,$v['uid'],'goodsorder_admin');

                }


                
            }

            if($v['status']==2){ //待收货 判断自动确认收货时间是否已满足

                //如果买家没有申请退款
            	if($v['refund_status']==0){
            		$receive_end=$v['shipment_time']+$effective_time['shop_receive_time']*60*60*24;
            	}else{
            		$receive_end=$v['refund_endtime']+$effective_time['shop_receive_time']*60*60*24;
            	}

                if($receive_end<=$now){
                    $data=array(
                        'status'=>3,
                        'receive_time'=>$now
                    );

                    changeShopOrderStatus($v['uid'],$v['id'],$data); //将订单改为待评价

                    //给买家发消息
                    $title="你购买的“".$v['goods_name']."”订单已自动确认收货";
                    $data1=array(
			            'uid'=>$v['uid'],
			            'orderid'=>$v['id'],
			            'title'=>$title,
			            'addtime'=>$now,
			            'type'=>'0'

			        );

			        addShopGoodsOrderMessage($data1);
			        //发送腾讯IM
        			txMessageIM($title,$v['uid'],'goodsorder_admin');
                }

            }


            if(($v['status']==3||$v['status']==4)&&$v['settlement_time']==0){  //待评价或已评价 且未结算

            	//判断是否有过退货处理 判断确认收货后是否达到后台设置的给卖家打款的时间
            	if($v['refund_status']==0){
            		$settlement_end=$v['receive_time']+$effective_time['shop_settlement_time']*60*60*24;
            	}else{
            		$settlement_end=$v['refund_endtime']+$effective_time['shop_settlement_time']*60*60*24;	
            	}

            	
            	if($settlement_end<=$now){

            		

			        //判断自动结算记录是否存在
			        $balance_record=DI()->notorm->user_balance_record->where("uid=? and touid=? and type=1 and action=2 and orderid=?",$v['shop_uid'],$v['uid'],$v['id'])->fetchOne();

			        if(!$balance_record){

			        	

	                    //计算主播代售平台商品佣金
                    	if($v['commission']>0 && $v['liveuid']){

                    		//给主播增加余额
                    		setUserBalance($v['liveuid'],1,$v['commission']);

                    		//写入余额操作记录
                    		$data3=array(
		                        'uid'=>$v['liveuid'], //主播ID
		                        'touid'=>$v['uid'], //买家用户ID
		                        'balance'=>$v['commission'],
		                        'type'=>1,
		                        'action'=>9, //代售平台商品佣金
		                        'orderid'=>$v['id'],
		                		'addtime'=>$now

		                    );

		                    addBalanceRecord($data3);

		                    //给主播发消息
		                    $title1="买家购买的“".$v['goods_name']."”订单佣金".$v['commission']."已自动结算到你的账户";

		                    $data4=array(
					            'uid'=>$v['liveuid'],
					            'orderid'=>$v['id'],
					            'title'=>$title1,
					            'addtime'=>$now,
					            'type'=>'1',
					            'is_commission'=>'1'

					        );

					        addShopGoodsOrderMessage($data4);
					        //发送腾讯IM
		        			txMessageIM($title,$v['liveuid'],'goodsorder_admin');

                    	}

                    	//计算分享用户的分享佣金
                    	if($v['shareuid']>0 && $v['share_income']){
                    		//给用户增加余额
                    		setUserBalance($v['shareuid'],1,$v['share_income']);

                    		//写入余额操作记录
                    		$data5=array(
		                        'uid'=>$v['shareuid'], //分享用户ID
		                        'touid'=>$v['uid'], //买家用户ID
		                        'balance'=>$v['share_income'],
		                        'type'=>1,
		                        'action'=>10, //分享商品给其他用户购买后获得佣金
		                        'orderid'=>$v['id'],
		                		'addtime'=>$now

		                    );

		                    addBalanceRecord($data5);

                    	}

                    	//给卖家增加余额
				        $balance=$v['total']-$v['share_income'];

				        if($v['order_percent']>0){
				            $balance=$balance*(100-$v['order_percent'])/100;
				            $balance=round($balance,2);
				        }


				        $res1=setUserBalance($v['shop_uid'],1,$balance);

				        //更改订单信息
				        $data=array(
				        	'settlement_time'=>$now
				        );

				        changeShopOrderStatus($v['uid'],$v['id'],$data);

				        //添加余额操作记录
	                    $data1=array(
	                        'uid'=>$v['shop_uid'],
	                        'touid'=>$v['uid'],
	                        'balance'=>$balance,
	                        'type'=>1,
	                        'action'=>2, //系统自动结算货款给卖家
	                        'orderid'=>$v['id'],
	                		'addtime'=>$now

	                    );

	                    addBalanceRecord($data1);

	                    //主播才发送消息,平台自营不发消息
	                    if($v['shop_uid']>1){

	                    	//给卖家发消息
		                    $title="买家购买的“".$v['goods_name']."”订单已自动结算到你的账户";
		                    $data2=array(
					            'uid'=>$v['shop_uid'],
					            'orderid'=>$v['id'],
					            'title'=>$title,
					            'addtime'=>$now,
					            'type'=>'1'					            

					        );

					        addShopGoodsOrderMessage($data2);
					        //发送腾讯IM
		        			txMessageIM($title,$v['shop_uid'],'goodsorder_admin');
	                    }


	                    

			        }

			        

            	}	


            }

            if($v['status']==5&&$v['refund_status']==0){ //退款 判断等待卖家处理的时间是否超出后台设定的时间，如果超出，自动退款

            	//获取退款申请信息
            	$where=array(
                    'orderid'=>$v['id']
            	);

	            $refund_info=getShopOrderRefundInfo($where);
	            

	            if($refund_info['is_platform_interpose']==0&&$refund_info['shop_result']==0){ //平台未介入且店家未处理

	            	$refund_end=$refund_info['addtime']+$effective_time['shop_refund_time']*60*60*24;


	            	if($refund_end<=$now){

	            		//更改订单退款状态
	            		$data=array(
	                        'refund_status'=>1,
	                        'refund_endtime'=>$now
	                    );

	                    changeShopOrderStatus($v['uid'],$v['id'],$data);

	                    //更改订单退款记录信息

	                    $data1=array(
	                    	'system_process_time'=>$now,
	                    	'status'=>1,

	                    );

	                    changeGoodsOrderRefund($where,$data1);

	            	
	            		//退还买家货款
	                    setUserBalance($v['uid'],1,$v['total']);

	                    //添加余额操作记录
	                    $data1=array(
	                        'uid'=>$v['uid'],
	                        'touid'=>$v['shop_uid'],
	                        'balance'=>$v['total'],
	                        'type'=>1,
	                        'action'=>4, //买家发起退款，卖家超时未处理，系统自动退款
	                        'orderid'=>$v['id'],
                    		'addtime'=>$now

	                    );

	                    addBalanceRecord($data1);

	                    //减去商品销量
            			changeShopGoodsSaleNums($v['goodsid'],0,$v['nums']);

            			//减去店铺销量
        				changeShopSaleNums($v['shop_uid'],0,$v['nums']);

        				//商品规格库存回增
        				changeShopGoodsSpecNum($v['goodsid'],$v['spec_id'],$v['nums'],1);

            			//给买家发消息
	                    $title="你申请的“".$v['goods_name']."”订单退款卖家超时未处理,已自动退款到你的余额账户中";
	                    $data2=array(
				            'uid'=>$v['uid'],
				            'orderid'=>$v['id'],
				            'title'=>$title,
				            'addtime'=>$now,
				            'type'=>'0'

				        );

				        addShopGoodsOrderMessage($data2);
				        //发送腾讯IM
	        			txMessageIM($title,$v['uid'],'goodsorder_admin');


	            	}
	            	
	            }

	            if($refund_info['is_platform_interpose']==0&&$refund_info['shop_result']==-1){ //未申请平台介入且店家已拒绝
	            	//超时，退款自动完成,订单自动进入退款前状态
	            	$finish_endtime=$refund_info['shop_process_time']+$effective_time['shop_refund_finish_time']*60*60*24;
	            	if($finish_endtime<=$now){

	            		//更改退款订单状态

	            		$data=array(
	            			'status'=>1,
	            			'system_process_time'=>$now
	            		);

	            		changeGoodsOrderRefund($where,$data);


	            		//更改订单状态
	            		$data1=array(
	            			'refund_endtime'=>$now,
	            			'refund_status'=>-1
	            		);

	            		if($v['receive_time']>0){
	            			$data1['status']=3; //待评价
	            		}else{

	            			if($v['shipment_time']>0){
		            			$data1['status']=2; //待收货
		            		}else{
		            			$data1['status']=1; //待发货
		            		}

	            		}

	            		changeShopOrderStatus($v['uid'],$v['id'],$data1);

	            		//给买家发消息
	                    $title="你购买的“".$v['goods_name']."”订单退款申请被卖家拒绝后,".$effective_time['shop_refund_finish_time']."天内你没有进一步操作,系统自动处理结束";
	                    $data2=array(
				            'uid'=>$v['uid'],
				            'orderid'=>$v['id'],
				            'title'=>$title,
				            'addtime'=>$now,
				            'type'=>'0'

				        );

				        addShopGoodsOrderMessage($data2);
				        //发送腾讯IM
	        			txMessageIM($title,$v['uid'],'goodsorder_admin');

	            	}
	            }

            }



        }

	}



	//商品订单详情处理
	function handleGoodsOrder($orderinfo){
		$orderinfo['address_format']=$orderinfo['province'].' '.$orderinfo['city'].' '.$orderinfo['area'].' '.$orderinfo['address'];
		$orderinfo['spec_thumb_format']=get_upload_path($orderinfo['spec_thumb']); //商品规格封面

		$effective_time=getShopEffectiveTime();

		$now=time();
		switch ($orderinfo['type']) {
			case '1':
				$orderinfo['type_name']='支付宝';
				break;

			case '2':
				$orderinfo['type_name']='微信';
				break;

			case '3':
				$orderinfo['type_name']='余额';
				break;

			case '4':
				$orderinfo['type_name']='微信小程序';
				break;
			case '5':
				$orderinfo['type_name']='Paypal';
				break;
		}

		$orderinfo['status_name']='';
		$orderinfo['status_desc']='';
		$orderinfo['is_refund']='0';

		switch ($orderinfo['status']) {
			case '-1': //已关闭
				$orderinfo['status_name']='交易关闭';
				$orderinfo['status_desc']='因支付超时,交易关闭';
				break;
			case '0': //待付款
				$orderinfo['status_name']='等待买家付款';
				$end=$orderinfo['addtime']+$effective_time['shop_payment_time']*60;
				$cha=$end-$now;
				$orderinfo['status_desc']='剩余时间 '.getSeconds($cha,1);
				break;
			case '1': //待发货
				$orderinfo['status_name']='支付成功,等待卖家发货';
				if($orderinfo['refund_status']==0){ //只要退款未处理过
					$orderinfo['is_refund']='1'; //是否可退款 0 否 1 是
				}

				break;
			case '2': //已发货 待收货
				$orderinfo['status_name']='卖家已发货';
				$end=$orderinfo['shipment_time']+$effective_time['shop_receive_time']*24*60*60;
				$cha=$end-$now;
				$orderinfo['status_desc']='自动确认收货还剩'.getSeconds($cha);

				if($orderinfo['refund_status']==0){ //只要退款未处理过
					$orderinfo['is_refund']='1'; //是否可退款 0 否 1 是
				}

				break;
			case '3': //已收货待评价
				$orderinfo['status_name']='已签收';
				$orderinfo['status_desc']='交易成功,快去评价一下吧';
				$end=$orderinfo['receive_time']+$effective_time['shop_receive_refund_time']*24*60*60;
				if(($orderinfo['refund_status']==0)&&($now<$end)){ //只要退款未处理过 且在后台设定的退货时间范围内就可以发起退款
					$orderinfo['is_refund']='1'; //是否可退款 0 否 1 是
				}
				break;
			case '4': //已评价
				$orderinfo['status_name']='订单已评价';
				break;

			case '5': //请求退款详情单独接口

				if($orderinfo['refund_status']==1){ //退款成功

					$orderinfo['status_name']='退款成功';

				}else if($orderinfo['refund_status']==0){ //退款中状态

					//获取退款详情
					$refund_where=array(
						'orderid'=>$orderinfo['id']
					);
					$refund_info=getShopOrderRefundInfo($refund_where);

					if($refund_info['is_platform_interpose']==0){

						if($refund_info['shop_result']==0){
							$orderinfo['status_name']='等待卖家处理';
						}else if($refund_info['shop_result']==-1){
							$orderinfo['status_name']='卖家已拒绝';
						}

					}else{
						$orderinfo['status_name']='等待平台处理';
					}

					

				}

				
				break;


		}

		$orderinfo['addtime']=date("Y-m-d H:i:s",$orderinfo['addtime']); //添加时间

		$orderinfo['cancel_time']=$orderinfo['cancel_time']>0?date("Y-m-d H:i:s",$orderinfo['cancel_time']):''; //取消时间
		
		$orderinfo['paytime']=$orderinfo['paytime']>0?date("Y-m-d H:i:s",$orderinfo['paytime']):''; //支付时间
		
		$orderinfo['shipment_time']=$orderinfo['shipment_time']>0?date("Y-m-d H:i:s",$orderinfo['shipment_time']):''; //发货时间
		
		$orderinfo['receive_time']=$orderinfo['receive_time']>0?date("Y-m-d H:i:s",$orderinfo['receive_time']):''; //收货时间
		
		$orderinfo['evaluate_time']=$orderinfo['evaluate_time']>0?date("Y-m-d H:i:s",$orderinfo['evaluate_time']):''; //评价时间

		$orderinfo['settlement_time']=$orderinfo['settlement_time']>0?date("Y-m-d H:i:s",$orderinfo['settlement_time']):''; //结算时间

		$orderinfo['refund_starttime']=$orderinfo['refund_starttime']>0?date("Y-m-d H:i:s",$orderinfo['refund_starttime']):''; //退款申请时间

		$orderinfo['refund_endtime']=$orderinfo['refund_endtime']>0?date("Y-m-d H:i:s",$orderinfo['refund_endtime']):''; //退款处理结束时间
		

		return $orderinfo;
	}

	//获取物流信息
	function getExpressInfoByKDN($express_code,$express_number,$phone){
		$configpri=getConfigPri();
        $express_type=isset($configpri['express_type'])?$configpri['express_type']:'';
        $EBusinessID=isset($configpri['express_id_dev'])?$configpri['express_id_dev']:'';
        $AppKey=isset($configpri['express_appkey_dev'])?$configpri['express_appkey_dev']:'';

        //$ReqURL='http://sandboxapi.kdniao.com:8080/kdniaosandbox/gateway/exterfaceInvoke.json'; //免费版即时查询【快递鸟测试账号专属查询地址】
        $ReqURL='http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx'; //免费版即时查询【已注册商户ID真实即时查询地址】

        if($express_type){ //正式付费物流跟踪版
            $EBusinessID=isset($configpri['express_id'])?$configpri['express_id']:'';
            $AppKey=isset($configpri['express_appkey'])?$configpri['express_appkey']:'';
            $ReqURL='http://api.kdniao.com/api/dist'; //物流跟踪版查询【已注册商户ID真实即时查询地址】
        }

        $requestData=array(
            'ShipperCode'=>$express_code,
            'LogisticCode'=>$express_number
        );

        if($express_code=='SF'){ //顺丰要带上发件人/收件人手机号的后四位
        	$requestData['CustomerName']=substr($phone, -4);
        }

        $requestData= json_encode($requestData);
        
        $datas = array(
            'EBusinessID' => $EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );

        //物流跟踪版消息报文
        if($express_type){
        	$datas['RequestType']='8001';
        }

        $datas['DataSign'] = encrypt_kdn($requestData, $AppKey);

        $result=sendPost_KDN($ReqURL, $datas);

        return json_decode($result,true);


	}

	/**
     * 快递鸟电商Sign签名生成
     * @param data 内容   
     * @param appkey Appkey
     * @return DataSign签名
     */
    function encrypt_kdn($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

    /**
     *  post提交数据 
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据 
     * @return url响应返回的html
     */
    function sendPost_KDN($url, $datas) {
        $temps = array();   
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);      
        }   
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;   
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);  
        
        return $gets;
    }

    function is_true($val, $return_null=false){
        $boolval = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
        return ( $boolval===null && !$return_null ? false : $boolval );
    }

    //获取物流状态【即时查询版】
    function getExpressStateInfo($express_code,$express_number,$express_name,$username,$phone){

    	$express_info=[];

    	$express_info_kdn=getExpressInfoByKDN($express_code,$express_number,$phone);
    	$express_state=$express_info_kdn['State']; //物流状态 0-暂无轨迹信息 1-已揽收 2-在途中  3-已签收4-问题件

    	if(!$express_state){
            $express_info['state_name']='包裹正在等待揽收';
            $express_info['desc']=$express_name.' '.$express_number;
        }elseif($express_state==1){
            $express_info['state_name']='包裹已揽收';
            $express_info['desc']=$express_name.' '.$express_number;
        }elseif($express_state==2){
            $express_info['state_name']='包裹运输中';
            $express_info['desc']=$express_name.' '.$express_number;
        }elseif($express_state==3){
            $express_info['state_name']='包裹已签收';
            $express_info['desc']='签收人：'.$username;
        }

        return $express_info;
    }

    //获取商城订单退款详情
    function getShopOrderRefundInfo($where){
    	$info=DI()->notorm->shop_order_refund
    			->where($where)
    			->fetchOne();

    	return $info;
    }

    //更改退款详情信息
    function changeGoodsOrderRefund($where,$data){
    	$res=DI()->notorm->shop_order_refund
    			->where($where)
    			->update($data);

    	return $res;
    }

    //添加退款操作记录
    function setGoodsOrderRefundList($data){
    	$res=DI()->notorm->shop_order_refund_list->insert($data);
    	return $res;
    }

    //更新商品的销量 type=0 减 type=1 增
	function changeShopGoodsSaleNums($goodsid,$type,$nums){
		if($type==0){

			$res=DI()->notorm->shop_goods
			->where("id=? and sale_nums>= ?",$goodsid,$nums)
			->update(
				array('sale_nums' => new NotORM_Literal("sale_nums - {$nums}"))
			);

		}else{
			$res=DI()->notorm->shop_goods
			->where("id=?",$goodsid)
			->update(
				array('sale_nums' => new NotORM_Literal("sale_nums + {$nums}"))
			);
		}

		return $res;
		
	}


	//更新商品的销量 type=0 减 type=1 增
	function changeShopSaleNums($uid,$type,$nums){
		if($type==0){

			$res=DI()->notorm->shop_apply
			->where("uid=? and sale_nums>= ?",$uid,$nums)
			->update(
				array('sale_nums' => new NotORM_Literal("sale_nums - {$nums}"))
			);

		}else{
			$res=DI()->notorm->shop_apply
			->where("uid=?",$uid)
			->update(
				array('sale_nums' => new NotORM_Literal("sale_nums + {$nums}"))
			);
		}

		return $res;
		
	}

	//获取商品评价的追评信息
	function getGoodsAppendComment($uid,$orderid){

		$info=DI()->notorm->shop_order_comments
				->where("uid=? and orderid=? and is_append=1",$uid,$orderid)
				->fetchOne();

		return $info;
	}

	//商品评价信息处理
	function handleGoodsComments($comments_info){

		$comments_info['time_format']=secondsFormat($comments_info['addtime']);
		$comments_info['video_thumb']=get_upload_path($comments_info['video_thumb']);
		$comments_info['video_url']=get_upload_path($comments_info['video_url']);

		if($comments_info['thumbs']!=''){
			$thumb_arr=explode(',',$comments_info['thumbs']);
			foreach ($thumb_arr as $k => $v) {
				$thumb_arr[$k]=get_upload_path($v);
			}
		}else{
			$thumb_arr=array();	
		}

		
		$comments_info['thumb_format']=$thumb_arr;

		$order_info=getShopOrderInfo(array('id'=>$comments_info['orderid']),'spec_name');


		$comments_info['spec_name']=$order_info['spec_name']; //商品规格名称

		//获取用户信息
		$user_info=DI()->notorm->user
					->where("id=?",$comments_info['uid'])
					->select("avatar,user_nickname")
					->fetchOne();

		$comments_info['user_nickname']=$user_info['user_nickname'];
		$comments_info['avatar']=get_upload_path($user_info['avatar']);
		if($comments_info['is_anonym']){
			$comments_info['user_nickname']='匿名用户';
			$comments_info['avatar']=get_upload_path("/anonym.png");
		}
		

		unset($comments_info['service_points']);
		unset($comments_info['express_points']);
		unset($comments_info['thumbs']);
		unset($comments_info['is_anonym']);

		return $comments_info;
	}

	/* 时长格式化 */
	function secondsFormat($time){

		$now=time();
		$cha=$now-$time;

		if($cha<60){
			return '刚刚';
		}

		if($cha>=4*24*60*60){ //超过4天
			$now_year=date('Y',$now);
			$time_year=date('Y',$time);

			if($now_year==$time_year){
				return date("m月d日",$time);
			}else{
				return date("Y年m月d日",$time);
			}

		}else{

			$iz=floor($cha/60);
			$hz=floor($iz/60);
			$dz=floor($hz/24);

			if($dz>3){
				return '3天前';
			}else if($dz>2){
				return '2天前';
			}else if($dz>1){
				return '1天前';
			}

			if($hz>1){
				return $hz.'小时前';
			}

			return $iz.'分钟前';
			

		}

	}

	//判断付费内容申请是否通过
	function checkPaidProgramIsPass($uid){
		$info=DI()->notorm->paidprogram_apply->where("uid=?",$uid)->fetchOne();
		if(!$info){
			return '0';
		}

		$status=$info['status'];
		if($status!=1){
			return '0';
		}

		return '1';
	}


	//写入订单操作记录
	function addShopGoodsOrderMessage($data){
		$res=DI()->notorm->shop_order_message->insert($data);
		return $res;
	}

	//更改商品库存
	function changeShopGoodsSpecNum($goodsid,$spec_id,$nums,$type){
		$goods_info=DI()->notorm->shop_goods
				->where("id=?",$goodsid)
				->fetchOne();

		if(!$goods_info){
			return 0;
		}

		$spec_arr=json_decode($goods_info['specs'],true);
		$specid_arr=array_column($spec_arr, 'spec_id');

		if(!in_array($spec_id, $specid_arr)){
			return 0;
		}

		foreach ($spec_arr as $k => $v) {
			if($v['spec_id']==$spec_id){
				if($type==1){
					$spec_num=$v['spec_num']+$nums;
				}else{
					$spec_num=$v['spec_num']-$nums;
				}
				
				if($spec_num<0){
					$spec_num=0;
				}

				$spec_arr[$k]['spec_num']=(string)$spec_num;
			}
		}


		$spec_str=json_encode($spec_arr);

		DI()->notorm->shop_goods->where("id=?",$goodsid)->update(array('specs'=>$spec_str));

		return 1;

	}

	//判断用户是否注销
	function checkIsDestroyByLogin($country_code,$user_login){
		$user_status=DI()->notorm->user->where("country_code=? and user_login=?",$country_code,$user_login)->fetchOne('user_status');
		if($user_status==3){
			return 1;
		}

		return 0;
	}

	//判断用户是否注销
	function checkIsDestroyByUid($uid){
		$user_status=DI()->notorm->user->where("id=?",$uid)->fetchOne('user_status');
		if($user_status==3){
			return 1;
		}

		return 0;
	}

	//获取播流地址
    function getPull($stream){
    	$pull='';
    	$live_info=DI()->notorm->live->where("stream=?",$stream)->fetchOne();
    	if($live_info['isvideo']==1){ //视频
    		$pull=$live_info['pull'];
    	}else{
    		$configpri=getConfigPri();
    		if($configpri['cdn_switch']==5){
    			$wyinfo=PrivateKeyA('rtmp',$stream,1);
				$pull=$wyinfo['ret']["rtmpPullUrl"];
    		}else{
    			$pull=PrivateKeyA('rtmp',$stream,0);
    		}
    	}

    	return $pull;
	}
    
    
	/* 商城分类-二级 */
    function getShopTwoClass(){
        $key="twoGoodsClass";
		$list=getcaches($key);
		if(!$list){
            $list=DI()->notorm->shop_goods_class
					->select("gc_id,gc_name,gc_icon")
					->where('gc_isshow=1 and gc_grade=2')
                    ->order("gc_sort")
					->fetchAll();
            if($list){
                setcaches($key,$list);
            }
			
		}
        foreach($list as $k=>$v){
            $v['gc_icon']=get_upload_path($v['gc_icon']);
            $list[$k]=$v;
        }
        return $list;        
        
    }
	
	/* 商城分类-三级级 */
    function getShopThreeClass($classid){
        $key="threeGoodsClass_".$classid;
		$list=getcaches($key);
		if(!$list){
            $list=DI()->notorm->shop_goods_class
					->select("gc_id,gc_name")
					->where("gc_isshow=1 and gc_grade=3 and gc_parentid={$classid}")
                    ->order("gc_sort")
					->fetchAll();
            if($list){
                setcaches($key,$list);
            }else{
				$list=[];
			}
			
		}

        return $list;        
        
    }
	
	//每日任务处理
	function dailyTasks($uid,$data){
		$configpri=getConfigPri();
		$type=$data['type'];  //type 任务类型 

		$dailytask_switch=$configpri['dailytask_switch'];
		if(!$dailytask_switch){
			return 0;
		}
		
		// 当天时间
		$time=strtotime(date("Y-m-d 00:00:00",time()));
		$where="uid={$uid} and type={$type}";	
		//每日任务
		$info=DI()->notorm->user_daily_tasks
    			->where($where)
    			->select("*")
    			->fetchOne();
    			
		if($info){

    		if($info['addtime']!=$time){
    			DI()->notorm->user_daily_tasks
    				->where($where)
    				->delete();
    			$info=[];
    		}else{
    			if($info['state']==1||$info['state']==2){
    				return 1;
    			}
    		}
    	}
				
		$save=[
			'uid'=>$uid,
			'type'=>$type,
			'addtime'=>$time,
			'uptime'=>time(),
		];
		$state='0';
		if($type==1){  //1观看直播
			$target=$configpri['watch_live_term'];
			$reward=$configpri['watch_live_coin'];

			
		}else if($type==2){ //2观看视频
			$target=$configpri['watch_video_term'];
			$reward=$configpri['watch_video_coin'];	

		}else if($type==3){ //3直播奖励
			$target=$configpri['open_live_term']*60;
			$reward=$configpri['open_live_coin'];
			

		}else if($type==4){ //4打赏奖励
			$target=$configpri['award_live_term'];
			$reward=$configpri['award_live_coin'];
			
			$schedule=ceil($data['total']);
			
		}else if($type==5){ //5分享奖励
			$target=$configpri['share_live_term'];
			$reward=$configpri['share_live_coin'];
			
			$schedule=ceil($data['nums']);
		}
		
		//关于时间奖励的处理
		if(in_array($type,['1','2','3'])){
			
			$day=date("d",$data['starttime']); 
			$day2=date("d",$data['endtime']);
			if($day!=$day2){ //判断结束时间是否超过当天, 超过则按照今天凌晨来算
				$data['starttime']=$time;
			}

			$schedulet=0;
			$time_diff=$data['endtime']-$data['starttime'];
			$schedule=$time_diff; //以秒为单位

		}
		
		
		
		if(!$info || $info['addtime']!=$time){  //当数据中查不到当天的数据时
			$save['target']=$target;
			$save['reward']=$reward;

			if(in_array($type,['1','2','3'])){
				$target_format=$target*60;
			}else{
				$target_format=$target;
			}

			if($schedule>=$target_format){
				$schedule=$target_format;
				$state='1';
			}
		}else{  //当有今天的数据时
			$schedule=$info['schedule']+$schedule;
			
			if(in_array($type,['1','2','3'])){
				$target_format=$info['target']*60;
			}else{
				$target_format=$info['target'];
			}

			if($schedule>=$target_format){
				$schedule=$target_format;
				$state='1';
			}
		}
		
		$save['schedule']=(int)$schedule;  //进度
		$save['state']=$state; //状态
		
		
		if(!$info){
			DI()->notorm->user_daily_tasks->insert($save);
		}else{
			DI()->notorm->user_daily_tasks->where('id=?',$info['id'])->update($save);
		}

		
		//删除用户每日任务数据
		$key="seeDailyTasks_".$uid;
		delcache($key);
	}
	
	
	//获取动态话题标签列表
	function getDynamicLabels($where,$order,$p,$isp=0){
		
		if($isp){  //是否使用分页
			if($p<1){
				$p=1;
			}
			$nums=20;
			$start=($p-1)*$nums;
		}else{
			$start=0;
			$start=$p;
		}
		
		$reportlist=DI()->notorm->dynamic_label
					->select("id,name,thumb,use_nums")
					->where($where)
					->order($order)
					->limit($start,$nums)
					->fetchAll();
		
		return $reportlist;
	
	}
	
	
	/* 判断商品是否收藏 */
	function isGoodsCollect($uid,$goodsid) {

		if($uid<0||$goodsid<0){
			return "0";
		}

		$isexist=DI()->notorm->user_goods_collect
					->select("*")
					->where('uid=? and goodsid=?',$uid,$goodsid)
					->fetchOne();
		if($isexist){
			return  '1';
		}else{
			return  '0';
		}			 
	}


	//检测姓名
	function checkUsername($username){
		$preg='/^(?=.*\d.*\b)/';
		$isok = preg_match($preg,$username);
		if($isok){
			return 1;
		}else{
			return 0;
		}
	}


	//获取店铺协商历史
	function getShopOrderRefundList($where){
		$list=DI()->notorm->shop_order_refund_list
			->where($where)
			->order("addtime desc")
			->fetchAll();
		
		return $list;
	}

	//代售平台商品列表格式化处理
	function handlePlatformGoods($list,$platform_list,$type){

		foreach ($list as $k => $v) {
            $thumb_arr=explode(',',$v['thumbs']);
            $list[$k]['thumb']=get_upload_path($thumb_arr[0]);

            
            if($v['type']==1){ //外链商品
                $list[$k]['price']=(string)$v['present_price'];
                $list[$k]['specs']=[];
            }else{
                $spec_arr=json_decode($v['specs'],true);
                $list[$k]['price']=(string)$spec_arr[0]['price'];
                $list[$k]['specs']=$spec_arr;
            }


        	if($platform_list){
        		foreach ($platform_list as $k1 => $v1) {
	                if($v1['goodsid']==$v['id']){
	                	$list[$k]['issale']=$v1['issale'];
	                    $list[$k]['live_isshow']=$v1['live_isshow'];
	                    break;
	                }
            	}
        	}
            	

            unset($list[$k]['thumbs']);
            unset($list[$k]['present_price']);
            unset($list[$k]['specs']);
        }

        return $list;
	}	

	//检测用户代售商品
	function checkUserSalePlatformGoods($where){
		$info=DI()->notorm->seller_platform_goods
		->where($where)
		->fetchOne();
		if(!$info){
			return 0;
		}

		return 1;
	}

	//获取代售平台商品记录
	function getOnsalePlatformInfo($where){
		$info=DI()->notorm->seller_platform_goods
		->where($where)
		->fetchOne();

		return $info;
	}

	//修改代售平台商品记录的信息
	function setOnsalePlatformInfo($where,$data){
		DI()->notorm->seller_platform_goods
		->where($where)
		->update($data);
	}

	//检测用户是否填写过邀请码
	function checkAgentIsExist($uid){
		$isexist=DI()->notorm->agent
                    ->select('*')
                    ->where('uid=?',$uid)
                    ->fetchOne();
        if(!$isexist){
        	return 0;
        }

        return 1;
	}

	//检查语音聊天室是否在直播
	function checkVoiceIsLive($uid,$stream){
		$live_info=DI()->notorm->live
    	->where("uid=? and stream=? and islive=1 and live_type=1",$uid,$stream)
    	->fetchOne();


    	if(!$live_info){
    		return 0;
    	}

    	return 1;
	}

	//获取语音聊天室麦位信息
	function getVoiceMicInfo($where){
		$mic_info=DI()->notorm->voicelive_mic
    	->where($where)
    	->fetchOne();

    	return $mic_info;
	}

	//获取低延迟推流和播流地址【废弃，腾讯rtmp低延迟流改为了trtc格式，参考getTxTrtcUrl】
	function getLowLatencyStreamBF($stream){

		$configpri=getConfigPri();
		$nowtime=time();
		$live_sdk=$configpri['live_sdk'];  //live_sdk  0表示直播模式 1表示直播+连麦模式

        if($live_sdk==1){
            $bizid = $configpri['tx_bizid'];
            $push_url_key = $configpri['tx_push_key'];
            $tx_acc_key = $configpri['tx_acc_key'];
            $push = $configpri['tx_push'];
            $pull = $configpri['tx_pull'];

            $now_time2 = $nowtime + 3*60*60;
            $txTime = dechex($now_time2);
            
            $live_code = $stream ;

            $txSecret = md5($push_url_key . $live_code . $txTime);
            $safe_url = "?txSecret=" . $txSecret."&txTime=" .$txTime;
            $push_url = "rtmp://" . $push . "/live/" .  $live_code .$safe_url. "&bizid=" . $bizid ;
            
            $txSecret2 = md5($tx_acc_key . $live_code . $txTime);
            $safe_url2 = "?txSecret=" . $txSecret2."&txTime=" .$txTime;
            $play_url = "rtmp://" . $pull . "/live/" .$live_code .$safe_url2. "&bizid=" . $bizid;
            
            
        }else if($configpri['cdn_switch']==5)
		{
			$wyinfo=PrivateKeyA('rtmp',$stream,1);
			$play_url=$wyinfo['ret']["rtmpPullUrl"];
			$wy_cid=$wyinfo['ret']["cid"];
			$push_url=$wyinfo['ret']["pushUrl"];
		}else{
			$push_url=PrivateKeyA('rtmp',$stream,1);
			$play_url=PrivateKeyA('rtmp',$stream,0);
		}
		
        $info=array(
			"pushurl" => $push_url,
			"timestamp" => $nowtime, 
			"playurl" => $play_url,
			"stream"=>$stream
		);

		return $info;
	}


	//获取rtmp推流和播流地址
	function getLowLatencyStream($stream){

		$configpri=getConfigPri();

        if($configpri['cdn_switch']==5){

			$wyinfo=PrivateKeyA('rtmp',$stream,1);
			$play_url=$wyinfo['ret']["rtmpPullUrl"];
			$wy_cid=$wyinfo['ret']["cid"];
			$push_url=$wyinfo['ret']["pushUrl"];

		}else{

			$push_url=PrivateKeyA('rtmp',$stream,1);
			$play_url=PrivateKeyA('rtmp',$stream,0);
		}
		
        $info=array(
			"pushurl" => $push_url,
			"timestamp" => time(), 
			"playurl" => $play_url,
			"stream"=>$stream
		);

		return $info;
	}




	//获取语音聊天室所有麦位上的用户信息 
	function getVoiceLiveMicList($mic_list){
		$curr_position=0;
    	$new_mic_list=[];

    	$empty_userinfo=array(
				'id'=>'0',
				'uid'=>'0', //ios专用
				'user_nickname'=>'',
				'avatar'=>'',
				'sex'=>'0',
				'level'=>'0',
				'mic_status'=>'0'
			);

    	for ($i=0; $i <8 ; $i++) {
    		$empty_userinfo['position']=(string)$i;
    		$new_mic_list[]=$empty_userinfo;
    	}

    	foreach ($mic_list as $k => $v) {
    		foreach ($new_mic_list as $k1 => $v1) {
    			if($v1['position']==$v['position']){
    				if($v['uid']>0){
    					$userinfo=getUserInfo($v['uid']);
    					$new_userinfo['id']=$userinfo['id'];
    					$new_userinfo['uid']=$userinfo['id']; //ios专用
    					$new_userinfo['user_nickname']=$userinfo['user_nickname'];
    					$new_userinfo['avatar']=$userinfo['avatar'];
    					$new_userinfo['sex']=$userinfo['sex'];
    					$new_userinfo['level']=$userinfo['level'];
    					$new_userinfo['mic_status']=$v['status'];
    					$new_userinfo['position']=$v1['position'];

    					$new_mic_list[$k1]=$new_userinfo;
    				}else{
    					$new_mic_list[$k1]['mic_status']=$v['status'];
    				}

    				break;
    			}
    			
    		}
    	}

    	return $new_mic_list;
	}

	//获取直播类型【视频直播或语音聊天室】
	function getLiveType($liveuid,$stream){
		$live_info=DI()->notorm->live
		->where("uid=? and stream=?",$liveuid,$stream)
		->select("live_type")
		->fetchOne();

		return $live_info['live_type'];
	}


    //获取用户收藏商品的数量
    function getGoodsCollectNums($uid){
    	$num=DI()->notorm->user_goods_collect
    		->where("uid=?",$uid)
    		->count();
    	
    	return $num;
    }

    //判断用户是否创建家族/是否加入家族
    function checkUserFamily($uid){
    	$family_info=DI()->notorm->family
			->where("uid=?",$uid)
			->fetchOne();

		if($family_info){
			if($family_info['state']==1){ //审核失败
				if($family_info['istip']==0){
					return 0;
				}
			}
			return 1;
		}

		//判断用户是否申请了家族
		$family_user=DI()->notorm->family_user
			->where("uid=?",$uid)
			->fetchOne();

		if($family_user){
			if($family_user['state']==1){ //审核被拒
				return 0;
			}
			return 1;
		}

		return 0;

    }


    //验证数字是否整数/两位小数
    function checkNumber($num){

    	if(floor($num) ==$num){
    		return 1;
    	}

    	if (preg_match('/^[0-9]+(.[0-9]{1,2})$/', $num)) {
    		return 1;
    	}

    	return 0;
    }

    //检测首充
    function checkUserFirstCharge($uid){
    	$info=DI()->notorm->user
    		->select("firstcharge_used")
    		->where(['id'=>$uid])
    		->fetchOne();

    	return $info['firstcharge_used'];
    }
    //禁播
    function getLiveBan($uid){
    	$res=array('is_ban'=>0,'endtime'=>0);
    	$live_ban=DI()->notorm->live_ban
    		->where(['liveuid'=>$uid])
    		->fetchOne();

    	if($live_ban){

    		$now=time();

    		if($live_ban['endtime']==0 && $live_ban['type']=='all'){
    			$res['is_ban']=1;
    		}else if( ($live_ban['endtime'] >0) && ($live_ban['endtime']<=$now) ){
    			DI()->notorm->live_ban
		    		->where(['liveuid'=>$uid])
		    		->delete();
		    	
    		}else{
    			$res['is_ban']=1;
    			$res['endtime']=$live_ban['endtime'];
    		}


    	}

    	return $res;
    }

    //直播间封禁规则
    function getLiveBanRules(){
    	$rules=[
			[
				'id'=>'1',
				'name'=>'30分钟',
				'type'=>'30min'
			],
			[
				'id'=>'2',
				'name'=>'1天',
				'type'=>'1day'
			],
			[
				'id'=>'3',
				'name'=>'7天',
				'type'=>'7day'
			],
			[
				'id'=>'4',
				'name'=>'15天',
				'type'=>'15day'
			],
			[
				'id'=>'5',
				'name'=>'30天',
				'type'=>'30day'
			],
			[
				'id'=>'6',
				'name'=>'90天',
				'type'=>'90day'
			],
			[
				'id'=>'7',
				'name'=>'180天',
				'type'=>'180day'
			],
			[
				'id'=>'8',
				'name'=>'永久',
				'type'=>'all'
			]
		];

		return $rules;
    }

    //添加用户点赞数
    function addUserPraise($uid,$nums){
    	DI()->notorm->user
    		->where(['id'=>$uid])
    		->update(
    			array('praise_num' => new NotORM_Literal("praise_num + {$nums}"))
    		);
    }

    //减少用户点赞数
    function reduceUserPraise($uid,$nums){
    	$praise_num=DI()->notorm->user
    		->where(['id'=>$uid])
    		->fetchOne('praise_num');

    	if($praise_num>=$nums){
    		DI()->notorm->user
	    		->where(['id'=>$uid])
	    		->update(
	    			array('praise_num' => new NotORM_Literal("praise_num - {$nums}"))
	    		);
    	}else{
    		DI()->notorm->user
	    		->where(['id'=>$uid])
	    		->update(['praise_num'=>0]);
    	}
    }

    //腾讯云IMUserSign
	function txImUserSign($id){
		$sig='';
		$configpri=getConfigPri();
		$appid=$configpri['tencentIM_appid'];
		$appkey=$configpri['tencentIM_appkey'];


		require_once API_ROOT.'/../sdk/tencentIM/TLSSigAPIv2.php';
		$api = new \Tencent\TLSSigAPIv2($appid,$appkey);
		$sign = $api->genUserSig($id);

		return $sign;
	}

	/**
	 * 发送腾讯IM
	 * @param  string 	$test  		文本消息内容
	 * @param  int 		$uid      	被通知用户id
	 * @param  string 	$adminName 	发送消息者
	 */
	function txMessageIM($test,$uid,$adminName){

        $identifier='administrator'; //跟腾讯云控制台即时通讯IM默认管理员保持一致

        $method_name='openim/sendmsg';

        $data=array(
        	"SyncOtherMachine"=>2,
        	"From_Account"=>(string)$adminName,
        	"To_Account"=>(string)$uid,
        	"MsgRandom"=>(int)get_str(8),
            "MsgBody"=>array(
            	0=>array(
	            	"MsgType"=>"TIMTextElem",
	            	"MsgContent"=>array(
	            		"Text"=>$test
	            	)
            ))
        );

        $data=json_encode($data);

        $response=txImPostParam($identifier,$method_name,$data);
        $result=json_decode($response,true);
        /*var_dump($result);
        die;*/
        //ErrorCode:10001 自定义参数，代表请求失败
        if($result && $result['ActionStatus']=='OK' && $result['ErrorCode']==0){

        }
	}


	//腾讯云IM数据请求 identifier:标识体，method_name:方法名，data:请求消息体【json字符串】
   function txImPostParam($identifier,$method_name,$data){

   		$configpri=getConfigPri();
   		$appid=$configpri['tencentIM_appid'];
   		$user_sign=txImUserSign($identifier);
   		$random=get_str(8);

   		$basic_url='https://console.tim.qq.com/v4/';
   		$url=$basic_url.$method_name;
   		$url.='?sdkappid='.$appid.'&identifier='.$identifier.'&usersig='.$user_sign.'&random='.$random.'&contenttype=json';

   		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$return_str = curl_exec($curl);
		if (curl_error($curl)) {
            curl_close($curl);
            return json_encode(['ActionStatus'=>'FAIL','ErrorCode'=>'10001']);
        }
		curl_close($curl);
		return $return_str;
   }

   //随机生成存数字字符串
	function get_str($length){
        $str = '0123456789';
		$len = strlen($str)-1;
		$randstr = '';
		for ($i=0;$i<$length;$i++) {
		 $num=mt_rand(0,$len);
		 $randstr .= $str[$num];
		}
		return $randstr;
        
   }

   /**
    * 腾讯云TPNS移动推送
    * @param  string  $title 推送标题
    * @param  string  $msg   推送消息内容
    * @param  string  $type  推送类型 all 全员推送 single 单账号推送 account_list 账号列表推送
    * @param  integer $uid   单账号用户id
    * @url https://cloud.tencent.com/document/product/548/39064
    */
   function txMessageTpns($title,$msg,$type,$uid=0,$account_list=[],$json_str=''){

   		require_once API_ROOT.'/../sdk/tencentTpns/tpns.php';
   		$configpri=getConfigPri();
   		$area=$configpri['tencentTpns_area'];
   		$accessid_android=$configpri['tencentTpns_accessid_android'];
   		$secretkey_android=$configpri['tencentTpns_secretkey_android'];
   		$accessid_ios=$configpri['tencentTpns_accessid_ios'];
   		$secretkey_ios=$configpri['tencentTpns_secretkey_ios'];
   		$ios_environment=$configpri['tencentTpns_ios_environment'];


   		if(
   			!in_array($area,['guangzhou','shanghai','hongkong','singapore']) || 
   			!$accessid_android || 
   			!$secretkey_android || 
   			!$accessid_ios || 
   			!$secretkey_ios
   		){
   			return;
   		}


   		if($area=='guangzhou'){
			$stub_android = new tpns\Stub($accessid_android, $secretkey_android, tpns\GUANGZHOU);
			$stub_ios = new tpns\Stub($accessid_ios, $secretkey_ios, tpns\GUANGZHOU);
		}else if($area=='shanghai'){
			$stub_android = new tpns\Stub($accessid_android, $secretkey_android, tpns\SHANGHAI);
			$stub_ios = new tpns\Stub($accessid_ios, $secretkey_ios, tpns\SHANGHAI);
		}else if($area=='hongkong'){
			$stub_android = new tpns\Stub($accessid_android, $secretkey_android, tpns\HONGKONG);
			$stub_ios = new tpns\Stub($accessid_ios, $secretkey_ios, tpns\HONGKONG);
		}else if($area=='singapore'){
			$stub_android = new tpns\Stub($accessid_android, $secretkey_android, tpns\SINGAPORE);
			$stub_ios = new tpns\Stub($accessid_ios, $secretkey_ios, tpns\SINGAPORE);
		}else{
			return;
		}


		if($type=='account_list' && count($account_list)==1){
			$type='single';
			$uid=$account_list[0];
		}

   		
   		if($type=='all'){

   			//Android推送
   			$android = new tpns\AndroidMessage;
   			if($json_str){
	   			$android->custom_content = $json_str;	
	   		}

	   		//控制通知点击时乱转到指定页面
	   		$action=[
                "action_type"=> 1,// 动作类型，1，打开activity或app本身；2，打开浏览器；3，打开Intent
                "activity"=> "com.yunbao.im.activity.ImMsgNotifyActivity"
            ];

            $android->action=(object)$action;

   			$req_android = tpns\NewRequest(
		        tpns\WithAudienceType(tpns\AUDIENCE_ALL),
		        tpns\WithMessageType(tpns\MESSAGE_NOTIFY),
		        tpns\WithTitle($title),
		        tpns\WithContent($msg),
		        tpns\WithAndroidMessage($android),
		        tpns\WithEnvironment(tpns\ENVIRONMENT_PROD)
		   	);

	   		$result_android = $stub_android->Push($req_android);
	   		//var_dump($result_android);

   			//iOS推送
   			$ios = new tpns\iOSMessage;
   			if($json_str){
	   			$ios->custom = $json_str;	
	   		}

   			$req_ios = tpns\NewRequest(
		        tpns\WithAudienceType(tpns\AUDIENCE_ALL),
		        tpns\WithMessageType(tpns\MESSAGE_NOTIFY),
		        tpns\WithTitle($title),
		        tpns\WithContent($msg),
		        tpns\WithIOSMessage($ios),
		        tpns\WithEnvironment(tpns\ENVIRONMENT_DEV)
		   	);

		   	if($ios_environment==0){ //开发
		   		$req_ios = tpns\NewRequest(
			        tpns\WithAudienceType(tpns\AUDIENCE_ALL),
			        tpns\WithMessageType(tpns\MESSAGE_NOTIFY),
			        tpns\WithTitle($title),
			        tpns\WithContent($msg),
			        tpns\WithIOSMessage($ios),
			        tpns\WithEnvironment(tpns\ENVIRONMENT_DEV)
			   	);
		   	}else{

		   		$req_ios = tpns\NewRequest(
			        tpns\WithAudienceType(tpns\AUDIENCE_ALL),
			        tpns\WithMessageType(tpns\MESSAGE_NOTIFY),
			        tpns\WithTitle($title),
			        tpns\WithContent($msg),
			        tpns\WithIOSMessage($ios),
			        tpns\WithEnvironment(tpns\ENVIRONMENT_PROD)
			   	);
		   	}

	   		$result_ios = $stub_ios->Push($req_ios);
	   		//var_dump($result_ios);

   		}else if($type=='single'){

   			if(!$uid){
   				return;
   			}

   			$uid=(string)$uid;

   			//Android推送
   			$android = new tpns\AndroidMessage;
   			if($json_str){
	   			$android->custom_content = $json_str;	
	   		}

	   		$action=[
                "action_type"=> 1,// 动作类型，1，打开activity或app本身；2，打开浏览器；3，打开Intent
                "activity"=> "com.yunbao.im.activity.ImMsgNotifyActivity"
            ];

            $android->action=(object)$action;

   			$req_android = tpns\NewRequest(
		        tpns\WithAudienceType(tpns\AUDIENCE_ACCOUNT),
		        tpns\WithMessageType(tpns\MESSAGE_NOTIFY),
		        tpns\WithTitle($title),
		        tpns\WithContent($msg),
		        tpns\WithAndroidMessage($android),
		        tpns\WithAccountList(array($uid)),
		        tpns\WithEnvironment(tpns\ENVIRONMENT_PROD)
		    );

	   		$result_android = $stub_android->Push($req_android);
	   		//var_dump($result_android);

	   		//iOS推送
	   		$ios = new tpns\iOSMessage;
	   		if($json_str){
	   			$ios->custom = $json_str;	
	   		}
	   		

	   		if($ios_environment==0){ //开发

	   			$req_ios = tpns\NewRequest(
			        tpns\WithAudienceType(tpns\AUDIENCE_ACCOUNT),
			        tpns\WithMessageType(tpns\MESSAGE_NOTIFY),
			        tpns\WithTitle($title),
			        tpns\WithContent($msg),
			        tpns\WithIOSMessage($ios),
			        tpns\WithAccountList(array($uid)),
			        tpns\WithEnvironment(tpns\ENVIRONMENT_DEV)
			    );

	   		}else{
	   			$req_ios = tpns\NewRequest(
			        tpns\WithAudienceType(tpns\AUDIENCE_ACCOUNT),
			        tpns\WithMessageType(tpns\MESSAGE_NOTIFY),
			        tpns\WithTitle($title),
			        tpns\WithContent($msg),
			        tpns\WithIOSMessage($ios),
			        tpns\WithAccountList(array($uid)),
			        tpns\WithEnvironment(tpns\ENVIRONMENT_PROD)
			    );
	   		}
	   		

	   		$result_ios = $stub_ios->Push($req_ios);
	   		//var_dump($result_ios);

   		}else if($type=='account_list'){

   			if(empty($account_list)){
   				return;
   			}

   			//Android推送
   			$android = new tpns\AndroidMessage;
   			if($json_str){
	   			$android->custom_content = $json_str;	
	   		}

	   		$action=[
                "action_type"=> 1,// 动作类型，1，打开activity或app本身；2，打开浏览器；3，打开Intent
                "activity"=> "com.yunbao.im.activity.ImMsgNotifyActivity"
            ];

            $android->action=(object)$action;

   			$req_android = tpns\NewRequest(
		        tpns\WithAudienceType(tpns\AUDIENCE_ACCOUNT_LIST),
		        tpns\WithMessageType(tpns\MESSAGE_NOTIFY),
		        tpns\WithTitle($title),
		        tpns\WithContent($msg),
		        tpns\WithAndroidMessage($android),
		        //tpns\WithIOSMessage($ios),
		        tpns\WithAccountList($account_list),
		        tpns\WithEnvironment(tpns\ENVIRONMENT_PROD)
		    );

		    $result_android = $stub_android->Push($req_android);
	   		//var_dump($result_android);

   			//iOS推送
   			$ios = new tpns\iOSMessage;
   			if($json_str){
	   			$ios->custom = $json_str;	
	   		}

   			if($ios_environment==0){ //开发
   				$req_ios = tpns\NewRequest(
			        tpns\WithAudienceType(tpns\AUDIENCE_ACCOUNT_LIST),
			        tpns\WithMessageType(tpns\MESSAGE_NOTIFY),
			        tpns\WithTitle($title),
			        tpns\WithContent($msg),
			        tpns\WithIOSMessage($ios),
			        tpns\WithAccountList($account_list),
			        tpns\WithEnvironment(tpns\ENVIRONMENT_DEV)
			    );
   			}else{
   				$req_ios = tpns\NewRequest(
			        tpns\WithAudienceType(tpns\AUDIENCE_ACCOUNT_LIST),
			        tpns\WithMessageType(tpns\MESSAGE_NOTIFY),
			        tpns\WithTitle($title),
			        tpns\WithContent($msg),
			        tpns\WithIOSMessage($ios),
			        tpns\WithAccountList($account_list),
			        tpns\WithEnvironment(tpns\ENVIRONMENT_PROD)
			    );
   			}
   			

		    $result_ios = $stub_ios->Push($req_ios);
	   		//var_dump($result_ios);

   		}
   
   }
