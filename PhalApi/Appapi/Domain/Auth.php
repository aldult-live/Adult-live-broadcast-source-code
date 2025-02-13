<?php

class Domain_Auth {
	public function getAuth($uid) {
		$rs = array();

		$model = new Model_Auth();
		$rs = $model->getAuth($uid);

		return $rs;
	}
	
	
	public function setAuth($data) {
		$rs = array();

		$model = new Model_Auth();
		$rs = $model->setAuth($data);

		return $rs;
	}


	
}
