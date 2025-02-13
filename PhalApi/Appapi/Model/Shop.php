<?php
session_start();
class Model_Shop extends PhalApi_Model_NotORM {

    //检测用户是否缴纳保证金
    public function getBond($uid){
        $info=DI()->notorm->shop_bond->where("uid=?",$uid)->fetchOne();
        if(!$info){
            return -1;
        }

        if($info['status']==0){
            return 1;
        }

        return 2;
    }

    //缴纳保证金
    public function deductBond($uid,$shop_bond){

        //检测用户是否已经缴纳
        $info=DI()->notorm->shop_bond->where("uid=? and status !=0",$uid)->fetchOne();
        if($info){
            return 1001;
        }

        $isok=DI()->notorm->user->where("id=? and coin>=?",$uid,$shop_bond)->update(array('coin' => new NotORM_Literal("coin - {$shop_bond}")) );
        if(!$isok){
            return 1002;
        }

        //判断是否存在保证金已退回的情况
        $info2=DI()->notorm->shop_bond->where("uid=? and status =0",$uid)->fetchOne();
        if($info2){

            //更新保证金记录
            $data=array(
                "bond"=>$shop_bond,
                "status"=>1,
                "addtime"=>time(),
                "uptime"=>0 
            );

            $res=DI()->notorm->shop_bond->where("uid=?",$uid)->update($data);

        }else{
            $data=array(
                "uid"=>$uid,
                "bond"=>$shop_bond,
                "status"=>1,
                "addtime"=>time(),
                "uptime"=>time() 
            );

            $res=DI()->notorm->shop_bond->insert($data);
        }

        if(!$res){
            return 1003;
        }

        //写入消费记录
        $data1=array(
            "type"=>'0',
            "action"=>'14',
            "uid"=>$uid,
            "touid"=>$uid,
            "giftid"=>0,
            "giftcount"=>1,
            "totalcoin"=>$shop_bond,
            "addtime"=>time()
        );

        DI()->notorm->user_coinrecord->insert($data1);

        return 1;
    }

    //获取一级商品分类
    public function getOneGoodsClass(){
        $list=[];

        $list1=DI()->notorm->shop_goods_class->select("gc_one_id")->where("gc_grade=3")->fetchAll();

        if(!$list1){
            return [];
        }

        $gc_one_ids=array_column($list1,"gc_one_id");

        $ids=array_unique($gc_one_ids);

        $list=DI()->notorm->shop_goods_class->select("gc_id,gc_name,gc_isshow")->where("gc_id",$ids)->where(" gc_isshow=1")->order("gc_sort")->fetchAll();
        return $list;
    }

    /*获取店铺认证信息*/
    public function getShopApplyInfo($uid){


        $res=array(
            'apply_status'=>'0',
            'apply_info'=>[]
        );

        $info=DI()->notorm->shop_apply
                ->where("uid=?",$uid)
                ->fetchOne();

        if(!$info){
            $res['apply_status']='-1';
            return $res;
        }

        unset($info['name'],$info['thumb'],$info['des'],$info['license']);

        $info['certificate_format']=get_upload_path($info['certificate']); //营业执照
        $info['other_format']=get_upload_path($info['other']); //其他证件

        //获取用户的经营类目
        $goods_classid=DI()->notorm->seller_goods_class->select("goods_classid as gc_id")->where("uid=? and status=1",$uid)->fetchAll();
        $info['goods_classid']=$goods_classid;

        $status=$info['status'];

        if($status==0){ //审核中
            $res['apply_status']='0';
            return $res;
        }else if($status==1){ //审核通过
            $res['apply_status']='1';
            $res['apply_info']=$info;
            return $res;
        }else if($status==2){ //审核拒绝
            $res['apply_status']='2';
            $res['apply_info']=$info;
            return $res;
        }

        

        return $res;
    }

