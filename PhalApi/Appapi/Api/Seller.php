<?php
/**
 * 卖家
 */
class Api_Seller extends PhalApi_Api {

	public function getRules() {
		return array(
            'getHome' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
				'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
			),

			'getGoodsClass'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
			),

			'setGoods' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
                'one_classid' => array('name' => 'one_classid', 'type' => 'int', 'desc' => '商品一级分类id'),
                'two_classid' => array('name' => 'two_classid', 'type' => 'int', 'desc' => '商品二级分类id'),
                'three_classid' => array('name' => 'three_classid', 'type' => 'int', 'desc' => '商品三级分类id'),
                'name' => array('name' => 'name', 'type' => 'string', 'desc' => '商品名'),
                'video_url' => array('name' => 'video_url', 'type' => 'string', 'desc' => '商品视频链接地址'),
                'video_thumb' => array('name' => 'video_thumb', 'type' => 'string', 'desc' => '商品视频封面地址'),
                'thumbs' => array('name' => 'thumbs', 'type' => 'string', 'desc' => '商品图片集合'),
                'content' => array('name' => 'content', 'type' => 'string', 'desc' => '商品文字详情'),
                'pictures' => array('name' => 'pictures', 'type' => 'string', 'desc' => '商品内容的图片集'),
                'specs' => array('name' => 'specs', 'type' => 'string', 'desc' => '商品规格json集'),
                'postage' => array('name' => 'postage', 'type' => 'string', 'desc' => '邮费'),
                'share_income' => array('name' => 'share_income', 'type' => 'string','default'=>'0', 'desc' => '分享佣金'),
			),

			'getGoodsNums'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
			),

			'getGoodsList'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
                'type' => array('name' => 'type', 'type' => 'string', 'desc' => '商品状态 onsale 在售 onexamine 审核中 remove_shelves 下架'),
                'p' => array('name' => 'p', 'type' => 'string', 'desc' => '分页数'),
			),

			'getReceiverAddress'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
			),

			'upReceiverAddress'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
                'receiver' => array('name' => 'receiver', 'type' => 'string', 'desc' => '用户姓名'),
                'receiver_phone' => array('name' => 'receiver_phone', 'type' => 'string', 'desc' => '用户姓名'),
                'receiver_province' => array('name' => 'receiver_province', 'type' => 'string', 'desc' => '省份'),
                'receiver_city' => array('name' => 'receiver_city', 'type' => 'string', 'desc' => '市'),
                'receiver_area' => array('name' => 'receiver_area', 'type' => 'string', 'desc' => '区'),
                'receiver_address' => array('name' => 'receiver_address', 'type' => 'string', 'desc' => '详细地址'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
			),

			'upGoodsSpecs'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
                'goodsid' => array('name' => 'goodsid', 'type' => 'int', 'desc' => '商品ID'),
                'specs' => array('name' => 'specs', 'type' => 'string', 'desc' => '商品规格json集'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
			),

			'upGoods'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
                'goodsid' => array('name' => 'goodsid', 'type' => 'string', 'desc' => '商品id'),
                'one_classid' => array('name' => 'one_classid', 'type' => 'int', 'desc' => '商品一级分类id'),
                'two_classid' => array('name' => 'two_classid', 'type' => 'int', 'desc' => '商品二级分类id'),
                'three_classid' => array('name' => 'three_classid', 'type' => 'int', 'desc' => '商品三级分类id'),
                'name' => array('name' => 'name', 'type' => 'string', 'desc' => '商品名'),
                'video_url' => array('name' => 'video_url', 'type' => 'string', 'desc' => '商品视频链接地址'),
                'video_thumb' => array('name' => 'video_thumb', 'type' => 'string', 'desc' => '商品视频封面地址'),
                'thumbs' => array('name' => 'thumbs', 'type' => 'string', 'desc' => '商品图片集合'),
                'content' => array('name' => 'content', 'type' => 'string', 'desc' => '商品文字详情'),
                'pictures' => array('name' => 'pictures', 'type' => 'string', 'desc' => '商品内容的图片集'),
                'specs' => array('name' => 'specs', 'type' => 'string', 'desc' => '商品规格json集'),
                'postage' => array('name' => 'postage', 'type' => 'string', 'desc' => '邮费'),
                'share_income' => array('name' => 'share_income', 'type' => 'string','default'=>'0', 'desc' => '分享佣金'),
			),

			'delGoods' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
                'goodsid' => array('name' => 'goodsid', 'type' => 'int', 'desc' => '商品ID'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
			),

			'upStatus' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
                'goodsid' => array('name' => 'goodsid', 'type' => 'int', 'desc' => '商品ID'),
                'status' => array('name' => 'status', 'type' => 'int', 'desc' => '状态，-1下架1上架'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
			),

			'getExpressList'=>array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'desc' => '用户token'),
			),

			'getGoodsOrderList'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'type' => array('name' => 'type', 'type' => 'string',  'desc' => '订单类型 all 所有订单 wait_payment 待付款 wait_shipment 待发货 wait_refund 待退款 all_refund 全部退款  wait_receive 已发货,待收货 wait_evaluate 已签收,待评价 closed 已关闭 finished 已完成'),
                'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页数'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
            ),

            'setExpressInfo'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'orderid' => array('name' => 'orderid', 'type' => 'int',  'desc' => '订单ID'),
                'expressid' => array('name' => 'expressid', 'type' => 'int',  'desc' => '物流公司ID'),
                'express_number' => array('name' => 'express_number', 'type' => 'string',  'desc' => '物流单号'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
            ),

            'delGoodsOrder'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'orderid' => array('name' => 'orderid', 'type' => 'int',  'desc' => '订单id'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
            ),

            'getGoodsOrderInfo'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'orderid' => array('name' => 'orderid', 'type' => 'int',  'desc' => '订单id'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
            ),

            'getGoodsOrderRefundInfo'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'orderid' => array('name' => 'orderid', 'type' => 'int',  'desc' => '订单id'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
            ),

            'getRefundRefuseReason'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
            ),

            'setGoodsOrderRefund'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'orderid' => array('name' => 'orderid', 'type' => 'int',  'desc' => '订单id'),
                'type' => array('name' => 'type', 'type' => 'int',  'desc' => '处理类型 0 拒绝 1 同意'),
                'reasonid' => array('name' => 'reasonid', 'type' => 'int',  'desc' => '拒绝原因ID'),
                'refuse_desc' => array('name' => 'refuse_desc', 'type' => 'string',  'desc' => '拒绝理由描述'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
            ),

            'getSettlementList'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
                'p' => array('name' => 'p', 'type' => 'int', 'desc' => '分页数'),
            ),

            'setOutsideGoods'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'href' => array('name' => 'href', 'type' => 'string',  'desc' => '外链商品链接地址'),
                'name' => array('name' => 'name', 'type' => 'string',  'desc' => '外链商品名称'),
                'original_price' => array('name' => 'original_price', 'type' => 'string',  'desc' => '外链商品原价'),
                'present_price' => array('name' => 'present_price', 'type' => 'string',  'desc' => '外链商品现价'),
                'goods_desc' => array('name' => 'goods_desc', 'type' => 'string',  'desc' => '外链商品简介'),
                'thumb' => array('name' => 'thumb', 'type' => 'string',  'desc' => '外链商品封面'),

            ),

            'upOutsideGoods'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'goodsid' => array('name' => 'goodsid', 'type' => 'string', 'desc' => '商品id'),
                'href' => array('name' => 'href', 'type' => 'string',  'desc' => '外链商品链接地址'),
                'name' => array('name' => 'name', 'type' => 'string',  'desc' => '外链商品名称'),
                'original_price' => array('name' => 'original_price', 'type' => 'string',  'desc' => '外链商品原价'),
                'present_price' => array('name' => 'present_price', 'type' => 'string',  'desc' => '外链商品现价'),
                'goods_desc' => array('name' => 'goods_desc', 'type' => 'string',  'desc' => '外链商品简介'),
                'thumb' => array('name' => 'thumb', 'type' => 'string',  'desc' => '外链商品封面'),
            ),

            'getPlatformGoodsLists'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
                'p' => array('name' => 'p', 'type' => 'int', 'desc' => '分页数'),
            ),

            'setPlatformGoods'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'goodsid' => array('name' => 'goodsid', 'type' => 'int', 'desc' => '商品ID'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
            ),

            'getOnsalePlatformGoods'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1,  'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户token'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名字符串'),
                'p' => array('name' => 'p', 'type' => 'int', 'desc' => '分页数'),
            ),

            

		);
	}
	

	/**
	 * 卖家个人中心信息
	 * @desc 用于 获取卖家个人中心信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].shopinfo 店铺信息
	 * @return string info[0].href 二维码链接
	 * @return string info[0].qr 二维码图片链接
	 * @return string msg 提示信息
	 */
	public function getHome() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        if($uid<0 || $token=='' || !$time || !$sign){
            $rs['code'] = 1000;
			$rs['msg'] = '信息错误';
			return $rs;
        }
        
        $checkToken = checkToken($uid,$token);
		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
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
            'time'=>$time,
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}
		
		$domain = new Domain_Seller();
		$res = $domain->getHome($uid);
        
		return $res;			
	}


	/**
	 * 卖家获取经营类目
	 * @desc 用于 卖家获取经营类目
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return int info[0].gc_id 一级商品分类ID
	 * @return string info[0].gc_name 一级商品分类名称
	 * @return array info[0].two_list 二级分类数组
	 * @return int info[0].two_list[0].gc_id 二级分类ID
	 * @return string info[0].two_list[0].gc_name 二级分类名称
	 * @return array info[0].two_list[0].three_list 三级分类数组
	 * @return int info[0].two_list[0].three_list[0].gc_id 三级分类ID
	 * @return string info[0].two_list[0].three_list[0].gc_name 三级分类名称
	 * @return string msg 提示信息
	 */
	public function getGoodsClass(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        
        $checkToken = checkToken($uid,$token);
		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		//判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

		$domain=new Domain_Seller();
		$res=$domain->getGoodsClass($uid);

		$rs['info']=$res;

		return $rs;

	}

	/**
	 * 卖家发布商品
	 * @desc 用于 卖家发布商品
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function setGoods(){

		$rs = array('code' => 0, 'msg' => '商品发布成功', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $one_classid=checkNull($this->one_classid);
        $two_classid=checkNull($this->two_classid);
        $three_classid=checkNull($this->three_classid);
        $name=checkNull($this->name);
        $video_url=checkNull($this->video_url);
        $video_thumb=checkNull($this->video_thumb);
        $thumbs=checkNull($this->thumbs);
        $content=checkNull($this->content);
        $pictures=checkNull($this->pictures);
        $specs=$this->specs;
        $postage=checkNull($this->postage);
        $share_income=checkNull($this->share_income);
        
        $checkToken = checkToken($uid,$token);
		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		//判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

		if(!$one_classid){
			$rs['code']=1001;
			$rs['msg']='请选择商品一级分类';
			return $rs;
		}

		if(!$two_classid){
			$rs['code']=1001;
			$rs['msg']='请选择商品二级分类';
			return $rs;
		}

		if(!$three_classid){
			$rs['code']=1001;
			$rs['msg']='请选择商品三级分类';
			return $rs;
		}

		if(!$name){
			$rs['code']=1001;
			$rs['msg']='请填写商品名称';
			return $rs;
		}

		if(mb_strlen($name)>15){
			$rs['code']=1001;
			$rs['msg']='商品名称不能超过15字';
			return $rs;
		}

		if(!$thumbs){
			$rs['code']=1001;
			$rs['msg']='请上传商品缩略图';
			return $rs;
		}

		if(!$content){
			$rs['code']=1001;
			$rs['msg']='请添加商品详情';
			return $rs;
		}

		if(mb_strlen($content)>300){
			$rs['code']=1001;
			$rs['msg']='商品详情不能超过300字';
			return $rs;
		}

		if(!$specs){
			$rs['code']=1001;
			$rs['msg']='请添加商品规格';
			return $rs;
		}

		$specname_false=0;
		$specname_len_false=0;
		$specnum_false=0;
		$specprice_false=0;
		$specthumb_false=0;
        $share_false=0;

		$specArr=json_decode($specs,true);

		foreach ($specArr as $k => $v) {
			if(!$v['spec_name']){
				$specname_false=1;
				break;
			}

			if(mb_strlen($v['spec_name'])>15){
				$specname_len_false=1;
				break;
			}

			if($v['spec_num']<1){
				$specnum_false=1;
				break;
			}

			if($v['spec_num']>9999999){
				$specnum_false=1;
				break;
			}

			if(floor($v['spec_num'])!=$v['spec_num']){
				$specnum_false=1;
				break;
			}

			if(!$v['price']){
				$specprice_false=1;
				break;
			}

			if($v['price']<1){
				$specprice_false=1;
				break;
			}

			if($v['price']>99999999){
				$specprice_false=1;
				break;
			}

            if($v['price']<$share_income){
                $share_false=1;
                break;
            }

			if(!$v['thumb']){
				$specthumb_false=1;
				break;
			}
		}

		if($specname_false){
			$rs['code']=1001;
			$rs['msg']='请填写商品规格名称';
			return $rs;
		}

		if($specname_len_false){
			$rs['code']=1001;
			$rs['msg']='商品规格名称应在15个字符以内';
			return $rs;
		}

		if($specnum_false){
			$rs['code']=1001;
			$rs['msg']='商品规格库存在1-9999999之间';
			return $rs;
		}

		if($specprice_false){
			$rs['code']=1001;
			$rs['msg']='商品规格单价在1-99999999之间';
			return $rs;
		}

		if($specthumb_false){
			$rs['code']=1001;
			$rs['msg']='请上传商品规格封面图';
			return $rs;
		}

		if($postage<0||$postage>99999){
			$rs['code']=1001;
			$rs['msg']='商品运费在0-99999之间';
			return $rs;
		}

        if($share_income<0||$share_income>99999){
            $rs['code']=1001;
            $rs['msg']='分享佣金在0-99999之间';
            return $rs;
        }

        $num_check=checkNumber($share_income);

        if(!$num_check){
            $rs['code']=1001;
            $rs['msg']='请确认分享佣金';
            return $rs;
        }

        if($share_false){
            $rs['code']=1001;
            $rs['msg']='分享佣金应低于商品价格';
            return $rs;
        }
		
		
		//获取规格最低价格
		$low_price=$specArr[0]['price'];

		$data=array(
			'uid'=>$uid,
			'name'=>$name,
			'one_classid'=>$one_classid,
			'two_classid'=>$two_classid,
			'three_classid'=>$three_classid,
			'video_url'=>$video_url,
			'video_thumb'=>$video_thumb,
			'thumbs'=>$thumbs,
			'content'=>$content,
			'pictures'=>$pictures,
			'specs'=>urldecode($specs),
			'postage'=>$postage,
			'low_price'=>$low_price,
			'addtime'=>time(),
            'share_income'=>$share_income

		);

		$configpri=getConfigPri();
		if(!$configpri['goods_switch']){ //商品审核开关关闭
			$data['status']=1;
		}

		$domain=new Domain_Seller();
		$res=$domain->setGoods($data);

		if($res==1001){
			$rs['code']=1001;
			$rs['msg']='您已经发布过同名商品';
			return $rs;
		}else if(!$res){
			$rs['code']=1001;
			$rs['msg']='商品发布失败';
			return $rs;
		}

		return $rs;

	}

	/**
	 * 卖家商品管理获取不同状态下的商品总数
	 * @desc 用于 卖家获取不同状态下的商品总数
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return array info[0].onsalse 在售总数 
	 * @return array info[0].onexamine 在审核总数 
	 * @return array info[0].remove_shelves 下架总数
     * @return array info[0].platform 代售平台商品总数
	 * @return string msg 提示信息
	 */
	public function getGoodsNums(){

		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        
        $checkToken = checkToken($uid,$token);
		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		//判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}
		
		$domain = new Domain_Seller();
		$res = $domain->getGoodsNums($uid);
		$rs['info'][0]=$res;

		return $rs;
	}

	/**
	 * 卖家获取不同类型下的商品列表
	 * @desc 用于 卖家获取不同类型下的商品列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return array info[0].id 商品id 
	 * @return array info[0].name 商品名称 
	 * @return array info[0].sale_nums 总销量 
	 * @return array info[0].thumb 商品缩略图 
	 * @return array info[0].price 商品售价 
	 * @return string msg 提示信息
	 */
	public function getGoodsList(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $type=checkNull($this->type);
        $p=checkNull($this->p);
        
        $checkToken = checkToken($uid,$token);
		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		//判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}
		
		$domain=new Domain_Seller();
		$res=$domain->getGoodsList($uid,$type,$p);

		$rs['info']=$res;

		return $rs;
	}


	/**
	 * 卖家获取退货地址信息
	 * @desc 用于 卖家获取退货地址信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return array info[0].receiver 收货人
	 * @return array info[0].receiver_phone 收货人电话 
	 * @return array info[0].receiver_province 收货人省份
	 * @return array info[0].receiver_city 收货人市 
	 * @return array info[0].receiver_area 收货人地区 
	 * @return array info[0].receiver_address 收货人详细地址 
	 * @return string msg 提示信息
	 */
	public function getReceiverAddress(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);

        $checkToken = checkToken($uid,$token);
		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		//判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

		$domain=new Domain_Seller();
		$res=$domain->getReceiverAddress($uid);

		if($res==1001){
			$rs['code']=1001;
			$rs['msg']='查询不到退货地址';
			return $rs;
		}

		$rs['info'][0]=$res;
		return $rs;
	}



	/**
	 * 卖家修改退货地址信息
	 * @desc 用于 卖家修改退货地址信息
	 * @return int code 操作码，0表示成功
	 * @return array info  
	 * @return string msg 提示信息
	 */
	public function upReceiverAddress(){
		$rs = array('code' => 0, 'msg' => '退货地址修改成功', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $receiver=checkNull($this->receiver);
        $receiver_phone=checkNull($this->receiver_phone);
        $receiver_province=checkNull($this->receiver_province);
        $receiver_city=checkNull($this->receiver_city);
        $receiver_area=checkNull($this->receiver_area);
        $receiver_address=checkNull($this->receiver_address);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);


        $checkToken = checkToken($uid,$token);
		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		//判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

		if(!$receiver){
        	$rs['code']=1001;
        	$rs['msg']="请填写姓名";
        	return $rs;
        }

        if(mb_strlen($receiver)>20){
        	$rs['code']=1001;
        	$rs['msg']="姓名长度不能超过20个字";
        	return $rs;
        }

        if(!$receiver_phone){
        	$rs['code']=1001;
        	$rs['msg']="请填写手机号";
        	return $rs;
        }

        $checkmobile=checkMobile($receiver_phone);
		if(!$checkmobile){
			$rs['code']=1001;
        	$rs['msg']="手机号码格式错误";
        	return $rs;
		}

		if(!$receiver_province){
        	$rs['code']=1001;
        	$rs['msg']="请选择所在省份";
        	return $rs;
        }

        if(!$receiver_city){
        	$rs['code']=1001;
        	$rs['msg']="请选择所在市";
        	return $rs;
        }

        if(!$receiver_area){
        	$rs['code']=1001;
        	$rs['msg']="请选择所在地区";
        	return $rs;
        }

        if(!$receiver_address){
        	$rs['code']=1001;
        	$rs['msg']="请填写详细地址";
        	return $rs;
        }

		if(!$time||!$sign){
			$rs['code']=1001;
			$rs['msg']='参数错误';
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
            'time'=>$time,
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $data=array(
        	'receiver'=>$receiver,
        	'receiver_phone'=>$receiver_phone,
        	'receiver_province'=>$receiver_province,
        	'receiver_city'=>$receiver_city,
        	'receiver_area'=>$receiver_area,
        	'receiver_address'=>$receiver_address,
        );

        $domain=new Domain_Seller();
        $res=$domain->upReceiverAddress($uid,$data);
        if(!$res){
        	$rs['code']=1002;
            $rs['msg']='修改失败';
            return $rs; 
        }

        return $rs;
	}


	/**
	 * 卖家更新商品规格
	 * @desc 用于 卖家更新商品规格
	 * @return int code 操作码，0表示成功
	 * @return array info  
	 * @return string msg 提示信息
	 */
	public function upGoodsSpecs(){
		$rs = array('code' => 0, 'msg' => '商品规格更新成功', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $goodsid=checkNull($this->goodsid);
        $specs=$this->specs;
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        $checkToken = checkToken($uid,$token);
		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		if(!$goodsid||!$time||!$sign){
			$rs['code']=1001;
			$rs['msg']='参数错误';
			return $rs;
		}


		if(!$specs){
			$rs['code']=1001;
			$rs['msg']='请确认商品规格';
			return $rs;
		}

		$now=time();

		if(($now-$time)>300){
			$rs['code']=1001;
			$rs['msg']='参数错误';
			return $rs;
		}

		$checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time,
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }


        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

        $specname_false=0;
		$specname_len_false=0;
		$specnum_false=0;
		$specprice_false=0;

		$specArr=json_decode($specs,true);

		foreach ($specArr as $k => $v) {
			if(!$v['spec_name']){
				$specname_false=1;
				break;
			}

			if(mb_strlen($v['spec_name'])>15){
				$specname_len_false=1;
				break;
			}

			if($v['spec_num']<1){
				$specnum_false=1;
				break;
			}

			if($v['spec_num']>9999999){
				$specnum_false=1;
				break;
			}

			if(floor($v['spec_num'])!=$v['spec_num']){
				$specnum_false=1;
				break;
			}

			if(!$v['price']){
				$specprice_false=1;
				break;
			}

			if($v['price']<1){
				$specprice_false=1;
				break;
			}

			if($v['price']>99999){
				$specprice_false=1;
				break;
			}
		}

		if($specname_false){
			$rs['code']=1001;
			$rs['msg']='请填写商品规格名称';
			return $rs;
		}

		if($specname_len_false){
			$rs['code']=1001;
			$rs['msg']='商品规格名称应在15个字符以内';
			return $rs;
		}

		if($specnum_false){
			$rs['code']=1001;
			$rs['msg']='商品规格库存在1-9999999之间';
			return $rs;
		}

		if($specprice_false){
			$rs['code']=1001;
			$rs['msg']='商品规格单价在1-99999之间';
			return $rs;
		}

		if($postage<0||$postage>999){
			$rs['code']=1001;
			$rs['msg']='商品规格运费在0-999之间';
			return $rs;
		}

		$domain=new Domain_Seller();
		$res=$domain->upGoodsSpecs($uid,$goodsid,$specs);
		if($res==1001){
			$rs['code']=1001;
			$rs['msg']='商品不存在';
			return $rs;
		}

		if($res==1002){
			$rs['code']=1002;
			$rs['msg']='商品不存在';
			return $rs;
		}

		if($res==1003){
			$rs['code']=1003;
			$rs['msg']='商品审核中,无法修改规格';
			return $rs;
		}

		if($res==1004){
			$rs['code']=1004;
			$rs['msg']='商品下架,无法修改规格';
			return $rs;
		}

		if($res==-1){
			$rs['code']=1005;
			$rs['msg']='商品规格更新失败';
			return $rs;
		}

		return $rs;

	}

	/**
	 * 卖家修改商品信息
	 * @desc 用于 卖家修改商品信息
	 * @return int code 操作码，0表示成功
	 * @return array info  
	 * @return string msg 提示信息
	 */
	public function upGoods(){
		$rs = array('code' => 0, 'msg' => '商品修改成功', 'info' => array());
		
        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $goodsid=checkNull($this->goodsid);
        $one_classid=checkNull($this->one_classid);
        $two_classid=checkNull($this->two_classid);
        $three_classid=checkNull($this->three_classid);
        $name=checkNull($this->name);
        $video_url=checkNull($this->video_url);
        $video_thumb=checkNull($this->video_thumb);
        $thumbs=checkNull($this->thumbs);
        $content=checkNull($this->content);
        $pictures=checkNull($this->pictures);
        $specs=$this->specs;
        $postage=checkNull($this->postage);
        $share_income=checkNull($this->share_income);
        
        $checkToken = checkToken($uid,$token);
		if($checkToken==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		//判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

        //判断商品信息
        $domain_shop=new Domain_Shop();
        $where=array(
            'id'=>$goodsid
        );
        $goods_info=$domain_shop->getGoods($where);


        if(!$goods_info){
            $rs['code']=1001;
            $rs['msg']='商品不存在';
            return $rs;
        }

        if($goods_info['status']==1){
            $rs['code']=1001;
            $rs['msg']='商品已审核通过,不可修改';
            return $rs;
        }


		if(!$one_classid){
			$rs['code']=1001;
			$rs['msg']='请选择商品一级分类';
			return $rs;
		}

		if(!$two_classid){
			$rs['code']=1001;
			$rs['msg']='请选择商品二级分类';
			return $rs;
		}

		if(!$three_classid){
			$rs['code']=1001;
			$rs['msg']='请选择商品三级分类';
			return $rs;
		}

		if(!$name){
			$rs['code']=1001;
			$rs['msg']='请填写商品名称';
			return $rs;
		}

		if(mb_strlen($name)>15){
			$rs['code']=1001;
			$rs['msg']='商品名称不能超过15字';
			return $rs;
		}

		if(!$thumbs){
			$rs['code']=1001;
			$rs['msg']='请上传商品缩略图';
			return $rs;
		}

		if(!$content){
			$rs['code']=1001;
			$rs['msg']='请添加商品详情';
			return $rs;
		}

		if(mb_strlen($content)>300){
			$rs['code']=1001;
			$rs['msg']='商品详情不能超过300字';
			return $rs;
		}

		if(!$specs){
			$rs['code']=1001;
			$rs['msg']='请添加商品规格';
			return $rs;
		}

		$specname_false=0;
		$specname_len_false=0;
		$specnum_false=0;
		$specprice_false=0;
		$specthumb_false=0;
        $share_false=0;

		$specArr=json_decode($specs,true);

		foreach ($specArr as $k => $v) {
			if(!$v['spec_name']){
				$specname_false=1;
				break;
			}

			if(mb_strlen($v['spec_name'])>15){
				$specname_len_false=1;
				break;
			}

			if($v['spec_num']<1){
				$specnum_false=1;
				break;
			}

			if($v['spec_num']>9999999){
				$specnum_false=1;
				break;
			}

			if(floor($v['spec_num'])!=$v['spec_num']){
				$specnum_false=1;
				break;
			}

			if(!$v['price']){
				$specprice_false=1;
				break;
			}

			if($v['price']<1){
				$specprice_false=1;
				break;
			}

			if($v['price']>99999){
				$specprice_false=1;
				break;
			}

            if($v['price']<$share_income){
                $share_false=1;
                break;
            }

			if(!$v['thumb']){
				$specthumb_false=1;
				break;
			}


		}

		if($specname_false){
			$rs['code']=1001;
			$rs['msg']='请填写商品规格名称';
			return $rs;
		}

		if($specname_len_false){
			$rs['code']=1001;
			$rs['msg']='商品规格名称应在15个字符以内';
			return $rs;
		}

		if($specnum_false){
			$rs['code']=1001;
			$rs['msg']='商品规格库存在1-9999999之间';
			return $rs;
		}

		if($specprice_false){
			$rs['code']=1001;
			$rs['msg']='商品规格单价在1-99999之间';
			return $rs;
		}

		if($specthumb_false){
			$rs['code']=1001;
			$rs['msg']='请上传商品规格封面图';
			return $rs;
		}

		if($postage<0||$postage>999){
			$rs['code']=1001;
			$rs['msg']='商品运费在0-999之间';
			return $rs;
		}

        if($share_income<0||$share_income>999){
            $rs['code']=1001;
            $rs['msg']='商品分享佣金在0-999之间';
            return $rs;
        }

        if($share_false){
            $rs['code']=1001;
            $rs['msg']='分享佣金应低于商品价格';
        }
		
		//获取规格最低价格
		$low_price=$specArr[0]['price'];

		$data=array(
			'name'=>$name,
			'one_classid'=>$one_classid,
			'two_classid'=>$two_classid,
			'three_classid'=>$three_classid,
			'video_url'=>$video_url,
			'video_thumb'=>$video_thumb,
			'thumbs'=>$thumbs,
			'content'=>$content,
			'pictures'=>$pictures,
			'specs'=>urldecode($specs),
			'postage'=>$postage,
            'share_income'=>$share_income,
			'uptime'=>time(),
			'low_price'=>$low_price,
			'status'=>0

		);

		$configpri=getConfigPri();
		if(!$configpri['goods_switch']){ //商品审核开关关闭,直接审核通过
			$data['status']=1;
		}

		$domain=new Domain_Seller();
		$res=$domain->upGoods($uid,$goodsid,$data);

		if($res==1001){
			$rs['code']=1001;
			$rs['msg']='商品不存在';
			return $rs;
		}

		if($res==1002){
			$rs['code']=1002;
			$rs['msg']='商品修改失败';
			return $rs;
		}

		return $rs;
	}
	
	/**
	 * 商家删除商品
	 * @desc 用于商家删除商品
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function delGoods() {
		$rs = array('code' => 0, 'msg' => '商品删除成功', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$goodsid=checkNull($this->goodsid);
		$time=checkNull($this->time);
		$sign=checkNull($this->sign);
        
        if($uid<0 || $token=='' || $goodsid<0 || !$time ||!$sign){
            $rs['code'] = 1000;
			$rs['msg'] = '信息错误';
			return $rs;
        }
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$now=time();

		if(($now-$time)>300){
			$rs['code']=1001;
			$rs['msg']='参数错误';
			return $rs;
		}

		$checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time,
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}
        
        
		$domain = new Domain_Seller();
        
		$res = $domain->delGoods($uid,$goodsid);

		return $res;			
	}

	/**
	 * 上架/下架商品
	 * @desc 用于上架/下架商品
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function upStatus() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$goodsid=checkNull($this->goodsid);
		$status=checkNull($this->status);
		$time=checkNull($this->time);
		$sign=checkNull($this->sign);
        
        if($uid<0 || $token=='' || $goodsid<0 || ($status!=-1 && $status!=1)||!$time||!$sign){
            $rs['code'] = 1000;
			$rs['msg'] = '信息错误';
			return $rs;
        }
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$now=time();

		if(($now-$time)>300){
			$rs['code']=1001;
			$rs['msg']='参数错误';
			return $rs;
		}

		$checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'goodsid'=>$goodsid,
            'time'=>$time,
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}
        
        
		$domain = new Domain_Seller();
        
		$res = $domain->upStatus($uid,$goodsid,$status);

		return $res;			
	}
	

	/**
	 * 获取物流公司列表
	 * @desc 用于获取物流公司列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return array info[].id 物流项目ID 
	 * @return array info[].express_name 物流名称
	 * @return array info[].express_phone 物流电话
	 * @return array info[].express_thumb 物流图片
	 * @return string msg 提示信息
	 */
	public function getExpressList(){
		$rs=array('code'=>0,'msg'=>'','info'=>array());

		$uid=checkNull($this->uid);
		$token=checkNull($this->token);

		if($uid<0||$token==""){
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

		//判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}
		
		$domain=new Domain_Seller();
		$res=$domain->getExpressList();

		$rs['info']=$res;

		return $rs;
		
	}


	/**
	 * 卖家根据不同类型获取订单列表
	 * @desc 用于卖家根据不同类型获取订单列表
	 * @return int code 操作码，0表示成功
	 * @return string msg 提示信息
	 * @return array info 返回数据列表
	 * @return array info[0]['list'] 返回商品订单列表
	 * @return int info[0]['list'][].id 商品订单ID
	 * @return int info[0]['list'][].uid 商品订单购买者ID
	 * @return int info[0]['list'][].shop_uid 商品订单商家ID
	 * @return int info[0]['list'][].goodsid 商品ID
	 * @return string info[0]['list'][].goods_name 商品名称
	 * @return string info[0]['list'][].spec_name 订单商品规格名称
	 * @return int info[0]['list'][].nums 订单购买数量
	 * @return float info[0]['list'][].price 订单商品单价
	 * @return float info[0]['list'][].total 订单商品总价
	 * @return float info[0]['list'][].postage 订单商品运费
	 * @return int info[0]['list'][].status 订单状态 -1 已关闭 0 待买家付款  1 待发货  2  待确认收货   3 待评价  4 已评价  5 退款
	 * @return string info[0]['list'][].orderno 订单编号
	 * @return int info[0]['list'][].refund_status 退款处理结果 -1 失败 0 处理中 1 成功
	 * @return string info[0]['list'][].status_name 状态标识
	 * @return array info[0]['type_list_nums'] 返回商品不同类型
	 * @return int info[0]['type_list_nums'].wait_payment_nums 待付款订单数
	 * @return int info[0]['type_list_nums'].wait_shipment_nums 待发货订单数
	 * @return int info[0]['type_list_nums'].wait_refund_nums  待退款订单数
	 * @return int info[0]['type_list_nums'].all_refund_nums  全部退款订单数
	 * @return int info[0]['type_list_nums'].wait_receive_nums  已发货、待收货订单数
	 * @return int info[0]['type_list_nums'].wait_evaluate_nums  已签收、待评价订单数
	 * @return int info[0]['type_list_nums'].finished_nums  已完成订单数
	 * @return int info[0]['type_list_nums'].all_nums  全部订单数
	 */
	public function getGoodsOrderList(){
		$rs=array('code'=>0,'msg'=>'','info'=>array());

		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$type=checkNull($this->type);
		$p=checkNull($this->p);
		$time=checkNull($this->time);
		$sign=checkNull($this->sign);

		$type_arr=['all','wait_payment','wait_shipment','wait_refund','all_refund','wait_receive','wait_evaluate','closed','finished'];

		if($uid<0||$token==''||!in_array($type, $type_arr)||$p<1||!$time||!$sign){
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

		$now=time();
		if($now-$time>300){
			$rs['code']=1001;
			$rs['msg']='参数错误';
			return $rs;
		}

		$checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time,
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

        $domain=new Domain_Seller();
        $res=$domain->getGoodsOrderList($uid,$type,$p);


        $type_list_nums=$domain->getTypeListNums($uid,$type_arr);
        $rs['info'][0]['list']=$res;
        $rs['info'][0]['type_list_nums']=$type_list_nums;

        return $rs;
	}

	/**
	 * 卖家给待发货订单填写物流信息
	 * @desc 用于卖家给待发货订单填写物流信息
	 * @return int code 状态码，0表示成功
	 * @return string msg 提示信息
	 * @return array info 返回信息
	 */
	public function setExpressInfo(){
		$rs=array('code'=>0,'msg'=>'订单发货成功','info'=>array());
		$uid=checkNull($this->uid);
		$token=checkNull($this->token);
		$orderid=checkNull($this->orderid);
		$expressid=checkNull($this->expressid);
		$express_number=checkNull($this->express_number);
		$time=checkNull($this->time);
		$sign=checkNull($this->sign);


		$pattern = '/[^\x00-\x80]/';
		if(preg_match($pattern,$express_number)){
			$rs['code']=1001;
			$rs['msg']='物流单号不能填写汉字';
			return $rs;
		}


		if($uid<0||$token==''||!$orderid||!$expressid||$express_number==''||!$express_number||!$time||!$sign){
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

		//判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

		$order_info=getShopOrderInfo(['shop_uid'=>$uid,'id'=>$orderid]);
		if(!$order_info){
			$rs['code'] = 1001;
			$rs['msg'] = '订单不存在';
			return $rs;
		}

		if($order_info['status']!=1){
			$rs['code'] = 1001;
			$rs['msg'] = '订单不符合发货状态';
			return $rs;
		}

		$express_info=getExpressInfo(['id'=>$expressid,'express_status'=>1]);

		if(!$express_info){
			$rs['code'] = 1001;
			$rs['msg'] = '没有符合条件的物流公司';
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
            'orderid'=>$orderid,
            'expressid'=>$expressid,
            'time'=>$time,
        );
        
        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $domain=new Domain_Seller();
		$res=$domain->setExpressInfo($uid,$orderid,$expressid,$express_number);
		if(!$res){
			$rs['code']=1002;
            $rs['msg']='订单发货失败,请重试';
            return $rs; 
		}

		return $rs;

	}

	/**
     * 卖家删除订单
     * @desc 用于卖家删除订单
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     * @return object info[0].order_info 返回订单信息
     * @return object info[0].order_info.address_format 返回订单收货地址格式化
     * @return object info[0].order_info.type_name 返回订单付款方式说明
     * @return object info[0].order_info.status_name 返回订单状态说明
     * @return object info[0].order_info.status_desc 返回订单状态简介
     * @return object info[0].shop_info 返回订单商品所属店铺信息
     * @return object info[0].express_info 返回订单物流信息
     * @return string info[0].express_info.state_name 返回订单物流的状态
     * @return string info[0].express_info.desc 返回订单物流的说明
     */
    public function delGoodsOrder(){

        $rs=array('code'=>0,'msg'=>'订单删除成功','info'=>array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $orderid=checkNull($this->orderid);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);
        
        if($uid<0||$token==''||$orderid<1||!$time||!$sign){
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

        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

        $where=array(
            'id'=>$orderid,
            'shop_uid'=>$uid
        );
        $order_info=getShopOrderInfo($where);
        if(!$order_info){
            $rs['code']=1001;
            $rs['msg']='订单不存在';
            return $rs;
        }

        $order_status=$order_info['status'];

        if($order_status!=-1 && $order_status!=4){ //已关闭 和已评价的订单可以删除
            $rs['code']=1001;
            $rs['msg']='订单不允许删除';
            return $rs;
        }

        if($order_info['isdel']==1 ||$order_info['isdel']==-2){ //卖家买家都删除了 或 卖家已删除
            $rs['code']=1001;
            $rs['msg']='订单已经删除';
            return $rs;
        }

        $data=array(
            'isdel'=>-2
        );

        if($order_info['isdel']==-1){
            $data=array(
                'isdel'=>1
            );
        }

        changeShopOrderStatus($uid,$orderid,$data);

        return $rs;
    }

    /**
     * 卖家获取订单详情
     * @desc 用于卖家获取订单详情
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     * @return array info[0].order_info 订单信息
     * @return array info[0].order_info['address_format'] 订单地址格式化
     * @return array info[0].order_info['spec_thumb_format'] 订单规格格式化
     * @return array info[0].order_info['type_name'] 订单付款方式
     * @return array info[0].shop_info 店铺信息
     * @return array info[0].express_info 物流信息
     * @return array info[0].express_info['state_name'] 物流状态
     * @return array info[0].express_info['state_desc'] 物流说明
     */
    public function getGoodsOrderInfo(){
    	$rs=array('code'=>0,'msg'=>'','info'=>array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $orderid=checkNull($this->orderid);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);
        
        if($uid<0||$token==''||$orderid<1||!$time||!$sign){
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


        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

        $where=array(
            'id'=>$orderid,
            'shop_uid'=>$uid
        );
        $order_info=getShopOrderInfo($where);
        if(!$order_info){
            $rs['code']=1001;
            $rs['msg']='订单不存在';
            return $rs;
        }

        $order_info=handleGoodsOrder($order_info);


        if($order_info['status']==1){ //订单待发货
        	$order_info['status_name']='待发货';
        	$order_info['status_desc']='请尽快发货,逾期将会自动关闭订单,钱款会退还给买家';
        }

        if($order_info['status']==2){ //订单已发货
        	$order_info['status_name']='已发货';
        }

        if($order_info['status']==3&&$order_info['settlement_time']==''){ //已签收 未结算
        	$order_info['status_name']='已签收';
        	$order_info['status_desc']='商品钱款会尽快核算发放';
        }

        if($order_info['status']==3&&$order_info['settlement_time']!=''){ //已签收 已结算
        	$order_info['status_name']='交易成功';
        	$order_info['status_desc']='商品钱款已发放到您的账户中';
        }

        $rs['info'][0]['order_info']=$order_info;

        $model=new Model_Shop();
        $shop_info=$model->getShop($order_info['shop_uid']);

        //判断店铺用户是否关注了订单用户
        $isattention=isAttention($uid,$order_info['uid']);
        $shop_info['isattention']=$isattention;
        $rs['info'][0]['shop_info']=$shop_info;

        $rs['info'][0]['express_info']=[];



        if($order_info['status']==2||$order_info['status']==3 ||$order_info['status']==4){
            $express_info=getExpressStateInfo($order_info['express_code'],$order_info['express_number'],$order_info['express_name'],$order_info['username'],$order_info['phone']);

            $rs['info'][0]['express_info']=$express_info;
        }

        return $rs;

    }

    /**
     * 卖家获取订单退款详情
     * @desc 用于卖家获取订单退款详情
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     * @return string info[0]['refund_info']['reason'] 退款选择的理由
     * @return string info[0]['refund_info']['content'] 退款填写的说明
     * @return int info[0]['refund_info']['shop_process_num'] 店铺拒绝次数
     * @return string info[0]['refund_info']['shop_process_time'] 店铺处理时间
     * @return int info[0]['refund_info']['shop_result'] 店铺处理结果 -1 拒绝 0 处理中 1 同意
     * @return string info[0]['refund_info']['platform_process_time'] 平台处理时间
     * @return int info[0]['refund_info']['platform_result'] 平台处理结果 -1 拒绝 0 处理中 1 同意
     * @return int info[0]['refund_info']['status'] 退款处理状态   0 处理中  -1买家已取消  1 已完成
     * @return int info[0]['refund_info']['is_platform_interpose'] 是否平台介入
     * @return stirng info[0]['refund_info']['system_process_time'] 系统自动处理时间
     * @return string info[0]['refund_info'].user_nickname 退款用户昵称
     * @return stirng info[0]['refund_info'].addtime  退款申请时间
     * @return stirng info[0]['refund_info'].can_handle  卖家是否可处理
     * @return stirng info[0]['refund_info'].status_name  退款处理名称
     * @return stirng info[0]['refund_info'].status_time  退款处理时间
     * @return stirng info[0]['refund_info'].status_desc  退款处理描述
     * @return stirng info[0]['order_info'].total 订单退款金额
     * @return stirng info[0]['order_info'].status_name 订单退款前的状态
     */
    public function getGoodsOrderRefundInfo(){
        $rs=array('code'=>0,'msg'=>'','info'=>array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $orderid=checkNull($this->orderid);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        if($uid<0||$token==""||$orderid<1||!$time||!$sign){
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

        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'orderid'=>$orderid,
            'time'=>$time
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

        $where=array(
            'id'=>$orderid,
            'shop_uid'=>$uid
        );

        $order_info=getShopOrderInfo($where);
        if(!$order_info){
            $rs['code']=1001;
            $rs['msg']='订单不存在';
            return $rs;
        }

        $status=$order_info['status'];

        if(($status==1||$status==2||$status==3)&&$refund_status==0){ //待发货 待收货 已收货的 可以申请退款
            $rs['code']=1001;
            $rs['msg']='订单未申请退款';
            return $rs;
        }

        if($status!=5){
            $rs['code']=1001;
            $rs['msg']='订单未申请退款';
            return $rs;
        }


        //获取退款详情
        $where1=array(
        	'orderid'=>$orderid,
            'shop_uid'=>$uid
        );

        $refund_info=getShopOrderRefundInfo($where1);

        $refund_status=$refund_info['status'];
        $refund_shop_result=$refund_info['shop_result'];
        $refund_platform_result=$refund_info['platform_result'];
        $is_platform_interpose=$refund_info['is_platform_interpose'];
        $shop_process_num=$refund_info['shop_process_num'];
        $status_name='';
        $status_time='';
        $status_desc='';
        $can_handle='0';

        $effective_time=getShopEffectiveTime();

        if($refund_info['status']==-1){ //买家取消

            $status_name='买家已取消退款申请';

        }elseif($refund_info['status']==0){ //处理中

        	if($is_platform_interpose==0){ //平台未介入

        		if($refund_shop_result==0){

	                $status_name='买家申请退款';
	                $end=$refund_info['addtime']+$effective_time['shop_refund_finish_time']*24*60*60;
	                $cha=$end-$now;
	                $status_time='还剩'.getSeconds($cha);
	                $can_handle='1';

	                if($shop_process_num>0){
	                	$status_name='买家重新申请退款';
	                }

	            }elseif($refund_shop_result==-1){ //卖家拒绝

	                $status_name='您已经拒绝了买家退款申请'.$shop_process_num.'次';
	                if($shop_process_num<3){ 
	                	$status_desc='买家可重新提交申请或申请平台介入';
	                	$can_handle='0';

	                }else{
	                	$status_desc='买家可申请平台介入';
	                }
	                
	            }

        	}else{

        		$status_name='等待平台处理中';

        	}

        }else{ //处理完成



            if($is_platform_interpose){ //平台介入


                if($refund_platform_result==-1){ //拒绝
                    $status_name='平台拒绝了买家的退款申请';
                    $status_time=date("Y-m-d H:i:s",$refund_info['platform_process_time']); //平台处理时间
                }elseif($refund_platform_result==1){
                    $status_name='平台同意了买家的退款申请';
                    $status_time=date("Y-m-d H:i:s",$refund_info['platform_process_time']); //平台处理时间
                    $status_desc='如有疑问请与平台协商';
                }

            }else{

                if($refund_shop_result==1){ //卖家同意
                   $status_name='退款成功'; 
                   $status_time=date("Y-m-d H:i:s",$refund_info['shop_process_time']); //店铺处理时间
                   $status_desc='您已同意退款,退款金额将退回到买家账户余额';
                }elseif($refund_shop_result==-1){ //卖家拒绝
                    $status_name='您已经拒绝了买家退款申请';
                    $status_time=date("Y-m-d H:i:s",$refund_info['system_process_time']); //系统自动处理时间
                    $status_desc='您已拒绝退款,拒绝原因:';
                }

            }

            
        }

        $refund_info['status_name']=$status_name;
        $refund_info['status_time']=$status_time;
        $refund_info['status_desc']=$status_desc;
        $refund_info['can_handle']=$can_handle; //卖家是否可操作

        $refund_info['addtime']=date("Y-m-d H:i:s",$refund_info['addtime']);

        $buyer_info=getUserInfo($refund_info['uid']);
        $refund_info['user_nickname']=$buyer_info['user_nickname'];

        $rs['info'][0]['order_info']=handleGoodsOrder($order_info);
        $rs['info'][0]['refund_info']=$refund_info;

        $model=new Model_Shop();
        $shop_info=$model->getShop($order_info['shop_uid']);
        
        //判断店铺用户是否关注了订单用户
        $isattention=isAttention($uid,$order_info['uid']);
        $shop_info['isattention']=$isattention;
        $rs['info'][0]['shop_info']=$shop_info;
                
        return $rs;        

    }

    /**
     * 卖家获取拒绝退款原因列表
     * @desc 用于卖家获取拒绝退款原因列表
     * @return int code 状态码，0标识成功
     * @return string msg 返回提示
     * @return array info 返回信息
     * @return array info[].id 原因ID
     * @return array info[].name 原因名称
     */
    public function getRefundRefuseReason(){
    	$rs=array('code'=>0,'msg'=>'','info'=>array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);

        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

        $domain=new Domain_Seller();
        $res=$domain->getRefundRefuseReason();
        $rs['info']=$res;

        return $rs;

    }

    /**
     * 卖家处理退款
     * @desc 用于卖家处理退款
     * @return int code 状态码，0标识成功
     * @return string msg 返回提示
     * @return array info 返回信息
     */
    public function setGoodsOrderRefund(){
    	$rs=array('code'=>0,'msg'=>'退款处理完成','info'=>array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $orderid=checkNull($this->orderid);
        $type=checkNull($this->type);
        $reasonid=checkNull($this->reasonid);
        $refuse_desc=checkNull($this->refuse_desc);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);

        if($uid<0||$token==""||$orderid<1||!in_array($type, ['0','1'])||!$time||!$sign){
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

        $has_reasonid=0;
        $refuse_reason='';

        //获取拒绝理由列表
        $domain=new Domain_Seller();
        $refuse_list=$domain->getRefundRefuseReason();

        if($type==0){ //拒绝

        	foreach ($refuse_list as $k => $v) {
        		if($v['id']==$reasonid){
        			$has_reasonid=1;
        			$refuse_reason=$v['name'];
        			break;
        		}
        	}

        	if(!$has_reasonid){
        		$rs['code']=1001;
	            $rs['msg']='请选择拒绝理由';
	            return $rs;
        	}

        	if($refuse_desc){
        		if(mb_strlen($refuse_desc)>300){
        			$rs['code']=1001;
		            $rs['msg']='拒绝详细原因应在300字以内';
		            return $rs;
        		}
        	}


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
            'orderid'=>$orderid,
            'time'=>$time
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

        $where=array(
            'id'=>$orderid,
            'shop_uid'=>$uid
        );

        $order_info=getShopOrderInfo($where);
        if(!$order_info){
            $rs['code']=1001;
            $rs['msg']='订单不存在';
            return $rs;
        }

        $status=$order_info['status'];

        if($status!=5){
            $rs['code']=1001;
            $rs['msg']='订单未申请退款';
            return $rs;
        }

        //获取退款详情
        $where1=array(
        	'orderid'=>$orderid,
            'shop_uid'=>$uid
        );

        $refund_info=getShopOrderRefundInfo($where1);

        $refund_status=$refund_info['status'];
        $is_platform_interpose=$refund_info['is_platform_interpose'];
        $platform_result=$refund_info['platform_result'];
        $shop_result=$refund_info['shop_result'];
        $shop_process_num=$refund_info['shop_process_num'];

        if($refund_status==-1){
        	$rs['code']=1001;
            $rs['msg']='买家已取消退款申请';
            return $rs;
        }

        if($refund_status==1){ //已经完成

        	if($is_platform_interpose==1){ //平台介入

        		if($platform_result==1){ //平台同意
        			$rs['code']=1001;
		            $rs['msg']='平台已同意退款';
		            return $rs;

        		}elseif($is_platform_interpose==-1){ //平台拒绝
        			$rs['code']=1001;
		            $rs['msg']='平台已拒绝退款';
		            return $rs;
        		}

        	}else{

        		if($shop_result==1){ //卖家同意
        			$rs['code']=1001;
		            $rs['msg']='您已同意退款';
		            return $rs;
        		}elseif($shop_result==-1){
        			$rs['code']=1001;
		            $rs['msg']='您已拒绝退款';
		            return $rs;
        		}

        	}

        }else{

        	if($shop_result==-1){
        		$rs['code']=1001;
	            $rs['msg']='您已经拒绝,不能再操作';
	            return $rs;
        	}elseif($shop_result==1){
        		$rs['code']=1001;
	            $rs['msg']='您已经同意,不能再操作';
	            return $rs;
        	}

        	if($shop_process_num>=3){
        		$rs['code']=1001;
	            $rs['msg']='您已经拒绝'.$shop_process_num.'次,不能再操作';
	            return $rs;
        	}

        }


        if($type==1){ //卖家同意

        	//更改退款信息
        	
        	$where=array(
        		'orderid'=>$orderid,
        		'shop_uid'=>$uid
        	);

        	$data=array(
        		'shop_result'=>1,
        		'shop_process_time'=>$now,
        		'status'=>1
        	);

        	$res=changeGoodsOrderRefund($where,$data);
        	if(!$res){
        		$rs['code']=1001;
	            $rs['msg']='退款处理失败,请重试';
	            return $rs;
        	}

        	//更改订单状态
        	$data1=array(
        		'refund_status'=>1,
        		'refund_shop_result'=>1,
        		'refund_endtime'=>$now,
        	);


        	changeShopOrderStatus($uid,$orderid,$data1);

        	//给买家退钱
        	setUserBalance($order_info['uid'],1,$order_info['total']);

        	//添加余额操作记录
        	$data2=array(
                'uid'=>$order_info['uid'],
                'touid'=>$order_info['shop_uid'],
                'balance'=>$order_info['total'],
                'type'=>1,
                'action'=>5, //买家发起退款，卖家同意
                'orderid'=>$orderid,
        		'addtime'=>$now

            );

            addBalanceRecord($data2);

            //减去商品销量
            changeShopGoodsSaleNums($order_info['goodsid'],0,$order_info['nums']);

            //减去店铺销量
        	changeShopSaleNums($order_info['shop_uid'],0,$order_info['nums']);

        	//商品规格库存回增
        	changeShopGoodsSpecNum($order_info['goodsid'],$order_info['spec_id'],$order_info['nums'],1);


            $title="你的商品“".$order_info['goods_name']."”卖家已经同意退款";

            //写入订单消息列表
            $data3=array(
            	'title'=>$title,
            	'orderid'=>$orderid,
            	'uid'=>$order_info['uid'],
            	'addtime'=>$now,
            	'type'=>'0'
            );

            addShopGoodsOrderMessage($data3);
            //发送腾讯IM
            txMessageIM($title,$order_info['uid'],'goodsorder_admin');
            

            $refund_history_data=array(
            	'orderid'=>$orderid,
            	'type'=>2,
            	'addtime'=>$now,
            	'desc'=>'卖家同意退款'
            );


        }else{ //卖家拒绝

        	//更新退款信息
        	$where=array(
        		'orderid'=>$orderid,
        		'shop_uid'=>$uid
        	);

        	$data=array(
        		'shop_result'=>-1,
        		'shop_process_time'=>$now,
        		'shop_process_num'=>$shop_process_num+1,
        	);

        	$res=changeGoodsOrderRefund($where,$data);

        	if(!$res){
        		$rs['code']=1001;
	            $rs['msg']='退款处理失败,请重试';
	            return $rs;
        	}

        	//修改订单信息
        	$data1=array(
        		'refund_shop_result'=>-1 //卖家处理状态为拒绝
        	);

        	changeShopOrderStatus($uid,$orderid,$data1);


        	$title="你的商品“".$order_info['goods_name']."”卖家拒绝退款";

            //写入订单消息列表
            $data3=array(
            	'title'=>$title,
            	'orderid'=>$orderid,
            	'uid'=>$order_info['uid'],
            	'addtime'=>$now,
            	'type'=>'0'
            );

            addShopGoodsOrderMessage($data3);
            //发送腾讯IM
            txMessageIM($title,$order_info['uid'],'goodsorder_admin');


        	$refund_history_data=array(
            	'orderid'=>$orderid,
            	'type'=>2,
            	'addtime'=>$now,
            	'desc'=>'卖家拒绝退款',
            	'refuse_reason'=>$refuse_reason,
            	'handle_desc'=>$refuse_desc
            );


        }

        //添加退款处理历史记录
        setGoodsOrderRefundList($refund_history_data);



        return $rs;	

    }


    /**
     * 卖家获取结算记录
     * @desc 用于卖家获取结算记录
     * @return int code 状态码，0标识成功
     * @return string msg 返回提示
     * @return array info 返回信息
     * @return object info[0]['user_balance'] 返回用户余额信息
     * @return object info[0]['user_balance']['balance'] 用户当前可用余额
     * @return object info[0]['user_balance']['balance_total'] 用户累计余额
     * @return object info[0]['user_balance']['wait_settlement'] 用户待结算余额
     */
    public function getSettlementList(){
    	$rs=array('code'=>0,'msg'=>'','info'=>array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);
        $p=checkNull($this->p);

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

        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

        $domain=new Domain_Seller();
        $res=$domain->getSettlementList($uid,$p);

        $user_balance=getUserShopBalance($uid);

        //获取卖家交易中的总金额
        $wait_settlement=$domain->getWaitSettlementTotal($uid);

        $user_balance['wait_settlement']=$wait_settlement;

        $rs['info'][0]['list']=$res;
        $rs['info'][0]['user_balance']=$user_balance;

        return $rs;
    }


    /**
     * 添加外部链接商品
     * @desc 用于添加外部链接商品
     * @return int code 状态码,0表示成功
     * @return string msg 状态码,0表示成功
     * @return array info 返回信息
     */
    public function setOutsideGoods(){
    	$rs=array('code'=>0,'msg'=>'商品发布成功','info'=>array());

    	$uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $href=$this->href;
        $name=checkNull($this->name);
        $original_price=checkNull($this->original_price);
        $present_price=checkNull($this->present_price);
        $goods_desc=checkNull($this->goods_desc);
        $thumb=checkNull($this->thumb);


        if($uid<0||$token==""){
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

        //判断店铺审核状态
        $domain=new Domain_Shop();
        $apply_info=$domain->getShopApplyInfo($uid);
        $apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

        if($apply_status==-1){
            $rs['code']=1001;
            $rs['msg']='请先进行店铺认证';
            return $rs;
        }

        if($apply_status==0){
            $rs['code']=1001;
            $rs['msg']='店铺正在审核中';
            return $rs;
        }

        if($apply_status==2){
            $rs['code']=1001;
            $rs['msg']='店铺认证被拒绝,请重新认证';
            return $rs;
        }

        if(!$href){
        	$rs['code']=1001;
            $rs['msg']='请填写商品外链地址';
            return $rs;
        }


        if(!$name){
        	$rs['code']=1001;
            $rs['msg']='请填写商品名称';
            return $rs;
        }

        if(mb_strlen(trim($name))>20){
        	$rs['code']=1001;
            $rs['msg']='商品名称必须在20字以内';
            return $rs;
        }

        if(!$original_price){
        	$rs['code']=1001;
            $rs['msg']='请填写商品原价';
            return $rs;
        }

        if(!is_numeric($original_price)){
        	$rs['code']=1001;
            $rs['msg']='商品原价必须为数字';
            return $rs;
        }

        if($original_price>99999999 || $original_price<=0){
            $rs['code']=1001;
            $rs['msg']='商品原价必须在0-99999999之间';
            return $rs;
        }

        if(!$present_price){
        	$rs['code']=1001;
            $rs['msg']='请填写商品现价';
            return $rs;
        }

        if(!is_numeric($present_price)){
        	$rs['code']=1001;
            $rs['msg']='商品现价必须为数字';
            return $rs;
        }

        if($present_price>99999999 || $present_price<=0){
            $rs['code']=1001;
            $rs['msg']='商品现价必须在0-99999999之间';
            return $rs;
        }

        if(!$goods_desc){
        	$rs['code']=1001;
            $rs['msg']='请填写商品简介';
            return $rs;
        }

        if(mb_strlen(trim($goods_desc))>50){
        	$rs['code']=1001;
            $rs['msg']='商品简介必须在50字以内';
            return $rs;
        }

        if(!$thumb){
        	$rs['code']=1001;
            $rs['msg']='请上传商品图片';
            return $rs;
        }

        $data=array(

        	'uid'=>$uid,
        	'name'=>$name,
        	'href'=>$href,
        	'thumbs'=>$thumb,
        	'original_price'=>$original_price,
        	'present_price'=>$present_price,
        	'goods_desc'=>$goods_desc,
        	'type'=>1,
        	'content'=>'',
        	'pictures'=>'',
        	'specs'=>'',
        	'low_price'=>$present_price,
        	'addtime'=>time()
        );

        $configpri=getConfigPri();
        if(!$configpri['goods_switch']){ //商品审核开关关闭
            $data['status']=1;
        }

        $domain=new Domain_Seller();

        $res=$domain->setOutsideGoods($data);

        if($res==1001){
        	$rs['code']=1001;
            $rs['msg']='您已经发布过同名商品';
            return $rs;
        }else if(!$res){
            $rs['code']=1001;
            $rs['msg']='商品发布失败';
            return $rs;
        }

        return $rs;

    }

    /**
     * 外链商品编辑提交
     * @desc 用于外链商品编辑提交
     * @return int code 状态码,0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function upOutsideGoods(){
        $rs=array('code'=>0,'msg'=>'商品修改成功','info'=>array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $goodsid=checkNull($this->goodsid);
        $href=$this->href;
        $name=checkNull($this->name);
        $original_price=checkNull($this->original_price);
        $present_price=checkNull($this->present_price);
        $goods_desc=checkNull($this->goods_desc);
        $thumb=checkNull($this->thumb);


        if($uid<0||$token==""||!$goodsid){
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

        //判断店铺审核状态
        $domain=new Domain_Shop();
        $apply_info=$domain->getShopApplyInfo($uid);
        $apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

        if($apply_status==-1){
            $rs['code']=1001;
            $rs['msg']='请先进行店铺认证';
            return $rs;
        }

        if($apply_status==0){
            $rs['code']=1001;
            $rs['msg']='店铺正在审核中';
            return $rs;
        }

        if($apply_status==2){
            $rs['code']=1001;
            $rs['msg']='店铺认证被拒绝,请重新认证';
            return $rs;
        }

        //判断商品信息
        $domain_shop=new Domain_Shop();
        $where=array(
            'id'=>$goodsid
        );
        $goods_info=$domain_shop->getGoods($where);


        if(!$goods_info){
            $rs['code']=1001;
            $rs['msg']='商品不存在';
            return $rs;
        }

        if($goods_info['status']==1){
            $rs['code']=1001;
            $rs['msg']='商品已审核通过,不能修改';
            return $rs;
        }

        if(!$href){
            $rs['code']=1001;
            $rs['msg']='请填写商品外链地址';
            return $rs;
        }


        if(!$name){
            $rs['code']=1001;
            $rs['msg']='请填写商品名称';
            return $rs;
        }

        if(mb_strlen(trim($name))>20){
            $rs['code']=1001;
            $rs['msg']='商品名称必须在20字以内';
            return $rs;
        }

        if(!$original_price){
            $rs['code']=1001;
            $rs['msg']='请填写商品原价';
            return $rs;
        }

        if(!is_numeric($original_price)){
            $rs['code']=1001;
            $rs['msg']='商品原价必须为数字';
            return $rs;
        }

        if($original_price>99999999 || $original_price<=0){
            $rs['code']=1001;
            $rs['msg']='商品原价必须在0-99999999之间';
            return $rs;
        }


        if(!$present_price){
            $rs['code']=1001;
            $rs['msg']='请填写商品现价';
            return $rs;
        }

        if(!is_numeric($present_price)){
            $rs['code']=1001;
            $rs['msg']='商品现价必须为数字';
            return $rs;
        }

        if($present_price>99999999 || $present_price<=0){
            $rs['code']=1001;
            $rs['msg']='商品现价必须在0-99999999之间';
            return $rs;
        }

        if(!$goods_desc){
            $rs['code']=1001;
            $rs['msg']='请填写商品简介';
            return $rs;
        }

        if(mb_strlen(trim($goods_desc))>50){
            $rs['code']=1001;
            $rs['msg']='商品简介必须在50字以内';
            return $rs;
        }

        if(!$thumb){
            $rs['code']=1001;
            $rs['msg']='请上传商品图片';
            return $rs;
        }

        $data=array(

            'uid'=>$uid,
            'name'=>$name,
            'href'=>$href,
            'thumbs'=>$thumb,
            'original_price'=>$original_price,
            'present_price'=>$present_price,
            'goods_desc'=>$goods_desc,
            'content'=>'',
            'pictures'=>'',
            'specs'=>'',
            'uptime'=>time(),
            'status'=>0
        );

        $configpri=getConfigPri();
        if(!$configpri['goods_switch']){ //商品审核开关关闭
            $data['status']=1;
        }

        $domain=new Domain_Seller();

        $res=$domain->upOutsideGoods($goodsid,$data);

        if($res==1001){
            $rs['code']=1001;
            $rs['msg']='您已经发布过同名商品';
            return $rs;
        }else if($res==1002){
            $rs['code']=1002;
            $rs['msg']='商品修改失败';
            return $rs;
        }

        return $rs;
    }

    /**
     * 卖家获取平台自营商品列表
     * @desc 用于卖家获取平台自营商品列表
     * @return int code 状态码，0表示成功
     * @return string msg 返回信息
     * @return array info 返回信息
     * @return array info[0].commission 商品代卖佣金
     * @return array info[0].sale_nums 商品总销量
     * @return array info[0].thumb 商品封面图
     */
    public function getPlatformGoodsLists(){
    	$rs=array('code'=>0,'msg'=>'','info'=>array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);
        $p=checkNull($this->p);

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

        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

		$domain=new Domain_Seller();
		$res=$domain->getPlatformGoodsLists($uid,$p);

		$rs['info']=$res;

		return $rs;
    }

    /**
     * 卖家将平台自营商品添加到店铺/取消代售
     * @desc 用于卖家将平台自营商品添加到店铺/取消代售
     * @return int code 状态码，0表示成功
     * @return string msg 返回提示信息
     * @return array info 返回信息
     * @return int info[0].status 返回在售状态 1代售中 0未添加代售
     */
    public function setPlatformGoods(){
    	$rs=array('code'=>0,'msg'=>'添加成功','info'=>array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $goodsid=checkNull($this->goodsid);
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

        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

		$domain_shop=new Domain_Shop();
		$where['id']=$goodsid;
		$goods_info=$domain_shop->getGoods($where);
		if(!$goods_info){
			$rs['code']=1002;
			$rs['msg']='商品不存在';
			return $rs;
		}


		$domain_seller=new Domain_Seller();
		$res=$domain_seller->setPlatformGoods($uid,$goodsid);
		if($res==1001){
			$rs['code']=1003;
			$rs['msg']='商品取消代售失败';
			return $rs;
		}

		if($res==1002){
			$rs['code']=1004;
			$rs['msg']='商品添加失败';
			return $rs;
		}

        if($res==1003){
            $rs['code']=1005;
            $rs['msg']='商品已下架';
            return $rs;
        }

        $status=$res['status'];
        if($status){
            $rs['msg']="添加成功";
        }else{
            $rs['msg']="取消成功";
        }
        $rs['info'][0]=$res;

		return $rs;

    }

    /**
     * 卖家商品管理中获取代售的平台商品列表
     * @desc 用于卖家商品管理中获取代售的平台商品列表
     * @return int code 状态码，0表示成功
     * @return string msg 返回的提示
     * @return array info 返回的信息
     */
    public function getOnsalePlatformGoods(){
    	$rs=array('code'=>0,'msg'=>'','info'=>array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $time=checkNull($this->time);
        $sign=checkNull($this->sign);
        $p=checkNull($this->p);

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

        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );

        $issign=checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        //判断店铺审核状态
		$domain=new Domain_Shop();
		$apply_info=$domain->getShopApplyInfo($uid);
		$apply_status=$apply_info['apply_status']; //-1 无审核记录 0 审核中 1 审核通过 2 审核拒绝

		if($apply_status==-1){
			$rs['code']=1001;
			$rs['msg']='请先进行店铺认证';
			return $rs;
		}

		if($apply_status==0){
			$rs['code']=1001;
			$rs['msg']='店铺正在审核中';
			return $rs;
		}

		if($apply_status==2){
			$rs['code']=1001;
			$rs['msg']='店铺认证被拒绝,请重新认证';
			return $rs;
		}

		$domain=new Domain_Seller();
		$res=$domain->getOnsalePlatformGoods($uid,$p);

		$rs['info']=$res;

		return $rs;

    }

    
}
