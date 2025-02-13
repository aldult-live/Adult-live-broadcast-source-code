<?php
/**
 * 登录、注册
 */
if (!session_id()) session_start();
class Api_Login extends PhalApi_Api { 
	public function getRules() {
        return array(
			'userLogin' => array(
                'country_code' => array('name' => 'country_code', 'type' => 'int', 'default'=>'86','require' => true,  'desc' => '国家代号'),
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'require' => true,  'min' => '6',  'max'=>'30', 'desc' => '账号'),
				'user_pass' => array('name' => 'user_pass', 'type' => 'string','require' => true,  'min' => '1',  'max'=>'30', 'desc' => '密码'),
				
            ),
			'userReg' => array(
                'country_code' => array('name' => 'country_code', 'type' => 'int','default'=>'86', 'require' => true,  'desc' => '国家代号'),
                'user_login' => array('name' => 'user_login', 'type' => 'string','require' => true,  'min' => '6',  'max'=>'30', 'desc' => '账号'),
				'user_pass' => array('name' => 'user_pass', 'type' => 'string','require' => true,  'min' => '1',  'max'=>'30', 'desc' => '密码'),
				'user_pass2' => array('name' => 'user_pass2', 'type' => 'string',  'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '确认密码'),
                'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '验证码'),
                'source' => array('name' => 'source', 'type' => 'string',  'default'=>'pc', 'desc' => '来源设备'),
                'source_type' => array('name' => 'source_type', 'type' => 'int',  'default'=>'0', 'desc' => '0：直播demo；1：小程序'),
            ),
			'userFindPass' => array(
                'country_code' => array('name' => 'country_code', 'type' => 'int','default'=>'86', 'require' => true,  'desc' => '国家代号'),
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'require' => true,  'min' => '6',  'max'=>'30', 'desc' => '账号'),
				'user_pass' => array('name' => 'user_pass', 'type' => 'string', 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '密码'),
				'user_pass2' => array('name' => 'user_pass2', 'type' => 'string', 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '确认密码'),
                'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '验证码'),
                'source_type' => array('name' => 'source_type', 'type' => 'int',  'default'=>'0', 'desc' => '0：直播demo；1：小程序'),
            ),	
			'userLoginByThird' => array(
                'openid' => array('name' => 'openid', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '第三方openid'),
                'type' => array('name' => 'type', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '第三方标识 qq/wx'),
                'nicename' => array('name' => 'nicename', 'type' => 'string',   'default'=>'',  'desc' => '第三方昵称'),
                'avatar' => array('name' => 'avatar', 'type' => 'string',  'default'=>'', 'desc' => '第三方头像'),
                'sign' => array('name' => 'sign', 'type' => 'string',  'default'=>'', 'desc' => '签名'),
                'source' => array('name' => 'source', 'type' => 'string',  'default'=>'pc', 'desc' => '来源设备'),
                'access_token' => array('name' => 'access_token', 'type' => 'string', 'require' => true, 'desc' => '三方接口调用凭证'),
            ),
			
			'getCode' => array(
                'country_code' => array('name' => 'country_code', 'type' => 'int','default'=>'86', 'require' => true,  'desc' => '国家代号'),
				'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '手机号'),
                'sign' => array('name' => 'sign', 'type' => 'string',  'default'=>'', 'desc' => '签名'),
			),
			
			'getForgetCode' => array(
                'country_code' => array('name' => 'country_code', 'type' => 'int','default'=>'86', 'require' => true,  'desc' => '国家代号'),
				'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '手机号'),
                'sign' => array('name' => 'sign', 'type' => 'string',  'default'=>'', 'desc' => '签名'),
			),
            'getUnionid' => array(
				'code' => array('name' => 'code', 'type' => 'string','desc' => '微信code'),
			),

            'logout' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
			),

