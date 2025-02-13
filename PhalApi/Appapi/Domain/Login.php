<?php

class Domain_Login {

    public function userLogin($country_code,$user_login,$user_pass) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->userLogin($country_code,$user_login,$user_pass);

        return $rs;
    }

    public function userReg($country_code,$user_login,$user_pass,$source) {
        $rs = array();
        $model = new Model_Login();
        $rs = $model->userReg($country_code,$user_login,$user_pass,$source);

        return $rs;
    }	
	
    public function userFindPass($country_code,$user_login,$user_pass) {
        $rs = array();
        $model = new Model_Login();
        $rs = $model->userFindPass($country_code,$user_login,$user_pass);

        return $rs;
    }	

    public function userLoginByThird($openid,$type,$nickname,$avatar,$source) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->userLoginByThird($openid,$type,$nickname,$avatar,$source);

        return $rs;
    }

	public function getUserban($user_login) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->getUserban($user_login);

        return $rs;
    }
	public function getThirdUserban($openid,$type) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->getThirdUserban($openid,$type);

        return $rs;
    }

    public function getCancelCondition($uid){
        $rs = array();

        $model = new Model_Login();
        $rs = $model->getCancelCondition($uid);

        return $rs;
    }

    public function cancelAccount($uid){
        $rs = array();

        $model = new Model_Login();
        $rs = $model->cancelAccount($uid);

        return $rs;
    }

}
