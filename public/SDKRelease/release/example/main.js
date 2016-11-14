require.config({
	paths : {
		'SDK' : '../dist/OpenSDK-v1.0.0.min',
	},
});

require([ 'SDK'], function(SDK) {
	SDK.connect({
		'accessKey' : 'IAzDhpyc0z9yGFajKp2P',
		'accessSecret' : 'HNKGRV2O2oeK7W2jtmFC',
		'logLevel' : 'info',
	});
	
	var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');
	
	dev.getDevAttr(['mac'],function(){
		
		console.log(arguments);
		
	},function(){
		console.log(arguments);
		
	});
	return SDK;
})
