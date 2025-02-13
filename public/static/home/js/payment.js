$(function(){
	$("#package-list .item").click(function(){
		$(this).siblings().removeClass("active");
		$(this).addClass("active");
		var changeid=$(this).attr("data-id");
		var price=$(this).attr("data-price");

		$("#changeid").val(changeid);
		//$("#price").val(price);
		$(".charge-cost .cost").html(price);
	})
	
	$("#charge-source-list .item").click(function(){
		$(this).siblings().removeClass("active");
		$(this).addClass("active");
		var source=$(this).attr("data-source");
		$("#source").val(source);
		 document.getElementById('c_PPPayID').value = source;
	})	
	
	$("#price").on("keydown", function(e) {
      if (!A(e.keyCode)) return ! 1
  }).on("keyup",function(e) {
      O($(this).val())
  }).on("blur",function(e) {
      O($(this).val(), !0)
  })
})

var O=function(e, n) {

}


function charge_submit(){

	 var a = jQuery("[class='item weixin active']").attr("data-index");
	 
	 if($("#package-list .active").length>0){

		 if($("#charge-source-list .active").length>0)
		 {
			 $("#fpost").submit();
		 }
		 else
		 {
			 alert("请选择支付方式");
		 }
		  
	 }
	 else
	 {
		 alert("请选择充值金额");
	 }
	
}