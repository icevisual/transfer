//require.config({
//	paths : {
//		'SDK' : './SDK/SDK',
//	},
//});

var SMSDK ,dev,ret;
ret = require([ './SDK/SDK'], function(SDK) {
	SDK.connect({
		'accessKey' : 'IAzDhpyc0z9yGFajKp2P',
		'accessSecret' : 'HNKGRV2O2oeK7W2jtmFC',
		'logLevel' : 'info',
	});
	SMSDK = SDK;
	
	dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');
	
//	dev.getDevAttr(['mac']);
	
//	dev.getDevAttr(['mac']);
	return SDK;
})
