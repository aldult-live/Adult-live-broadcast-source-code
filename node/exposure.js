var site='https://test.bjyjzx.net'; //站点域名
var schedule = require("node-schedule");
var request  = require('request');

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

//定时处理订单状态
var rule = new schedule.RecurrenceRule();

var times = [];
var minutes=0;

for(var i=0; i<12; i++){
    minutes=i*5;
    times.push(minutes);
}

var lastid=0;
rule.minute = times;
rule.second = 0;
// console.log(times);

var j = schedule.scheduleJob(rule, function(){
    // time=FormatNowDate();
    // console.log("执行任务:"+time);
    changeShopOrder(lastid);
});

//定期处理订单状态
function changeShopOrder(lastid){
    // var time=FormatNowDate();
    // console.log("执行任务setVal"+lastid+'--'+time);
    request(site+"/appapi/Shoporder/checkOrder?lastid="+lastid,function(error, response, body){
        //console.log(error);
        if(error) return;
        if(!body) return;
        // console.log('setVal-body-'+lastid+'--'+time);
        // console.log(body);
        if(body!='NO'){
            var strs=[];
            strs=body.split("-");
            
            // console.log(strs);
            if(strs[0]=='OK' && strs[1]!='0'){
                changeShopOrder(strs[1]);
            }
            
        }
    });
    
}


//定时处理直播
var rule2 = new schedule.RecurrenceRule();

var times2 = [];

for(var i=0; i<60; i++){
    times2.push(i);
}

rule2.second = times2;
// console.log(times);

var j2 = schedule.scheduleJob(rule2, function(){
    // time=FormatNowDate();
    // console.log("执行任务:"+time);
    uplive();
});


//定期处理直播状态
function uplive(){
    // var time=FormatNowDate();
    // console.log("执行任务setVal"+lastid+'--'+time);
    request(site+"/appapi/liveback/uplive",function(error, response, body){
        //console.log(error);
        if(error) return;
        if(!body) return;
        // console.log('setVal-body-'+lastid+'--'+time);
        // console.log(body);
    });
    
}


//定时处理青少年模式时间

var rule3 = new schedule.RecurrenceRule();

rule3.hour=0;
rule3.minute = 0;
rule3.second = 0;


var k = schedule.scheduleJob(rule3, function(){
    upTeenagerTime();
});


function upTeenagerTime(){
    request(site+"/appapi/teenagertime/index",function(error, response, body){
        //console.log(error);
        if(error) return;
        if(!body) return;

    });
}