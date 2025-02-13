var checkPhone=/^\d{1,11}$/;
var checkPass=/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,12}$/;

//登录请求
$('#userlogin').on('click',function(){
	var code=$("#code").find("option:selected").val()
	var mobile=$("input[name=mobile]").val();
	var pws=$("input[name=pws]").val();
	if(!checkPhone.test(mobile)){
		layer.msg("您输入的手机号有误");
		return !1;
	}
	

	if(!checkPass.test(pws)){
		layer.msg("密码必须包含字母和数字，6-12位");
		return !1;
	}
	
	$.ajax({
		url: '/wxshare/user/userLogin',
		data: {mobile:mobile,pws:pws,code:code},
		type: "POST",
		dataType: "json",
		cache: !1,
		success:function(data){
			
			if(data && data.code ==0){
				layer.msg(data.msg,{time:3000},function(){
					location.href='/wxshare/Share/index';
				});
				
			}else{
				layer.msg(data.msg);
				return !1;
			}
			
			
		}
	})	
});

//获取验证码
var countdown=60; 
var iscode=1;
function sendemail(type){
	if(iscode==1){
		var obj = $("#codeget");
		
		var code=$("#code").find("option:selected").val()
		var mobile=$("input[name=mobile]").val();
		
		if(!checkPhone.test(mobile)){
			layer.msg("您输入的手机号有误");
			return !1;
		}
		
		var url_post='/wxshare/user/getCode';
		if(type==2){
			url_post='/wxshare/user/getForgetCode';
		}
		$.ajax({
			url: url_post,
			data: {mobile:mobile,code:code},
			type: "POST",
			dataType: "json",
			cache: !1,
			success:function(data){
				if(data.code==0){
					settime(obj);
					iscode=0;
				}
				layer.msg(data.msg);
			}
		})
	}
	
}
function settime(obj) { //发送验证码倒计时
	if (countdown == 0) { 
		obj.attr('disabled',false); 
		//obj.removeattr("disabled"); 
		obj.text("获取验证码");
		countdown = 60; 
		iscode=1;
		return;
	} else { 
		obj.attr('disabled',true);
		obj.text("重新发送(" + countdown + ")");
		countdown--; 
	} 
setTimeout(function() { 
	settime(obj) }
	,1000) 
}

//注册请求
$('#userreg').on('click',function(){
	var code=$("#code").find("option:selected").val()
	var mobile=$("input[name=mobile]").val();
	var mcode=$("input[name=mcode]").val();
	var pws=$("input[name=pws]").val();
	
	console.log(pws);
	var pwds=$("input[name=pwds]").val();
	if(!checkPhone.test(mobile)){
		layer.msg("您输入的手机号有误");
		return !1;
	}
	if(mcode==''){
		layer.msg("请填写验证码");
		return !1;
	}
	if(!checkPass.test(pws)){
		layer.msg("密码必须包含字母和数字，6-12位");
		return !1;
	}
	if(pws!=pwds){
		layer.msg("您输入的密码，两次不一致");
		return !1;
	}
	
	$.ajax({
		url: '/wxshare/user/userReg',
		data: {mobile:mobile,pws:pws,code:code,mcode:mcode},
		type: "POST",
		dataType: "json",
		cache: !1,
		success:function(data){
			
			if(data && data.code ==0){
				layer.msg(data.msg,{time:3000},function(){
					location.href='/wxshare/Share/index';
				});
				
			}else{
				layer.msg(data.msg);
				return !1;
			}
			
			
		}
	})	
});


//注册请求
$('#userforget').on('click',function(){
	var code=$("#code").find("option:selected").val()
	var mobile=$("input[name=mobile]").val();
	var mcode=$("input[name=mcode]").val();
	var pws=$("input[name=pws]").val();
	
	console.log(pws);
	var pwds=$("input[name=pwds]").val();
	if(!checkPhone.test(mobile)){
		layer.msg("您输入的手机号有误");
		return !1;
	}
	if(mcode==''){
		layer.msg("请填写验证码");
		return !1;
	}
	if(!checkPass.test(pws)){
		layer.msg("密码必须包含字母和数字，6-12位");
		return !1;
	}
	if(pws!=pwds){
		layer.msg("您输入的密码，两次不一致");
		return !1;
	}
	
	$.ajax({
		url: '/wxshare/user/userForget',
		data: {mobile:mobile,pws:pws,code:code,mcode:mcode},
		type: "POST",
		dataType: "json",
		cache: !1,
		success:function(data){
			
			if(data && data.code ==0){
				layer.msg(data.msg,{time:3000},function(){
					location.href='/wxshare/user/login';
				});
				
			}else{
				layer.msg(data.msg);
				return !1;
			}
			
			
		}
	})	
});