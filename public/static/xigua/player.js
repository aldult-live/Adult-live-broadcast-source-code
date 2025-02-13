var xg_player=null;
/* 单个切换播放 */
function xgPlay(htmlid,pull,stream='',data={}){
    if(xg_player){
        var par=document.getElementById(htmlid).parentNode;
        xg_player.destroy(false);
        //xg_player.pause();
        par.innerHTML ='<div id="'+htmlid+'"></div>';
    }
    xg_play(htmlid,pull,stream,data);
}

/* 多个同时播放 */
function xgPlays(htmlid,pull,stream,data={}){
    xg_play(htmlid,pull,stream,data);
}
function xg_play(htmlid,pull,stream,data={}){
    if(htmlid=='' || pull==''){
        return !1;
    }
    var last_len=pull.lastIndexOf(".")+1;
    var last_len2=pull.lastIndexOf("?");
    var len = pull.length;
    if(last_len2>0){
        len=last_len2;
    }
    var pathf = pull.substring(last_len,len).toLowerCase();
    
    var data_play={
		"id": htmlid,
		"url": pull,
		"volume":0.2,
		"width":'100%',
		"height":'100%',
		"ignores": ['time','replay'],
		"autoplay": true
		
	};
    
    data_play= $.extend(data_play, data); //jq 合并对象

        
    if(pathf=='flv'){
       // xg_player=new FlvJsPlayer(data_play);//--未升级版本
		let xg_player = new window.FlvJsPlayer(data_play);
		//视频开始播放事件
		xg_player.on('play',function(){
		 //alert("视频开始播放了！");
		  
		})
		
		xg_player.on('pause',function(){
		 //alert("视频暂停了！");
		  
		})
		
        return !0;
    }else if(pathf=='m3u8'){
        // xg_player=new HlsJsPlayer(data_play); //--未升级版本
        let xg_player=new window.HlsJsPlayer(data_play);
        return !0;
    }else{
		// xg_player=new Player(data_play); //--未升级版本
		let xg_player=new Player(data_play);
	
		return !0;
	}
    
    
}
