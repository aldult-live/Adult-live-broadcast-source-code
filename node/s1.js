
//引入http模块
var socketio = require('socket.io'),
	fs 	= require('fs'),
	https     = require('https'),
	domain   = require('domain'),
	redis    = require('redis'),
    redisio  = require('socket.io-redis'),
    request  = require('request'),
    md5  = require('md5-node'),
    config   = require('./config.js');

var d = domain.create();

d.on("error", function(err) {
	//console.log(err);
});
 var options = {
      key: fs.readFileSync('/www/server/nginx/conf/ssl/test.bjyjzx.net.key'),
      cert: fs.readFileSync('/www/server/nginx/conf/ssl/test.bjyjzx.net.crt')
    }; 
//var numscount=0;// 在线人数统计
var sockets = {};
var chat_history={};
var chat_interval={};
/* 主播连麦关系 */
var LiveConnect={};
var LiveConnect_pull={};

// redis 链接
var clientRedis  = redis.createClient(config['REDISPORT'],config['REDISHOST']);
clientRedis.auth(config['REDISPASS']);
var server = https.createServer(options,function(req, res) {
//var server = https.createServer(function(req, res) {
	res.writeHead(200, {
		'Content-type': 'text/html;charset=utf-8'
	});
    
   //res.write("人数: " + numscount );
	res.end();
}).listen(config['socket_port'], function() {
	//console.log('服务开启19965');
});

var field=[];

var io = socketio.listen(server,{
	pingTimeout: 60000,
  	pingInterval: 25000
});

/* 多端口监听 内容统一 */
/* var pub = redis.createClient(config['REDISPORT'], config['REDISHOST'], { auth_pass: config['REDISPASS'] });
   var sub = redis.createClient(config['REDISPORT'], config['REDISHOST'], { auth_pass: config['REDISPASS'] });
 io.adapter(redisio({ pubClient: pub, subClient: sub })); */
 
 /* 资源释放 */
//setInterval(function(){
  //global.gc();
  //console.log('GC done')
//}, 1000*30); 

