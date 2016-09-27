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


function log(){
    for(var i in arguments){
        console.log("["+ now() +"] " + arguments[i]);
    }
}

// called when the client connects
function onConnect() {
    // Once a connection has been made, make a subscription and send a message.
    log("onConnect,clientId = " +clientId);
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
// called when a message arrives
function onMessageArrived(message) {
    var data = message.payloadString;
    console.log(message);
    log("onMessageArrived:" + data);
    try{
        var dd = Car.decode(message.payloadBytes);
        console.log(dd.model);
    }catch (e){
        console.log('error');
    }
    // var m = Car.decode(message.payloadString);
    // log("onMessageArrived:" + m.model);
}
function sendMessage(){
    var data = ['','/'+clientId];
    for(var i in arguments){
        data[i] = arguments[i];
    }
    message = new Paho.MQTT.Message(data[0]);
    message.destinationName = data[1]+"";
    client.send(message);
    log(data[0] + " ==> " + data[1]);
    return;
}

function subscribe(){
    client.subscribe("/word");
}

function sendProto(){
    message = new Paho.MQTT.Message(car.encode().toArrayBuffer());
    message.destinationName = "/word";
    client.send(message);
    return;
}