    /*店铺申请*/
    public function shopApply($uid,$data,$apply_status,$classid_arr){

        if($apply_status==-1){ //无申请记录
            $res=DI()->notorm->shop_apply->insert($data);
        }

        if($apply_status==2){
            $res=DI()->notorm->shop_apply->where("uid={$uid}")->update($data);
        }

        if(!$res){
            return 1001;
        }

        if($apply_status=1){

            //写入店铺总评分记录
            $data1=array(
                'shop_uid'=>$uid
            );

            DI()->notorm->shop_points->insert($data1);
        }

        //更新商家经营类目
        DI()->notorm->seller_goods_class->where("uid=?",$uid)->delete();
        foreach ($classid_arr as $k => $v) {
            if($v){
                $data1=array(
                    'uid'=>$uid,
                    'goods_classid'=>$v,
                    'status'=>1
                );
                DI()->notorm->seller_goods_class->insert($data1);
            }
        }
        

        return 1;
    }


	/* 商铺信息 */
	public function getShop($uid,$fields='') {

        if(!$fields){
            $fields='uid,sale_nums,quality_points,service_points,express_points,certificate,other,service_phone,province,city,area,status';
        }

        $shop_info=DI()->notorm->shop_apply
                    ->select($fields)
                    ->where('uid=?',$uid)
                    ->fetchOne();

        if(!$shop_info){
            return [];
        }

        if($uid==1){ //平台自营店铺
            $configpub=getConfigPub();
            $shop_info['user_nickname']="平台自营";
            $shop_info['name']=$configpub['site_name'].'小店';
        }else{

            //获取用户信息
            $userinfo=getUserInfo($uid);
            $shop_info['user_nickname']=$userinfo['user_nickname']; //用于进入私信聊天顶部显示昵称
            $shop_info['name']=$userinfo['user_nickname'].'的小店';
        }

        

        if($shop_info['certificate']){
           $shop_info['certificate']=get_upload_path($shop_info['certificate']);
        }

        if($shop_info['other']){
            $shop_info['other']=get_upload_path($shop_info['other']);
        }
        
        $shop_info['sale_nums']=NumberFormat($shop_info['sale_nums']);
        if($uid==1){
            $shop_info['avatar']=get_upload_path("/default.jpg");
        }else{
           $shop_info['avatar']=get_upload_path($userinfo['avatar']);
        }
        
        $shop_info['composite_points']=(string)number_format(($shop_info['quality_points']+$shop_info['service_points']+$shop_info['express_points'])/3,'1');
        $shop_info['composite_points']=$shop_info['composite_points']==0?'0.0':$shop_info['composite_points'];
        $shop_info['quality_points']=$shop_info['quality_points']>0?(string)$shop_info['quality_points']:'暂无评分';
        $shop_info['service_points']=$shop_info['service_points']>0?(string)$shop_info['service_points']:'暂无评分';
        $shop_info['express_points']=$shop_info['express_points']>0?(string)$shop_info['express_points']:'暂无评分';

        //获取店铺的上架产品总数
        $where=[];
        $where['uid']=$uid;
        $where['status']=1;

        $count=$this->countGoods($where);
        $shop_info['goods_nums']=$count;
        $shop_info['address_format']=$shop_info['city'].$shop_info['area'];

        //获取后台配置的店铺资质说明
        $configpri=getConfigPri();
        $shop_info['certificate_desc']=$configpri['shop_certificate_desc'];
            
		return $shop_info;
	}

     
    /* 自己店铺商品总数 */
    public function countGoods($where=[]){

        $nums=DI()->notorm->shop_goods
                ->where($where)
                ->count();

        
        return $nums;
    }

    /* 获取商品信息 */
    public function getGoods($where=[]){
        
        $info=[];
        
        if($where){
            $info=DI()->notorm->shop_goods
                    ->where($where)
                    ->fetchOne();
        }

        return $info;
    }

    /* 更新商品信息 */
    public function upGoods($where=[],$data=[]){
        $result=false;
        
        if($data){
            $result=DI()->notorm->shop_goods
                    ->where($where)
                    ->update($data);
        }

        return $result;
    }



