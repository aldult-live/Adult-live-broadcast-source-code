<?php

class Model_Auth extends PhalApi_Model_NotORM {
    
    /*获取认证信息*/
	public function getAuth($uid) {
		$rs = array(
			'uid'=>$uid,
			'real_name'=>'',
			'mobile'=>'',
			'cer_no'=>'',
			'front_view'=>'',
			'back_view'=>'',
			'handset_view'=>'',
			'status'=>'-1',
			
		);
		$info=DI()->notorm->user_auth
				->select("uid,real_name,mobile,cer_no,status,front_view,back_view,handset_view")
				->where('uid=?',$uid)
				->fetchOne();
		if($info){
			$info['front_view']=get_upload_path($info['front_view']);
			$info['back_view']=get_upload_path($info['back_view']);
			$info['handset_view']=get_upload_path($info['handset_view']);
			$rs = $info;
		}
		
		return $rs;
	}
    
	
	public function setAuth($data) {
		
		
		//认证已存在,则进行更新
		$setdata=$data;
		$setdata['uptime']=time();
		
		return DI()->notorm->user_auth
			->insert_update(['uid'=>$data['uid']],$data,$setdata);
	}
	
	
	

}
