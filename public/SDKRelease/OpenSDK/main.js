//require.config({
//	paths : {
//		'SDK' : './SDK/SDK',
//	},
//});

var SMSDK ,dev,ret;
ret = require([ './SDK/SDK'], function(SDK) {
	SDK.connect({
		'accessKey' : 'testIAzDhpyc0z9yGFajKp2P',
		'accessSecret' : 'HNKGRV2O2oeK7W2jtmFC',
		'logLevel' : 'debug',
		'mqtt' : {
			 'hostname' : '120.26.109.169',
//			'hostname' : '192.168.5.21',
			'port' : '8083',
		},
	});
	SMSDK = SDK;
//	TCeOp0gzzrWhAMoOa3Mm Linux-PC
//	dev = SDK.usingDevice('Linux-PC',{
//		'timeout' : 10,// Response Timeout Seconds
//	});
	dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm',{
		'timeout' : 5,// Response Timeout Seconds
	});
	
	
	
	
	
	var Simple = SDK.protoRoot;
//	dev.setDevAttr({
//	   'mac' : '0a-03-0a-03-0a-03' ,
//	   'wifissid' : 'renrenwifi',
//	   'wifipwd' : 'renren2016'
//	},function(){
//		console.log('setDevAttr',arguments);
//	},function(){
//		console.log(arguments);
//	});
	dev.sleep(function(){
		return
		var href =  window.location.href,time = 0,pos = -1;
		if((pos = href.indexOf('?') ) != -1){
			time = parseInt(href.substring(pos + 1));
		}
		var url = href.substring(0,pos) + '?' + (++time);
//		alert(url);
		SMSDK.disconnect();
		window.location.href = url;
	},function(){
		var href =  window.location.href,time = 0,pos = -1;
		if((pos = href.indexOf('?') ) != -1){
			time = parseInt(href.substring(pos + 1));
		}
		var url = href.substring(0,pos);
		
		SMSDK.utils.ajax({
			'url' : '/recordMaxAttampt',
			'data' : {
				'n' : time
			},
			'dataType' : 'json',
			'success' : function(d){
//				console.log(time);
				SMSDK.disconnect();
				window.location.href = url;
			}
		});
	});
//	dev.disconnect();
	
//	dev.getDevAttr(['mac','deviceName','wifissid']);
//	dev.setDevAttr({'mac' : '0a-03-0a-03-0a-03'});
	return SDK;
})