    //获取商品列表
    public function getGoodsList($where,$p){
        
        if($p>1){ //强制一页返回数据
            return [];
        }

        $list=DI()->notorm->shop_goods
                ->select("id,name,thumbs,sale_nums,specs,hits,issale,type,original_price,present_price,status,live_isshow,commission")
                ->where($where)
                ->order("addtime desc")
                ->fetchAll();

        if(!$list){
            return [];
        }

        foreach ($list as $k => $v) {
            $thumb_arr=explode(',',$v['thumbs']);
            $list[$k]['thumb']=get_upload_path($thumb_arr[0]);

            
            if($v['type']==1){ //外链商品
                $list[$k]['price']=$v['present_price'];
                $list[$k]['specs']=[];
            }else{
                $spec_arr=json_decode($v['specs'],true);
                $list[$k]['price']=$spec_arr[0]['price'];
                $list[$k]['specs']=$spec_arr;
            }
 

            unset($list[$k]['thumbs']);
            unset($list[$k]['present_price']);
            unset($list[$k]['specs']);
        }


        return $list;
    }

    //获取商品评价总数
    public function getGoodsCommentNums($goodsid){
        $count=DI()->notorm->shop_order_comments->where("goodsid=? and is_append=0",$goodsid)->count();
        return $count;
    }

    //获取商品最新的三条评价
    public function getTopThreeGoodsComments($goodsid){
        $list=DI()->notorm->shop_order_comments
                ->where("goodsid=? and is_append=0",$goodsid)
                ->order("addtime desc")
                ->limit(0,3)
                ->fetchAll();


        if($list){
            foreach ($list as $k => $v) {
                $list[$k]=handleGoodsComments($v);
                $list[$k]['has_append_comment']='0';
                //获取评论的追评信息
                //$append_comment=getGoodsAppendComment($v['uid'],$v['orderid']);
                $list[$k]['append_comment']=(object)[];
                /*if($append_comment){
                    $list[$k]['has_append_comment']='1';

                    $cha=$append_comment['addtime']-$v['addtime'];

                    if($cha<24*60*60){
                        $append_comment['date_tips']='当日评论';
                    }else{
                        
                        $append_comment['date_tips']=floor($cha/(24*60*60)).'天后评论';
                    }
                    $list[$k]['append_comment']=handleGoodsComments($append_comment); 
                }*/
                
            }
        }

        return $list;
    }


    //获取商品评论列表
    public function getGoodsCommentList($uid,$goodsid,$type,$p){

        if($p<1){
            $p=1;
        }

        $pnums=50;

        $where="goodsid={$goodsid} and is_append=0";

        switch ($type) {

            case 'all':
                //$where="goodsid={$goodsid}";
                break;
            case 'img':
                $where="goodsid={$goodsid} and is_append=0 and thumbs !=''";
                break;

            case 'video':
                $where="goodsid={$goodsid} and is_append=0 and video_url !=''";
                break;

            case 'append':

                //获取有追评的评论订单ID
                $orderids=DI()->notorm->shop_order_comments->where("goodsid={$goodsid} and is_append=1")->select("orderid")->fetchAll();
                if($orderids){
                    $orderid_arr=array_column($orderids, 'orderid');

                }else{

                    return [];
                }

                
                break;
            
        }

        if($p>1){
            $goodscomment_endtime=$_SESSION['goodscomment_endtime'];
            if($goodscomment_endtime){
                $where.=" and addtime<".$goodscomment_endtime;
            }
            
        }


        if($type=='append'){

            $list=DI()->notorm->shop_order_comments
                ->where($where)
                ->where('orderid',$orderid_arr)
                ->order("addtime desc")
                ->limit(0,$pnums)
                ->fetchAll();


        }else{

            $list=DI()->notorm->shop_order_comments
                ->where($where)
                ->order("addtime desc")
                ->limit(0,$pnums)
                ->fetchAll();
        }

        foreach ($list as $k => $v) {
            $v=handleGoodsComments($v);
            $list[$k]=$v;
            $list[$k]['has_append_comment']='0';
            //获取评论的追评信息
            $append_comment=getGoodsAppendComment($v['uid'],$v['orderid']);

            $list[$k]['append_comment']=(object)[];

            if($append_comment){

                $list[$k]['has_append_comment']='1';
                $cha=$append_comment['addtime']-$v['addtime'];

                if($cha<24*60*60){
                    $append_comment['date_tips']='当日评论';
                }else{
                    
                    $append_comment['date_tips']=floor($cha/(24*60*60)).'天后评论';
                }

                $list[$k]['append_comment']=handleGoodsComments($append_comment);
            }
            
        }

        $end=end($list);
        if($end){
            $_SESSION['goodscomment_endtime']=$end['addtime'];
        }

        return $list;

    }


