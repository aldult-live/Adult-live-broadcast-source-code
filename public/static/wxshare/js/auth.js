$(function(){
	
	var upload_this;
    $(".up_img img").click(function(){
        //if(status!='' && status==0){
        if(status!='' && status!=2){
            return !1;
        }
        upload_this=$(this).parent();
		console.log(upload_this);
        upload();
    })

    function upload() {

		var iptt=$('.file_input',upload_this)[0];

		//var iptt=document.getElementById(index);
		if(window.addEventListener) { // Mozilla, Netscape, Firefox
			iptt.removeEventListener('change',ajaxFileUpload,false);
			iptt.addEventListener('change',ajaxFileUpload,false);
		}else{
			iptt.detachEvent('onchange',ajaxFileUpload);
			iptt.attachEvent('onchange',ajaxFileUpload);
		}
		iptt.click();
    }
    
	function ajaxFileUpload() {
		var _this=upload_this;
		var iptt=$('.file_input',_this);
		if(iptt.val()==''){
			return !1;
		}
		var animate_div=$(".progress_sp",_this);
		var shadd=$(".shadd",_this);
		shadd.show();
		animate_div.css({"width":"0px"});

		var id=_this.data('fileid');
		animate_div.animate({"width":"100%"},700,function(){
			
			$.ajax({url: "/wxshare/auth/getuploadtoken", success: function(res){
				
				res=JSON.parse(res);;
				console.log(res.token);
				var token = res.token;
				var domain = res.domain;
				var name = 'auth_'+ new Date().getTime()+'.jpg';
				var imgurl = qiniu_expedite_url+name; //加速域名模板上定义
				
				console.log(qiniu_upload_url);
				
				$.ajaxFileUpload({
					url: qiniu_upload_url, //模板上定义
					secureuri: false,
					type:'POST',
					fileElementId: id,
					data: { 'x:name':name,fname:name,key:name,token:token },
					dataType: 'json',
					success: function(data,status,xhr) {
						//七牛不返回ajaxFileUpload可使用的错误提示，只能自行访问图片尝试
						if(status=='success'){
							layer.msg("上传成功");
							$("img",_this).attr("src",imgurl);
							$(".img_input",_this).attr("value",name);
							shadd.hide();
						}else{
							layer.msg("上传失败");
							shadd.hide();
						}
					}
					
				})
				
			}});
			
			return true;
		})
			
    }   
	
	/*点击提交审核*/
	var is_submit=0;
	$(".auth_ok").click(function(){
		if(is_submit==1){
			return;
		}

		var realname=$("#realname").val();
		var phone=$("#phone").val();
		var cardno=$("#cardno").val();
		var front=$("#front").val();
		var back=$("#back").val();
		var hand=$("#hand").val();

		if(realname==''){
			layer.msg("请输入姓名");
			return;
		}
        
        if(phone==''){
			layer.msg("请输入手机号码");
			return;
		}
        
        if(cardno==''){
			layer.msg("请输入身份证号码");
			return;
		}
        
        if(front==''){
			layer.msg("请上传证件正面");
			return;
		}
        
        if(back==''){
			layer.msg("请上传证件反面");
			return;
		}
        
        if(hand==''){
			layer.msg("请上传手持证件正面照");
			return;
		}

		is_submit=1;

		$.ajax({
			url: '/wxshare/Auth/auth_save',
			type: 'POST',
			dataType: 'json',
			data: {uid:uid,token:token,realname: realname,phone:phone,cardno:cardno,front:front,back:back,hand:hand},
			success:function(data){
                is_submit=0;
				var code=data.code;
				if(code!=0){
					layer.msg(data.msg);
					return;
				}else{
					layer.msg('提交成功', {time:1000},function(){
						// location.reload();
						window.location.href="/wxshare/Auth/index";
					});


				}
			},
			error:function(e){
                is_submit=0;
				console.log(e);
			}
		});
		
	});
});