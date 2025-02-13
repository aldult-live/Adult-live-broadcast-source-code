<?php

class Domain_Family {

    public function createFamily($info) {
        $rs = array();
        $model = new Model_Family();
        $rs = $model->createFamily($info);
        return $rs;
    }
		
}