    //获取商品评论不同类型下的评论总数
    public function getGoodsCommentsTypeNums($goodsid){

        $data=array();

        $data['all_nums']='0';
        $data['img_nums']='0';
        $data['video_nums']='0';
        $data['append_nums']='0';

        
        $all_nums=DI()->notorm->shop_order_comments->where("goodsid=? and is_append=0",$goodsid)->count();

        $img_nums=DI()->notorm->shop_order_comments->where("goodsid=? and is_append=0 and thumbs !=''",$goodsid)->count();

        $video_nums=DI()->notorm->shop_order_comments->where("goodsid=? and is_append=0 and video_url !=''",$goodsid)->count();
        
        $append_nums=DI()->notorm->shop_order_comments->where("goodsid=? and is_append=1 ",$goodsid)->count();
                

        $data['all_nums']=$all_nums;
        $data['img_nums']=$img_nums;
        $data['video_nums']=$video_nums;
        $data['append_nums']=$append_nums;

        return $data;

    }

    public function searchShopGoods($uid,$keywords,$p){
        if($p<1){
            $p=1;
        }

        $pnums=50;
        $start=($p-1)*$pnums;

        $where="uid={$uid} and status=1";

        if($keywords!=''){
            $where.=" and name like '%".$keywords."%'";
        }

        $list=DI()->notorm->shop_goods
                ->select("id,specs,name,addtime")
                ->where($where)
                ->order("addtime desc")
                ->limit($start,$pnums)
                ->fetchAll();

        foreach ($list as $k => $v) {
            $goods_info=handleGoods($v);
            $list[$k]['price']=$goods_info['specs_format'][0]['price'];
            $list[$k]['thumb']=$goods_info['specs_format'][0]['thumb'];
            unset($list[$k]['addtime']);
            unset($list[$k]['specs']);
        }

        return $list;
    }
	
	
	/* 收藏商品 */
	public function setCollect($uid,$goodsid,$goodsuid){
		//判断收藏列表情况
		$isexist=DI()->notorm->user_goods_collect
					->select("*")
					->where('uid=? and goodsid=?',$uid,$goodsid)
					->fetchOne();
		if($isexist){
			DI()->notorm->user_goods_collect
				->where('uid=? and goodsid=?',$uid,$goodsid)
				->delete();
			return '0';
		}else{
			DI()->notorm->user_goods_collect
				->insert(array("uid"=>$uid,"goodsid"=>$goodsid,"goodsuid"=>$goodsuid,"addtime"=>time()));
				
			return '1';
		}			 
	}
	