            'getCancelCondition'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
            ),

            'cancelAccount'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
            ),

            'getCountrys'=>array(
                'field' => array('name' => 'field', 'type' => 'string', 'default'=>'', 'desc' => '搜索json串'),
            ),


        );
	}
	
    /**
     * 会员登陆 需要密码
     * @desc 用于用户登陆信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nickname 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string info[0].usersig 腾讯IM签名
     * @return string msg 提示信息
     */
    public function userLogin() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

		$country_code=checkNull($this->country_code);
        $user_login=checkNull($this->user_login);
		$user_pass=checkNull($this->user_pass);

        $domain = new Domain_Login();
        $info = $domain->userLogin($country_code,$user_login,$user_pass);

		if($info==1001){
			$rs['code'] = 1001;
            $rs['msg'] = '账号或密码错误';
            return $rs;	
		}else if($info==1002){
			$rs['code'] = 1002;
			//禁用信息
			$baninfo=$domain->getUserban($user_login);
            $rs['info'][0] =$baninfo;
            return $rs;	
		}else if($info==1003){
			$rs['code'] = 1003;
            $rs['msg'] = '该账号已被禁用';
            return $rs;	
		}else if($info==1004){
            $rs['code'] = 1004;
            $rs['msg'] = '该账号已注销';
            return $rs; 
        }else if($info==1005){
            $rs['code'] = 1005;
            $rs['msg'] = '请先下麦再登录';
            return $rs; 
        }


	
        $rs['info'][0] = $info;
        
        
				
		
        return $rs;
    }		
   /**
     * 会员注册
     * @desc 用于用户注册信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nickname 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function userReg() {

        $rs = array('code' => 0, 'msg' => '注册成功', 'info' => array());
	   
		$country_code=checkNull($this->country_code);
        $user_login=checkNull($this->user_login);
		$user_pass=checkNull($this->user_pass);
		$user_pass2=checkNull($this->user_pass2);
		$source=checkNull($this->source);
		$code=checkNull($this->code);
        $source_type=checkNull($this->source_type);

        if($source_type!='1'){

            if(!$_SESSION['reg_mobile'] || !$_SESSION['reg_mobile_code']){
                $rs['code'] = 1001;
                $rs['msg'] = '请先获取验证码';
                return $rs;     
            }

            if($country_code!=$_SESSION['country_code']){
                $rs['code'] = 1001;
                $rs['msg'] = '国家不一致';
                return $rs;                 
            }

        
            if($user_login!=$_SESSION['reg_mobile']){
                $rs['code'] = 1001;
                $rs['msg'] = '手机号码不一致';
                return $rs;                 
            }

            if($code!=$_SESSION['reg_mobile_code']){
                $rs['code'] = 1002;
                $rs['msg'] = '验证码错误';
                return $rs;                 
            }   

        }
        
        

		if($user_pass!=$user_pass2){
            $rs['code'] = 1003;
            $rs['msg'] = '两次输入的密码不一致';
            return $rs;					
		}	
        
		$check = passcheck($user_pass);

		if(!$check){
            $rs['code'] = 1004;
            $rs['msg'] = '密码为6-20位字母数字组合';
            return $rs;										
        }
        
		$domain = new Domain_Login();
		$info = $domain->userReg($country_code,$user_login,$user_pass,$source);

		if($info==1006){
			$rs['code'] = 1006;
            $rs['msg'] = '该手机号已被注册！';
            return $rs;	
		}else if($info==1007){
			$rs['code'] = 1007;
            $rs['msg'] = '注册失败，请重试';
            return $rs;	
		}

        $rs['info'][0] = $info;
		
		$_SESSION['reg_mobile'] = '';
		$_SESSION['reg_mobile_code'] = '';
		$_SESSION['reg_mobile_expiretime'] = '';
			
        return $rs;
    }		
	/**
     * 会员找回密码
     * @desc 用于会员找回密码
     * @return int code 操作码，0表示成功，1表示验证码错误，2表示用户密码不一致,3短信手机和登录手机不一致 4、用户不存在 801 密码6-12位数字与字母
     * @return array info 
     * @return string msg 提示信息
     */
    public function userFindPass() {
		
        $rs = array('code' => 0, 'msg' => '密码找回成功', 'info' => array());
		
		$country_code=checkNull($this->country_code);
        $user_login=checkNull($this->user_login);
		$user_pass=checkNull($this->user_pass);
		$user_pass2=checkNull($this->user_pass2);
		$code=checkNull($this->code);
        $source_type=checkNull($this->source_type);//0:直播demo；1：小程序

        if($source_type!='1'){

            if(!$_SESSION['forget_country_code'] || !$_SESSION['forget_mobile'] || !$_SESSION['forget_mobile_code']){
                $rs['code'] = 1001;
                $rs['msg'] = '请先获取验证码';
                return $rs;     
            }

            if($country_code!=$_SESSION['forget_country_code']){
                $rs['code'] = 1001;
                $rs['msg'] = '国家不一致';
                return $rs;                 
            }
            
            if($user_login!=$_SESSION['forget_mobile']){
                $rs['code'] = 1001;
                $rs['msg'] = '手机号码不一致';
                return $rs;                 
            }

            if($code!=$_SESSION['forget_mobile_code']){
                $rs['code'] = 1002;
                $rs['msg'] = '验证码错误';
                return $rs;                 
            }

        }
		
			
		

		if($user_pass!=$user_pass2){
            $rs['code'] = 1003;
            $rs['msg'] = '两次输入的密码不一致';
            return $rs;					
		}	

		$check = passcheck($user_pass);
		if(!$check){
            $rs['code'] = 1004;
            $rs['msg'] = '密码为6-20位字母数字组合';
            return $rs;										
        }	

		$domain = new Domain_Login();
        $info = $domain->userFindPass($country_code,$user_login,$user_pass);	
		
		if($info==1006){
			$rs['code'] = 1006;
            $rs['msg'] = '该帐号不存在';
            return $rs;	
		}else if($info===false){
			$rs['code'] = 1007;
            $rs['msg'] = '重置失败，请重试';
            return $rs;	
		}
		
        $_SESSION['forget_mobile_code'] = '';
		$_SESSION['forget_mobile'] = '';
		$_SESSION['forget_mobile_code'] = '';
		$_SESSION['forget_mobile_expiretime'] = '';

        return $rs;
    }
	
    /**
     * 第三方登录
     * @desc 用于用户登陆信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nickname 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string info[0].usersig 腾讯IM签名
     * @return string msg 提示信息
     */
    public function userLoginByThird() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		$openid=checkNull($this->openid);
		$type=checkNull($this->type);
		$nicename=checkNull($this->nicename);
		$avatar=checkNull($this->avatar);
		$source=checkNull($this->source);
		$sign=checkNull($this->sign);
        $access_token=checkNull($this->access_token);
        
        
        $checkdata=array(
            'openid'=>$openid
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']='签名错误';
			return $rs;	
        }

        if($access_token){
            if($type == 'wx'){
                $url="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid;

                 //file_put_contents(API_ROOT.'/../log/phalapi/login_userLoginByThird_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' wx access_token:'.json_encode($access_token)."\r\n",FILE_APPEND);
                 //file_put_contents(API_ROOT.'/../log/phalapi/login_userLoginByThird_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' wx openid:'.json_encode($openid)."\r\n",FILE_APPEND);

                $res=$this->checkThirdUserInfo($url,$type);

                //file_put_contents(API_ROOT.'/../log/phalapi/login_userLoginByThird_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 三方信息wx验证结果:'.json_encode($res)."\r\n\r\n",FILE_APPEND);

                if($res['code'] !=0){
                    $res['msg']='信息验证失败';
                    return $res;
                }


            }else if($type == 'qq'){

                $configpri=getConfigPri();
                $url="https://graph.qq.com/user/get_user_info?access_token=".$access_token."&oauth_consumer_key=".$configpri['login_qq_appid']."&openid=".$openid;

                //file_put_contents(API_ROOT.'/../log/phalapi/login_userLoginByThird_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' qq access_token:'.json_encode($access_token)."\r\n",FILE_APPEND);
                //file_put_contents(API_ROOT.'/../log/phalapi/login_userLoginByThird_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' qq openid:'.json_encode($openid)."\r\n",FILE_APPEND);

                $res=$this->checkThirdUserInfo($url,$type);

                //file_put_contents(API_ROOT.'/../log/phalapi/login_userLoginByThird_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 三方信息qq验证结果:'.json_encode($res)."\r\n",FILE_APPEND);

                if($res['code'] !=0){
                    $res['msg']='信息验证失败';
                    return $res;
                }

            }
        }


        $domain = new Domain_Login();
        $info = $domain->userLoginByThird($openid,$type,$nicename,$avatar,$source);
		
        if($info==1002){
            $rs['code'] = 1002;
			//禁用信息
			$baninfo=$domain->getThirdUserban($openid,$type);
            $rs['info'][0] =$baninfo;
            return $rs;					
		}else if($info==1003){
			$rs['code'] = 1003;
            $rs['msg'] = '该账号已被禁用';
            return $rs;	
		}else if($info==1004){
            $rs['code'] = 1004;
            $rs['msg'] = '该账号已注销';
            return $rs; 
        }else if($info==1005){
            $rs['code'] = 1005;
            $rs['msg'] = '请先下麦再登录';
            return $rs; 
        }

        $rs['info'][0] = $info;
        

        return $rs;
    }
	
	/**
	 * 获取注册短信验证码
	 * @desc 用于注册获取短信验证码
	 * @return int code 操作码，0表示成功,2发送失败
	 * @return array info 
	 * @return string msg 提示信息
	 */
	 
	public function getCode() {
		$rs = array('code' => 0, 'msg' => '发送成功', 'info' => array(),"verificationcode"=>0);
		
		$country_code = checkNull($this->country_code);
        $mobile = checkNull($this->mobile);
		$sign = checkNull($this->sign);

        $sms_check=$this->checkSmsType($country_code,$mobile);
        if($sms_check['code'] !=0){
            return $sms_check;
        }
		
        
        $checkdata=array(
            'mobile'=>$mobile
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']='签名错误';
			return $rs;	
        }
		
        $where="country_code='{$country_code}' and user_login='{$mobile}'";
        
		$checkuser = checkUser($where);	
        
        if($checkuser){
            $rs['code']=1004;
			$rs['msg']='该手机号已注册';
			return $rs;
        }

		if($_SESSION['country_code']==$country_code && $_SESSION['reg_mobile']==$mobile && $_SESSION['reg_mobile_expiretime']> time() ){
			$rs['code']=1002;
			$rs['msg']='验证码5分钟有效，请勿多次发送';
			return $rs;
		}
		
        $limit = ip_limit();	
		if( $limit == 1){
			$rs['code']=1003;
			$rs['msg']='您当日已发送次数过多';
			return $rs;
		}		
		$mobile_code = random(6,1);
		
		/* 发送验证码 */
 		$result=sendCode($country_code,$mobile,$mobile_code);
		if($result['code']==0){
            $rs['verificationcode']=$mobile_code;
            $_SESSION['country_code'] = $country_code;
			$_SESSION['reg_mobile'] = $mobile;
			$_SESSION['reg_mobile_code'] = $mobile_code;
			$_SESSION['reg_mobile_expiretime'] = time() +60*5;	
		}else if($result['code']==667){
			$_SESSION['country_code'] = $country_code;
            $_SESSION['reg_mobile'] = $mobile;
            $_SESSION['reg_mobile_code'] = $result['msg'];
            $_SESSION['reg_mobile_expiretime'] = time() +60*5;
            
            $rs['verificationcode']='123456';
            $rs['code']=1002;
			$rs['msg']='验证码为：'.$result['msg'];
		}else{
			$rs['code']=1002;
			$rs['msg']=$result['msg'];
		} 
		
		
		return $rs;
	}		

	/**
	 * 获取找回密码短信验证码
	 * @desc 用于找回密码获取短信验证码
	 * @return int code 操作码，0表示成功,2发送失败
	 * @return array info 
	 * @return string msg 提示信息
	 */
	 
	public function getForgetCode() {
		$rs = array('code' => 0, 'msg' => '发送成功', 'info' => array(),"verificationcode"=>0);
		
		$country_code = checkNull($this->country_code);
        $mobile = checkNull($this->mobile);
		$sign = checkNull($this->sign);
		
        $sms_check=$this->checkSmsType($country_code,$mobile);
        if($sms_check['code'] !=0){
            return $sms_check;
        }
		
        
        $checkdata=array(
            'mobile'=>$mobile
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']='签名错误';
			return $rs;	
        }
        
        $where="country_code='{$country_code}' and user_login='{$mobile}'";
        $checkuser = checkUser($where);	
        
        if(!$checkuser){
            $rs['code']=1004;
			$rs['msg']='该手机号未注册';
			return $rs;
        }

        //判断手机号是否注销
        $is_destroy=checkIsDestroyByLogin($country_code,$mobile);
        if($is_destroy){
            $rs['code']=1005;
            $rs['msg']='该手机号已注销';
            return $rs;
        }

		if($_SESSION['forget_country_code']==$country_code && $_SESSION['forget_mobile']==$mobile && $_SESSION['forget_mobile_expiretime']> time() ){
			$rs['code']=1002;
			$rs['msg']='验证码5分钟有效，请勿多次发送';
			return $rs;
		}

        $limit = ip_limit();	
		if( $limit == 1){
			$rs['code']=1003;
			$rs['msg']='您已当日发送次数过多';
			return $rs;
		}	
		$mobile_code = random(6,1);
		
		/* 发送验证码 */
 		$result=sendCode($country_code,$mobile,$mobile_code);
		if($result['code']==0){
            $rs['verificationcode']=$mobile_code;
			$_SESSION['forget_country_code'] = $country_code;
            $_SESSION['forget_mobile'] = $mobile;
			$_SESSION['forget_mobile_code'] = $mobile_code;
			$_SESSION['forget_mobile_expiretime'] = time() +60*5;	
		}else if($result['code']==667){
            $_SESSION['forget_country_code'] = $country_code;
			$_SESSION['forget_mobile'] = $mobile;
            $_SESSION['forget_mobile_code'] = $result['msg'];
            $_SESSION['forget_mobile_expiretime'] = time() +60*5;
            
            $rs['verificationcode']='123456';
            $rs['code']=1002;
			$rs['msg']='验证码为：'.$result['msg'];
		}else{
			$rs['code']=1002;
			$rs['msg']=$result['msg'];
		} 
		
		return $rs;
	}	
    
	/**
	 * 获取微信登录unionid
	 * @desc 用于获取微信登录unionid
	 * @return int code 操作码，0表示成功,2发送失败
	 * @return array info 
	 * @return string info[0].unionid 微信unionid
	 * @return string msg 提示信息
	 */    
    public function getUnionid(){
        
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $code=checkNull($this->code);
        
        if($code==''){
            $rs['code']=1001;
			$rs['msg']='参数错误';
			return $rs;
            
        }

        $configpri=getConfigPri();
    
        $AppID = $configpri['wx_mini_appid'];
        $AppSecret = $configpri['wx_mini_appsecret'];
        /* 获取token */
        //$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$AppID}&secret={$AppSecret}&code={$code}&grant_type=authorization_code";
        $url="https://api.weixin.qq.com/sns/jscode2session?appid={$AppID}&secret={$AppSecret}&js_code={$code}&grant_type=authorization_code";
        file_put_contents(API_ROOT.'/../log/phalapi/login_getUnionid_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 请求地址参数信息 url:'.$url."\r\n",FILE_APPEND);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,1);
        file_put_contents(API_ROOT.'/../log/phalapi/login_getUnionid_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 code:'.json_encode($code)."\r\n",FILE_APPEND);
        file_put_contents(API_ROOT.'/../log/phalapi/login_getUnionid_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 返回参数信息 arr:'.json_encode($arr)."\r\n",FILE_APPEND);
        if($arr['errcode']){
            $rs['code']=1003;
			$rs['msg']='小程序信息配置错误';
//            file_put_contents(API_ROOT.'/../log/phalapi/login_getUnionid_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 返回错误信息 arr:'.json_encode($arr)."\r\n\r\n",FILE_APPEND);
			return $rs;
        }

        // 小程序 绑定到 开放平台 才有 unionid  否则 用 openid
        $unionid=$arr['unionid'];

        if(!$unionid){
            //$rs['code']=1002;
			//$rs['msg']='公众号未绑定到开放平台';
			//return $rs;
            
            $unionid=$arr['openid'];
        }
        
        $rs['info'][0]['unionid'] = $unionid;
        $rs['info'][0]['openid'] = $arr['openid'];
        return $rs;
    }
    
	/**
	 * 退出
	 * @desc 用于用户退出 注销极光
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function logout() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid = checkNull($this->uid);
		$token=checkNull($this->token);
        
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}


		return $rs;			
	}



    /**
     * 获取注销账号的条件
     * @desc 用于获取注销账号的条件
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     * @return array info[0]['list'] 条件数组
     * @return string info[0]['list'][]['title'] 标题
     * @return string info[0]['list'][]['content'] 内容
     * @return string info[0]['list'][]['is_ok'] 是否满足条件 0 否 1 是
     * @return string info[0]['can_cancel'] 是否可以注销账号 0 否 1 是
     */
    public function getCancelCondition(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        
        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $domain=new Domain_Login();
        $res=$domain->getCancelCondition($uid);

        $rs['info'][0]=$res;

        return $rs;
    }

    /**
     * 用户注销账号
     * @desc 用于用户注销账号
     * @return int code 状态码,0表示成功
     * @return string msg 返回提示信息
     * @return array info 返回信息
     */
    public function cancelAccount(){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        if(!$time||!$sign){
            $rs['code'] = 1001;
            $rs['msg'] = '参数错误';
            return $rs;
        }

        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $domain=new Domain_Login();
        $res=$domain->cancelAccount($uid);

        if($res==1001){
        	$rs['code']=1001;
            $rs['msg']='相关内容不符合注销账号条件';
            return $rs;
        }

        $rs['msg']='注销成功,手机号、身份证号等信息已解除';
        return $rs;
    }

    /**
     * 获取国家列表
     * @desc 用于获取国家列表
     * string field 搜索内容
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string name 国家中文名称
     * @return string name_name 国家英文名称
     * @return string tel 国家区号
     * @return string msg 提示信息
     */
    public function getCountrys() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $field=checkNull($this->field);
        
        $key='getCountrys';
        $info=getcaches($key);
        if(!$info){
            $country=API_ROOT.'/../data/config/country.json';
            // 从文件中读取数据到PHP变量 
            $json_string = file_get_contents($country); 
             // 用参数true把JSON字符串强制转成PHP数组 
            $data = json_decode($json_string, true);

            $info=$data['country']; //国家
            
            setcaches($key,$info);
        }
        if($field){
            $rs['info']=$this->country_searchs($field,$info);
            return $rs;
        }
     
        $rs['info']=$info;
        return $rs;
    }

    private function country_searchs($field,$data) {

        $arr=array();
        foreach($data as $k => $v){
        
            $lists=$v['lists'];
        
            foreach ($lists as $k => $v) {
                
                if(strstr($v['name'], $field) !== false){//英文搜索替换为：$v['name_en']
                    
                    array_push($arr, $v);
                }
            }

        
        }
        return $arr;
    }

    /**
     * 验证三方登录用户信息
     * @param  string $url api地址
     * @param  string $type api类型
     * @return int code 状态码 0表示验证成功
     * @return string msg 返回提示信息
     * @return  code 状态码 0表示验证成功
     * 微信:https://developers.weixin.qq.com/doc/oplatform/Mobile_App/WeChat_Login/Authorized_API_call_UnionID.html
     * qq:https://wiki.connect.qq.com/%e5%bc%80%e5%8f%91%e6%94%bb%e7%95%a5_server-side
     */
    private function checkThirdUserInfo($url,$type){

        $rs=array('code'=>0,'msg'=>'','info'=>array());

        $header = array(
           'Accept: application/json',
        );
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 超时设置,以秒为单位
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
     
        // 超时设置，以毫秒为单位
        // curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);
     
        // 设置请求头
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        //执行命令
        $result = curl_exec($curl);
     
        // 显示错误信息
        if (curl_error($curl)) {
            $rs['code']=1001;
            $rs['msg']='信息验证失败';
            
        } else {

            // 打印返回的内容
            $res_arr=json_decode($result,true);

            if($type=='wx'){

                //验证失败
                if(isset($res_arr['errcode'])){
                    $rs['code']=$res_arr['errcode'];
                    $rs['msg']=$res_arr['errmsg'];
                }

            }else if($type=='qq'){
                //验证失败
                if($res_arr['ret'] !=0){
                    $rs['code']=50001;
                    $rs['msg']=$res_arr['msg'];
                }
            }

            curl_close($curl);
        }

        return $rs;


    }


    /**
     * 检测短信开关
     */
    private function checkSmsType($country_code,$mobile){
        $rs=array('code'=>0,'msg'=>'','info'=>array());

        $configpri=getConfigPri();
        $typecode_switch=$configpri['typecode_switch'];

        if($typecode_switch==1){ //阿里云验证码

            $aly_sendcode_type=$configpri['aly_sendcode_type'];

            if($aly_sendcode_type==1){ //国内验证码
                if($country_code!=86){
                    $rs['code']=1001;
                    $rs['msg']='平台只允许选择中国大陆';
                    return $rs;
                }

                $ismobile=checkMobile($mobile);
                if(!$ismobile){
                    $rs['code']=1001;
                    $rs['msg']='请输入正确的手机号';
                    return $rs; 
                }

            }else if($aly_sendcode_type==2){ //海外/港澳台 验证码
                if($country_code==86){
                    $rs['code']=1001;
                    $rs['msg']='平台只允许选择除中国大陆外的国家/地区';
                    return $rs;
                }
            }
        }else if($typecode_switch==2){ //容联云

            $ismobile=checkMobile($mobile);
            if(!$ismobile){
                $rs['code']=1001;
                $rs['msg']='请输入正确的手机号';
                return $rs; 
            }
        }else if($typecode_switch==3){ //腾讯云
            
            $tencent_sendcode_type=$configpri['tencent_sendcode_type'];
            if($tencent_sendcode_type==1){ //中国大陆
                if($country_code!=86){
                    $rs['code']=1001;
                    $rs['msg']='平台只允许选择中国大陆';
                    return $rs;
                }

                $ismobile=checkMobile($mobile);
                if(!$ismobile){
                    $rs['code']=1001;
                    $rs['msg']='请输入正确的手机号';
                    return $rs; 
                }
            }else if($tencent_sendcode_type==2){ //海外/港澳台 验证码
                if($country_code==86){
                    $rs['code']=1001;
                    $rs['msg']='平台只允许选择除中国大陆外的国家/地区';
                    return $rs;
                }
            }
        }

        return $rs;
    }

}
