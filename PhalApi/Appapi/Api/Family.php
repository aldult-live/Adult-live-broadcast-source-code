<?php
/**
 * 家族
 */
class Api_Family extends PhalApi_Api {

	public function getRules() {
		return array(
			'createFamily'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'require' => true, 'desc' => '用户Token'),
                'familyname' => array('name' => 'familyname', 'type' => 'string',  'desc' => '家族名称'),
                'username' => array('name' => 'username', 'type' => 'string',  'desc' => '家族长姓名'),
                'cardno' => array('name' => 'cardno', 'type' => 'string',  'desc' => '身份证号'),
                'divide_family' => array('name' => 'divide_family', 'type' => 'string',  'desc' => '抽成比例'),
                'briefing' => array('name' => 'briefing', 'type' => 'string',  'desc' => '家族简介'),
                'apply_pos' => array('name' => 'apply_pos', 'type' => 'string',  'desc' => '身份证正面照'),
                'apply_side' => array('name' => 'apply_side', 'type' => 'string',  'desc' => '身份证背面照'),
                'apply_map' => array('name' => 'apply_map', 'type' => 'string',  'desc' => '家族图标'),
			),
		);
	}
	

	/**
	 * 创建家族
	 * @desc 用于 创建家族
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].switch 开关，0关1开
	 * @return string info[0].type 类型，0图片1视频
	 * @return string info[0].time 图片时间
	 * @return array info[0].list
	 * @return string info[0].list[].thumb 图片、视频链接
	 * @return string info[0].list[].href 页面链接
	 * @return string msg 提示信息
	 */
	public function createFamily() {
		$rs = array('code' => 0, 'msg' => '家族创建成功', 'info' => array());
		
		$uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $familyname=checkNull($this->familyname);
        $username=checkNull($this->username);
        $cardno=checkNull($this->cardno);
        $divide_family=checkNull($this->divide_family);
        $briefing=checkNull($this->briefing);
        $apply_pos=checkNull($this->apply_pos);
        $apply_side=checkNull($this->apply_side);
        $apply_map=checkNull($this->apply_map);

        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        if(!$familyname){
        	$rs['code']=1001;
        	$rs['msg']='请填写家族名称';
        	return $rs;
        }

        if(mb_strlen($familyname)>15){
        	$rs['code']=1001;
        	$rs['msg']='家族名称在15个字符以内';
        	return $rs;
        }

        if(!$username){
        	$rs['code']=1002;
        	$rs['msg']='请填写姓名';
        	return $rs;
        }

        if(mb_strlen($username)>4){
        	$rs['code']=1002;
        	$rs['msg']='姓名在4个字符以内';
        	return $rs;
        }
        if(!$cardno){
        	$rs['code']=1003;
        	$rs['msg']='请填写身份证号';
        	return $rs;
        }

        $cardno_check=isCreditNo($cardno);
        if(!$cardno_check){
        	$rs['code']=1004;
        	$rs['msg']='身份证号不合法';
        	return $rs;
        }

        if(!is_numeric($divide_family)){
        	$rs['code']=1004;
        	$rs['msg']='抽成比例必须是数字';
        	return $rs;
        }

        if($divide_family<0 || $divide_family>100 || floor($divide_family)!=$divide_family){
        	$rs['code']=1004;
        	$rs['msg']='抽成比例必须是0-100之间的整数';
        	return $rs;
        }

        if(!$briefing){
        	$rs['code']=1005;
        	$rs['msg']='请填写家族简介';
        	return $rs;
        }
        if(mb_strlen($briefing)>150){
        	$rs['code']=1005;
        	$rs['msg']='家族简介应在150字符以内';
        	return $rs;
        }

        if(!$apply_pos){
        	$rs['code']=1006;
        	$rs['msg']='请上传身份证正面照';
        	return $rs;
        }

        if(!$apply_side){
        	$rs['code']=1007;
        	$rs['msg']='请上传身份证背面照';
        	return $rs;
        }

        if(!$apply_map){
        	$rs['code']=1008;
        	$rs['msg']='请上传家族图标';
        	return $rs;
        }

        $info=array(
        	'uid'=>$uid,
        	'name'=>$familyname,
        	'badge'=>$apply_map,
        	'apply_pos'=>$apply_pos,
        	'apply_side'=>$apply_side,
        	'briefing'=>$briefing,
        	'carded'=>$cardno,
        	'fullname'=>$username,
        	'addtime'=>time(),
        	'state'=>0,
        	'divide_family'=>$divide_family
        );

		$domain = new Domain_Family();
		$res = $domain->createFamily($info);
		if($res==1001){
			$rs['code']=1009;
			$rs['msg']='家族创建失败';
			return $rs;		
		}

		if($res==1002){
			$rs['code']=1010;
			$rs['msg']='您提交的家族申请正在审核中';
			return $rs;		
		}

		if($res==1003){
			$rs['code']=1011;
			$rs['msg']='家族已创建成功';
			return $rs;		
		}

		return $rs;
	}		
	

}
