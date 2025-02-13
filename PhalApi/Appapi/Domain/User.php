<?php

class Domain_User {

	public function getBaseInfo($userId) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getBaseInfo($userId);

			return $rs;
	}
	
	public function checkName($uid,$name) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->checkName($uid,$name);

			return $rs;
	}
	
	public function userUpdate($uid,$fields) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->userUpdate($uid,$fields);

			return $rs;
	}
	
	public function updatePass($uid,$oldpass,$pass) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->updatePass($uid,$oldpass,$pass);

			return $rs;
	}

	public function getBalance($uid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getBalance($uid);

			return $rs;
	}
	
	public function getChargeRules() {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getChargeRules();

			return $rs;
	}
	
	public function getProfit($uid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getProfit($uid);

			return $rs;
	}

	public function setCash($data) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->setCash($data);

			return $rs;
	}
	
	public function setAttent($uid,$touid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->setAttent($uid,$touid);

			return $rs;
	}
	
	public function setBlack($uid,$touid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->setBlack($uid,$touid);

			return $rs;
	}
	
	public function getFollowsList($uid,$touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getFollowsList($uid,$touid,$p);

			return $rs;
	}
	
	public function getFansList($uid,$touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getFansList($uid,$touid,$p);

			return $rs;
	}

	public function getBlackList($uid,$touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getBlackList($uid,$touid,$p);

			return $rs;
	}

	public function getLiverecord($touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getLiverecord($touid,$p);

			return $rs;
	}
	
	public function getUserHome($uid,$touid) {
		$rs = array();

		$model = new Model_User();
		$rs = $model->getUserHome($uid,$touid);
		return $rs;
	}	
	
	public function getContributeList($touid,$p) {
		$rs = array();

		$model = new Model_User();
		$rs = $model->getContributeList($touid,$p);
		return $rs;
	}	
	
	public function setDistribut($uid,$code) {
		$rs = array();

		$model = new Model_User();
		$rs = $model->setDistribut($uid,$code);
		return $rs;
	}

	public function getImpressionLabel() {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->getImpressionLabel();

        return $rs;
    }	

	public function getUserLabel($uid,$touid) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->getUserLabel($uid,$touid);

        return $rs;
    }	

	public function setUserLabel($uid,$touid,$labels) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->setUserLabel($uid,$touid,$labels);

        return $rs;
    }	

	public function getMyLabel($uid) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->getMyLabel($uid);

        return $rs;
    }	

	public function getPerSetting() {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->getPerSetting();

        return $rs;
    }	

	public function getUserAccountList($uid) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->getUserAccountList($uid);

        return $rs;
    }	

	public function getUserAccount($where) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->getUserAccount($where);

        return $rs;
    }	

	public function setUserAccount($data) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->setUserAccount($data);

        return $rs;
    }

	public function delUserAccount($data) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->delUserAccount($data);

        return $rs;
    }	
	
	public function LoginBonus($uid){
		$rs = array();
		$model = new Model_User();
		$rs = $model->LoginBonus($uid);
		return $rs;

	}

	public function getLoginBonus($uid){
		$rs = array();
		$model = new Model_User();
		$rs = $model->getLoginBonus($uid);
		return $rs;

	}

	public function checkIsAgent($uid){
		$rs = array();
		$model = new Model_User();
		$rs = $model->checkIsAgent($uid);
		return $rs;
	}
    
    //用户申请店铺余额提现
	public function setShopCash($data){
		$rs = array();

		$model = new Model_User();
		$rs = $model->setShopCash($data);

		return $rs;
	}

	public function getAuthInfo($uid){
		$rs = array();

		$model = new Model_User();
		$rs = $model->getAuthInfo($uid);

		return $rs;
	}

	public function seeDailyTasks($uid){
		$rs = array();

		$model = new Model_User();
		$rs = $model->seeDailyTasks($uid);

		return $rs;
	}

	public function receiveTaskReward($uid,$taskid){
		$rs = array();

		$model = new Model_User();
		$rs = $model->receiveTaskReward($uid,$taskid);

		return $rs;
	}

	public function setBeautyParams($uid,$params){
		$rs = array();

		$model = new Model_User();
		$rs = $model->setBeautyParams($uid,$params);

		return $rs;
	}

	public function getBeautyParams($uid){
		$rs = array();

		$model = new Model_User();
		$rs = $model->getBeautyParams($uid);

		return $rs;
	}

	public function BraintreeCallback($uid,$orderno,$ordertype,$nonce,$money){
		$rs = array();

		$model = new Model_User();
		$rs = $model->BraintreeCallback($uid,$orderno,$ordertype,$nonce,$money);

		return $rs;
	}

	public function getTurntableWinLists($uid,$p){
		$rs = array();

		$model = new Model_User();
		$rs = $model->getTurntableWinLists($uid,$p);

		return $rs;
	}

	public function clearTurntableWinLists($uid){
		$rs = array();

		$model = new Model_User();
		$rs = $model->clearTurntableWinLists($uid);

		return $rs;
	}

	public function checkTeenager($uid){
		$rs = array();

		$model = new Model_User();
		$rs = $model->checkTeenager($uid);

		return $rs;
	}

	public function setTeenagerPassword($uid,$password,$type){
		$rs = array();

		$model = new Model_User();
		$rs = $model->setTeenagerPassword($uid,$password,$type);

		return $rs;
	}

	public function updateTeenagerPassword($uid,$oldpassword,$password){
		$rs = array();

		$model = new Model_User();
		$rs = $model->updateTeenagerPassword($uid,$oldpassword,$password);

		return $rs;
	}

	public function closeTeenager($uid,$password){
		$rs = array();

		$model = new Model_User();
		$rs = $model->closeTeenager($uid,$password);

		return $rs;
	}

	public function addTeenagerTime($uid){
		$rs = array();

		$model = new Model_User();
		$rs = $model->addTeenagerTime($uid);

		return $rs;
	}

	public function updateBgImg($uid,$img){
		$rs = array();
		$model = new Model_User();
		$rs = $model->updateBgImg($uid,$img);
		return $rs;
	}

	public function checkTeenagerIsOvertime($uid){
		$rs = array();
		$model = new Model_User();
		$rs = $model->checkTeenagerIsOvertime($uid);
		return $rs;
	}

}