io.on('connection', function(socket) {
    //console.log(FormatNowDate());
	//console.log('连接成功');
	//numscount++;
							
	var interval;

    /* 获取服务端配置 */
    request(config['WEBADDRESS']+"?service=Home.getFilterField",function(error, response, body){
        if(error){
            //console.log(error);
            return;
        } ;
        if(!body){
            //console.log(body);
            return;
        } ;
        var res = evalJson(body);
        //console.log(body);
        //console.log(res.data.info);
        //console.log("sssss");
        field = res.data.info;
    });

	//进入房间
	socket.on('conn', function(data) {
		// console.log('data');
		// console.log(data);
		if(!data || !data.token){
				return !1;
		}
		
		userid=data.uid;

        if(userid<0 && userid==-9999){ //app端
            userid=data.mobileid;
            data.uid=data.mobileid;
            data.token=data.mobileid;
        }

		old_socket = sockets[userid];
		if (old_socket && old_socket != socket) {
			
			if(data.uid != data.roomnum && data.uid==old_socket.roomnum){
                /* 进房间 但旧链接是 主播 */
                var data_str='{"retmsg":"ok","retcode":"000000","msg":[{"msgtype":"1","_method_":"StartEndLive","action":"19","ct":"直播关闭"}]}';
				old_socket.emit('broadcastingListen',[data_str]);
			}else if(data.uid== data.roomnum && data.stream==old_socket.stream){
                /* 主播重连 */
				old_socket.reusing = 1;
				//console.log("重用");
			}else if(data.uid== data.roomnum && data.stream!=old_socket.stream){
                /* 主播多端开播 */
				var data_str='{"retmsg":"ok","retcode":"000000","msg":[{"msgtype":"1","_method_":"StartEndLive","action":"19","ct":"直播关闭"}]}';
				old_socket.emit('broadcastingListen',[data_str]);
			}
			old_socket.disconnect()
		}
		
		clientRedis.get(data.token,function(error,res){
            //console.log(error);
			if(error){
				return;
			}else if(res==null){
				//console.log("[获取token失败]"+data.uid);
			}else{
				if(res != null){
					
					var userInfo = evalJson(res);
					//console.log("用户进入直播间-----------"+res); 
					if(userInfo['id'] == data.uid ){
						//console.log("[初始化验证成功]--"+data.uid+"---"+data.roomnum+'---'+data.stream);
						//获取验证token
						socket.token   = data.token; 
						socket.roomnum = data.roomnum;
						socket.stream = data.stream;
						socket.nickname = userInfo['user_nickname'];
						socket.level = userInfo['level'];
						socket.avatar = userInfo['avatar'];
						socket.sign = Number(userInfo['sign']);
						socket.usertype   = parseInt(userInfo['usertype']);
						socket.uid     = data.uid;
						socket.reusing = 0;
						
						socket.join(data.roomnum);
						sockets[userid] = socket;
						socket.emit('conn',['ok']);
                        
						if( socket.roomnum!=socket.uid && socket.uid >0 ){
                            // 处理有时 无座驾信息会崩溃的问题 
                            var car_id='0';
                            var car_swf='';
                            var car_swftime='';
                            var car_words='';
                            if(userInfo.hasOwnProperty("car")){
                                var carinfo=userInfo['car'];
                                if(carinfo.hasOwnProperty("id")){
                                    car_id=carinfo['id'];
                                }
                                if(carinfo.hasOwnProperty("swf")){
                                    car_swf=carinfo['swf'];
                                }
                                if(carinfo.hasOwnProperty("swftime")){
                                    car_swftime=carinfo['swftime'];
                                }
                                if(carinfo.hasOwnProperty("words")){
                                    car_words=carinfo['words'];
                                }
                            }
                            
							var data_obj={
                                            "msg":[
                                                {
                                                    "_method_":"SendMsg",
                                                    "action":"0",
                                                    "ct":{
                                                        "id":''+userInfo['id'],
                                                        "user_nickname":''+userInfo['user_nickname'],
                                                        "avatar":userInfo['avatar'],
                                                        "avatar_thumb":userInfo['avatar_thumb'],
                                                        "level":''+userInfo['level'],
                                                        "usertype":''+userInfo['usertype'],
                                                        "vip_type":''+userInfo['vip']['type'],
                                                        "guard_type":''+userInfo['guard_type'],
                                                        "liangname":''+userInfo['liang']['name'],
                                                        "car_id":''+car_id,
                                                        "car_swf":''+car_swf,
                                                        "car_swftime":''+car_swftime,
                                                        "car_words":''+car_words
                                                    },
                                                    "msgtype":"0"
                                                }
                                            ],
                                            "retcode":"000000",
                                            "retmsg":"OK"
                                        };
							process_msg(io,socket.roomnum,JSON.stringify(data_obj));
							if(socket.stream){
								clientRedis.zadd('user_'+socket.stream,socket.sign,userInfo['id']);	
							}
						}						
						 
						// sendSystemMsg(socket,"直播内容包含任何低俗、暴露和涉黄内容，账号会被封禁；安全部门会24小时巡查哦～");
                        sendSystemMsg(socket,"欢迎来到直播间,我们倡导绿色直播。直播内容和封面有违法违规、色情低俗、抽烟喝酒、诱导欺诈、聚众闹事等行为账号会被封禁,网警24小时在线巡查哦！");
						return;
					}else{
						socket.disconnect();
					}
				}
			}
			
			socket.emit('conn',['no']);
		});
        
		
	});

	socket.on('broadcast',function(data){
           // console.log(data);
		    if(socket.token != undefined){
		    	var dataObj  = typeof data == 'object'?data:evalJson(data);
			    var msg      = dataObj['msg'][0]; 
			    var token    = dataObj['token'];
				var method   = msg['_method_'];
			    var action   = msg['action'];
			    var data_str =  typeof data == 'object'?JSON.stringify(data):data;
			    switch(method){
			    	case 'SendMsg':{     //聊天

                        dataObj['msg'][0]['ct']=filter(dataObj['msg'][0]['ct']);
						clientRedis.hget( "super",socket.uid,function(error,res){
							if(error) return;
                            
							if(res != null){
                                
								var data_str2={
                                                "msg":[
                                                    {
                                                        "_method_":"SystemNot",
                                                        "action":"1",
                                                        "ct":''+dataObj['msg'][0]['ct'],
                                                        "msgtype":"4"
                                                    }
                                                ],
                                                "retcode":"000000",
                                                "retmsg":"OK"
                                            };

								process_msg(io,socket.roomnum,JSON.stringify(data_str2));
		    				}else{

                                data_str=JSON.stringify(dataObj);

								clientRedis.hget(socket.roomnum + "shutup",socket.uid,function(error,res){
									if(error) return;
									if(res != null){
                                        var newData  = dataObj;
                                        newData['retcode'] = '409002';
                                        socket.emit('broadcastingListen',[JSON.stringify(newData)]);
									}else{
										process_msg(io,socket.roomnum,data_str);
									}	
								});
		    				}							
						});
			    		break;
			    	}
			    	case 'SendGift':{    //送礼物
						var gifToken = dataObj['msg'][0]['ct'];
			    		clientRedis.get(gifToken,function(error,res){
			    			if(!error&&res != null){
			    				
                                var sendGiftObj = evalJson(res);
                                //console.log(sendGiftObj);

                                //console.log(sendGiftObj);

                                var len=sendGiftObj.length;
                                //console.log("长度:"+len);

                                for (var i = 0; i < len; i++) {
                                    var  resObj= sendGiftObj[i];

                                    dataObj['msg'][0]['ct'] = resObj;
                                    var ifpk='0',
                                    pkuid1='0',
                                    pkuid2='0',
                                    pktotal1='0',
                                    pktotal2='0';
                                
                                    //console.log('SendGift');
                                    //console.log(resObj);
                                    if(resObj['ispk']==1 ){
                                        ifpk='1';
                                        pkuid1=''+resObj['pkuid1'];
                                        pkuid2=''+resObj['pkuid2'];
                                        pktotal1=''+resObj['pktotal1'];
                                        pktotal2=''+resObj['pktotal2'];
                                        //console.log('pk');
                                    }
                                    
                                    dataObj['msg'][0]['ifpk']=ifpk;
                                    dataObj['msg'][0]['pkuid1']=pkuid1;
                                    dataObj['msg'][0]['pkuid2']=pkuid2;
                                    dataObj['msg'][0]['pktotal1']=pktotal1;
                                    dataObj['msg'][0]['pktotal2']=pktotal2;
                                    
                                    //console.log('dataObj');
                                    //console.log(dataObj);
                                    if(resObj['isplatgift']=='1'){//全站礼物
                                     
                                         var platdata_obj={
                                                "msg":[
                                                    {
                                                        "_method_":"Sendplatgift",
                                                        "action":"0",
                                                        "giftcount":resObj['giftcount'],
                                                        "giftname":resObj['giftname'],
                                                        "uname":dataObj['msg'][0]['uname'],
                                                        "livename":dataObj['msg'][0]['livename'],
                                                        "liveuid":socket.roomnum,
                                                        "stream":socket.stream,
                                                        "msgtype":'0',
                                                    }
                                                ],
                                                "retcode":"000000",
                                                "retmsg":"OK"
                                            };
                                         io.emit('broadcastingListen',[JSON.stringify(platdata_obj)]);  
                                         // io.emit('broadcastingListen',['{"msg":[{"_method_":"Sendplatgift","action":"0","giftcount":"'+resObj['giftcount']+'","giftname":"'+resObj['giftname']+'","uname":"'+dataObj['msg'][0]['uname']+'","livename":"'+dataObj['msg'][0]['livename']+'","liveuid":"'+socket.roomnum+'","stream":"'+socket.stream+'","msgtype":"0"}],"retcode":"000000","retmsg":"OK"}']); 
                                    }
                                    
                                    io.sockets.in(socket.roomnum).emit('broadcastingListen',[JSON.stringify(dataObj)]);
                                    if(pkuid2>0){
                                        io.sockets.in(pkuid2).emit('broadcastingListen',[JSON.stringify(dataObj)]);
                                    }
                                    
                                    /* 幸运中奖 */
                                    if(resObj.isluck==1 && resObj.isluckall==1 ){
                                        var data_luck={
                                            "msg":[
                                                {
                                                    "_method_":"luckWin",
                                                    "action":"1",
                                                    "uid":''+ msg.uid,
                                                    "uname":''+ msg.uname,
                                                    "uhead":''+ msg.uhead,
                                                    "giftid":''+ resObj.giftid,
                                                    "giftname":''+ resObj.giftname,
                                                    "lucktimes":''+ resObj.lucktimes,
                                                    "msgtype":"4"
                                                }
                                            ],
                                            "retcode":"000000",
                                            "retmsg":"OK"
                                        };
                                        io.emit('broadcastingListen',[JSON.stringify(data_luck)]);
                                    }
                                    /* 幸运中奖 */
                                    
                                    /* 奖池升级 */
                                    if(resObj.isup==1){
                                        var data_up={
                                            "msg":[
                                                {
                                                    "_method_":"jackpotUp",
                                                    "action":"1",
                                                    "uplevel":''+ resObj.uplevel,
                                                    "msgtype":"4"
                                                }
                                            ],
                                            "retcode":"000000",
                                            "retmsg":"OK"
                                        };
                                        io.emit('broadcastingListen',[JSON.stringify(data_up)]);
                                    }
                                    /* 奖池升级 */
                                    
                                    /* 奖池中奖 */
                                    if(resObj.iswin==1){
                                        var data_win={
                                            "msg":[
                                                {
                                                    "_method_":"jackpotWin",
                                                    "action":"1",
                                                    "uid":''+ msg.uid,
                                                    "uname":''+ msg.uname,
                                                    "uhead":''+ msg.uhead,
                                                    "wincoin":''+ resObj.wincoin,
                                                    "msgtype":"4"
                                                }
                                            ],
                                            "retcode":"000000",
                                            "retmsg":"OK"
                                        };
                                        io.emit('broadcastingListen',[JSON.stringify(data_win)]);
                                        
                                        var data_up={
                                            "msg":[
                                                {
                                                    "_method_":"jackpotUp",
                                                    "action":"1",
                                                    "uplevel":'0',
                                                    "msgtype":"4"
                                                }
                                            ],
                                            "retcode":"000000",
                                            "retmsg":"OK"
                                        };
                                        io.emit('broadcastingListen',[JSON.stringify(data_up)]);
                                    }
                                    /* 奖池中奖 */




                                }

			    				
                                
			    				clientRedis.del(gifToken);
			    			}
			    		});
			    		break;
			    	}
					case 'SendBarrage':{    //弹幕
						var barragetoken = dataObj['msg'][0]['ct'];
			    		clientRedis.get(barragetoken,function(error,res){
			    			if(!error&&res != null){
			    				var resObj = evalJson(res);

                                var con=resObj.content;
                                var res = filter(con);
                                resObj.content=res;

			    				dataObj['msg'][0]['ct'] = resObj;
								var data_str=JSON.stringify(dataObj);
								process_msg(io,socket.roomnum,data_str);
			    				clientRedis.del(barragetoken);
			    			}	
			    		});
			    		break;
			    	}
					case 'ConnectVideo' :{ //用户和主播连麦
                        //1：发起连麦；2；接受连麦；3:拒绝连麦；4：连麦成功通知；5.发起者断开连麦；6：主播断开连麦;7:主播正忙碌 8:主播无响应
                        if(action=='5' || action=='6'){
                            clientRedis.hget('ShowVideo',socket.roomnum,function(error,res){
                                if(error){
                                    return !1;
                                }
                                if(!res){
                                    return !1;
                                }
                                var res_j=JSON.parse(res);
                                
                                if( socket.uid==res_j['uid'] || socket.uid==socket.roomnum ){
                                    clientRedis.hdel('ShowVideo',socket.roomnum);
                                    process_msg(io,socket.roomnum,data_str);									
                                }							 
                            });                           
                        }else if(action=='2'){
                            //console.log('主播同意连麦');
                            var touid=msg['touid'];
							//console.log(touid);
                            
                            request(config['WEBADDRESS']+"?service=Live.showVideo&uid="+socket.uid + "&token=" + socket.token+ "&touid="+touid+"&pull_url=''",function(error, response, body){
                                //console.log('showVideo');
                                //console.log(body);
                            });
                            
							process_msg(io,socket.roomnum,data_str);
                        }else{
                            process_msg(io,socket.roomnum,data_str);
                        }
	                    break;
			    	}
					case 'light' :{     //点亮
						process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}
					case 'changeLive' :{//切换房间收费
						process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}
					case 'updateVotes' :{//更新映票
						process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}
			    	/*case 'CloseLive' :{//关闭直播
			    		if(socket.usertype == 50 ){
							process_msg(io,socket.roomnum,data_str);
			    	    }
	                    break;
			    	}*/
			    	case 'KickUser' :{//踢人
						process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}
			    	case 'ShutUpUser' :{//禁言
						process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}
					case 'stopLive' :{//超管关播
						clientRedis.hget( "super",socket.uid,function(error,res){
							if(error) return;
							if(res != null){
								process_msg(io,socket.roomnum,'stopplay');								
		    				}							
						});
						break;
			    	}
			    	case 'ResumeUser' :{//恢复发言
			    		if(socket.usertype == 50 || socket.usertype == 40){
							process_msg(io,socket.roomnum,data_str);
			    	    }
			    	    break;
			    	} 
                    case 'setAdmin' :{//设置/取消管理员
			    		if(socket.usertype == 50 ){
							process_msg(io,socket.roomnum,data_str);
			    	    }
			    	    break;
			    	} 
			    	case 'StartEndLive':{
			    		if(socket.usertype == 50 ){
			    		   socket.broadcast.to(socket.roomnum).emit('broadcastingListen',[data_str]);
			    	    }else{
			    	    	clientRedis.get("LiveAuthority" + socket.uid,function(error,res){
			    	    		if(error) return;
			    	    		if(parseInt(res) == 5 ||parseInt(res) == 1 || parseInt(res) == 2){
		    	    				socket.broadcast.to(socket.roomnum).emit('broadcastingListen',[data_str]);
		    	    			}
			    	    	})
			    	    }
			    	    break;

			    	}
			    	case 'BuyGuard':{//购买守护
						process_msg(io,socket.roomnum,data_str);
			    		break;
			    	}
                    case 'SendRed' :{//送红包
						process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}
                    case 'LiveConnect':{//主播和主播连麦
                        if(socket.roomnum != socket.uid){
                            //非主播不能操作
                            return !1;
                        }
                        var pkuid=msg['pkuid'];
                        var pkpull=msg['pkpull'];

                        //1：发起连麦；2；接受连麦;3:拒绝连麦;4：连麦成功通知;5.手动断开连麦;7:对方正忙碌 8:对方无响应 9:主播繁忙（游戏）
                        if(action=='1'){
                            // console.log('发起连麦');
                            // console.log(FormatNowDate());
                            // console.log(socket.uid+'---'+pkuid);
                            // console.log(pkpull);
                            // console.log('LiveConnect_pull--'+socket.uid+'-----'+pkpull);
                            LiveConnect_pull[socket.uid]=pkpull;
                            clientRedis.hset('LiveConnect_pull',socket.uid,pkpull);
                        }
                        if(action=='2'){
                            // console.log('接受连麦');
                            // console.log(FormatNowDate());
                            // console.log(socket.uid+'---'+pkuid);
                            // console.log(pkpull);
                            // console.log('LiveConnect_pull--'+socket.uid+'-----'+pkpull);
                            LiveConnect_pull[socket.uid]=pkpull;
                            clientRedis.hset('LiveConnect_pull',socket.uid,pkpull);

                            /* 更新数据库 */
                            var sign_data={uid:socket.uid,pkuid:pkuid,type:1};
                            var sign=setSign(sign_data);
                            request(config['WEBADDRESS']+"?service=Livepk.changeLive&uid="+socket.uid + "&pkuid=" + pkuid+ "&type=1&sign=" +sign,function(error, response, body){
                                // console.log('changeLive');
                                // console.log(body);
                            });
                            
                            /* 发送连麦成功信息 */
                            /* 当前房间 */
                            var data_obj={
                                        "msg":[
                                            {
                                                "_method_":"LiveConnect",
                                                "action":"4",
                                                "msgtype":"10",
                                                "pkuid":""+pkuid,
                                                "pkpull":""+LiveConnect_pull[pkuid],
                                                "uid":""+socket.uid,
                                                "uname":""+socket.nicename
                                            }
                                        ],
                                        "retcode":"000000",
                                        "retmsg":"OK"
                                    };
                            // console.log(data_obj);
                            process_msg(io,socket.roomnum,JSON.stringify(data_obj));
                            /* 对方房间 */
                            var data_obj_pk={
                                        "msg":[
                                            {
                                                "_method_":"LiveConnect",
                                                "action":"4",
                                                "msgtype":"10",
                                                "pkuid":""+socket.uid,
                                                "pkpull":""+LiveConnect_pull[socket.uid],
                                                "uid":""+socket.uid,
                                                "uname":""+socket.nicename
                                            }
                                        ],
                                        "retcode":"000000",
                                        "retmsg":"OK"
                                    };
                            // console.log(data_obj_pk);
                            process_msg(io,pkuid,JSON.stringify(data_obj_pk));
                            
                        }else if(action=='5'){ //主播和主播断开连麦
                            /* 清除连麦信息 */
                            //console.log('endLiveConnect--action--5');
                            endLiveConnect(io,socket.uid);
                        }else{
                            var socket_pkuid=sockets[pkuid];
                            if(socket_pkuid){
                                socket_pkuid.emit('broadcastingListen',[data_str]);
                            }
                            
                        }
	                    break;
			    	}
                    case 'LivePK':{//主播PK
                        if(socket.roomnum != socket.uid){
                            //非主播不能操作
                            return !1;
                        }
                        var uid=msg['uid'];
                        var pkuid=msg['pkuid'];
                        //1：发起连麦；2；接受连麦;3:拒绝连麦;4：连麦成功通知;5.手动断开连麦;7:对方正忙碌 8:对方无响应; 9:PK结果
                        if(action=='1'){
                            // console.log('发起PK');
                            // console.log(FormatNowDate());
                            // console.log(socket.uid+'---'+pkuid);
                        }
                        if(action=='2'){
                            /* 更新PK状态 */
                            // console.log('LivePK');
                            // console.log(socket.uid);
                            // console.log(pkuid);
                            // console.log('开始PK');
                            // console.log(FormatNowDate());
                            // console.log(socket.uid+'---'+pkuid);
                            var sign_data={uid:socket.uid,pkuid:pkuid};
                            var sign=setSign(sign_data);
                            request(config['WEBADDRESS']+"?service=Livepk.setPK&uid="+socket.uid + "&pkuid=" + pkuid+ "&sign=" +sign,function(error, response, body){
                                if(error) return;
                                // console.log('setPK');
                                // console.log(body);
                                var res = evalJson(body);
                                if( response.statusCode == 200 && res.data.code == 0){
                                    
                                    var info = res.data.info[0];
                                    // console.log('info');
                                    // console.log(info);
                                    /* 发送连麦成功信息 */
                                    /* 当前房间 */
                                    var data_obj={
                                                "msg":[
                                                    {
                                                        "_method_":"LivePK",
                                                        "action":"4",
                                                        "msgtype":"10",
                                                        "pkuid":""+pkuid,
                                                        "uid":""+socket.uid,
                                                        "uname":""+socket.nicename
                                                    }
                                                ],
                                                "retcode":"000000",
                                                "retmsg":"OK"
                                            };
                                    
                                    process_msg(io,socket.roomnum,JSON.stringify(data_obj));
                                    /* 对方房间 */
                                    var data_obj_pk={
                                                "msg":[
                                                    {
                                                        "_method_":"LivePK",
                                                        "action":"4",
                                                        "msgtype":"10",
                                                        "pkuid":""+socket.uid,
                                                        "uid":""+socket.uid,
                                                        "uname":""+socket.nicename
                                                    }
                                                ],
                                                "retcode":"000000",
                                                "retmsg":"OK"
                                            };
                                    process_msg(io,pkuid,JSON.stringify(data_obj_pk));
                                    
                                    setTimeout(function() {//定时发送结果
                                        // console.log('计时器');
                                        // console.log(FormatNowDate());
                                        // console.log('uid---'+socket.uid);
                                        endLivePk(io,socket.uid,0,info['addtime']);
                                    }, 5*60*1000);
                                }

                            });
                        }else if(action=='5'){
                            /* 清除PK信息 */
                            // console.log('endLivePk --action--5');
                            // console.log(FormatNowDate());
                            endLivePk(io,socket.uid,1,0);
                        }else{
                            var socket_pkuid=sockets[pkuid];
                            if(socket_pkuid){
                                socket_pkuid.emit('broadcastingListen',[data_str]);
                            }
                            //process_msg(io,pkuid,data_str);
                        }
	                    break;
			    	}
			    	case 'SystemNot':{//系统通知
						process_msg(io,socket.roomnum,data_str);
			    		break;
			    	}
                    case 'shangzhuang' :{//上、下庄
						process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}

                    case 'goodsLiveShow' :{//商品在直播间展示/不展示
                        process_msg(io,socket.roomnum,data_str);
                        break;
                    }

					case 'startGame':{//炸金花游戏
						process_msg(io,socket.roomnum,data_str);
						if(action==4)
						{
							var time=msg['time']*1000;
							var gameid=msg['gameid'];
							setTimeout(function() {//定时发送结果
                                request(config['WEBADDRESS']+"?service=Game.endGame&liveuid="+socket.uid + "&token=" + socket.token+ "&gameid=" + gameid+"&type=1",function(error, response, body){
                                    if(error) return;
                                    var res = evalJson(body);
                                    if( response.statusCode == 200 && res.data.code == 0){
                                        var resObj = res.data.info;
                                        dataObj['msg'][0]['ct'] = resObj;
                                        dataObj['msg'][0]['_method_'] = "startGame";
                                        dataObj['msg'][0]['action']="6";
                                        var data_str2=JSON.stringify(dataObj);
                                        process_msg(io,socket.roomnum,data_str2);
                                    }
                                });
							}, time);
						}
						break;
					}
					case 'startRotationGame':{//转盘
						process_msg(io,socket.roomnum,data_str);
						if(action==4)
						{
							var time=msg['time']*1000;
							var gameid=msg['gameid'];
							setTimeout(function() {//定时发送结果
                                request(config['WEBADDRESS']+"?service=Game.endGame&liveuid="+socket.uid + "&token=" + socket.token+ "&gameid=" + gameid+"&type=1",function(error, response, body){
                                    if(error) return;
                                    //console.log(body);
                                    var res = evalJson(body);
                                                    
                                    if( response.statusCode == 200 && res.data.code == 0){
                                        var resObj = res.data.info;
                                        dataObj['msg'][0]['ct'] = resObj;
                                        dataObj['msg'][0]['_method_'] = "startRotationGame";
                                        dataObj['msg'][0]['action']="6";
                                        var data_str2=JSON.stringify(dataObj);
                                        process_msg(io,socket.roomnum,data_str2);
                                    }
                                });
							}, time);
						}
						break;
					}
					case 'startCattleGame':{//开心牛仔
						process_msg(io,socket.roomnum,data_str);
						if(action==4)
						{
							var time=msg['time']*1000;
							var gameid=msg['gameid'];
							setTimeout(function() {//定时发送结果
                                request(config['WEBADDRESS']+"?service=Game.endGame&liveuid="+socket.uid + "&token=" + socket.token+ "&gameid=" + gameid+"&type=1",function(error, response, body){
                                    if(error) return;
                                    var res = evalJson(body);

                                    if( response.statusCode == 200 && res.data.code == 0){
                                        var resObj = res.data.info;
                                        dataObj['msg'][0]['ct'] = resObj;
                                        dataObj['msg'][0]['_method_'] = "startCattleGame";
                                        dataObj['msg'][0]['action']="6";
                                        var data_str2=JSON.stringify(dataObj);
                                        process_msg(io,socket.roomnum,data_str2);
                                    }
                                });
							}, time);
						}
						break;
					}
					case 'startLodumaniGame':{//海盗船长
						process_msg(io,socket.roomnum,data_str);
						if(action==4)
						{
							var time=msg['time']*1000;
							var gameid=msg['gameid'];
							setTimeout(function() {//定时发送结果
                                request(config['WEBADDRESS']+"?service=Game.endGame&liveuid="+socket.uid + "&token=" + socket.token+ "&gameid=" + gameid+"&type=1",function(error, response, body){
                                    if(error) return;
                                    var res = evalJson(body);
                                    
                                    if( response.statusCode == 200 && res.data.code == 0){
                                        var resObj = res.data.info;
                                        dataObj['msg'][0]['ct'] = resObj;
                                        dataObj['msg'][0]['_method_'] = "startLodumaniGame";
                                        dataObj['msg'][0]['action']="6";
                                        var data_str2=JSON.stringify(dataObj);
                                        process_msg(io,socket.roomnum,data_str2);
                                    }
                                });
							}, time);
						}
						break;
					}
					case 'startShellGame':{//二八贝
						process_msg(io,socket.roomnum,data_str);
						if(action==4)
						{
							var time=msg['time']*1000;
							var gameid=msg['gameid'];
							setTimeout(function() {//定时发送结果
                                request(config['WEBADDRESS']+"?service=Game.endGame&liveuid="+socket.uid + "&token=" + socket.token+ "&gameid=" + gameid+"&type=1",function(error, response, body){
                                    if(error) return;
                                    var res = evalJson(body);
                                    if( response.statusCode == 200 && res.data.code == 0){
                                        var resObj = res.data.info;
                                        dataObj['msg'][0]['ct'] = resObj;
                                        dataObj['msg'][0]['_method_'] = "startShellGame";
                                        dataObj['msg'][0]['action']="6";
                                        var data_str2=JSON.stringify(dataObj);
                                        process_msg(io,socket.roomnum,data_str2);
                                    }
                                });
							}, time);
						}
						break;
					}
			    	case 'requestFans':{
							request(config['WEBADDRESS']+"?service=Live.getZombie&stream=" + socket.stream+"&uid=" + socket.uid,function(error, response, body){
								if(error) return;
								var res = evalJson(body);
                                //console.log(res);
								if( response.statusCode == 200 && res.data.code == 0){
									var data_str2="{\"msg\":[{\"_method_\":\"requestFans\",\"action\":\"3\",\"ct\": "+ body + ",\"msgtype\":\"0\"}],\"retcode\":\"000000\",\"retmsg\":\"OK\"}";
									process_msg(io,socket.roomnum,data_str2);
								}
							});

			    	}
					case 'shopGoodsLiveFloat':{    //直播间购物飘屏
						process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}

                    case 'voiceRoom' :{//语音聊天室
                        process_msg(io,socket.roomnum,data_str);
                        break;
                    }
						
			    }
		    }
		    
	});
	
	socket.on('superadminaction',function(data){
    	if(data['token'] == config['TOKEN']){
            io.sockets.in(data['roomnum']).emit("broadcastingListen", ['stopplay']);
    	}
    });



    socket.on('warning',function(data){
        
        if(data['token'] == config['TOKEN']){
            var data_obj={
                "msg":[
                    {
                        "_method_":"warning",
                        "ct":"您已涉及违规,请立即改正否则将会关闭直播间"
                    }
                ],
                "retcode":"000000",
                "retmsg":"OK"
            };

            process_msg(io,data['roomnum'],JSON.stringify(data_obj));
        }
        
    });


	/* 系统信息 */
	socket.on('systemadmin',function(data){
    	if(data['token'] == config['TOKEN']){
            var data_obj={
                            "msg":[
                                {
                                    "_method_":"SystemNot",
                                    "action":"1",
                                    "ct":''+ data.content,
                                    "msgtype":"4"
                                }
                            ],
                            "retcode":"000000",
                            "retmsg":"OK"
                        };
    		io.emit('broadcastingListen',[JSON.stringify(data_obj)]);
    	}
    });
	
    //资源释放
	socket.on('disconnect', function() { 
        // console.log(FormatNowDate());
        // console.log('disconnect');
			/* numscount--; 
            if(numscount<0){
				numscount=0;
			}   */
          			
			if(socket.roomnum ==null || socket.token==null || socket.uid <=0){
				return !1;
			}
				
			d.run(function() {
				/* 用户连麦 */
				clientRedis.hget('ShowVideo',socket.roomnum,function(error,res){
                    if(error){
                        return !1;
                    }
                    if(!res){
                        return !1;
                    }
                    var res_j=JSON.parse(res);
                    
                    if( socket.uid == res_j['uid'] || socket.uid == socket.roomnum ){
                        clientRedis.hdel('ShowVideo',socket.roomnum);
                        var data_obj={
                                        "msg":[
                                            {
                                                "_method_":"ConnectVideo",
                                                "action":"5",
                                                "msgtype":"10",
                                                "uid":""+socket.uid,
                                                "uname":""+socket.nicename
                                            }
                                        ],
                                        "retcode":"000000",
                                        "retmsg":"OK"
                                    };
                        process_msg(io,socket.roomnum,JSON.stringify(data_obj));									
                    }	
					 
				});
				
				
				if(socket.roomnum==socket.uid){
					/* 主播 */ 
					if(socket.reusing==0){
						request(config['WEBADDRESS']+"?service=Live.stopRoom&uid="+socket.uid + "&token=" + socket.token+ "&type=1&source=socket&stream=" + socket.stream,function(error, response, body){
                            var data_obj={
                                        "retmsg":"ok",
                                        "retcode":"000000",
                                        "msg":[
                                            {
                                                "msgtype":"1",
                                                "_method_":"StartEndLive",
                                                "action":"18",
                                                "ct":"直播关闭"
                                            }
                                        ]
                                    };
                            process_msg(io,socket.roomnum,JSON.stringify(data_obj));
                            // console.log('关播');
                            // console.log(FormatNowDate());
                            // console.log('uid---'+socket.uid);
                        });
                        endLiveConnect(io,socket.uid);
					}
                    
                    
                    
				}else{

					/* 观众 */
                    clientRedis.zrem('user_'+socket.stream,socket.uid,function(error,res){
						if(error) return;
						if(res){
							//用于每日任务--用户观看直播时长统计
							request(config['WEBADDRESS']+"?service=Live.signOutWatchLive&uid="+socket.uid + "&token=" + socket.token,function(error, response, body){});
							var data_obj={
                                            "msg":[
                                                {
                                                    "_method_":"disconnect",
                                                    "action":"1",
                                                    "ct":{
                                                        "id":''+socket.uid,
                                                        "user_nickname":''+socket.nickname,
                                                        "avatar":socket.avatar,
                                                        "level":''+socket.level
                                                    },
                                                    "msgtype":"0",
                                                    "uid":''+socket.uid,
                                                    "uname":socket.nicename
                                                }
                                            ],
                                            "retcode":"000000",
                                            "retmsg":"OK"
                                        };
							process_msg(io,socket.roomnum,JSON.stringify(data_obj));	
						}
						
					});

                    clientRedis.del(socket.token);
                    clientRedis.hdel('super',socket.uid);

                    //用户退出房间要将上麦记录删除
                    request(config['WEBADDRESS']+"?service=Live.userCloseVoiceMic&uid="+socket.uid + "&token=" + socket.token + "&stream="+socket.stream,function(error, response, body){});
                    var data_obj={
                                    "msg":[
                                        {
                                            "_method_":"voiceRoom",
                                            "action":"3",
                                            "uid":''+socket.uid,
                                            "type":"0"
                                        }
                                    ],
                                    "retcode":"000000",
                                    "retmsg":"OK"
                                };
                    process_msg(io,socket.roomnum,JSON.stringify(data_obj));

					
				}
				//console.log(socket.roomnum+"==="+socket.token+"===="+socket.uid+"======"+socket.stream);
				
				socket.leave(socket.roomnum);
				delete io.sockets.sockets[socket.id];
				sockets[socket.uid] = null;
				delete sockets[socket.uid];

			});
	});

});
function sendSystemMsg(socket,msg){
    var data_obj={
                    "msg":[
                        {
                            "_method_":"SystemNot",
                            "action":"1",
                            "ct":""+ msg,
                            "msgtype":"4"
                        }
                    ],
                    "retcode":"000000",
                    "retmsg":"OK"
                };
	socket.emit('broadcastingListen',[JSON.stringify(data_obj)]);
						
}
function evalJson(data){
	return eval("("+data+")");
}

