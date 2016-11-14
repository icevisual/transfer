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
		'mqtt' : {
			 'hostname' : '120.26.109.169',
//			'hostname' : '192.168.5.21',
			'port' : '8083',
		},
	});
})
