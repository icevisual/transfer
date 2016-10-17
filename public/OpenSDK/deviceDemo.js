
var arrivesMsg ,byteBody;
//http://120.26.109.169:18083/
SmellOpen = {
    initFlag: false,
    defaults: {
        'deviceId': '0CRngr3ddpVzUBoeF',
        'deviceSecret': 'XqCEMSzhsdWHfwhm',
        'mqtt': {
//            'hostname': '120.26.109.169',
        	'hostname': '192.168.5.21',
            'port': '8083',
        },
        'AES':{
        	'key' : 'XqCEMSzhsdWHfwhm',
        	'iv' : '00000000000Pkcs7',
        }
    },
    configs: {},
    init: function(configs) {
        this.configs = this.defaults;
        for (var i in configs) {
            if (configs[i] instanceof Object) {
                for (var j in configs[i]) {
                    this.configs[i][j] = configs[i][j] + '';
                }
            } else {
                this.configs[i] = configs[i] + '';
            }
        };
        this.initFlag = true;
    },
    client: {},
    connect: function() {
        if (!this.initFlag) {
            this.init();
        }
        var mqttConfig = this.configs;
        console.log('SDK Config',mqttConfig);
        // Create a client instance
        this.client = new Paho.MQTT.Client(mqttConfig.mqtt.hostname, Number(mqttConfig.mqtt.port), mqttConfig.deviceId);
        // set callback handlers
        this.client.onConnectionLost = this.onConnectionLost;
        this.client.onMessageArrived = this.onMessageArrived;
        // connect the client
        this.client.connect({
            onSuccess: this.onConnect
        });
    },
    publish: function(topic, message) {
        message = new Paho.MQTT.Message(message);
        message.destinationName = topic;
        this.client.send(message);
    },
    subscribe: function(topic) {
        this.client.subscribe(topic);
    },
    unsubscribe: function(topic) {
        this.client.unsubscribe(topic);
    },

    onConnect: function() {
        SmellOpen.utils.log("onConnect,clientId = " + SmellOpen.configs.deviceId);
        SmellOpen.subscribe("/" + SmellOpen.configs.deviceId);
    },
    onConnectionLost: function(responseObject) {
    	console.log('onConnectionLost',responseObject);
        if (responseObject.errorCode !== 0) {
            SmellOpen.utils.log("onConnectionLost:" + responseObject.errorMessage);
        }
    },
    onMessageArrived: function(message) {
        // 读取内容，分析指令
        arrivesMsg = message;
        console.log("onMessageArrived From Topic " + message.destinationName);
        var headerInfo = SmellOpen.analyzeHeader(message.payloadBytes);
        console.log('analyzing End');
        if (false === headerInfo) {
            console.log('Header Not Match');
            console.log(message.payloadString);
            console.log(message.payloadBytes);
            if(message.payloadBytes[0] == 10){
            	console.log(pRoot.tutorial.AddressBook.decode(message.payloadBytes));
            }
//            console.log(pRoot.tutorial.AddressBook.decode(message.payloadBytes));
            //pRoot.tutorial.Person.decode(message.payloadBytes);
        } else {
            console.log('Header Found');
            console.log(headerInfo);
//            analyze header
            try {
                var headerLength = 8;
                var payloadBytesBody = message.payloadBytes.slice(headerLength);// Get payload body
                byteBody = payloadBytesBody;
                console.log('payloadBytesBody',payloadBytesBody);
                var payloadHex = SmellOpen.utils.intArray2HexStr(payloadBytesBody);// Convert 2 hex string
                console.log('payloadHexString',payloadHex);
                var b64str = CryptoJS.enc.Base64.stringify(CryptoJS.enc.Hex.parse(payloadHex));// Convert 2 base64 string 
                var res1 = SmellOpen.utils.AESDecrypt(b64str,SmellOpen.configs.AES.key,SmellOpen.configs.AES.iv);// AES decrypt
                
                console.log('AESDecrypted Data',SmellOpen.utils.hex2IntArray(CryptoJS.enc.Hex.stringify(res1)));
                
                var obj = Scentrealm.AuthRequest.decodeHex(res1.toString());// Proto decode
                console.log('Proto Data',obj);
            } catch (e) {
                console.log('protobuf decode error');
            }
        }
    },
    analyzeHeader: function(payloadBytes) {
        var headerLength = 8;
        var msgHeader = new Uint8Array(payloadBytes);
        console.log('analyzing');
        console.log(msgHeader);
        if (msgHeader[0] == 0xfe) {
            var info = {
                'version': msgHeader[1],
                'length': parseInt(msgHeader[2] << 8) + parseInt(msgHeader[3]),
                'cmdId': parseInt(msgHeader[4] << 8) + parseInt(msgHeader[5]),
                'seqId': parseInt(msgHeader[6] << 8) + parseInt(msgHeader[7]),
            }
            return info;
        } else {
            return false;
        }
    },
    protoDataPackage: function(protoDataArrayBuffer,cmdId,seqId) {
        var msgData = new Uint8Array(protoDataArrayBuffer);
        var payloadLength = msgData.length;
        var payloadByteLength = msgData.byteLength;
        var headerLength = 8; //包头字节长度
        var b = new Uint8Array(payloadLength + headerLength);
        b.set(msgData, headerLength);
        var len = SmellOpen.utils.ten2sixteen(payloadByteLength + headerLength);
        var cmd = SmellOpen.utils.ten2sixteen(cmdId);
        var seq = SmellOpen.utils.ten2sixteen(seqId);
        var header = [0xfe, 0x01, len[0], len[1], cmd[0], cmd[1], seq[0], seq[1]];
        for (var i in header) {
            b[i] = header[i];
        }
        return b;
    },
    protoDataPackageWithAES: function(protoData,cmdId,seqId) {
        var hexData = CryptoJS.enc.Hex.parse(protoData.encodeHex());// Word Array
        var encryptData = SmellOpen.utils.AESEncrypt(hexData, SmellOpen.configs.AES.key,SmellOpen.configs.AES.iv);// AES encrypt
        var base64Words = CryptoJS.enc.Base64.parse(encryptData.toString());//Base64 Decode 2 Word Array
        var hexEncryptedStr = CryptoJS.enc.Hex.stringify(base64Words);// Convert 2 hex String 
        var intArray = SmellOpen.utils.hex2IntArray(hexEncryptedStr);// Convert 2 int Array 
        var u8ArrayBuffer = new Uint8Array(intArray).buffer;// Convert 2 ArrayBuffer
        return this.protoDataPackage(u8ArrayBuffer,cmdId,seqId); // Call package function
    },
    sendProtoTest: function() {
        var b = this.protoDataPackage(auth.encode().toArrayBuffer(),10001,1);
        console.log('protoDataPackage',b);
        this.publish("/" + this.configs.deviceId, b);
        return true;
    },
    sendPerson: function() {
    	var msgData = new Uint8Array(msgBook.encode().toArrayBuffer());
    	var b = msgData;
        console.log('Sending Data Bytes',b);
        this.publish("/test/Person", b);
        return true;
    },
    sendProtoAesTest: function() {
        var b = this.protoDataPackageWithAES(auth,10001,1);
        console.log('protoDataPackageWithAES',b);
        this.publish("/" + this.configs.deviceId, b);
        return true;
    },
};
var u8 ;
var u9 ;
SmellOpen.utils = {
    log: function() {
        for (var i in arguments) {
            console.log("[" + now() + "] " + arguments[i]);
        }
    },
    ten2sixteen: function(d){// 256 => [01,00]
        return [ d >> 8 , d > 256 ? d - 256 : d];
    },
    hex2IntArray:function(hexStr){
    	if(hexStr.length % 2 ){
    		hexStr = hexStr + '0';
    	}
    	var intArray = [];
        for(var i = 0 ; i < hexStr.length ; i +=2){
        	var s = hexStr.substr(i,2);
        	intArray[i/2] = parseInt(s,16);
        }
        return intArray;
    },
    intArray2HexStr:function(intArray){
        var sss = '';
        for(var i = 0 ; i < intArray.length ; i ++){
        	var s = parseInt(intArray[i]).toString(16);
        	if(s.length == 1){
        		s = '0' + s;
        	}
        	sss += s;
        }
        return sss;
    },
    AESEncrypt: function(data, key, iv) { //加密
    	var key_hash = CryptoJS.MD5(key);
        var key = CryptoJS.enc.Utf8.parse(key_hash);
        var iv = CryptoJS.enc.Utf8.parse(iv);
        var encrypted = CryptoJS.AES.encrypt(data, key, {
            iv: iv,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.ZeroPadding
        });
        return encrypted;
    },
    AESDecrypt: function(encrypted, key, iv) { //解密
    	var key_hash = CryptoJS.MD5(key);
        var key = CryptoJS.enc.Utf8.parse(key_hash);
        var iv = CryptoJS.enc.Utf8.parse(iv);
        var decrypted = CryptoJS.AES.decrypt(encrypted, key, {
            iv: iv,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.ZeroPadding
        });
        return decrypted;
        return decrypted.toString(CryptoJS.enc.Utf8);
    },
    AESTest:function(){
        var key = 'XqCEMSzhsdWHfwhm'; //密钥
        var iv = '00000000000Pkcs7';
        var car = auth;
        console.log('============Test AES============');
        var ecp = this.AESEncrypt("123456dsfdfas789", key, iv);// AES encrypt
        console.log(ecp.toString());
        var res = this.AESDecrypt(ecp.toString(), key, iv);
        console.log(res.toString(CryptoJS.enc.Utf8));
        return;
        
        console.log('============Test Proto & AES============');
        console.log('Uint8Array',new Uint8Array(car.encodeAB()));// Proto data ArrayBuffer
        var u8data = CryptoJS.enc.Hex.parse(car.encodeHex());// Convert 2 word array
        console.log('EncodeHex',u8data.toString());
        console.log('============Encrypt Data============');
        u8 = this.AESEncrypt(u8data, key, iv);// AES encrypt
        console.log('AESEncrypt.toString',u8.toString());// Base64 string
        var base64Words = CryptoJS.enc.Base64.parse(u8.toString());// Convert 2 word array
        console.log('AESEncrypt.toWords',base64Words);
        u9 = base64Words;
        console.log('AESEncrypt.words',base64Words.words);
        var hexEncryptedStr = CryptoJS.enc.Hex.stringify(base64Words);// Convert 2 hex string
        console.log('AESEncrypt.toHex',hexEncryptedStr);
        console.log('AESEncrypt.length',hexEncryptedStr.length);
        var intArray = SmellOpen.utils.hex2IntArray(hexEncryptedStr);// Convert 2 int Array 
        // ...transfer
        // ...receive ArrayBuffer
        var sss = SmellOpen.utils.intArray2HexStr(intArray);// Convert 2 hex string
        console.log('AESEncrypt.sssss',sss);
        console.log('AESEncrypt.intArray',intArray.length,intArray);
        console.log('AESEncrypt.Uint8Array',new Uint8Array(intArray));
        console.log('AESEncrypt.u8',new Uint8Array(intArray));
        var b64str = CryptoJS.enc.Base64.stringify(CryptoJS.enc.Hex.parse(sss));// Convert base64 string
        console.log('Decrypted.b64str',b64str);
        var decryptedStr = this.AESDecrypt(b64str, key, iv);// AES decrypt
        console.log('============Decrypted Data============');
        console.log(decryptedStr.words);
        console.log(decryptedStr.toString());// Hex string
        console.log(root.Scentrealm.AuthRequest.decodeHex(decryptedStr.toString()));// Proto Decode
    }
};

SmellOpen.connect();
//SmellOpen.utils.AESTest();


