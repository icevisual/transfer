
var arrivesMsg ;

SmellOpen = {
    initFlag: false,
    defaults: {
        'deviceId': '0CRngr3ddpVzUBoeF',
        'deviceSecret': 'XqCEMSzhsdWHfwhm',
        'mqtt': {
            'hostname': '192.168.5.21',
            'port': '8083',
        }
    },
    configs: {},
    log: function() {
        for (var i in arguments) {
            console.log("[" + now() + "] " + arguments[i]);
        }
    },
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
        SmellOpen.log("onConnect,clientId = " + SmellOpen.configs.deviceId);
        SmellOpen.subscribe("/" + SmellOpen.configs.deviceId);
    },
    onConnectionLost: function(responseObject) {
        if (responseObject.errorCode !== 0) {
            SmellOpen.log("onConnectionLost:" + responseObject.errorMessage);
        }
    },
    onMessageArrived: function(message) {
        // 读取内容，分析指令
        arrivesMsg = message;
        console.log("onMessageArrived From Topic " + message.destinationName);
        var res = SmellOpen.analyzeHeader(message.payloadBytes);
        console.log('analyzing End');
        if (false === res) {
            console.log('Header Not Match');
            console.log(message.payloadString);
        } else {
            console.log('Header Found');
            try {
                var headerLength = 8;
                payloadBytesBody = message.payloadBytes.slice(headerLength);
                var msgHeader = new Uint8Array(payloadBytesBody);
                console.log(payloadBytesBody);
                // 截取
                var dd = Car.decode(payloadBytesBody);
                console.log(dd.model);
                console.log(dd.vendor);
                console.log(dd.vendor.name);
                console.log(dd.vendor.address);
                console.log(dd.vendor.address.country);
                console.log(dd.speed);
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
    protoDataPackage: function(protoData,cmdId,seqId) {
        var msgData = new Uint8Array(protoData.encode().toArrayBuffer());
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
    sendProtoTest: function() {
        var b = this.protoDataPackage(car,10001,1);
        console.log(b);
        this.publish("/" + this.configs.deviceId, b);
        return true;
    },
    sendProtoAesTest: function() {
        var b = this.protoDataPackage(car,10001,1);
        console.log(b);
        this.publish("/" + this.configs.deviceId, b);
        return true;
    },
};

SmellOpen.utils = {
    ten2sixteen: function(d){
        return [ d >> 8 , d > 256 ? d - 256 : d];
    },
    AESEncrypt: function(data, key, iv) { //加密
        var key = CryptoJS.enc.Hex.parse(key);
        var iv = CryptoJS.enc.Latin1.parse(iv);
        var encrypted = CryptoJS.AES.encrypt(data, key, {
            iv: iv,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.Pkcs7
        });
        return encrypted;
    },
    AESDecrypt: function(encrypted, key, iv) { //解密
        var key = CryptoJS.enc.Hex.parse(key);
        var iv = CryptoJS.enc.Latin1.parse(iv);
        var decrypted = CryptoJS.AES.decrypt(encrypted, key, {
            iv: iv,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.Pkcs7
        });
        return decrypted;
        return decrypted.toString(CryptoJS.enc.Utf8);
    },
    AESTest:function(){
        var key = '1231231231231232'; //密钥
        var iv = 'Pkcs7';
        var encrypted = this.AESEncrypt('123', key, iv); //密文
        console.log(encrypted.toLocaleString());
        var decryptedStr = this.AESDecrypt(encrypted.toLocaleString(), key, iv);
        console.log(decryptedStr);
        console.log('============Test Proto & AES============');

        var u8data = CryptoJS.enc.Hex.parse(car.encodeHex());
        console.log('============Encrypt Data============');
        console.log(u8data);
        var res = this.AESEncrypt(u8data, key, iv).toString();
        console.log('============Encrypted Data============');
        console.log(res);
        var decryptedStr = this.AESDecrypt(res, key, iv);

        console.log('============Decrypted Data============');
        console.log(decryptedStr.words);
        console.log(Car.decodeHex(decryptedStr.toString()));
    }
};

SmellOpen.connect();
SmellOpen.utils.AESTest();