	/* 收藏商品列表 */
	public function getGoodsCollect($uid,$p){
		
        
        $nums=50;
        $start=($p-1)*$nums;
		
		//收藏列表
		$lists=DI()->notorm->user_goods_collect
					->select("goodsid")
					->where('uid=?',$uid)
					->order('addtime desc')
					->limit($start,$nums)
					->fetchAll();

		return $lists;		 
	}
	
	
	//获取正在经营的一级商品分类
    public function getBusinessCategory($uid){
        $list=[];

        $list1=DI()->notorm->shop_goods_class->select("gc_one_id")->where("gc_grade=3")->fetchAll();
        if(!$list1){
            return [];
        }

        $gc_one_ids=array_column($list1,"gc_one_id");
        $ids=array_unique($gc_one_ids);
        $list=DI()->notorm->shop_goods_class
			->select("gc_id,gc_name,gc_isshow")
			->where("gc_id",$ids)
			->where("gc_isshow=1")
			->order("gc_sort")
			->fetchAll();
			
		//获取用户的经营类目
        $goods_classid_list=DI()->notorm->seller_goods_class
			->select("goods_classid as gc_id")
			->where("uid=? and status=1",$uid)
			->fetchAll();
		
		$goods_gc_ids=array_column($goods_classid_list,"gc_id");
        $goods_classids=array_unique($goods_gc_ids);
		
		foreach($list as $k=>$v){			
			$isexists='0';
			if(in_array($v['gc_id'],$goods_classids)){
				$isexists='1';
			}
			$list[$k]['isexists']=$isexists;
		}
		
        return $list;
    }
	
	
	//获取正在申请的经营类目
	public function getApplyBusinessCategory($uid){
		$rs = array();

		$info=DI()->notorm->apply_goods_class
			->select("goods_classid,status,reason")
			->where("uid=? and status!=1",$uid)
			->fetchOne();
			

		if($info){
			$classid_arr=explode(",",$info['goods_classid']);
			$list=[];
			foreach ($classid_arr as $k => $v) {
				$class_info=DI()->notorm->shop_goods_class
					->select("gc_id,gc_name,gc_isshow")
					->where("gc_id=?",$v)
					->fetchOne();
				if($class_info){
					$list[]=$class_info;
				}
			}
			
			
			$info['goods_class_list']=$list;
			unset($info['goods_classid']);
			$rs=$info;
		}


		return $rs;
	}
	
	
	//申请经营类目
	public function applyBusinessCategory($uid,$classid){
		
		$apply=DI()->notorm->apply_goods_class
			->where("uid=? and status=0",$uid)
			->fetchOne();
		if($apply){
			return 1001;
		}
		
		
		//申请类目,添加或修改
		$data=array(
			'uid'=>$uid,
			'goods_classid'=>$classid,
			'reason'=>'',
			'status'=>0,
			'addtime'=>time(),
		);
		
		$configpri=getConfigPri();
		$show_category_switch=$configpri['show_category_switch'];
		if(!$show_category_switch){
			$data['status']=1;
			$classids=explode(",",$classid);
			//更新用户经营类目 
			foreach ($classids as $k => $v){
				//获取一级分类的状态
				$status=DI()->notorm->shop_goods_class
					->select("gc_isshow")
					->where("gc_id=?",$v)
					->fetchOne();
				$data1=array(
					'uid'=>$uid,
					'goods_classid'=>$v,
					'status'=>$status['gc_isshow']
				);
				DI()->notorm->seller_goods_class->insert($data1);
			}
		}
		
		$apply=DI()->notorm->apply_goods_class
			->where("uid=? and status!=1",$uid)
			->update($data);
		if(!$apply){
			$apply=DI()->notorm->apply_goods_class->insert($data);
		}
		return $apply;
	}
	
	
	
	//判断商品是否删除及下架
	public function getGoodExistence($uid,$goodsid){
		
		$info=DI()->notorm->shop_goods
            ->where("id=?",$goodsid)
            ->fetchOne();

        if(($uid!=$info['uid'])&&$info['status']!=1){

            return 0;
        }
        
        return $info;
	}

    /* 代卖平台商品总数 */
    public function countPlatformSale($where){

        $nums=DI()->notorm->seller_platform_goods
                ->where($where)
                ->count();

        
        return $nums;
    }