function process_msg(io,roomnum,data){
	if(!chat_history[roomnum]){
		chat_history[roomnum]=[];
	}
	chat_history[roomnum].push(data);
	chat_interval[roomnum] || (chat_interval[roomnum]=setInterval(function(){
		if(chat_history[roomnum].length>0){
			send_msg(io,roomnum);
		}else{
			clearInterval(chat_interval[roomnum]);
			chat_interval[roomnum]=null;
		}
	},200));
}

function send_msg(io,roomnum){
	var data=chat_history[roomnum].splice(0,chat_history[roomnum].length);
    io.sockets.in(roomnum).emit("broadcastingListen", data);
}

/* 主播连麦结束处理 */
function endLiveConnect(io,uid){
    
    //console.log('结束连麦');
    //console.log(FormatNowDate());
    //console.log('uid--'+uid);
    clientRedis.hget('LiveConnect',uid,function(error,res){
        //console.log('res');
        if(error){
            return !1;
        }
        if(!res){
            return !1;
        }
        var pkuid=res;
        //console.log('pkuid---'+pkuid);
        /* 更新数据库 */
        var sign_data={uid:uid,pkuid:pkuid,type:0};
        var sign=setSign(sign_data);
        request(config['WEBADDRESS']+"?service=Livepk.changeLive&uid="+uid + "&pkuid=" + pkuid+ "&type=0&sign=" +sign,function(error, response, body){
            if(error) return;
            //console.log('changeLive');
            //console.log(body);
            var res = evalJson(body);
            if( response.statusCode == 200 && res.data.code == 0){
                var data_obj={
                        "msg":[
                            {
                                "_method_":"LiveConnect",
                                "action":"5",
                                "msgtype":"10",
                                "uid":""+uid,
                                "uname":""
                            }
                        ],
                        "retcode":"000000",
                        "retmsg":"OK"
                    };
        
                process_msg(io,uid,JSON.stringify(data_obj));
                process_msg(io,pkuid,JSON.stringify(data_obj)); 
                
            }        
            
        });	
         
    });
        
}
/* PK结束处理 */
function endLivePk(io,uid,type,addtime){
    // console.log('结束PK');
    // console.log('uid-'+uid);
    // console.log('addtime-'+addtime);
    // console.log('type-'+type);
    var sign_data={uid:uid,addtime:addtime,type:type};
    var sign=setSign(sign_data);
    request(config['WEBADDRESS']+"?service=Livepk.endPK&uid="+uid + "&addtime=" + addtime+ "&type=" + type+ "&sign=" +sign,function(error, response, body){
        if(error) return;
        // console.log('endPK');
        // console.log(body);
        var res = evalJson(body);
        if( response.statusCode == 200 && res.data.code == 0){
            var info=res.data.info[0];
            var data_obj={
                    "msg":[
                        {
                            "_method_":"LivePK",
                            "action":"9",
                            "msgtype":"10",
                            "win_uid":""+info['win_uid'],
                            "uid":""+uid,
                            "uname":""
                        }
                    ],
                    "retcode":"000000",
                    "retmsg":"OK"
                };
    
            process_msg(io,uid,JSON.stringify(data_obj));
            process_msg(io,info['pkuid'],JSON.stringify(data_obj));
            
        }
        
    });
    
}
//时间格式化
function FormatNowDate(){
	var mDate = new Date();
	var Y = mDate.getFullYear();
	var M = mDate.getMonth()+1;
	var D = mDate.getDate();
	var H = mDate.getHours();
	var i = mDate.getMinutes();
	var s = mDate.getSeconds();
	return Y +'-' + M + '-' + D + ' ' + H + ':' + i + ':' + s;
}

