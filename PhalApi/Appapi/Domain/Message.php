<?php

class Domain_Message {
	public function getList($uid,$p) {
		$rs = array();

		$model = new Model_Message();
		$rs = $model->getList($uid,$p);

		return $rs;
	}

	public function getShopOrderList($uid,$p){
		$rs = array();

		$model = new Model_Message();
		$rs = $model->getShopOrderList($uid,$p);

		return $rs;
	}

	public function fansLists($uid,$p){
        $rs = array();

        $model = new Model_Message();
        $rs = $model->fansLists($uid,$p);

        return $rs;
    }

    public function praiseLists($uid,$p){
        $rs = array();

        $model = new Model_Message();
        $rs = $model->praiseLists($uid,$p);

        return $rs;
    }

    public function atLists($uid,$p){
        $rs = array();

        $model = new Model_Message();
        $rs = $model->atLists($uid,$p);

        return $rs;
    }

    public function commentLists($uid,$p){
        $rs = array();

        $model = new Model_Message();
        $rs = $model->commentLists($uid,$p);

        return $rs;
    }
	
}
