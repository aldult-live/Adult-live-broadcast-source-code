<?php
/**
 * 用户信息
 */
if (!session_id()) session_start();

//Braintree支付专用--start
include API_ROOT.'/../vendor/Braintree/vendor/autoload.php';
use Braintree\ClientToken;
//Braintree支付专用--start

class Api_User extends PhalApi_Api {

	public function getRules() {
		return array(
			'iftoken' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),
			
			'getBaseInfo' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'version_ios' => array('name' => 'version_ios', 'type' => 'string', 'desc' => 'IOS版本号'),
			),
			
			'updateAvatar' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				/*'file' => array('name' => 'file','type' => 'file', 'min' => 0, 'max' => 1024 * 1024 * 30, 'range' => array('image/jpg', 'image/jpeg', 'image/png'), 'ext' => array('jpg', 'jpeg', 'png')),*/
				'avatar' => array('name' => 'avatar', 'type' => 'string',  'desc' => '用户头像地址'),
			),
			
			'updateFields' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'fields' => array('name' => 'fields', 'type' => 'string', 'require' => true, 'desc' => '修改信息，json字符串'),
			),
			
			'updatePass' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'oldpass' => array('name' => 'oldpass', 'type' => 'string', 'require' => true, 'desc' => '旧密码'),
				'pass' => array('name' => 'pass', 'type' => 'string', 'require' => true, 'desc' => '新密码'),
				'pass2' => array('name' => 'pass2', 'type' => 'string', 'require' => true, 'desc' => '确认密码'),
			),
			
			'getBalance' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'type' => array('name' => 'type', 'type' => 'string', 'desc' => '设备类型，0android，1IOS'),
                'version_ios' => array('name' => 'version_ios', 'type' => 'string', 'desc' => 'IOS版本号'),
			),
			
			'getProfit' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),
			
			'setCash' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'accountid' => array('name' => 'accountid', 'type' => 'int', 'require' => true, 'desc' => '账号ID'),
				'cashvote' => array('name' => 'cashvote', 'type' => 'int', 'require' => true, 'desc' => '提现的票数'),
			),
			
			'setAttent' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'isAttent' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'isBlacked' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			'checkBlack' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),

			'setBlack' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'getBindCode' => array(
				'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '手机号'),
			),
			
			'setMobile' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '手机号'),
				'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '验证码'),
			),
			
			'getFollowsList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
			),
			
			'getFansList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
			),
			
			'getBlackList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
			),
			
			'getLiverecord' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
			),
			
			'getAliCdnRecord' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '直播记录ID'),
            ),
			
			'getUserHome' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'getContributeList' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
			),
			
			'getPmUserInfo' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			
			'getMultiInfo' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'uids' => array('name' => 'uids', 'type' => 'string', 'min' => 1,'require' => true, 'desc' => '用户ID，多个以逗号分割'),
				'type' => array('name' => 'type', 'type' => 'int', 'require' => true, 'desc' => '关注类型，0 未关注 1 已关注'),
			),
            
            'getUidsInfo' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'uids' => array('name' => 'uids', 'type' => 'string', 'min' => 1,'require' => true, 'desc' => '用户ID，多个以逗号分割'),
			),
			'Bonus' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),
            'getBonus' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),
			'setDistribut' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'code' => array('name' => 'code', 'type' => 'string', 'require' => true, 'desc' => '邀请码'),
			),

			'getUserLabel' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
            
            'setUserLabel' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
                'labels' => array('name' => 'labels', 'type' => 'string', 'require' => true, 'desc' => '印象标签ID，多个以逗号分割'),
			),

            'getMyLabel' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),

            'getUserAccountList' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),

            'setUserAccount' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'type' => array('name' => 'type', 'type' => 'int', 'require' => true, 'desc' => '账号类型，1表示支付宝，2表示微信，3表示银行卡'),
                'account_bank' => array('name' => 'account_bank', 'type' => 'string', 'default' => '', 'desc' => '银行名称'),
                'account' => array('name' => 'account', 'type' => 'string', 'require' => true, 'desc' => '账号'),
                'name' => array('name' => 'name', 'type' => 'string', 'default' => '', 'desc' => '姓名'),
			),
            
            'delUserAccount' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '账号ID'),
			),

			'setShopCash' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'accountid' => array('name' => 'accountid', 'type' => 'int', 'require' => true, 'desc' => '账号ID'),
				'money' => array('name' => 'money', 'type' => 'float', 'require' => true, 'desc' => '提现的金额'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
			),

			'getAuthInfo'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),
			
			'seeDailyTasks'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'default' => '0', 'desc' => '主播ID'),
				'islive' => array('name' => 'islive', 'type' => 'int', 'default' => '0',  'desc' => '是否在直播间 0不在 1在'),
			),
			'receiveTaskReward'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'taskid' => array('name' => 'taskid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '任务ID'),
			),

			'setBeautyParams'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'params' => array('name' => 'params', 'type' => 'string', 'require' => true, 'desc' => '用户设置的美颜参数'),
			),

			'getBeautyParams'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),

			'getBraintreeToken' => array( 
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
			),

			'BraintreeCallback'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'ordertype' => array('name' => 'ordertype', 'type' => 'string', 'require' => true, 'desc' => 'order_type 订单类型；coin_charge： 钻石充值；order_pay 商品订单支付；paidprogram_pay：付费内容'),
				'orderno' => array('name' => 'orderno', 'type' => 'string',  'require' => true, 'desc' => '系统的订单编号'),
				'nonce' => array('name' => 'nonce', 'type' => 'string', 'require' => true, 'desc' => 'braintree返回的三方订单编号'),
				'money' => array('name' => 'money', 'type' => 'string', 'require' => true, 'desc' => '充值金额'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),

			),

			'getTurntableWinLists'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
			),

			'clearTurntableWinLists'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),

			'checkTeenager'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),

			'setTeenagerPassword'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'password'=>array('name' => 'password', 'type' => 'string', 'require' => true, 'desc' => '青少年模式密码'),
				'type'=>array('name' => 'type', 'type' => 'int', 'require' => true, 'desc' => '操作类型 0 设置密码 1开启青少年模式'),
			),

			'updateTeenagerPassword'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'oldpassword'=>array('name' => 'oldpassword', 'type' => 'string', 'require' => true, 'desc' => '青少年模式旧密码'),
				'password'=>array('name' => 'password', 'type' => 'string', 'require' => true, 'desc' => '青少年模式新密码'),
			),

			'closeTeenager'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'password'=>array('name' => 'password', 'type' => 'string', 'require' => true, 'desc' => '青少年模式密码'),
			),

			'addTeenagerTime'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
			),

			'updateBgImg' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'),
				'img' => array('name' => 'img','type' => 'string','require' => true, 'desc' => '背景图' ),
			),
			
		);
	}
	/**
	 * 判断token
	 * @desc 用于判断token
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function iftoken() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$checkToken=checkToken($this->uid,$this->token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		return $rs;
	}
	/**
	 * 获取用户信息
	 * @desc 用于获取单个用户基本信息
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return array info[0] 用户信息
	 * @return int info[0].id 用户ID
	 * @return string info[0].level 等级
	 * @return string info[0].lives 直播数量
	 * @return string info[0].follows 关注数
	 * @return string info[0].fans 粉丝数
	 * @return string info[0].agent_switch 分销开关
	 * @return string info[0].family_switch 家族开关
	 * @return string msg 提示信息
	 */
	public function getBaseInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$domain = new Domain_User();
		$info = $domain->getBaseInfo($uid);
        if(!$info){
            $rs['code'] = 700;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
        }
		
		$configpri=getConfigPri();

		$configpub=getConfigPub();
		$agent_switch=$configpri['agent_switch'];
		$family_switch=$configpri['family_switch'];
		$service_switch=$configpri['service_switch'];
		$service_url=$configpri['service_url'];
		$ios_shelves=$configpub['ios_shelves'];
		$dailytask_switch=$configpri['dailytask_switch'];
		
		$info['agent_switch']=$agent_switch;
		$info['family_switch']=$family_switch;

		//判断用户是否申请了店铺
		$shop_switch=checkShopIsPass($uid);
        $info['shop_switch']=$shop_switch;

        //判断用户是否开通了付费内容
        $info['paidprogram_switch']=checkPaidProgramIsPass($uid);
        //商品收藏数量
        $info['goods_collect_nums']=getGoodsCollectNums($uid);

        $info['isauth'] = isAuth($uid);


		/* 个人中心菜单 */
		$version_ios=$this->version_ios;
		$list=array();
		$list1=array();
		$list2=array();
		$shelves=1;
		if($version_ios && $version_ios==$ios_shelves){
			$agent_switch=0;
			$family_switch=0;
			$shelves=0;
		}

		$checkTeenager=$domain->checkTeenager($uid);
		//开启了青少年模式
		if($checkTeenager['info'][0]['is_setpassword']==1 && $checkTeenager['info'][0]['status']==1){

			$list1[]=array('id'=>'23','name'=>'动态','thumb'=>get_upload_path("/static/appapi/images/personal/dynamic.png"),'href'=>'' );
			$list1[]=array('id'=>'27','name'=>'青少年模式','thumb'=>get_upload_path("/static/appapi/images/personal/teenager.png") ,'href'=>'');
	        if($service_switch && $service_url){
	           $list1[]=array('id'=>'21','name'=>'在线客服','thumb'=>get_upload_path("/static/appapi/images/personal/kefu.png") ,'href'=>$service_url); 
	        }

			$list[0]['title']='更多服务';
	        $list[0]['list']=$list1;

		}else{

			$list1[]=array('id'=>'19','name'=>'视频','thumb'=>get_upload_path("/static/appapi/images/personal/video.png"),'href'=>'' );

	        $list1[]=array('id'=>'23','name'=>'动态','thumb'=>get_upload_path("/static/appapi/images/personal/dynamic.png"),'href'=>'' );

	        $list1[]=array('id'=>'3','name'=>'等级','thumb'=>get_upload_path("/static/appapi/images/personal/level.png") ,'href'=>get_upload_path("/appapi/Level/index"));

	        $list1[]=array('id'=>'2','name'=>'道具','thumb'=>get_upload_path("/static/appapi/images/personal/props.png") ,'href'=>get_upload_path("/appapi/Mall/index"));

	        if($shelves){
				$list1[]=array('id'=>'1','name'=>'收益','thumb'=>get_upload_path("/static/appapi/images/personal/votes.png"),'href'=>'' );
			}

	        //转为app固定
	        //$list1[]=array('id'=>'11','name'=>'认证','thumb'=>get_upload_path("/static/appapi/images/personal/auth.png") ,'href'=>get_upload_path("/appapi/Auth/index"));

			if($dailytask_switch){
	        	$list1[]=array('id'=>'25','name'=>'每日任务','thumb'=>get_upload_path("/static/appapi/images/personal/task.png") ,'href'=>'');
	        }

			//直播小店
			$list1[]=array('id'=>'22','name'=>$configpri['shop_system_name'],'thumb'=>get_upload_path("/static/appapi/images/personal/shop.png") ,'href'=>'' ); 
	        
	        $list1[]=array('id'=>'24','name'=>'付费内容','thumb'=>get_upload_path("/static/appapi/images/personal/pay.png") ,'href'=>'' );

	        $list1[]=array('id'=>'20','name'=>'房间管理','thumb'=>get_upload_path("/static/appapi/images/personal/room.png") ,'href'=>'');


	        
			if($shelves){

				$list2[]=array('id'=>'5','name'=>'装备中心','thumb'=>get_upload_path("/static/appapi/images/personal/equipment.png") ,'href'=>get_upload_path("/appapi/Equipment/index"));
			}
	        
	        
			
			if($family_switch){
				$family_status=checkUserFamily($uid);

				if($family_status==1){ //用户创建了家族/申请加入了家族
					$family_href='/appapi/Family/home';
				}else{
					$family_href='/appapi/Family/index';
				}

				$list2[]=array('id'=>'6','name'=>'家族中心','thumb'=>get_upload_path("/static/appapi/images/personal/family.png") ,'href'=>get_upload_path($family_href));
				
			}
			
			if($agent_switch){
				$list2[]=array('id'=>'8','name'=>'邀请奖励','thumb'=>get_upload_path("/static/appapi/images/personal/agent.png") ,'href'=>get_upload_path("/appapi/Agent/index"));
			}

			$list2[]=array('id'=>'26','name'=>'中奖记录','thumb'=>get_upload_path("/static/appapi/images/personal/turntable.png") ,'href'=>'');

			$list2[]=array('id'=>'27','name'=>'青少年模式','thumb'=>get_upload_path("/static/appapi/images/personal/teenager.png") ,'href'=>'');
			
	        if($service_switch && $service_url){
	           $list2[]=array('id'=>'21','name'=>'在线客服','thumb'=>get_upload_path("/static/appapi/images/personal/kefu.png") ,'href'=>$service_url); 
	        }
			
			$list[0]['title']='我的服务';
	        $list[0]['list']=$list1;
	        $list[1]['title']='更多服务';
	        $list[1]['list']=$list2;

		}

        

		$info['list']=$list;
		$rs['info'][0] = $info;

		return $rs;
	}

	/**
	 * 头像上传 
	 * @desc 用于用户修改头像
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string list[0].avatar 用户主头像
	 * @return string list[0].avatar_thumb 用户头像缩略图
	 * @return string msg 提示信息
	 */
	public function updateAvatar() {
		$rs = array('code' => 0 , 'msg' => '设置头像成功', 'info' => array());

		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$avatar_str=checkNull($this->avatar);

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}


		//APP原生上传存储到数据库start

		if(!$avatar_str){
			$rs['code'] = 1001;
			$rs['msg'] = '请上传头像';
			return $rs;
		}

		$configpri=getConfigPri();
		$cloudtype=$configpri['cloudtype'];
		if($cloudtype==1){ //七牛云存储
			$avatar= $avatar_str.'?imageView2/2/w/600/h/600'; //600 X 600
			$avatar_thumb= $avatar_str.'?imageView2/2/w/200/h/200'; // 200 X 200
		
		}else{ //亚马逊存储
			$avatar=$avatar_str;
			$avatar_thumb=$avatar_str;
		}

		$data=array(
			"avatar"=>get_upload_path($avatar),
			"avatar_thumb"=>get_upload_path($avatar_thumb),
		);


		$data2=array(
			"avatar"=>$avatar,
			"avatar_thumb"=>$avatar_thumb,
		);

		//APP原生上传存储到数据库end


		//接口获取文件上传 存储到数据库start
		
		/*if (!isset($_FILES['file'])) {
			$rs['code'] = 1001;
			$rs['msg'] = "请选择上传文件";
			return $rs;
		}

		if ($_FILES["file"]["error"] > 0) {
			$rs['code'] = 1002;
			$rs['msg']='上传失败'.$_FILES["file"]["error"];
			//$rs['msg'] = T('failed to upload file with error: {error}', array('error' => $_FILES['file']['error']));
			DI()->logger->debug('failed to upload file with error: ' . $_FILES['file']['error']);
			return $rs;
		}

		$uptype=DI()->config->get('app.uptype');

		if($uptype==1){
			//七牛
			$url = DI()->qiniu->uploadFile($_FILES['file']['tmp_name']);

			if (!empty($url)) {
				$avatar=  $url.'?imageView2/2/w/600/h/600'; //600 X 600
				$avatar_thumb=  $url.'?imageView2/2/w/200/h/200'; // 200 X 200
				$data=array(
					"avatar"=>$avatar,
					"avatar_thumb"=>$avatar_thumb,
				);
                
                $data2=array(
					"avatar"=>$avatar,
					"avatar_thumb"=>$avatar_thumb,
				);

				
				// 统一服务器 格式
				// $space_host= DI()->config->get('app.Qiniu.space_host');
				// $avatar2=str_replace($space_host.'/', "", $avatar);
				// $avatar_thumb2=str_replace($space_host.'/', "", $avatar_thumb);
				// $data2=array(
				// 	"avatar"=>$avatar2,
				// 	"avatar_thumb"=>$avatar_thumb2,
				// ); 
			}
		}else if($uptype==2){
			//本地上传
			//设置上传路径 设置方法参考3.2
			DI()->ucloud->set('save_path','avatar/'.date("Ymd"));

			//新增修改文件名设置上传的文件名称
		   // DI()->ucloud->set('file_name', $this->uid);

			//上传表单名
			$res = DI()->ucloud->upfile($_FILES['file']);
			
			$files='../upload'.$res['file'];
			$newfiles=str_replace(".png","_thumb.png",$files);
			$newfiles=str_replace(".jpg","_thumb.jpg",$newfiles);
			$newfiles=str_replace(".gif","_thumb.gif",$newfiles); 
			$PhalApi_Image = new Image_Lite();
			//打开图片
			$PhalApi_Image->open($files);
			
			 //可以支持其他类型的缩略图生成，设置包括下列常量或者对应的数字：
			 // IMAGE_THUMB_SCALING      //常量，标识缩略图等比例缩放类型
			 // IMAGE_THUMB_FILLED       //常量，标识缩略图缩放后填充类型
			 // IMAGE_THUMB_CENTER       //常量，标识缩略图居中裁剪类型
			 // IMAGE_THUMB_NORTHWEST    //常量，标识缩略图左上角裁剪类型
			 // IMAGE_THUMB_SOUTHEAST    //常量，标识缩略图右下角裁剪类型
			 // IMAGE_THUMB_FIXED        //常量，标识缩略图固定尺寸缩放类型
			 

			// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
			
			$PhalApi_Image->thumb(660, 660, IMAGE_THUMB_SCALING);
			$PhalApi_Image->save($files);

			$PhalApi_Image->thumb(200, 200, IMAGE_THUMB_SCALING);
			$PhalApi_Image->save($newfiles);			
			
			$avatar=  '/upload'.$res['file']; //600 X 600
			
			$avatar_thumb=str_replace(".png","_thumb.png",$avatar);
			$avatar_thumb=str_replace(".jpg","_thumb.jpg",$avatar_thumb);
			$avatar_thumb=str_replace(".gif","_thumb.gif",$avatar_thumb);

			$data=array(
				"avatar"=>get_upload_path($avatar),
				"avatar_thumb"=>get_upload_path($avatar_thumb),
			);
            
            $data2=array(
				"avatar"=>$avatar,
				"avatar_thumb"=>$avatar_thumb,
			);
			
		}
		
		@unlink($_FILES['file']['tmp_name']);
        if(!$data){
            $rs['code'] = 1003;
			$rs['msg'] = '更换失败，请稍候重试';
			return $rs;
        }*/

        //接口获取文件上传 存储到数据库end

		// 清除缓存
		delCache("userinfo_".$uid);
		
		$domain = new Domain_User();
		$info = $domain->userUpdate($uid,$data2);

		$rs['info'][0] = $data;

		return $rs;

	}
	
	/**
	 * 修改用户信息
	 * @desc 用于修改用户信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string list[0].msg 修改成功提示信息 
	 * @return string msg 提示信息
	 */
	public function updateFields() {
		$rs = array('code' => 0, 'msg' => '修改成功', 'info' => array());
		
		$checkToken=checkToken($this->uid,$this->token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		$fields=json_decode($this->fields,true);
		
        $allow=['user_nickname','sex','signature','birthday','location'];
		$domain = new Domain_User();
		foreach($fields as $k=>$v){
            if(in_array($k,$allow)){
                $fields[$k]=checkNull($v);
            }else{
                unset($fields[$k]);
            }
			
		}
		
		if(array_key_exists('user_nickname', $fields)){
			if($fields['user_nickname']==''){
				$rs['code'] = 1002;
				$rs['msg'] = '昵称不能为空';
				return $rs;
			}
			$isexist = $domain->checkName($this->uid,$fields['user_nickname']);
			if(!$isexist){
				$rs['code'] = 1002;
				$rs['msg'] = '昵称重复，请修改';
				return $rs;
			}



			if(strstr($fields['user_nickname'], '已注销')!==false){ //昵称包含已注销三个字
				$rs['code'] = 10011;
				$rs['msg'] = '输入非法，请重新输入';
				return $rs;
			}

			if(mb_substr($fields['user_nickname'], 0,1)=='='){
				$rs['code'] = 10011;
				$rs['msg'] = '输入非法，请重新输入';
				return $rs;
			}


			//$fields['user_nickname']=filterField($fields['user_nickname']);
            $sensitivewords=sensitiveField($fields['user_nickname']);
			if($sensitivewords==1001){
				$rs['code'] = 10011;
				$rs['msg'] = '输入非法，请重新输入';
				return $rs;
			}
		}
		if(array_key_exists('signature', $fields)){
			$sensitivewords=sensitiveField($fields['signature']);
			if($sensitivewords==1001){
				$rs['code'] = 10011;
				$rs['msg'] = '输入非法，请重新输入';
				return $rs;
			}
		}
        
        if(array_key_exists('birthday', $fields)){
			$fields['birthday']=strtotime($fields['birthday']);
		}
        
		$info = $domain->userUpdate($this->uid,$fields);
	 
		if($info===false){
			$rs['code'] = 1001;
			$rs['msg'] = '修改失败';
			return $rs;
		}
		/* 清除缓存 */
		delCache("userinfo_".$this->uid);
		$rs['info'][0]['msg']='修改成功';
		return $rs;
	}

	/**
	 * 修改密码
	 * @desc 用于修改用户密码
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string list[0].msg 修改成功提示信息
	 * @return string msg 提示信息
	 */
	public function updatePass() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$oldpass=checkNull($this->oldpass);
		$pass=checkNull($this->pass);
		$pass2=checkNull($this->pass2);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		if($pass != $pass2){
			$rs['code'] = 1002;
			$rs['msg'] = '两次新密码不一致';
			return $rs;
		}
		
		$check = passcheck($pass);
		if(!$check ){
			$rs['code'] = 1004;
			$rs['msg'] = '密码为6-20位字母数字组合';
			return $rs;										
		}
		
		$domain = new Domain_User();
		$info = $domain->updatePass($uid,$oldpass,$pass);
	 
		if($info==1003){
			$rs['code'] = 1003;
			$rs['msg'] = '旧密码错误';
			return $rs;
		}else if($info===false){
			$rs['code'] = 1001;
			$rs['msg'] = '修改失败';
			return $rs;
		}

		$rs['info'][0]['msg']='修改成功';
		return $rs;
	}	
	
	/**
	 * 我的钻石
	 * @desc 用于获取用户余额,充值规则 支付方式信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].coin 用户钻石余额
	 * @return array info[0].rules 充值规则
	 * @return string info[0].rules[].id 充值规则
	 * @return string info[0].rules[].coin 钻石
	 * @return string info[0].rules[].money 价格
	 * @return string info[0].rules[].money_ios 苹果充值价格
	 * @return string info[0].rules[].product_id 苹果项目ID
	 * @return string info[0].rules[].give 赠送钻石，为0时不显示赠送
	 * @return string info[0].aliapp_switch 支付宝开关，0表示关闭，1表示开启
	 * @return string info[0].aliapp_partner 支付宝合作者身份ID
	 * @return string info[0].aliapp_seller_id 支付宝帐号	
	 * @return string info[0].aliapp_key_android 支付宝安卓密钥
	 * @return string info[0].aliapp_key_ios 支付宝苹果密钥
	 * @return string info[0].wx_switch 微信支付开关，0表示关闭，1表示开启
	 * @return string info[0].wx_appid 开放平台账号AppID
	 * @return string info[0].wx_appsecret 微信应用appsecret
	 * @return string info[0].wx_mchid 微信商户号mchid
	 * @return string info[0].wx_key 微信密钥key
	 * @return string msg 提示信息
	 */
	public function getBalance() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $type=checkNull($this->type);
        $version_ios=checkNull($this->version_ios);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain = new Domain_User();
		$info = $domain->getBalance($uid);
		
		$key='getChargeRules';
		$rules=getcaches($key);
		if(!$rules){
			$rules= $domain->getChargeRules();
			setcaches($key,$rules);
		}
		$info['rules'] =$rules;
		
		$configpub=getConfigPub();
		$configpri=getConfigPri();
		
		$aliapp_switch=$configpri['aliapp_switch'];
		
		$info['aliapp_switch']=$aliapp_switch;
		$info['aliapp_partner']=$aliapp_switch==1?$configpri['aliapp_partner']:'';
		$info['aliapp_seller_id']=$aliapp_switch==1?$configpri['aliapp_seller_id']:'';
		$info['aliapp_key_android']=$aliapp_switch==1?$configpri['aliapp_key_android']:'';
		$info['aliapp_key_ios']=$aliapp_switch==1?$configpri['aliapp_key_ios']:'';

        $wx_switch=$configpri['wx_switch'];
		$info['wx_switch']=$wx_switch;
		$info['wx_appid']=$wx_switch==1?$configpri['wx_appid']:'';
		$info['wx_appsecret']=$wx_switch==1?$configpri['wx_appsecret']:'';
		$info['wx_mchid']=$wx_switch==1?$configpri['wx_mchid']:'';
		$info['wx_key']=$wx_switch==1?$configpri['wx_key']:'';

		/*【原Paypal支付因无法使用已废弃】
		$paypal_switch=$configpri['paypal_switch'];
		$info['paypal_switch']=$paypal_switch;
		$info['paypal_sandbox']=$configpri['paypal_sandbox'];
		$info['sandbox_clientid']=$configpri['sandbox_clientid'];//Paypal支付沙盒客户端ID
		$info['product_clientid']=$configpri['product_clientid'];//Paypal支付生产客户端ID
		*/

		$braintree_paypal_switch=$configpri['braintree_paypal_switch'];

        $wx_mini_switch=$configpri['wx_mini_switch'];
        $info['wx_mini_switch']=$wx_mini_switch;

        /* 支付列表 */
        $shelves=1;
        $ios_shelves=$configpub['ios_shelves'];
        if($version_ios && $version_ios==$ios_shelves){
			$shelves=0;
		}
        
        $paylist=[];
        
        if($aliapp_switch && $shelves){
            $paylist[]=[
                'id'=>'ali',
                'name'=>'支付宝支付',
                'thumb'=>get_upload_path("/static/app/pay/ali.png"),
                'href'=>'',
            ];
        }
        
        if($wx_switch && $shelves){
            $paylist[]=[
                'id'=>'wx',
                'name'=>'微信支付',
                'thumb'=>get_upload_path("/static/app/pay/wx.png"),
                'href'=>'',
            ];
        }

        /*【原Paypal支付因无法使用已废弃】
        if($paypal_switch && $shelves){
            $paylist[]=[
                'id'=>'paypal',
                'name'=>'Paypal支付',
                'thumb'=>get_upload_path("/static/app/pay/paypal.png"),
                'href'=>'',
            ];
        }*/

        if($braintree_paypal_switch && $shelves){
            $paylist[]=[
                'id'=>'paypal',
                'name'=>'Paypal支付',
                'thumb'=>get_upload_path("/static/app/pay/paypal.png"),
                'href'=>'',
            ];
        }
        
        /*if($shelves==0 && $type==1){
            $paylist[]=[
                'id'=>'apple',
                'name'=>'苹果支付',
                'thumb'=>get_upload_path("/static/app/pay/apple.png"),
                'href'=>'',
            ];
        }*/

        if($type==1){
            $paylist[]=[
                'id'=>'apple',
                'name'=>'苹果支付',
                'thumb'=>get_upload_path("/static/app/pay/apple.png"),
                'href'=>'',
            ];
        }
        
        
        $info['paylist'] =$paylist;
        $info['tip_t'] =$configpub['name_coin'].'/'.$configpub['name_score'].'说明:';
        $info['tip_d'] =$configpub['name_coin'].'可通过平台提供的支付方式进行充值获得，'.$configpub['name_coin'].'适用于平台内所有消费； '.$configpub['name_score'].'可通过直播间内游戏奖励获得，所得'.$configpub['name_score'].'可用于平台商城内兑换会员、坐 骑、靓号等服务，不可提现。';
        
        
     
		$rs['info'][0]=$info;
		return $rs;
	}
	
	/**
	 * 我的收益
	 * @desc 用于获取用户收益，包括可体现金额，今日可提现金额
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].votes 可提取映票数
	 * @return string info[0].votestotal 总映票
	 * @return string info[0].cash_rate 映票兑换比例
	 * @return string info[0].total 可提现金额
	 * @return string info[0].tips 温馨提示
	 * @return string msg 提示信息
	 */
	public function getProfit() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		} 
		
		$domain = new Domain_User();
		$info = $domain->getProfit($uid);
	 
		$rs['info'][0]=$info;
		return $rs;
	}	
	
	/**
	 * 用户提现
	 * @desc 用于进行用户提现
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].msg 提现成功信息
	 * @return string msg 提示信息
	 */
	public function setCash() {
		$rs = array('code' => 0, 'msg' => '提现成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);		
        $accountid=checkNull($this->accountid);		
        $cashvote=checkNull($this->cashvote);		
        
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        if(!$accountid){
            $rs['code'] = 1001;
			$rs['msg'] = '请选择提现账号';
			return $rs;
        }
        
        if(!$cashvote){
            $rs['code'] = 1002;
			$rs['msg'] = '请输入有效的提现票数';
			return $rs;
        }
		
        $data=array(
            'uid'=>$uid,
            'accountid'=>$accountid,
            'cashvote'=>$cashvote,
        );
        $config=getConfigPri();
		$domain = new Domain_User();
		$info = $domain->setCash($data);
		if($info==1001){
			$rs['code'] = 1001;
			$rs['msg'] = '您输入的金额大于可提现金额';
			return $rs;
		}else if($info==1003){
			$rs['code'] = 1003;
			$rs['msg'] = '请先进行身份认证';
			return $rs;
		}else if($info==1004){
			$rs['code'] = 1004;
			$rs['msg'] = '提现最低额度为'.$config['cash_min'].'元';
			return $rs;
		}else if($info==1005){
			$rs['code'] = 1005;
			$rs['msg'] = '不在提现期限内，不能提现';
			return $rs;
		}else if($info==1006){
			$rs['code'] = 1006;
			$rs['msg'] = '每月只可提现'.$config['cash_max_times'].'次,已达上限';
			return $rs;
		}else if($info==1007){
			$rs['code'] = 1007;
			$rs['msg'] = '提现账号信息不正确';
			return $rs;
		}else if(!$info){
			$rs['code'] = 1002;
			$rs['msg'] = '提现失败，请重试';
			return $rs;
		}
	 
		$rs['info'][0]['msg']='提现成功';
		return $rs;
	}		
	/**
	 * 判断是否关注
	 * @desc 用于判断是否关注
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isattent 关注信息，0表示未关注，1表示已关注
	 * @return string msg 提示信息
	 */
	public function isAttent() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$touid=checkNull($this->touid);
		$info = isAttention($uid,$touid);
	 
		$rs['info'][0]['isattent']=(string)$info;
		return $rs;
	}			
	
	/**
	 * 关注/取消关注
	 * @desc 用于关注/取消关注
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isattent 关注信息，0表示未关注，1表示已关注
	 * @return string msg 提示信息
	 */
	public function setAttent() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$touid=checkNull($this->touid);

		if($uid==$touid){
			$rs['code']=1001;
			$rs['msg']='不能关注自己';
			return $rs;	
		}
		$domain = new Domain_User();
		$info = $domain->setAttent($uid,$touid);
	 
		$rs['info'][0]['isattent']=(string)$info;
		return $rs;
	}			
	
	/**
	 * 判断是否拉黑
	 * @desc 用于判断是否拉黑
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isattent  拉黑信息,0表示未拉黑，1表示已拉黑
	 * @return string msg 提示信息
	 */
	public function isBlacked() {
			$rs = array('code' => 0, 'msg' => '', 'info' => array());
			
			$uid=checkNull($this->uid);
			$touid=checkNull($this->touid);

			$info = isBlack($uid,$touid);
		 
			$rs['info'][0]['isblack']=(string)$info;
			return $rs;
	}	

	/**
	 * 检测拉黑状态
	 * @desc 用于私信聊天时判断私聊双方的拉黑状态
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].u2t  是否拉黑对方,0表示未拉黑，1表示已拉黑
	 * @return string info[0].t2u  是否被对方拉黑,0表示未拉黑，1表示已拉黑
	 * @return string msg 提示信息
	 */
	public function checkBlack() {
			$rs = array('code' => 0, 'msg' => '', 'info' => array());

			$uid=checkNull($this->uid);
			$touid=checkNull($this->touid);

			//判断对方是否已注销
			$is_destroy=checkIsDestroyByUid($touid);
			if($is_destroy){
				$rs['code']=1001;
				$rs['msg']='对方已注销';
				return $rs;
			}
			
			$u2t = isBlack($uid,$touid);
			$t2u = isBlack($touid,$uid);
		 
			$rs['info'][0]['u2t']=(string)$u2t;
			$rs['info'][0]['t2u']=(string)$t2u;
			return $rs;
	}			
		
	/**
	 * 拉黑/取消拉黑
	 * @desc 用于拉黑/取消拉黑
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isblack 拉黑信息,0表示未拉黑，1表示已拉黑
	 * @return string msg 提示信息
	 */
	public function setBlack() {
			$rs = array('code' => 0, 'msg' => '', 'info' => array());
			
			$uid=checkNull($this->uid);
			$touid=checkNull($this->touid);

			$domain = new Domain_User();
			$info = $domain->setBlack($uid,$touid);
		 
			$rs['info'][0]['isblack']=(string)$info;
			return $rs;
	}		
	
	/**
	 * 获取找回密码短信验证码
	 * @desc 用于找回密码获取短信验证码
	 * @return int code 操作码，0表示成功,2发送失败
	 * @return array info 
	 * @return array info[0]  
	 * @return string msg 提示信息
	 */
	 
	public function getBindCode() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$mobile = checkNull($this->mobile);
		
		$ismobile=checkMobile($mobile);
		if(!$ismobile){
			$rs['code']=1001;
			$rs['msg']='请输入正确的手机号';
			return $rs;	
		}

		if($_SESSION['set_mobile']==$mobile && $_SESSION['set_mobile_expiretime']> time() ){
			$rs['code']=1002;
			$rs['msg']='验证码5分钟有效，请勿多次发送';
			return $rs;
		}

		$mobile_code = random(6,1);
		
		/* 发送验证码 */
		$result=sendCode($mobile,$mobile_code);
		if($result['code']===0){
			$_SESSION['set_mobile'] = $mobile;
			$_SESSION['set_mobile_code'] = $mobile_code;
			$_SESSION['set_mobile_expiretime'] = time() +60*5;	
		}else if($result['code']==667){
			$_SESSION['set_mobile'] = $mobile;
            $_SESSION['set_mobile_code'] = $result['msg'];
            $_SESSION['set_mobile_expiretime'] = time() +60*5;
            
            $rs['code']=1002;
			$rs['msg']='验证码为：'.$result['msg'];
            
		}else{
			$rs['code']=1002;
			$rs['msg']=$result['msg'];
		}

		
		return $rs;
	}		

	/**
	 * 绑定手机号
	 * @desc 用于用户绑定手机号
	 * @return int code 操作码，0表示成功，非0表示有错误
	 * @return array info 
	 * @return object info[0].msg 绑定成功提示
	 * @return string msg 提示信息
	 */
	public function setMobile() {

		$rs = array('code' => 0, 'msg' => '', 'info' => array());

		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$mobile=checkNull($this->mobile);
		$code=checkNull($this->code);

		if($mobile!=$_SESSION['set_mobile']){
			$rs['code'] = 1001;
			$rs['msg'] = '手机号码不一致';
			return $rs;					
		}

		if($code!=$_SESSION['set_mobile_code']){
			$rs['code'] = 1002;
			$rs['msg'] = '验证码错误';
			return $rs;					
		}	

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
			
		$domain = new Domain_User();

		//更新数据库
		$data=array("mobile"=>$mobile);
		$result = $domain->userUpdate($uid,$data);
		if($result===false){
			$rs['code'] = 1003;
			$rs['msg'] = '绑定失败';
			return $rs;
		}
	
		$rs['info'][0]['msg'] = '绑定成功';

		return $rs;
	}		
	
	/**
	 * 关注列表
	 * @desc 用于获取用户的关注列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].isattent 是否关注,0表示未关注，1表示已关注
	 * @return string msg 提示信息
	 */
	public function getFollowsList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$touid=checkNull($this->touid);
		$p=checkNull($this->p);

		$domain = new Domain_User();
		$info = $domain->getFollowsList($uid,$touid,$p);
	 
		$rs['info']=$info;
		return $rs;
	}		
	
	/**
	 * 粉丝列表
	 * @desc 用于获取用户的粉丝列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].isattent 是否关注,0表示未关注，1表示已关注
	 * @return string msg 提示信息
	 */
	public function getFansList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$touid=checkNull($this->touid);
		$p=checkNull($this->p);

		$domain = new Domain_User();
		$info = $domain->getFansList($uid,$touid,$p);
	 
		$rs['info']=$info;
		return $rs;
	}	

	/**
	 * 黑名单列表
	 * @desc 用于获取用户的黑名单列表
	 * @return int code 操作码，0表示成功
	 * @return array info 用户基本信息
	 * @return string msg 提示信息
	 */
	public function getBlackList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$touid=checkNull($this->touid);
		$p=checkNull($this->p);

		$domain = new Domain_User();
		$info = $domain->getBlackList($uid,$touid,$p);
	 
		$rs['info']=$info;
		return $rs;
	}		
	
	/**
	 * 直播记录
	 * @desc 用于获取用户的直播记录
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].nums 观看人数
	 * @return string info[].datestarttime 格式化的开播时间
	 * @return string info[].dateendtime 格式化的结束时间
	 * @return string info[].video_url 回放地址
	 * @return string info[].file_id 回放标示
	 * @return string msg 提示信息
	 */
	public function getLiverecord() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$touid=checkNull($this->touid);
		$p=checkNull($this->p);

		$domain = new Domain_User();
		$info = $domain->getLiverecord($touid,$p);
	 
		$rs['info']=$info;
		return $rs;
	}	

    /**
     *获取阿里云cdn录播地址
     *@desc 如果使用的阿里云cdn，则使用该接口获取录播地址
     *@return int code 操作码，0表示成功
     *@return string info[0].url 录播视频地址
	 * @return string msg 提示信息
    */		
    public function getAliCdnRecord(){
        $rs = array('code' => 0,'msg' => '', 'info' => array());

        $id=checkNull($this->id);
        $domain = new Domain_Cdnrecord();
        $info = $domain->getCdnRecord($id);
        
        if(!$info['video_url']){
            $rs['code']=1002;
            $rs['msg']='直播回放不存在';
            return $rs;
        }

        $rs['info'][0]['url']=$info['video_url'];

        return $rs;
    }	


	/**
	 * 个人主页 
	 * @desc 用于获取个人主页数据
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].follows 关注数
	 * @return string info[0].fans 粉丝数
	 * @return string info[0].isattention 是否关注，0表示未关注，1表示已关注
	 * @return string info[0].isblack 我是否拉黑对方，0表示未拉黑，1表示已拉黑
	 * @return string info[0].isblack2 对方是否拉黑我，0表示未拉黑，1表示已拉黑
	 * @return array info[0].contribute 贡献榜前三
	 * @return array info[0].contribute[].avatar 头像
	 * @return string info[0].islive 是否正在直播，0表示未直播，1表示直播
	 * @return string info[0].videonums 视频数
	 * @return string info[0].livenums 直播数
	 * @return array info[0].liverecord 直播记录
	 * @return array info[0].label 印象标签
	 * @return string info[0].isshop 是否有店铺，0否1是
	 * @return object info[0].shop 店铺信息
	 * @return string info[0].shop.name 名称
	 * @return string info[0].shop.thumb 封面
	 * @return string info[0].shop.nums 商品数量
	 * @return string msg 提示信息
	 */
	public function getUserHome() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $touid=checkNull($this->touid);
        
		$domain = new Domain_User();
		$info=$domain->getUserHome($uid,$touid);
        
        /* 守护 */
        $data=array(
			"liveuid"=>$touid,
		);

		$domain_guard = new Domain_Guard();
		$guardlist = $domain_guard->getGuardList($data);
        
        $info['guardlist']=array_slice($guardlist,0,3);
        
        /* 标签 */
        $key="getMyLabel_".$touid;
        $label=getcaches($key);
        if(!$label){
            $label = $domain->getMyLabel($touid);
            setcaches($key,$label); 
        }
        
        $labels=array_slice($label,0,3);
        
        $info['label']=$labels;
        
        /* 视频 */
        $domain_video = new Domain_Video();
		$video = $domain_video->getHomeVideo($uid,$touid,1);
        
        $info['videolist']=$video;
        
        /* 店铺 */
        $isshop='0';
        $shop=(object)[];
        
        $domain_shop = new Domain_Shop();
		$shopinfo = $domain_shop->getShop($touid);
        if($shopinfo && $shopinfo['status']=="1"){
            $isshop='1';

            
            $where=[
                'uid'=>$touid,
                'status'=>1,
            ];
            $nums = $domain_shop->countGoods($where);
            
            $shopinfo['nums']=$nums;
            $shop=$shopinfo;
        }
        
        $info['isshop']=$isshop;
        $info['shop']=$shop;
		
		$rs['info'][0]=$info;
		return $rs;
	}		

	/**
	 * 贡献榜 
	 * @desc 用于获取贡献榜
	 * @return int code 操作码，0表示成功
	 * @return array info 排行榜列表
	 * @return string info[].total 贡献总数
	 * @return string info[].userinfo 用户信息
	 * @return string msg 提示信息
	 */
	public function getContributeList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$touid=checkNull($this->touid);
		$p=checkNull($this->p);

		$domain = new Domain_User();
		$info=$domain->getContributeList($touid,$p);
		
		$rs['info']=$info;
		return $rs;
	}	
	
	/**
     * 私信用户信息
     * @desc 用于获取其他用户基本信息
     * @return int code 操作码，0表示成功，1表示用户不存在
     * @return array info   
     * @return string info[0].id 用户ID
     * @return string info[0].isattention 我是否关注对方，0未关注，1已关注
     * @return string info[0].isattention2 对方是否关注我，0未关注，1已关注
     * @return string msg 提示信息
     */
    public function getPmUserInfo() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid=checkNull($this->uid);
		$touid=checkNull($this->touid);

        $info = getUserInfo($touid);
		 if (empty($info)) {
            $rs['code'] = 1001;
            $rs['msg'] = '用户不存在';
            return $rs;
        }
        $info['isattention2']= (string)isAttention($touid,$uid);
        $info['isattention']= (string)isAttention($uid,$touid);
       
        $rs['info'][0] = $info;

        return $rs;
    }		

	/**
	 * 获取多用户信息 
	 * @desc 用于获取获取多用户信息
	 * @return int code 操作码，0表示成功
	 * @return array info 排行榜列表
	 * @return string info[].utot 是否关注，0未关注，1已关注
	 * @return string info[].ttou 对方是否关注我，0未关注，1已关注
	 * @return string msg 提示信息
	 */
	public function getMultiInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

		$uid=checkNull($this->uid);
		$uids=checkNull($this->uids);
		$type=checkNull($this->type);
        
        $configpri=getConfigPri();
        
        if($configpri['letter_switch']!=1){
            return $rs;
        }
		
		$uids=explode(",",$uids);

		foreach ($uids as $k=>$userId) {
			if($userId){
				$userinfo= getUserInfo($userId);
				if($userinfo){
					$userinfo['utot']= isAttention($uid,$userId);
					
					$userinfo['ttou']= isAttention($userId,$uid);
					
					if($userinfo['utot']==$type){						
						$rs['info'][]=$userinfo;
					}												
				}					
			}
		}

		return $rs;
	}	

	/**
	 * 获取多用户信息(不区分是否关注)
	 * @desc 用于获取多用户信息
	 * @return int code 操作码，0表示成功
	 * @return array info 排行榜列表
	 * @return string info[].utot 是否关注，0未关注，1已关注
	 * @return string info[].ttou 对方是否关注我，0未关注，1已关注
	 * @return string msg 提示信息
	 */
	public function getUidsInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$uids=checkNull($this->uids);
		$uids=explode(",",$uids);

		foreach ($uids as $k=>$userId) {
			if($userId){
				$userinfo= getUserInfo($userId);
				if($userinfo){
					$userinfo['utot']= isAttention($uid,$userId);
					
					$userinfo['ttou']= isAttention($userId,$uid);					
                    
                    $rs['info'][]=$userinfo;
											
				}					
			}
		}

		return $rs;
	}	

	/**
	 * 登录奖励
	 * @desc 用于用户登录奖励
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].bonus_switch 登录开关，0表示未开启
	 * @return string info[0].bonus_day 登录天数,0表示已奖励
	 * @return string info[0].count_day 连续登陆天数
	 * @return string info[0].bonus_list 登录奖励列表
	 * @return string info[0].bonus_list[].day 登录天数
	 * @return string info[0].bonus_list[].coin 登录奖励
	 * @return string msg 提示信息
	 */
	public function Bonus() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		$domain = new Domain_User();
		$info=$domain->LoginBonus($uid);

		$rs['info'][0]=$info;

		return $rs;
	}		
    
	/**
	 * 登录奖励
	 * @desc 用于用户登录奖励
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].bonus_switch 登录开关，0表示未开启
	 * @return string info[0].bonus_day 登录天数,0表示已奖励
	 * @return string msg 提示信息
	 */
	public function getBonus() {
		$rs = array('code' => 0, 'msg' => '领取成功', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		$domain = new Domain_User();
		$info=$domain->getLoginBonus($uid);

		if(!$info){
            $rs['code'] = 1001;
			$rs['msg'] = '领取失败';
			return $rs;
        }

		return $rs;
	}
	
	/**
	 * 设置分销上级 
	 * @desc 用于用户首次登录设置分销关系
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].msg 提示信息
	 * @return string msg 提示信息
	 */
	public function setDistribut() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$code=checkNull($this->code);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		if($code==''){
			$rs['code']=1001;
			$rs['msg']='请输入邀请码';
			return $rs;
		}
		
		$domain = new Domain_User();
		$info=$domain->setDistribut($uid,$code);
		if($info==1004){
			$rs['code']=1004;
			$rs['msg']='已设置，不能更改';
			return $rs;
		}
        
		if($info==1002){
			$rs['code']=1002;
			$rs['msg']='邀请码错误';
			return $rs;
		}
        
        if($info==1003){
			$rs['code']=1003;
			$rs['msg']='不能填写自己下级的邀请码';
			return $rs;
		}
		
		$rs['info'][0]['msg']='设置成功';

		return $rs;
	}	

	/**
	 * 获取用户间印象标签 
	 * @desc 用于获取用户间印象标签
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].id 标签ID
	 * @return string info[].name 名称
	 * @return string info[].colour 色值
	 * @return string info[].ifcheck 是否选择
	 * @return string msg 提示信息
	 */
	public function getUserLabel() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $touid=checkNull($this->touid);
        
        $key="getUserLabel_".$uid.'_'.$touid;
		$label=getcaches($key);

		if(!$label){
            $domain = new Domain_User();
			$info = $domain->getUserLabel($uid,$touid);
            $label=$info['label'];
			setcaches($key,$label); 
		}
        
        $label_check=preg_split('/,|，/',$label);
		
        $label_check=array_filter($label_check);
        
        $label_check=array_values($label_check);
        
        
        $key2="getImpressionLabel";
		$label_list=getcaches($key2);
		if(!$label_list){
            $domain = new Domain_User();
			$label_list = $domain->getImpressionLabel();
		}
        
        foreach($label_list as $k=>$v){
            $ifcheck='0';
            if(in_array($v['id'],$label_check)){
                $ifcheck='1';
            }
            $label_list[$k]['ifcheck']=$ifcheck;
        }
        
		$rs['info']=$label_list;

		return $rs;
	}	


	/**
	 * 获取用户间印象标签 
	 * @desc 用于获取用户间印象标签
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].id 标签ID
	 * @return string info[].name 名称
	 * @return string info[].colour 色值
	 * @return string msg 提示信息
	 */
	public function setUserLabel() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $touid=checkNull($this->touid);
        $labels=checkNull($this->labels);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
        if($uid==$touid){
            $rs['code'] = 1003;
			$rs['msg'] = '不能给自己设置标签';
			return $rs;
        }
        
        if($labels==''){
            $rs['code'] = 1001;
			$rs['msg'] = '请选择印象';
			return $rs;
        }
        
        $labels_a=preg_split('/,|，/',$labels);
        $labels_a=array_filter($labels_a);
        $nums=count($labels_a);
        if($nums>3){
            $rs['code'] = 1002;
			$rs['msg'] = '最多只能选择3个印象';
			return $rs;
        }
        

        $domain = new Domain_User();
        $result = $domain->setUserLabel($uid,$touid,$labels);

        if($result){
            $key="getUserLabel_".$uid.'_'.$touid;
            setcaches($key,$labels); 
            
            $key2="getMyLabel_".$touid;
            delcache($key2);
        }

		
		$rs['msg']='设置成功';

		return $rs;
	}	


	/**
	 * 获取自己所有的印象标签 
	 * @desc 用于获取自己所有的印象标签
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].id 标签ID
	 * @return string info[].name 名称
	 * @return string info[].colour 色值
	 * @return string info[].nums 数量
	 * @return string msg 提示信息
	 */
	public function getMyLabel() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);

        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
    
        $key="getMyLabel_".$uid;
		$info=getcaches($key);
		
		if(!$info){
            $domain = new Domain_User();
            $info = $domain->getMyLabel($uid);
			

			setcaches($key,$info); 
		}

		$rs['info']=$info;

		return $rs;
	}	
    

	/**
	 * 获取个性设置列表 
	 * @desc 用于获取个性设置列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function getPerSetting() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_User();
        $info = $domain->getPerSetting();

        $info[]=array('id'=>'17','name'=>'意见反馈','thumb'=>'' ,'href'=>get_upload_path('/appapi/feedback/index'));
        $info[]=array('id'=>'15','name'=>'修改密码','thumb'=>'' ,'href'=>'');
        $info[]=array('id'=>'18','name'=>'清除缓存','thumb'=>'' ,'href'=>'');
        $info[]=array('id'=>'19','name'=>'注销账号','thumb'=>'' ,'href'=>get_upload_path('/portal/page/index?id=44'));
        $info[]=array('id'=>'16','name'=>'检查更新','thumb'=>'' ,'href'=>'');
        

		$rs['info']=$info;

		return $rs;
	}	

	/**
	 * 获取用户提现账号 
	 * @desc 用于获取用户提现账号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].id 账号ID
	 * @return string info[].type 账号类型
	 * @return string info[].account_bank 银行名称
	 * @return string info[].account 账号
	 * @return string info[].name 姓名
	 * @return string msg 提示信息
	 */
	public function getUserAccountList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);

        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}        
    

        $domain = new Domain_User();
        $info = $domain->getUserAccountList($uid);

		$rs['info']=$info;

		return $rs;
	}	

	/**
	 * 添加提现账号 
	 * @desc 用于添加提现账号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function setUserAccount() {
		$rs = array('code' => 0, 'msg' => '添加成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        
        $type=checkNull($this->type);
        $account_bank=checkNull($this->account_bank);
        $account=checkNull($this->account);
        $name=checkNull($this->name);

        if($type==3){
            if($account_bank==''){
                $rs['code'] = 1001;
                $rs['msg'] = '银行名称不能为空';
                return $rs;
            }
        }
        
        if($account==''){
            $rs['code'] = 1002;
            $rs['msg'] = '账号不能为空';
            return $rs;
        }
        
        
        if(mb_strlen($account)>40){
            $rs['code'] = 1002;
            $rs['msg'] = '账号长度不能超过40个字符';
            return $rs;
        }
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}        
        
        $data=array(
            'uid'=>$uid,
            'type'=>$type,
            'account_bank'=>$account_bank,
            'account'=>$account,
            'name'=>$name,
            'addtime'=>time(),
        );
        
        $domain = new Domain_User();
        $where=[
            'uid'=>$uid,
            'type'=>$type,
            'account_bank'=>$account_bank,
            'account'=>$account,
        ];
        $isexist=$domain->getUserAccount($where);
        if($isexist){
            $rs['code'] = 1004;
            $rs['msg'] = '账号已存在';
            return $rs;
        }
        
        $result = $domain->setUserAccount($data);

        if(!$result){
            $rs['code'] = 1003;
            $rs['msg'] = '添加失败，请重试';
            return $rs;
        }
        
        $rs['info'][0]=$result;

		return $rs;
	}	


	/**
	 * 删除用户提现账号 
	 * @desc 用于删除用户提现账号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function delUserAccount() {
		$rs = array('code' => 0, 'msg' => '删除成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        
        $id=checkNull($this->id);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}        
        
        $data=array(
            'uid'=>$uid,
            'id'=>$id,
        );
        
        $domain = new Domain_User();
        $result = $domain->delUserAccount($data);

        if(!$result){
            $rs['code'] = 1003;
            $rs['msg'] = '删除失败，请重试';
            return $rs;
        }

		return $rs;
	}	
    

    /**
     * 用户申请店铺余额提现
     * @desc 用于用户申请店铺余额提现
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function setShopCash(){
    	$rs = array('code' => 0, 'msg' => '提现成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);		
        $accountid=checkNull($this->accountid);		
        $money=checkNull($this->money);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        if($uid<0||$token==""||!$time||!$sign){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		if(!$accountid){
            $rs['code'] = 1001;
			$rs['msg'] = '请选择提现账号';
			return $rs;
        }

        if(!$money){
            $rs['code'] = 1002;
			$rs['msg'] = '请输入有效的提现金额';
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
            'accountid'=>$accountid,
            'time'=>$time
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $configpri=getConfigPri();

        $data=array(
            'uid'=>$uid,
            'accountid'=>$accountid,
            'money'=>$money,
        );

        $domain=new Domain_User();
        $res = $domain->setShopCash($data);

        if($res==1001){
			$rs['code'] = 1001;
			$rs['msg'] = '余额不足';
			return $rs;
		}else if($res==1004){
			$rs['code'] = 1004;
			$rs['msg'] = '提现最低额度为'.$configpri['balance_cash_min'].'元';
			return $rs;
		}else if($res==1005){
			$rs['code'] = 1005;
			$rs['msg'] = '不在提现期限内，不能提现';
			return $rs;
		}else if($res==1006){
			$rs['code'] = 1006;
			$rs['msg'] = '每月只可提现'.$configpri['balance_cash_max_times'].'次,已达上限';
			return $rs;
		}else if($res==1007){
			$rs['code'] = 1007;
			$rs['msg'] = '提现账号信息不正确';
			return $rs;
		}else if(!$res){
			$rs['code'] = 1002;
			$rs['msg'] = '提现失败，请重试';
			return $rs;
		}
	 
		$rs['info'][0]['msg']='提现成功';
		return $rs;

    }

    /**
     * 获取用户的认证信息
     * @desc 用于获取用户的认证信息
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function getAuthInfo(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);

        $checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$isauth=isAuth($uid);
		if(!$isauth){
			$rs['code']=1001;
			$rs['msg']='请先进行实名认证';
			return $rs;
		}

		$domain=new Domain_User();
		$res=$domain->getAuthInfo($uid);

		$rs['info'][0]=$res;
		return $rs;

    }
    

    /**
     * 查看每日任务
     * @desc 用于用户查看每日任务的进度
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function seeDailyTasks(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $liveuid=checkNull($this->liveuid);
        $islive=checkNull($this->islive);

        $checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$configpri=getConfigPri();
		$dailytask_switch=$configpri['dailytask_switch'];
		
		if($islive==1){   //判断请求是否在直播间
			if($uid==$liveuid){ //主播访问
				//主播直播计时---每日任务--取出用户时间
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

				if($dailytask_switch){
					//主播直播计时---用于每日任务--记录主播请求时间
					$enterRoom_time=time();
					setcaches($key,$enterRoom_time);
				}
				
				
			}else{  //用户访问
			
				//观看直播计时---每日任务--取出用户进入时间
				$key='watch_live_daily_tasks_'.$uid;
				$starttime=getcaches($key);
				if($starttime){ 
					$endtime=time();  //当前时间
					$data=[
						'type'=>'1',
						'starttime'=>$starttime,
						'endtime'=>$endtime,
					];
					dailyTasks($uid,$data);
					//删除当前存入的时间
					delcache($key);
				}
				
				if($dailytask_switch){
					//观看直播计时---用于每日任务--记录用户进入时间
					$enterRoom_time=time();
					setcaches($key,$enterRoom_time);
				}

			}
		}
		
		$domain=new Domain_User();
		$info=$domain->seeDailyTasks($uid);

		$configpub=getConfigPub();
		$name_coin=$configpub['name_coin']; //钻石名称

		$rs['info'][0]['tip_m']="温馨提示：当您某个任务达成时就会获得平台奖励给您的{$name_coin}，获得的奖励需要您手动领取才可放入余额中，当日不领取次日系统会自动清零，亲爱的您一定要记得领取当日奖励哦~";
		$rs['info'][0]['list']=$info;
		return $rs;

    }
	
	
	/**
     * 领取每日任务奖励
     * @desc 用于用户领取每日任务奖励
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function receiveTaskReward(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $taskid=checkNull($this->taskid);

        $checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		$domain=new Domain_User();
		$info=$domain->receiveTaskReward($uid,$taskid);

		
		return $info;

    }


    /**
     * 获取七牛上传Token
     * @desc 用于获取七牛上传Token
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function getQiniuToken(){
	   	$rs = array('code' => 0, 'msg' => '', 'info' =>array());

	   	//获取后台配置的腾讯云存储信息
		$Qiniu=DI()->config->get('app.Qiniu');

		
		require_once API_ROOT.'/../sdk/qiniu/autoload.php';
		
		// 需要填写你的 Access Key 和 Secret Key
		// 需要填写你的 Access Key 和 Secret Key
		$accessKey =$Qiniu['accessKey'];// $configpri['qiniu_accesskey'];
		
		$secretKey = $Qiniu['secretKey'];//$configpri['qiniu_secretkey'];
		$bucket =$Qiniu['space_bucket'];// $configpri['qiniu_bucket'];
		$qiniu_domain_url = $Qiniu['space_host'];
		// 构建鉴权对象
		$auth = new Qiniu\Auth($accessKey, $secretKey);
		// 生成上传 Token
		$token = $auth->uploadToken($bucket);
		$rs['info'][0]['token']=$token ; 
		return $rs; 
		
	}

	/**
     * 用户设置美颜参数
     * @desc 用于用户设置美颜参数
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function setBeautyParams(){
    	$rs = array('code' => 0, 'msg' => '设置成功', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $params=$this->params;

        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}else if($checkToken==10020){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}


		$domain=new Domain_User();
		$res=$domain->setBeautyParams($uid,$params);
		if(!$res){
			$rs['code'] = 1001;
			$rs['msg'] = '设置失败';
			return $rs;
		}

		return $rs;
    }

    /**
     * 获取用户设置的美颜参数
     * @desc 用于获取用户设置的美颜参数
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function getBeautyParams(){

    	$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}else if($checkToken==10020){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}

		$domain=new Domain_User();
		$res=$domain->getBeautyParams($uid);
		$rs['info'][0]=$res;

		return $rs;
    }

    /**
     * 用于APP端调用Braintree支付时的token验证
     * @desc 用于APP端调用Braintree支付时的token验证
     * @return int code 状态码,0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function getBraintreeToken(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());
    	$uid=checkNull($this->uid);
        $token=checkNull($this->token);

        $checkToken = checkToken($uid,$token);

		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			return $rs;
		}else if($checkToken==10020){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}

		$getway_back=$this->getBrainTreeGateway();
		

        if($getway_back['code']!=0){
        	return $getway_back;
        }else{
        	$gateway=$getway_back['info'];
        }

		$clientToken = $gateway->clientToken()->generate();

		$rs['info'][0]['braintreeToken']=$clientToken;
		return $rs;
    }

    /**
     * BrainTree支付回调
     * @desc BrainTree支付回调
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function BraintreeCallback(){
    	$rs = array('code' => 0, 'msg' => '回调成功', 'info' => array());

    	$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$orderno=checkNull($this->orderno);
		$ordertype=checkNull($this->ordertype);
		$nonce=checkNull($this->nonce);
		$money=checkNull($this->money);
		$time=checkNull($this->time);
		$sign=checkNull($this->sign);


		//file_put_contents('./111111.txt',date('y-m-d H:i:s').' 提交参数信息:'.json_encode($nonce)."\r\n",FILE_APPEND);

		if(!in_array($ordertype, ['coin_charge','order_pay','paidprogram_pay'])){
			$rs['code'] = 1001;
			$rs['msg'] = '参数错误';
			return $rs;
		}

		if(!$nonce){
			$rs['code'] = 1002;
			$rs['msg'] = '三方订单编号错误';
			return $rs;
		}

		if(!$money){
			$rs['code'] = 1002;
			$rs['msg'] = '金额错误';
			return $rs;
		}

		$checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
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
            'ordertype'=>$ordertype,
            'orderno'=>$orderno,
            'time'=>$time,
			'nonce'=>$nonce
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1002;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $getway_back=$this->getBrainTreeGateway();

        if($getway_back['code']!=0){
        	return $getway_back;
        }else{
        	$gateway=$getway_back['info'];
        }

		$result = $gateway->transaction()->sale([
		    'amount' => $money,
		    'paymentMethodNonce' => $nonce,
		    'options' => [ 'submitForSettlement' => true ]
		]);

		if($result->success){

			$domain=new Domain_User();
	        $res=$domain->BraintreeCallback($uid,$orderno,$ordertype,$nonce,$money);
	        if($res==1001){
	        	$rs['code']=1001;
	            $rs['msg']='订单不存在';
	            return $rs; 
	        }

	        if($res==1002){
	        	$rs['code']=1002;
	            $rs['msg']='订单已支付';
	            return $rs; 
	        }

	        return $rs;

		}else{

			$rs['code']=1002;
            $rs['msg']='订单回调验证失败';
            return $rs;

		}

    }

    /**
     * 获取BrainTreeGateway
     */
    private function getBrainTreeGateway(){

    	$rs = array('code' => 0, 'msg' => '', 'info' => array());

    	$configpri=getConfigPri();

		$environment=$configpri['braintree_paypal_environment'];

		$merchantId='';
		$publicKey='';
		$privateKey='';

		if($environment==0){ //沙盒
			$merchantId=$configpri['braintree_merchantid_sandbox'];
			$publicKey=$configpri['braintree_publickey_sandbox'];
			$privateKey=$configpri['braintree_privatekey_sandbox'];
			$environment='sandbox';
			
		}else{ //生产

			$merchantId=$configpri['braintree_merchantid_product'];
			$publicKey=$configpri['braintree_publickey_product'];
			$privateKey=$configpri['braintree_privatekey_product'];
			$environment='production';
			
		}

		if(!$merchantId || !$publicKey ||!$privateKey){
			$rs['code']=1001;
			$rs['msg']='BraintreePaypal未配置';
			return $rs;
		}

		$gateway = new Braintree\Gateway([
			'environment' => $environment,
			'merchantId' => $merchantId,
			'publicKey' => $publicKey,
			'privateKey' => $privateKey
		]);

		$rs['info']=$gateway;
		return $rs;
    }

    /**
     * 获取转盘中奖记录
     * @desc 获取转盘中奖记录
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function getTurntableWinLists(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());

    	$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$p=checkNull($this->p);

		$checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		if($p<1){
			$p=1;
		}

		$domain=new Domain_User();
		$res=$domain->getTurntableWinLists($uid,$p);
		$rs['info']=$res;

		return $rs;
    }

    /**
     * 清除转盘中奖记录
     * @desc 清除转盘中奖记录
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function clearTurntableWinLists(){
		$rs = array('code' => 0, 'msg' => '清空成功', 'info' => array());

    	$uid=checkNull($this->uid);
		$token=checkNull($this->token);

		$checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$domain=new Domain_User();
		$res=$domain->clearTurntableWinLists($uid);

		return $rs;
    }

    /**
     * 检查用户是否开启了青少年模式
     * @desc 检查用户是否开启了青少年模式
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     * @return array info[0].is_setpassword 是否设置过密码 0 否 1 是
     * @return array info[0].status 是否开启青少年模式 0 否 1 是
     * @return array info[0].is_tip 是否提示用户弹窗显示青少年模式下不能继续使用app   0 否  1 是
     * @return array info[0].tips  弹窗显示青少年模式下不能继续使用app的提示语
     * @return array info[0].teenager_des  青少年模式提示语
     */
    public function checkTeenager(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());

    	$uid=checkNull($this->uid);
		$token=checkNull($this->token);

		$checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$domain = new Domain_User();
		$res = $domain->checkTeenager($uid);

		$configpub=getConfigPub();

		$res['info'][0]['is_tip']='0';
		$res['info'][0]['tips']='';
		$res['info'][0]['teenager_des']=$configpub['teenager_des'];

		//开启了青少年模式
		if($res['info'][0]['is_setpassword'] && $res['info'][0]['status']){
			$overtime = $domain->checkTeenagerIsOvertime($uid);

			if($overtime['code']!=0){
				$res['info'][0]['is_tip']='1';
				$res['info'][0]['tips']=$overtime['msg'];
			}
		}

		return $res;
    }

    /**
     * 用户开启青少年模式/初次设置密码后重新设置密码
     * @desc 用户开启青少年模式/初次设置密码后重新设置密码
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function setTeenagerPassword(){
    	$rs = array('code' => 0, 'msg' => '青少年模式开启成功', 'info' => array());

    	$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$password=checkNull($this->password);
		$type=checkNull($this->type);

		$checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		if(mb_strlen($password)!=4){
			$rs['code'] = 1001;
			$rs['msg'] ='密码必须为4位';
			return $rs;
		}

		$domain=new Domain_User();
		$res=$domain->setTeenagerPassword($uid,$password,$type);

		if($res==1001){
			$rs['code'] = 1002;
			$rs['msg'] ='密码错误';
			return $rs;
		}

		if($res==1002){
			$rs['code'] = 1003;
			$rs['msg'] ='密码设置失败,请稍后重试';
			return $rs;
		}

		return $rs;
    }

    /**
     * 用户修改青少年模式密码
     * @desc 用户修改青少年模式密码
     * @return int code 状态码,0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function updateTeenagerPassword(){
    	$rs = array('code' => 0, 'msg' => '密码修改成功', 'info' => array());

    	$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$oldpassword=checkNull($this->oldpassword);
		$password=checkNull($this->password);

		if(!$oldpassword){
			$rs['code']=1001;
			$rs['msg']='请输入原密码';
			return $rs;
		}

		if(mb_strlen($oldpassword)!=4){
			$rs['code']=1002;
			$rs['msg']='原密码长度必须为4位';
			return $rs;
		}

		if(!$password){
			$rs['code']=1001;
			$rs['msg']='请输入新密码';
			return $rs;
		}

		if(mb_strlen($password)!=4){
			$rs['code']=1002;
			$rs['msg']='新密码长度必须为4位';
			return $rs;
		}

		$checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$domain = new Domain_User();
		$res=$domain->updateTeenagerPassword($uid,$oldpassword,$password);

		if($res==1001){
			$rs['code']=1003;
			$rs['msg']='你还未设置密码';
			return $rs;
		}

		if($res==1002){
			$rs['code']=1004;
			$rs['msg']='原密码错误';
			return $rs;
		}

		if(!$res){
			$rs['code']=1005;
			$rs['msg']='密码修改失败';
			return $rs;
		}

		return $rs;
    }

    /**
     * 用户关闭青少年模式
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function closeTeenager(){

    	$rs = array('code' => 0, 'msg' => '青少年模式关闭成功', 'info' => array());

    	$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$password=checkNull($this->password);

		if(!$password){
			$rs['code']=1001;
			$rs['msg']='请输入密码';
			return $rs;
		}

		if(mb_strlen($password)!=4){
			$rs['code']=1002;
			$rs['msg']='密码长度必须为4位';
			return $rs;
		}

		$checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$domain = new Domain_User();
		$res=$domain->closeTeenager($uid,$password);
		if($res==1001){
			$rs['code'] = 1003;
			$rs['msg'] = '你还未开启青少年模式';
			return $rs;
		}

		if($res==1002){
			$rs['code'] = 1004;
			$rs['msg'] = '青少年模式未开启';
			return $rs;
		}

		if($res==1003){
			$rs['code'] = 1005;
			$rs['msg'] = '密码错误';
			return $rs;
		}

		if(!$res){
			$rs['code']=1006;
			$rs['msg']='青少年模式关闭失败';
			return $rs;
		}

		return $rs;
    }

    /**
     * 定时增加用户使用青少年模式时间
     * @desc 定时增加用户使用青少年模式时间
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function addTeenagerTime(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());

    	$uid=checkNull($this->uid);
		$token=checkNull($this->token);

		$checkToken=checkToken($uid,$token);

		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$domain = new Domain_User();
		$res=$domain->addTeenagerTime($uid);

		return $res;
    }


    /**
	 * 更换个人中心背景图
	 * @desc 更换个人中心背景图
	 * @retun int code 状态码,0表示成功
	 * @retun string msg 返回信息
	 * @retun array info 返回信息
	 * @retun array info[0]['bg_img'] 返回上传的背景图
	 * */
	public function updateBgImg(){
		$rs = array('code' => 0, 'msg' => '背景图更换成功', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$img=checkNull($this->img);

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		if(!$img){
			$rs['code']=1001;
			$rs['msg']='请上传背景图';
			return $rs;
		}

		$domain=new Domain_User();
		$res=$domain->updateBgImg($uid,$img);

		if($res==1001){
			$rs['code']=1002;
			$rs['msg']='背景图更换失败';
			return $rs;
		}

		$userinfo=getUserInfo($uid);
		$rs['info'][0]['bg_img']=$userinfo['bg_img'];
		return $rs;
	}
    
}