/* sign加密 */
function setSign(obj) {//排序的函数
    var str='';
    var newkey = Object.keys(obj).sort();
//先用Object内置类的keys方法获取要排序对象的属性名，再利用Array原型上的sort方法对获取的属性名进行排序，newkey是一个数组
    var newObj = {};//创建一个新的对象，用于存放排好序的键值对
    for (var i = 0; i < newkey.length; i++) {//遍历newkey数组
        //newObj[newkey[i]] = obj[newkey[i]];//向新创建的对象中按照排好的顺序依次增加键值对
        str+=newkey[i]+'='+obj[newkey[i]]+'&';
    }
    str+=config['sign_key'];
    
    var sign=md5(str);
    return sign;
}

//过滤函数
function filter(str) {

  // 多个敏感词，这里直接以数组的形式展示出来
  var arrMg = field;
  //console.log(arrMg);
  // 显示的内容--showContent
  var showContent = str;
//console.log(showContent);
  // 正则表达式
  // \d 匹配数字
  for (var i = 0; i < arrMg.length; i++) {
    // 创建一个正则表达式
    var r = new RegExp(arrMg[i], "ig");
    var re='';
    for(var n=0;n<arrMg[i].length;n++){
        re+='*';
    }
    showContent = showContent.replace(r, re);
  }
  // 显示的内容--showInput
    //console.log(showContent);
    return showContent;

}



