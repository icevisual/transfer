var arrivesMsg;
// http://120.26.109.169:18083/
var Simple;
var AddressBook;
var simpleData;
SmellOpen = {
	initFlag : false,
	defaults : {
		'deviceId' : '0CRngr3ddpVzUBoeF',
		'deviceSecret' : 'XqCEMSzhsdWHfwhm',
		'env' : 'debug',
		'mqtt' : {
			// 'hostname': '120.26.109.169',
			'hostname' : '192.168.5.21',
			'port' : '8083',
		},
		'AES' : {
			'key' : 'XqCEMSzhsdWHfwhm',
			'iv' : '00000000000Pkcs7',
		}
	},
	configs : null,
	loadConfigs : function(configs) {
		this.configs = this.defaults;
		for ( var i in configs) {
			if (configs[i] instanceof Object) {
				for ( var j in configs[i]) {
					this.configs[i][j] = configs[i][j] + '';
				}
			} else {
				this.configs[i] = configs[i] + '';
			}
		}
	},
	loadProto : function() {
		var simpleRoot = SmellOpen.utils.loadProto('Simple.proto.js');
		Simple = simpleRoot.Proto2.Scentrealm.Simple;
		
		var personRoot = SmellOpen.utils.loadProto('test/Person.pro');
		AddressBook = personRoot.Proto2.Tutorial.AddressBook;
		// 每周1-5 中午 11:30 - 12:30 和 17:30 - 18:30--  ===   ## (9s) 循环
		// 
		var sData = new Simple.PlaySmell();
		// Generate A test PlaySmell Data
		sData.startAt = new Array();
		sData.cycleMode = Simple.SrCycleMode.SCM_cycle_infinite;
		sData.cycleTime = 0;
		
		sData.startAt[0] = new Simple.TimePoint(
				Simple.SrTimeMode.STM_weekday, 1, 5);
		sData.startAt[1] = new Simple.TimePoint(
				Simple.SrTimeMode.STM_daytime, 41400, 45000);
		sData.startAt[2] = new Simple.TimePoint(
				Simple.SrTimeMode.STM_daytime, 63000, 66600);
		// 11:30 - 12:30  41400 45000
		// 17:30 - 18:30  63000 66600

		sData.play = new Array();
		sData.play[0] = new Simple.PlayAction({
			'bottle' : '0000000001',
			'duration' : 2,
			'power' : 5,
		});
		sData.play[1] = new Simple.PlayAction({
			'bottle' : '0000000002',
			'duration' : 3,
			'power' : 7,
		});
		sData.play[2] = new Simple.PlayAction({
			'bottle' : '0000000003',
			'duration' : 2,
			'power' : 5,
		});
		sData.play[3] = new Simple.PlayAction({
			'bottle' : '',
			'duration' : 2,
			'power' : 0,
		});
		sData.play[4] = new Simple.PlayAction({
			'bottle' : '',
			'duration' : 3,
			'power' : 0,
		});
		// 14 
		sData.trace = new Array();
		sData.trace[0] = new Simple.PlayTrace({
			'actionId' : [0,3,1,4,2],
			'beforeStart' : 0,
			'cycleMode' : Simple.SrCycleMode.SCM_cycle_yes,
			'interval' : 0,
			'cycleTime' : 278
		});
		simpleData = sData;

		SmellOpenLog.debug('sData', sData);
	},
	initialize : function() {
		if (null == this.configs) {
			this.loadConfigs();
		}
		this.loadProto();
		this.initFlag = true;
	},
	client : null,
	connect : function() {
		this.initialize();

		var mqttConfig = this.configs;
		SmellOpenLog.debug('SDK Config', mqttConfig);
		// Create a client instance
		this.client = new Paho.MQTT.Client(mqttConfig.mqtt.hostname,
				Number(mqttConfig.mqtt.port), mqttConfig.deviceId);
		// set callback handlers
		this.client.onConnectionLost = this.onConnectionLost;
		this.client.onMessageArrived = this.onMessageArrived;
		this.client.onMessageDelivered = this.onMessageDelivered;
		// connect the client
		this.client.connect({
			onSuccess : this.onConnect
		});
	},
	publish : function(topic, message) {
		message = new Paho.MQTT.Message(message);
		message.destinationName = topic;
		this.client.send(message);
	},
	subscribe : function(topic) {
		this.client.subscribe(topic);
	},
	unsubscribe : function(topic) {
		this.client.unsubscribe(topic);
	},
	onConnect : function() {
		SmellOpenLog
				.debug("onConnect,clientId = " + SmellOpen.configs.deviceId);
		SmellOpen.subscribe("/" + SmellOpen.configs.deviceId);
	},
	onConnectionLost : function(responseObject) {
		SmellOpenLog.debug('onConnectionLost', responseObject);
		if (responseObject.errorCode !== 0) {
			SmellOpen.utils.log("onConnectionLost:"
					+ responseObject.errorMessage);
		}
	},
	onMessageDelivered : function(message) {
		SmellOpenLog.debug('onMessageDelivered.payloadBytes',
				message.payloadBytes);
		// console.log('onMessageDelivered.payloadString',message.payloadString);
	},
	onMessageArrived : function(message) {
		// 读取内容，分析指令
		arrivesMsg = message;
		SmellOpenLog.debug("onMessageArrived From Topic "
				+ message.destinationName);
		var headerInfo = SmellOpen.analyzeHeader(message.payloadBytes);
		if (false === headerInfo) {
			SmellOpenLog.debug('Header Not Match');
			SmellOpenLog.debug('payloadString', message.payloadString);
			SmellOpenLog.debug('payloadBytes', message.payloadBytes);
		} else {
			SmellOpenLog.debug('Header Found', headerInfo);
			
			if(headerInfo.cmdId == 2222){
				SmellOpen.decodeData(message.payloadBytes, AddressBook, 8)
			}
			if(headerInfo.cmdId == Simple.SrCmdId.SCI_req_playSmell){
				SmellOpen.decodeData(message.payloadBytes, Simple.PlaySmell, 8)
			}
			// analyze header
		}
	},
	decodeData : function(payloadBytes, decodeClass, removeHeaderLength, isAES) {
		try {
			var payloadBytesBody = payloadBytes
			if (removeHeaderLength) {
				// Get payload body
				payloadBytesBody = payloadBytes.slice(removeHeaderLength);
			}
			var decodeObj;
			SmellOpenLog.debug('payloadBytesBody', payloadBytesBody);
			if (isAES === true) {
				// Convert 2 hex string
				var payloadHex = SmellOpen.utils
						.intArray2HexStr(payloadBytesBody);
				SmellOpenLog.debug('payloadHexString', payloadHex);
				// Convert 2 base64 string
				var b64str = CryptoJS.enc.Base64.stringify(CryptoJS.enc.Hex
						.parse(payloadHex));
				// AES decrypt
				var res1 = SmellOpen.utils.AESDecrypt(b64str,
						SmellOpen.configs.AES.key, SmellOpen.configs.AES.iv);
				SmellOpenLog.debug('AESDecrypted Data', SmellOpen.utils
						.hex2IntArray(CryptoJS.enc.Hex.stringify(res1)));
				// Proto decode
				decodeObj = decodeClass.decodeHex(res1.toString());
			} else {
				decodeObj = decodeClass.decode(payloadBytesBody);
			}
			SmellOpenLog.debug('Proto Data', decodeObj);
			return decodeObj;
		} catch (e) {
			SmellOpenLog.waring('protobuf decode error');
		}
		return false;
	},
	analyzeHeader : function(payloadBytes) {
		var headerLength = 8;
		var msgHeader = new Uint8Array(payloadBytes);
		SmellOpenLog.debug('analyzing Header', msgHeader);
		if (msgHeader[0] == 0xfe) {
			var info = {
				'version' : msgHeader[1],
				'length' : msgHeader[2] << 8 | msgHeader[3],
				'cmdId' : msgHeader[4] << 8 | msgHeader[5],
				'seqId' : msgHeader[6] << 8 | msgHeader[7],
			}
			return info;
		} else {
			return false;
		}
	},
	protoDataPackage : function(protoDataArrayBuffer, cmdId, seqId) {
		var msgData = new Uint8Array(protoDataArrayBuffer);
		var payloadLength = msgData.length;
		var payloadByteLength = msgData.byteLength;
		var headerLength = 8; // 包头字节长度
		var b = new Uint8Array(payloadLength + headerLength);
		b.set(msgData, headerLength);
		var len = SmellOpen.utils.ten2sixteen(payloadByteLength + headerLength);
		SmellOpenLog.debug('protoDataPackage len', payloadByteLength
				+ headerLength);
		var cmd = SmellOpen.utils.ten2sixteen(cmdId);
		var seq = SmellOpen.utils.ten2sixteen(seqId);
		var header = [ 0xfe, 0x01, len[0], len[1], cmd[0], cmd[1], seq[0],
				seq[1] ];
		for ( var i in header) {
			b[i] = header[i];
		}
		return b;
	},
	protoDataPackageWithAES : function(protoData, cmdId, seqId) {
		// Word Array
		var hexData = CryptoJS.enc.Hex.parse(protoData.encodeHex());
		// AES encrypt
		var encryptData = SmellOpen.utils.AESEncrypt(hexData,
				SmellOpen.configs.AES.key, SmellOpen.configs.AES.iv);
		// Base64 Decode 2 Word Array
		var base64Words = CryptoJS.enc.Base64.parse(encryptData.toString());
		// Convert 2 hex String
		var hexEncryptedStr = CryptoJS.enc.Hex.stringify(base64Words);
		// Convert 2 int Array
		var intArray = SmellOpen.utils.hex2IntArray(hexEncryptedStr);
		// Convert 2 ArrayBuffer
		var u8ArrayBuffer = new Uint8Array(intArray).buffer;
		// Call package function
		return this.protoDataPackage(u8ArrayBuffer, cmdId, seqId);
	},
	sendProtoTest : function() {
		SmellOpenLog.debug('simpleData', simpleData);
		var b = this.protoDataPackage(simpleData.encode().toArrayBuffer(),
				Simple.SrCmdId.SCI_req_playSmell, 1);
		SmellOpenLog.debug('protoDataPackage', b);
		this.publish("/" + this.configs.deviceId, b);
		return true;
	},
	sendProtoAesTest : function() {
		var b = this.protoDataPackageWithAES(auth, 10001, 1);
		SmellOpenLog.debug('protoDataPackageWithAES', b);
		this.publish("/" + this.configs.deviceId, b);
		return true;
	},
};
SmellOpenLog = {
	debug : function() {
		this.log('debug', arguments);
	},
	info : function() {
		this.log('info', arguments);
	},
	notice : function() {
		this.log('notice', arguments);
	},
	warning : function() {
		this.log('warning', arguments);
	},
	error : function() {
		this.log('error', arguments);
	},
	levelCompare : function(maxLevel, nowLevel) {
		var level = {
			'debug' : 1,
			'info' : 2,
			'notice' : 3,
			'warning' : 4,
			'error' : 5,
		};
		if (!level[nowLevel] || !level[maxLevel]) {
			return false;
		}
		return level[nowLevel] >= level[maxLevel];
	},
	log : function(level, data) {
		if (data.length > 0 & this.levelCompare(SmellOpen.configs.env, level)) {
			var dateStr = "[" + now() + "] " + level + " : ";
			var output;
			if (typeof data[0] == "string") {
				data[0] = (dateStr += data[0]);
				output = data;
			} else {
				output = new Array();
				output.push(dateStr);
				for ( var i in data) {
					output.push(data[i]);
				}
			}
			return console.log.apply(console, output);
			for ( var i in output) {
				console.log(output[i]);
			}
		}
	}
};
SmellOpen.utils = {
	loadProto : function(filename) {
		var ProtoBuf = dcodeIO.ProtoBuf;
		var builder = ProtoBuf.loadProtoFile(filename);
		return builder.build();
		;
	},
	log : function() {
		for ( var i in arguments) {
			console.log("[" + now() + "] " + arguments[i]);
		}
	},
	ten2sixteen : function(d) {// 256 => [01,00]
		// return [ d >> 8, d > 256 ? d - 256 : d ];
		return [ d >> 8, d & 0xff ];
	},
	hex2IntArray : function(hexStr) {
		if (hexStr.length % 2) {
			hexStr = hexStr + '0';
		}
		var intArray = [];
		for (var i = 0; i < hexStr.length; i += 2) {
			var s = hexStr.substr(i, 2);
			intArray[i / 2] = parseInt(s, 16);
		}
		return intArray;
	},
	intArray2HexStr : function(intArray) {
		var sss = '';
		for (var i = 0; i < intArray.length; i++) {
			var s = parseInt(intArray[i]).toString(16);
			if (s.length == 1) {
				s = '0' + s;
			}
			sss += s;
		}
		return sss;
	},
	AESEncrypt : function(data, key, iv) { // 加密
		var key_hash = CryptoJS.MD5(key);
		var key = CryptoJS.enc.Utf8.parse(key_hash);
		var iv = CryptoJS.enc.Utf8.parse(iv);
		var encrypted = CryptoJS.AES.encrypt(data, key, {
			iv : iv,
			mode : CryptoJS.mode.CBC,
			padding : CryptoJS.pad.ZeroPadding
		});
		return encrypted;
	},
	AESDecrypt : function(encrypted, key, iv) { // 解密
		var key_hash = CryptoJS.MD5(key);
		var key = CryptoJS.enc.Utf8.parse(key_hash);
		var iv = CryptoJS.enc.Utf8.parse(iv);
		var decrypted = CryptoJS.AES.decrypt(encrypted, key, {
			iv : iv,
			mode : CryptoJS.mode.CBC,
			padding : CryptoJS.pad.ZeroPadding
		});
		return decrypted;
		return decrypted.toString(CryptoJS.enc.Utf8);
	},
	AESTest : function() {
		var key = 'XqCEMSzhsdWHfwhm'; // 密钥
		var iv = '00000000000Pkcs7';
		var car = auth;
		console.log('============Test AES============');
		// AES encrypt
		var ecp = this.AESEncrypt("123456dsfdfas789", key, iv);
		console.log(ecp.toString());
		var res = this.AESDecrypt(ecp.toString(), key, iv);
		console.log(res.toString(CryptoJS.enc.Utf8));
		return;
		var u8;// make it global
		var u9;
		console.log('============Test Proto & AES============');
		// Proto data ArrayBuffer
		console.log('Uint8Array', new Uint8Array(car.encodeAB()));
		// Convert 2 word array
		var u8data = CryptoJS.enc.Hex.parse(car.encodeHex());
		console.log('EncodeHex', u8data.toString());
		console.log('============Encrypt Data============');
		// AES encrypt
		u8 = this.AESEncrypt(u8data, key, iv);
		// Base64 string
		console.log('AESEncrypt.toString', u8.toString());
		// Convert 2 word arrays
		var base64Words = CryptoJS.enc.Base64.parse(u8.toString());
		console.log('AESEncrypt.toWords', base64Words);
		u9 = base64Words;
		console.log('AESEncrypt.words', base64Words.words);
		// Convert 2 hex string
		var hexEncryptedStr = CryptoJS.enc.Hex.stringify(base64Words);
		console.log('AESEncrypt.toHex', hexEncryptedStr);
		console.log('AESEncrypt.length', hexEncryptedStr.length);
		// Convert 2 int Array
		// ...transfer
		// ...receive ArrayBuffer
		var intArray = SmellOpen.utils.hex2IntArray(hexEncryptedStr);
		// Convert 2 hex string
		var sss = SmellOpen.utils.intArray2HexStr(intArray);
		console.log('AESEncrypt.sssss', sss);
		console.log('AESEncrypt.intArray', intArray.length, intArray);
		console.log('AESEncrypt.Uint8Array', new Uint8Array(intArray));
		console.log('AESEncrypt.u8', new Uint8Array(intArray));
		// Convert base64 string
		var b64str = CryptoJS.enc.Base64.stringify(CryptoJS.enc.Hex.parse(sss));
		console.log('Decrypted.b64str', b64str);
		// AES decrypt
		var decryptedStr = this.AESDecrypt(b64str, key, iv);
		console.log('============Decrypted Data============');
		console.log(decryptedStr.words);
		// Hex string
		console.log(decryptedStr.toString());
		// Proto Decode
		console.log(root.Scentrealm.AuthRequest.decodeHex(decryptedStr
				.toString()));
	}
};

SmellOpen.connect();
// SmellOpen.utils.AESTest();

