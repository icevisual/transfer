//require.config({
//	paths : {
//		'SDK' : './SDK/DeviceSDK',
//	},
//});

require([ 'SDK/DeviceSDK' ], function(SDK) {
	SDK.connect({
		'accessKey' : 'TCeOp0gzzrWhAMoOa3Mm',
		'accessSecret' : 'POVX1lgIvo8q1KHYpoD9',
		'logLevel' : 'debug',
	});
})
