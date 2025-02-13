<?php

class Model_Message extends PhalApi_Model_NotORM {
	/* 信息列表 */
	public function getList($uid,$p) {
        if($p<1){
            $p=1;
        }
		$pnum=50;
		$start=($p-1)*$pnum;
        
		$list=DI()->notorm->pushrecord
                    ->select('content,addtime')
                    ->where("(type=0 and (touid='' or( touid!='' and (touid = '{$uid}' or touid like '{$uid},%' or touid like '%,{$uid},%' or touid like '%,{$uid}') ))) or (type=1 and touid='{$uid}') or (type=2 and touid='{$uid}')")
                    ->order('addtime desc')
                    ->limit($start,$pnum)
                    ->fetchAll();

		return $list;
	}

    //店铺订单信息列表
    public function getShopOrderList($uid,$p){
        if($p<1){
            $p=1;
        }
        $pnum=50;
        $start=($p-1)*$pnum;

        $list=DI()->notorm->shop_order_message
                ->select("title,orderid,addtime,type,is_commission")
                ->where("uid=?",$uid)
                ->order("addtime desc")
                ->limit($start,$pnum)
                ->fetchAll();

        foreach ($list as $k => $v) {
            $list[$k]['addtime']=date("Y-m-d H:i",$v['addtime']);
            $list[$k]['avatar']=get_upload_path('/orderMsg.png');

            $where['id']=$v['orderid'];
            $order_info=getShopOrderInfo($where,'status');
            $list[$k]['status']=$order_info['status'];
        }

        return $list;
    }

    /*获取粉丝关注信息列表*/
    public function fansLists($uid,$p){
        $nums=20;
        $start=($p-1)*$nums;


        $list=DI()->notorm->user_attention_messages
            ->select("*")
            ->where("touid=?",$uid)
            ->order("addtime desc")
            ->limit($start,$nums)
            ->fetchAll();


        if(!$list){
            return 0;
        }

        foreach ($list as $k => $v) {
            $list[$k]['addtime']=datetime($v['addtime']);
            unset($list[$k]['touid']);
            $userinfo=getUserInfo($v['uid']);
            $list[$k]['userinfo']=$userinfo;
            $list[$k]['isattention']=isAttention($uid,$v['uid']);
        }

        return $list;
    }

    //点赞信息列表
    public function praiseLists($uid,$p){
        $nums=20;
        $start=($p-1)*$nums;
        $list=DI()->notorm->praise_messages
            ->select("*")
            ->where("touid='{$uid}' and status=1")
            ->order("addtime desc")
            ->limit($start,$nums)
            ->fetchAll();

        if(!$list){
            return 0;
        }

        foreach ($list as $k => $v) {
            $list[$k]['addtime']=datetime($v['addtime']);
            unset($list[$k]['touid']);
            $userinfo=getUserInfo($v['uid']);
            
            $list[$k]['user_nickname']=$userinfo['user_nickname'];
            $list[$k]['avatar']=get_upload_path($userinfo['avatar']);
            $videoinfo=DI()->notorm->video
                ->select("uid")
                ->where("id=?",$v['videoid'])
                ->fetchOne();
                
            $list[$k]['videouid']=$videoinfo['uid'];
            
            $list[$k]['video_thumb']=get_upload_path($v['video_thumb']);           
        }

        return $list;
    }

    public function atLists($uid,$p){
        $nums=20;
        $start=($p-1)*$nums;
        $list=DI()->notorm->video_comments_at_messages->select("*")->where("touid='{$uid}' and status=1")->order("addtime desc")->limit($start,$nums)->fetchAll();
        if(!$list){
            return 0;
        }

        foreach ($list as $k => $v) {
            $userinfo=getUserInfo($v['uid']);
            $list[$k]['avatar']=get_upload_path($userinfo['avatar']);
            $list[$k]['user_nickname']=$userinfo['user_nickname'];
            $videoinfo=DI()->notorm->video->select("title,thumb,uid")->where("id='{$v['videoid']}'")->fetchOne();
            if(!$videoinfo['title']){
                $videoinfo['title']='视频';
            }
            $list[$k]['video_title']=$videoinfo['title'];
            $list[$k]['video_thumb']=get_upload_path($videoinfo['thumb']);
            $list[$k]['addtime']=datetime($v['addtime']);
            $list[$k]['videouid']=$videoinfo['uid'];
        }

        return $list;

    }

    public function commentLists($uid,$p){
        $nums=20;
        $start=($p-1)*$nums;
        $list=DI()->notorm->video_comments_messages
            ->select("*")
            ->where("touid='{$uid}' and status=1")
            ->order("addtime desc")
            ->limit($start,$nums)
            ->fetchAll();
        if(!$list){
            return 0;
        }

        foreach ($list as $k => $v) {
            $videoinfo=DI()->notorm->video->select("title,thumb,uid")->where("id='{$v['videoid']}'")->fetchOne();
            $list[$k]['addtime']=datetime($v['addtime']);
            $list[$k]['video_thumb']=get_upload_path($videoinfo['thumb']);   
            $userinfo=getUserInfo($v['uid']);
            $list[$k]['user_nickname']=$userinfo['user_nickname'];
            $list[$k]['avatar']=get_upload_path($userinfo['avatar']);
            $list[$k]['videouid']=$videoinfo['uid'];
            
                     
        }

        return $list;
    }

}
