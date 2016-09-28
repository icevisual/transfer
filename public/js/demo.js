var mqttConfig = {
    'hostname': '192.168.5.21',
    'port': '8083',
};
var clientId = parseInt(Math.random() * 100000000);
// Create a client instance
client = new Paho.MQTT.Client(mqttConfig.hostname, Number(mqttConfig.port), clientId + "");

// set callback handlers
client.onConnectionLost = onConnectionLost;
client.onMessageArrived = onMessageArrived;

// connect the client
client.connect({
    onSuccess: onConnect
});


function log() {
    for (var i in arguments) {
        console.log("[" + now() + "] " + arguments[i]);
    }
}

// called when the client connects
function onConnect() {
    // Once a connection has been made, make a subscription and send a message.
    log("onConnect,clientId = " + clientId);
    client.subscribe("/" + clientId);
    message = new Paho.MQTT.Message("Hello");
    message.destinationName = "/World";
    client.send(message);
}

// called when the client loses its connection
function onConnectionLost(responseObject) {
    if (responseObject.errorCode !== 0) {
        log("onConnectionLost:" + responseObject.errorMessage);
    }
}

var arriveMsg;

// called when a message arrives
function onMessageArrived(message) {
    arriveMsg = message;
    var data = message.payloadString;
    console.log(message);
    log("onMessageArrived:" + data);
    try {
        var dd = Car.decode(message.payloadBytes);
        console.log(dd.model);
    } catch (e) {
        console.log('error');
    }
    // var m = Car.decode(message.payloadString);
    // log("onMessageArrived:" + m.model);
}

function sendMessage() {
    var data = ['', '/' + clientId];
    for (var i in arguments) {
        data[i] = arguments[i];
    }
    message = new Paho.MQTT.Message(data[0]);
    message.destinationName = data[1] + "";
    client.send(message);
    log(data[0] + " ==> " + data[1]);
    return;
}

function subscribe() {
    client.subscribe("/word");
}

function sendProto() {
    var msgData = car.encode().toArrayBuffer();

    var msgData = new Uint8Array(car.encode().toArrayBuffer());
    var payloadLength = msgData.length;
    var payloadByteLength = msgData.byteLength;
    var headerLength = 8; //包头字节长度
    var b = new Uint8Array(payloadLength + headerLength);
    b.set(msgData, headerLength);
    // fe（MagicNumber）01（版本号）00 3b（总长度） 2711（命令号）0001（Seq）
    // （变长包体：
    // fe 01 00 3b 27 11 00 01 
    var header = [0xfe, 0x01, 0x00, payloadByteLength + headerLength, 0x27, 0x11, 0x00, 0x01];
    for (var i in header) {
        b[i] = header[i];
    }
    console.log(b);
    message = new Paho.MQTT.Message(msgData);
    message.destinationName = "/word";
    client.send(message);
    return;
}

function AESEncrypt(data, key, iv) { //加密
    var key = CryptoJS.enc.Hex.parse(key);
    var iv = CryptoJS.enc.Latin1.parse(iv);
    var encrypted = CryptoJS.AES.encrypt(data, key, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    return encrypted;
}

function AESDecrypt(encrypted, key, iv) { //解密
    var key = CryptoJS.enc.Hex.parse(key);
    var iv = CryptoJS.enc.Latin1.parse(iv);
    var decrypted = CryptoJS.AES.decrypt(encrypted, key, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    return decrypted;
    console.log(decrypted);
    return decrypted.toString(CryptoJS.enc.Utf8);
}
var key = '1231231231231232'; //密钥
var iv = 'Pkcs7';
var encrypted = AESEncrypt('123', key, iv); //密文
console.log(encrypted.toLocaleString());
var decryptedStr = AESDecrypt(encrypted.toLocaleString(), key, iv);
console.log(decryptedStr);
console.log('============Test Proto & AES============');

var u8data = CryptoJS.enc.Hex.parse(car.encodeHex());
console.log('============Encrypt Data============');
console.log(u8data);
var res = AESEncrypt(u8data, key, iv).toString();
console.log('============Encrypted Data============');
console.log(res);
var decryptedStr = AESDecrypt(res, key, iv);

console.log('============Decrypted Data============');
console.log(decryptedStr.words);
// console.log(Car.decodeHex(decrypted));
var v1 = new Uint32Array(decryptedStr.words);
console.log(new Uint8Array(v1.buffer));
console.log(new Uint8Array(car.encode().toArrayBuffer()));
// console.log(Car.decode(v1.buffer));