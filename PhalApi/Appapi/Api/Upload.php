<?php
/**
 * 上传
 */
class Api_Upload extends PhalApi_Api {

	public function getRules() {
		return array(

		);
	}
	
	/**
	 * 获取云存储方式、获取七牛云存储上传验证token字符串等信息、获取亚马逊存储相关配置信息
	 * @desc 用于获取云存储方式、获取七牛云存储上传验证token字符串等信息、获取亚马逊存储相关配置信息
	 * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
	 */
	public function getCosInfo(){
		$rs=array("code"=>0,"msg"=>"","info"=>array());

		$configpri=getConfigPri();
		$cloudtype=$configpri['cloudtype'];

		if(!$cloudtype){
			$rs['code']=1001;
            $rs['msg']="无指定存储方式";
            return $rs;
		}

		$qiniuInfo=array(
            'qiniuToken'=>'',
            'qiniu_domain'=>'',
            'qiniu_zone'=>''  //华东:qiniu_hd 华北:qiniu_hb  华南:qiniu_hn  北美:qiniu_bm   新加坡:qiniu_xjp 不可随意更改，app已固定好规则
        );

        $awsInfo=array(
            'aws_bucket'=>'',
            'aws_region'=>'',
            'aws_identitypoolid'=>'',
        );

        if($cloudtype=='1'){ //七牛云存储

        	$qiniuToken=$this->getQiniuToken();
        	$space_host=DI()->config->get('app.Qiniu.space_host');
        	$region= DI()->config->get('app.Qiniu.region');
        	$qiniu_zone='';
			
			if($region=='z0'){
				$qiniu_zone='qiniu_hd';
			}else if($region=='z1'){
				$qiniu_zone='qiniu_hb';
			}else if($region=='z2'){
				$qiniu_zone='qiniu_hn';
			}else if($region=='na0'){
				$qiniu_zone='qiniu_bm';
			}else if($region=='as0'){
				$qiniu_zone='qiniu_xjp';
			}

        	$qiniuInfo=array(
	            'qiniuToken'=>$qiniuToken,
	            'qiniu_domain'=>$space_host,
	            'qiniu_zone'=>$qiniu_zone  //华东:qiniu_hd 华北:qiniu_hb  华南:qiniu_hn  北美:qiniu_bm   新加坡:qiniu_xjp 不可随意更改，app已固定好规则
	        );

        }else if($cloudtype=='2'){ //亚马逊
        	$configpri=getConfigPri();
        	$awsInfo=array(
	            'aws_bucket'=>$configpri['aws_bucket'],
	            'aws_region'=>$configpri['aws_region'],
	            'aws_identitypoolid'=>$configpri['aws_identitypoolid'],
	        );
        }


        switch ($cloudtype) {
        	case '1':
        		$cloudtype='qiniu';
        		break;
        	case '2':
        		$cloudtype='aws';
        		break;
        	
        }

        $rs['info'][0]['qiniuInfo']=$qiniuInfo;
        $rs['info'][0]['awsInfo']=$awsInfo;
        $rs['info'][0]['cloudtype']=$cloudtype;

        return $rs;

	}
	
	//获取七牛token
	private function getQiniuToken(){
		$token = DI()->qiniu->getQiniuToken();
		return $token;
	}
}