    //获取代卖平台商品列表
    public function onsalePlatformList($where,$p){

        if($p>1){ //加$p是为了适应小程序请求，其实是一次性返回数据
            return [];
        }

        $platform_list=DI()->notorm->seller_platform_goods
            ->where($where)
            ->where("status=1 and issale=1")
            ->select("*")
            ->fetchAll();

        if(!$platform_list){
            return [];
        }

        $goodsid_arr=[];

        foreach ($platform_list as $k => $v) {
            $goodsid_arr[]=$v['goodsid'];
        }



        $list=DI()->notorm->shop_goods
                ->select("id,name,thumbs,sale_nums,specs,hits,issale,type,original_price,present_price,status,live_isshow,commission")
                ->where("id ",$goodsid_arr)
                ->order("addtime desc")
                ->fetchAll();


        if(!$list){
            return [];
        }

        $type=1;
        $list=handlePlatformGoods($list,$platform_list,$type);


        return $list;
    }

    //主播增删代售的平台商品
    public function setPlatformGoodsSale($uid,$goodsid,$issale){
        //判断用户是否代卖了该商品
        
        $where=[];
        $where['uid']=$uid;
        $where['goodsid']=$goodsid;

        $info=DI()->notorm->seller_platform_goods
        ->where($where)
        ->fetchOne();

        if(!$info){
            return 1001;
        }

        if(!$info['status']){
            return 1002;
        }

        if($issale){ //添加到直播间销售
            if($info['issale']){
                return 1004;
            }
        }else{
            if(!$info['issale']){
                return 1005;
            }
        }



        $data['issale']=$issale;
        if($issale==0){
            $data['live_isshow']=0;
        }
        

        $res=DI()->notorm->seller_platform_goods
        ->where($where)
        ->update($data);

        if(!$res){
            return 1003;
        }

        return 1;
    }

    //用户代售平台的商品搜索
    public function searchOnsalePlatformGoods($uid,$keywords,$p){
        if($p>1){
            return [];
        }

        $where=[];
        $where['uid']=$uid;
        $where['status']=1;

        $platform_list=DI()->notorm->seller_platform_goods
        ->where($where)
        ->fetchAll();

        if(!$platform_list){
            return [];
        }

        $goodsid_arr=[];
        foreach ($platform_list as $k => $v) {
            $goodsid_arr[]=$v['goodsid'];
        }

        $list=DI()->notorm->shop_goods
                ->select("id,name,thumbs,sale_nums,specs,hits,issale,type,original_price,present_price,status,live_isshow,commission")
                ->where("id ",$goodsid_arr)
                ->where("name like '%".$keywords."%'")
                ->order("addtime desc")
                ->fetchAll();


        if(!$list){
            return [];
        }

        $type=0;
        $list=handlePlatformGoods($list,$platform_list,$type);

        return $list;

    }

    //获取店铺代售平台商品列表
    public function getOnsalePlatformGoods($uid,$p){
        if($p<1){
            $p=1;
        }

        $pnums=50;

        $where="uid=".$uid." and status=1";

        if($p>1){
            $onsale_platform_addtime=$_SESSION['onsale_platform_addtime'];
            if($onsale_platform_addtime){
                $where.=" and addtime<".$onsale_platform_addtime;
            }  
        }


        $list=DI()->notorm->seller_platform_goods
        ->where($where)
        ->limit(0,$pnums)
        ->order("addtime desc")
        ->fetchAll();


        $goodsid_arr=[];
        foreach ($list as $k => $v) {
            $goodsid_arr[]=$v['goodsid'];
        }

        $goods_list=DI()->notorm->shop_goods
                ->select("id,name,thumbs,sale_nums,specs,hits,issale,type,original_price,present_price,status,live_isshow,commission")
                ->where("id ",$goodsid_arr)
                ->order("addtime desc")
                ->fetchAll();

        $goods_list=handlePlatformGoods($goods_list,[],1);

        $end=end($list);
        if($end){

            $_SESSION['onsale_platform_addtime']=$end['addtime'];
        }

        return $goods_list;
    }
	
    
    public function delGoodsCollect($uid,$new_arr){
        $res=DI()->notorm->user_goods_collect
            ->where("uid=?",$uid)
            ->where("goodsid",$new_arr)
            ->delete();

        return $res;
    }
}
