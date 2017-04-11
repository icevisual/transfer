define(function() {
    var handlers = function(app) {
        var Utils = app.utils,
        Logger = app.logger,
        Simple = app.protoRoot;
        return {
            onConnect: function() {
                var accessKey = app.getEssentialConfig('accessKey');
                Logger.info("onConnect,clientId = " + accessKey);
                var selfTopic = "/" + accessKey;
                app.subscribe(selfTopic);
                // 订阅本身 发送 0xff 确认 订阅的有效性
                app.subscribedThen(selfTopic,function() {
                    var evtKey = 'self-confirm';
                    // send self-confirm frame
                    app.publish(selfTopic, new Uint8Array([0xff]));
                    // self-confirm success & fire onServerConnect
                    app.evt.addHandler(evtKey,function() {
                        app.setConnected();
                        app.evt.fire('onServerConnect', [app]);
                    });
                    // add timeout listener
                    var rid = setTimeout(function() {
                        app.logger.error('self-confirm failed');
                        app.disconnect();
                        app.connect();
                    },1000);
                    // Clear Timeout handler
                    app.evt.addHandler(evtKey,function() {
                        clearTimeout(rid);
                    });
                });
            },
            onConnectionLost: function(responseObject) {
                var onConnectionLostCallback = app.getEssentialConfig('event.onConnectionLost');
                onConnectionLostCallback();
                Logger.info('onConnectionLost', onConnectionLostCallback);
                
                
                Logger.info('onConnectionLost', responseObject);
                if (responseObject.errorCode !== 0) {
                    Logger.info("onConnectionLost:" + responseObject.errorMessage);
                }
            },
            onMessageDelivered: function(message) {
                Logger.info('onMessageDelivered.payloadBytes ' + message.destinationName, message.payloadBytes);
                // console.log('onMessageDelivered.payloadString',message.payloadString);
            },
            onMessageArrived: function(message) {
                // 读取内容，分析指令
                // Logger.debug("onMessageArrived message.qos " +
                // message.qos);
                // Logger.debug("onMessageArrived message.duplicate "
                // + message.duplicate);
                Logger.info("onMessageArrived From Topic " + message.destinationName);
                var headerInfo = app.analyzeHeader(message.payloadBytes);
                if (false === headerInfo) {
                    Logger.debug('Header Not Match');
                    Logger.debug('PayloadBytes', message.payloadBytes);
                    if (message.payloadBytes.length == 1 && message.payloadBytes[0] == 0xff) {
                        Logger.info('self-confirm found');
                        app.evt.fire('self-confirm', []);
                    }
                } else {
                    // TODO : 加入 seq 判断，只处理自己发出的请求
                    Logger.debug('Header Found', headerInfo);
                    var SrCmdId = Utils.EnumGetKey('SCI_', headerInfo.COMMAND_ID);
                    if (SrCmdId !== undefined) {
                        Logger.info('Got SrCmdId ', SrCmdId);
                        Logger.info('Message payloadBytes', message.payloadBytes);
                        // 触发 消息事件
                        app.evt.fire(headerInfo.COMMAND_ID + '/' + headerInfo.SEQUENCE_NUMBER, [headerInfo, message, app]);
                    } else {
                        Logger.warning('SrCmdId Not Match', SrCmdId);
                    }
                }
            }
        };
    }
    return handlers;
});