<?php

class Domain_Charge {
	public function getOrderId($changeid,$orderinfo) {
		$rs = array();

		$model = new Model_Charge();
		$rs = $model->getOrderId($changeid,$orderinfo);

		return $rs;
	}

	public function getFirstChargeRules(){
		$rs = array();

		$model = new Model_Charge();
		$rs = $model->getFirstChargeRules();

		return $rs;
	}
	
}
