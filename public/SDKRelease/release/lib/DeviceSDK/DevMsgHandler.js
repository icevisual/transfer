define(['device'],function(Dev) {
    var handlers = function(app) {
        var Utils = app.utils,
        Logger = app.logger,
        Simple = app.protoRoot,
        Event = app.evt;
        var evtPrefix = "DEV-";
        
        var MsgHandler = {
            SCI_REQ_SLEEP: function(header, message) {
                app.logger.debug('Got SCI_REQ_SLEEP');
                var resp = new Simple.BaseResponse(Simple.SrErrorCode.SEC_SUCCESS, 'SCI_req_sleep');
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_SLEEP, resp
                //								,{'retained' : true}
                );
                Event.loopFire(evtPrefix + 'SCI_REQ_SLEEP', [header, decode]);
                Dev.actions.sleep();
                
            },
            SCI_REQ_WAKEUP: function(header, message) {
                app.logger.debug('Got SCI_REQ_WAKEUP');
                var resp = new Simple.BaseResponse(Simple.SrErrorCode.SEC_SUCCESS, 'SCI_req_wakeup');
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_WAKEUP, resp);
                Event.loopFire(evtPrefix + 'SCI_REQ_WAKEUP', [header, decode]);
                Dev.actions.wakeup();
            },
            SCI_REQ_USEDSECONDS: function(header, message) {
                
                var used = Dev.getUsedTime();
                
                var resp = new Simple.UsedTimeResponse({
                    'response': {
                        'code': Simple.SrErrorCode.SEC_SUCCESS,
                        'msg': 'SCI_REQ_USEDSECONDS'
                    },
                    'used_time': used
                });
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_USEDSECONDS, resp);
                
                Event.loopFire(evtPrefix + 'SCI_REQ_USEDSECONDS', [header, decode]);
                
            },
            SCI_REQ_PLAYSMELL: function(header, message, app) {
                var decode = app.decodePayload(header, message.payloadBytes);
                app.logger.info('SCI_REQ_PLAYSMELL', decode);

                var resp = new Simple.BaseResponse({
                    'code': Simple.SrErrorCode.SEC_ACCEPT,
                    'msg': 'SCI_REQ_PLAYSMELL'
                });
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_PLAYSMELL, resp);
                
                Dev.actions.playSmell(decode);
                
                Event.loopFire(evtPrefix + 'SCI_REQ_PLAYSMELL', [header, decode]);
                
                
            },
            SCI_REQ_GETDEVATTR: function(header, message, app) {
                // // 设备属性类型
                var decode = app.decodePayload(header, message.payloadBytes);
                var attrs = [];

                for (var i in decode.attrs) {
                    var attrName = app.utils.EnumGetKey('SDST_', decode.attrs[i]);
                    attrs.push({
                        'attr': decode.attrs[i],
                        'value': Dev.getAttr(attrName)
                    });
                }
                // in Simple {'prefix_name':int_value}
                // Get id by name
                // Get name by id
                // Type , Prefix ,Map
                var resp = new Simple.GetDevAttrsResponse({
                    'attrs': attrs
                });
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_GETDEVATTR, resp);
            },
            SCI_REQ_SETDEVATTR: function(header, message, app) {
                var decode = app.decodePayload(header, message.payloadBytes);
                app.logger.info('decode Obj', decode);
                var attrs = [];
                for (var i in decode.attrs) {
                    var attrName = app.utils.EnumGetKey('SDST_', decode.attrs[i]['attr']);
                    var ret = Dev.setAttr(attrName, decode.attrs[i]['value']);
                    app.logger.info('decode attrName,ret', attrName, ret);
                }
                var resp = new Simple.BaseResponse({
                    'code': Simple.SrErrorCode.SEC_SUCCESS,
                    'msg': 'SCI_REQ_SETDEVATTR'
                });
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_SETDEVATTR, resp);
            },
            SCI_REQ_DEVICE_MODEL: function(header, message, app) {
                var SrCmdId = Simple.SrCmdId;
                var model = Dev.getModel();
                model['response'] = {
                    'code': Simple.SrErrorCode.SEC_SUCCESS,
                    'msg': 'SCI_REQ_DEVICE_MODEL'
                };
                app.logger.debug('model data',model);
                var DeviceModelResponse = new Simple.DeviceModelResponse(model);
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_DEVICE_MODEL, DeviceModelResponse);
            },
            SCI_REQ_STOP_PLAY : function(header, message, app) {
            	var decode = app.decodePayload(header, message.payloadBytes);
                var resp = new Simple.BaseResponse({
                    'code': Simple.SrErrorCode.SEC_SUCCESS,
                    'msg': 'SCI_REQ_STOP_PLAY'
                });
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_STOP_PLAY, resp);
                
                Event.loopFire(evtPrefix + 'SCI_REQ_STOP_PLAY', [header, decode]);
                
                
            },
            SCI_REQ_REMOVE_TASK : function(header, message, app) {
            	var decode = app.decodePayload(header, message.payloadBytes);
                var resp = new Simple.BaseResponse({
                    'code': Simple.SrErrorCode.SEC_SUCCESS,
                    'msg': 'SCI_REQ_REMOVE_TASK'
                });
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_REMOVE_TASK, resp);
            },
            SCI_REQ_SET_CONTROL_ATTR : function(header, message, app) {
                var decode = app.decodePayload(header, message.payloadBytes);
                var resp = new Simple.BaseResponse({
                    'code': Simple.SrErrorCode.SEC_ACCEPT,
                    'msg': 'SCI_REQ_SET_CONTROL_ATTR'
                });
                console.log(resp);
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_SET_CONTROL_ATTR, resp);
                
                Dev.actions.setControlAttr(decode);
            },
            SCI_REQ_GET_SMELL_LIST : function(header, message, app) {
                var decode = app.decodePayload(header, message.payloadBytes);
                var resp = new Simple.SmellListResponse({
                    'response' : {
                        'code': Simple.SrErrorCode.SEC_SUCCESS,
                        'msg': 'SCI_REQ_GET_SMELL_LIST'
                    },
                    'smell_list' : Dev.getDeviceSmellList()
                });
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_GET_SMELL_LIST, resp);
                
                
                Event.loopFire(evtPrefix + 'SCI_REQ_GET_SMELL_LIST', [header, decode]);
                
            },
            SCI_REQ_STOP_ALL : function(header, message, app) {
                var decode = app.decodePayload(header, message.payloadBytes);
                var resp = new Simple.BaseResponse({
                    'code': Simple.SrErrorCode.SEC_SUCCESS,
                    'msg': 'SCI_REQ_STOP_ALL'
                });
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_STOP_ALL, resp);
                
                Event.loopFire(evtPrefix + 'SCI_REQ_STOP_ALL', [header, decode]);
                
            },
            SCI_REQ_CURRENT_PLAY : function(header, message, app) {
                var decode = app.decodePayload(header, message.payloadBytes);
                
                var resp = new Simple.CurrentPlayRespones({
                    'response' : {
                        'code': Simple.SrErrorCode.SEC_SUCCESS,
                        'msg': 'SCI_REQ_CURRENT_PLAY'
                    },
                    'bottle': '00000001b',
                    'remain_second': 3,
                    'total_second': 6
                });
                app.sendCmdResp(header.SEQUENCE_NUMBER, Simple.SrCmdId.SCI_RESP_CURRENT_PLAY, resp);
                
                Event.loopFire(evtPrefix + 'SCI_REQ_CURRENT_PLAY', [header, decode]);

            }
        };
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
                    Logger.debug('Header Found', headerInfo);
                    
                    var prefix = 'SCI_', SrCmdId = Utils.EnumGetKey(prefix, headerInfo.COMMAND_ID);
                    Logger.info('Got SrCmdId ', SrCmdId);
                    if (undefined != SrCmdId && undefined != MsgHandler[ prefix + SrCmdId]) {
                        MsgHandler[prefix + SrCmdId].apply(null, [headerInfo, message, app]);
                    }else{
                    	Logger.warning('SrCmdId Not Match',SrCmdId);
                    }
                    
                }
            }
        };
    }
    return handlers;
});



