define(
		[ 'Paho', 'Utils', 'logger', 'event', 'msgHandler', 'protocolStruct',
				 'CmdApi' ],
		function(Paho, Utils, Logger, Event, Handlers, Simple,
				CmdApi) {
			var SmellOpen = {
				defaults : {
//				    'auth' : {
//				        'accessKey' : 'accessKey',
//	                    'SecretKey' : 'accessSecret',
//				    },
					'accessKey' : 'accessKey',
					'accessSecret' : 'accessSecret',
					'logLevel' : 'error',
					'mqtt' : {
					    'basepath' : 'http://open.smell.com/',
						'hostname' : '120.26.109.169',
						'port' : '8083',
					},
					'clientIDWithRandom' : true,
					'event' : {
						'onSuccess' : function() {},
						'onFailure' : function() {},
						'onConnectionLost' : function() {},
					},
					'registerEvent' : {
					    
					},
					'apiTimeout' : 5,
//					'AES' : {// AES 加密信息，当前未使用
//						'key' : 'XqCEMSzhsdWHfwhm',
//						'iv' : '00000000000Pkcs7',
//					},
					'maxConnectAttampt' : 5,// 最大自动重连次数
					'protobuf' : {// protocol buffer 结构信息
						'headerLength' : 10, // 包头长度
						'headerStruct' : { // 包头结构
							'MAGIC_NUMBER' : 1,
							'VERSION' : 1,
							'BODY_LENGTH' : 2,
							'COMMAND_ID' : 2,
							'SEQUENCE_NUMBER' : 4,
						},
						'headerData' : { // 包头默认数据
							'MAGIC_NUMBER' : 0xfe,
							'VERSION' : 0x01,
						},
						'decodeClassMap' : {},// 指令对应解码类映射
						'listenCmdMap' : {},// 请求指令和回应指令映射表
					}
				},
				// 减少重连次数
				decreaseConnectAttampt : function(){
					this.configs.maxConnectAttampt -- ;
				},
				// 获取必要配置信息，无则报错
				getEssentialConfig : function(key, defaultValue) {
					var ret = this.getConfig(key, defaultValue);
					if (undefined == ret) {
						throw Error('Essential Config Not Found('+key+')');
					}
					return ret;
				},
				getConfig : function(key, defaultValue) {
					var config = this.configs, keys;
					if (undefined == key) {
						return config;
					}
					if (key.indexOf('.') != -1) {
						keys = key.split('.');
					} else {
						keys = [ key ];
					}
					for ( var i in keys) {
						if (undefined == config[keys[i]]) {
							return defaultValue;
						} else {
							config = config[keys[i]];
						}
					}
					return config;
				},
				// 合并默认配置金额用户配置
				loadConfigs : function(configs) {
					this.configs = this.defaults;
					for ( var i in configs) {
						if (configs[i] instanceof Object) {
							for ( var j in configs[i]) {
								this.configs[i][j] = configs[i][j];
							}
						} else {
							this.configs[i] = configs[i] ;
						}
					}
				},
				client : null,// mqtt 客户端
				configs : null,// 配置信息
				evt : null,// 事件引擎
				utils : null,// 工具类
				logger : null,// 日志类
				protoRoot : null,// protocol buffer 协议 结构跟目录
				serverConnected : false,// mqtt server 连接状态
				subscribeArray : {},// 订阅信息
				// 初始化 映射表
				initProtocolConfig : function(){
					// init decodeClassMap
					var dcm = {}, lcm = {},SrCmdId = Simple.SrCmdId;
					dcm[SrCmdId.SCI_RESP_USEDSECONDS] = Simple.UsedTimeResponse;
					dcm[SrCmdId.SCI_REQ_PLAYSMELL] = Simple.PlayRequest;
					dcm[SrCmdId.SCI_REQ_GETDEVATTR] = Simple.GetDevAttrsRequest;
					dcm[SrCmdId.SCI_RESP_GETDEVATTR] = Simple.GetDevAttrsResponse;
					dcm[SrCmdId.SCI_REQ_SETDEVATTR] = Simple.SetDevAttrsRequest;
					dcm[SrCmdId.SCI_RESP_DEVICE_MODEL] = Simple.DeviceModelResponse;
					dcm[SrCmdId.SCI_REQ_STOP_PLAY] = Simple.StopPlayRequest;
					dcm[SrCmdId.SCI_REQ_REMOVE_TASK] = Simple.RemoveTaskRequest;
					dcm[SrCmdId.SCI_REQ_SET_CONTROL_ATTR] = Simple.SetControlAttrRequest;
					dcm[SrCmdId.SCI_RESP_GET_SMELL_LIST] = Simple.SmellListResponse;
					dcm[SrCmdId.SCI_RESP_CURRENT_PLAY] = Simple.CurrentPlayRespones;
                    
					this.configs.protobuf.decodeClassMap = dcm;
					// init listenCmdMap
					
					lcm[SrCmdId.SCI_REQ_SLEEP] = SrCmdId.SCI_RESP_SLEEP;
					lcm[SrCmdId.SCI_REQ_WAKEUP] = SrCmdId.SCI_RESP_WAKEUP;
					lcm[SrCmdId.SCI_REQ_USEDSECONDS] = SrCmdId.SCI_RESP_USEDSECONDS;
					lcm[SrCmdId.SCI_REQ_PLAYSMELL] = SrCmdId.SCI_RESP_PLAYSMELL;
					lcm[SrCmdId.SCI_REQ_GETDEVATTR] = SrCmdId.SCI_RESP_GETDEVATTR;
					lcm[SrCmdId.SCI_REQ_SETDEVATTR] = SrCmdId.SCI_RESP_SETDEVATTR;
					lcm[SrCmdId.SCI_REQ_DEVICE_MODEL] = SrCmdId.SCI_RESP_DEVICE_MODEL;
					lcm[SrCmdId.SCI_REQ_STOP_PLAY] = SrCmdId.SCI_RESP_STOP_PLAY;
					lcm[SrCmdId.SCI_REQ_REMOVE_TASK] = SrCmdId.SCI_RESP_REMOVE_TASK;
					lcm[SrCmdId.SCI_REQ_SET_CONTROL_ATTR] = SrCmdId.SCI_RESP_SET_CONTROL_ATTR;
					lcm[SrCmdId.SCI_REQ_GET_SMELL_LIST] = SrCmdId.SCI_RESP_GET_SMELL_LIST;
					lcm[SrCmdId.SCI_REQ_STOP_ALL] = SrCmdId.SCI_RESP_STOP_ALL;
					lcm[SrCmdId.SCI_REQ_CURRENT_PLAY] = SrCmdId.SCI_RESP_CURRENT_PLAY;
                    
					this.configs.protobuf.listenCmdMap = lcm;
				},
				registerDeviceEvent : function(){
				    var registerEvent = this.getConfig('registerEvent');
				    for(var i in registerEvent){
				        if(this.utils.isFunction(registerEvent[i])){
				            this.evt.addHandler(i,registerEvent[i]);
				        }
				    }
				},
				// 初始化 
				initialize : function(cfg) {
					this.loadConfigs(cfg);
					this.initProtocolConfig();
					this.evt = Event;
					this.utils = Utils;
					this.logger = Logger;
					this.logger.setLevel(this.getEssentialConfig('logLevel'));
					this.protoRoot = Simple;

					this.utils.EnumMapRegister(Simple.SrCmdId, 'SCI_');
					this.utils.EnumMapRegister(Simple.SrDevAttrType, 'SDST_');
					this.utils.EnumMapRegister(Simple.SrFeatureType, 'SFT_');
					this.utils.EnumMapRegister(Simple.SrFeatureAttrType,
							'SFAT_');
					
					this.registerDeviceEvent();
				},
//				connect : function(username,password,options) {
//				    
//				}
				// 连接mqtt server
				connect : function(cfg) {
					
					this.initialize(cfg);

					if(this.getEssentialConfig('maxConnectAttampt') > 0){
						this.decreaseConnectAttampt();
					}else{
						throw Error('Reach Max Connect Attampt,Connect Failed'); 
					}
					
					var mqttConfig = this.getEssentialConfig();
					Logger.debug('SDK Config', mqttConfig);
					// Create a client instance
					var clientIDWithRandom = this.getEssentialConfig('clientIDWithRandom');
					
					var clientInstanceID = mqttConfig.accessKey;
                    
					if(clientIDWithRandom){
					    clientInstanceID = clientInstanceID + '#' + Utils.getRandomNum4();
					}
					
					this.client = new Paho.MQTT.Client(
							mqttConfig.mqtt.hostname,
							Number(mqttConfig.mqtt.port), clientInstanceID );

					var hdls = new Handlers(this);

					// set callback handlers

					this.client.onConnectionLost = hdls.onConnectionLost;
					this.client.onMessageArrived = hdls.onMessageArrived;
					this.client.onMessageDelivered = hdls.onMessageDelivered;
					var onCnnError = this.getEssentialConfig('event.onFailure'),
					onCnnSuccess = this.getEssentialConfig('event.onSuccess');
					
					var connFailCallback = (onCnnError && Utils.isFunction (onCnnError)) ? onCnnError : function() {Logger.error('Connect Error',arguments);};
					var connSuccessCallback = (onCnnSuccess && Utils.isFunction (onCnnSuccess)) ? onCnnSuccess : function() {};
					this.evt.addHandler('onServerConnect',connSuccessCallback);
					var app = this;
					var connectFailCallback = function(error){
					    app.evt.clearHandler('onServerConnect');
					    connFailCallback(error,app);
					}
					// connect the client
					this.client.connect({
//						cleanSession : true,
						onSuccess : hdls.onConnect,
						onFailure : connectFailCallback,
						userName : mqttConfig.accessKey,
						password : Utils.SecretEncrpt(mqttConfig.accessKey,
								mqttConfig.accessSecret),
					});
					return this;
				},
				// 连接后 执行
				connectedThen : function(callback){
					if(this.isConnected()){
						callback(this);
					}else{
						this.evt.addHandler('onServerConnect',callback);
					}
				},
				disconnect : function() {
					this.client.disconnect();
				},
				// 设置server 连接状态
				setConnected : function(){
					this.serverConnected = true;
				},
				// 获取 server 连接状态
				isConnected : function(){
					return this.serverConnected;
				},
				// 获取设备的控制权限 opt 包含超时时间
				usingDevice : function(deviceAccess,opt) {
					// TODO : check the device is avaliable
					return new CmdApi(this, deviceAccess,opt);
				},
				bindDevice : function(deviceName,devicePass,options){
				    var defaultOptions = {
				        'onSuccess' : function(){
				            console.log('onSuccess');
				        },
				        'onFailure' : function(){
				            console.log('onFailure');
				        }
				    };
				    defaultOptions = Utils.extendOptions(defaultOptions,options);
				    console.log('defaultOptions',defaultOptions);
				    var app = this;
				    var basepath = app.getEssentialConfig('mqtt.basepath');
			        var accessKey = app.getEssentialConfig('accessKey'),
			        accessSecret = app.getEssentialConfig('accessSecret');
			        app.utils.ajax({
			            'url' : basepath + 'api/mqtt/bindDevice',
			            'async' : false,
			            'method' : 'POST',
			            'data' : {
			                'developer_access' : accessKey,
			                'developer_pass' : app.utils.SecretEncrpt(accessKey,accessSecret),
			                'device_name' : deviceName,
			                'device_pass' : app.utils.SecretEncrpt('',devicePass),
			            },
			            'success' : function(resp){
			                app.logger.info(resp);
			                if(1 == resp.code){
			                    Utils.fireFunction(defaultOptions.onSuccess,[resp,app]);
			                    return true;
			                }else{
			                    Utils.fireFunction(defaultOptions.onFailure,[resp,app]);
			                    errorMsg = resp.msg;
			                }
			            },
			            'error' : function(resp){
			                app.logger.error(resp);
			                Utils.fireFunction(defaultOptions.onFailure,[app]);
			                return false;
			            }
			        });
				},
				unbindDevice : function(deviceAccess,options){
                    var defaultOptions = {
                        'onSuccess' : function(){
                            console.log('onSuccess');
                        },
                        'onFailure' : function(){
                            console.log('onFailure');
                        }
                    };
                    defaultOptions = Utils.extendOptions(defaultOptions,options);
                    console.log('defaultOptions',defaultOptions);
                    var app = this;
                    var basepath = app.getEssentialConfig('mqtt.basepath');
                    var accessKey = app.getEssentialConfig('accessKey'),
                    accessSecret = app.getEssentialConfig('accessSecret');
                    app.utils.ajax({
                        'url' : basepath + 'api/mqtt/unbindDevice',
                        'async' : false,
                        'method' : 'POST',
                        'data' : {
                            'developer_access' : accessKey,
                            'developer_pass' : app.utils.SecretEncrpt(accessKey,accessSecret),
                            'device_access' : deviceAccess
                        },
                        'success' : function(resp){
                            app.logger.info(resp);
                            if(1 == resp.code){
                                Utils.fireFunction(defaultOptions.onSuccess,[resp,app]);
                                return true;
                            }else{
                                Utils.fireFunction(defaultOptions.onFailure,[resp,app]);
                                errorMsg = resp.msg;
                            }
                        },
                        'error' : function(resp){
                            app.logger.error(resp);
                            Utils.fireFunction(defaultOptions.onFailure,[app]);
                            return false;
                        }
                    });
                },
				errorReport : function(errorData){
				    var app = this;
				    var basepath = app.getEssentialConfig('mqtt.basepath');
//				    basepath = 'http://open.smell.com/';
                    var accessKey = app.getEssentialConfig('accessKey'),
                    accessSecret = app.getEssentialConfig('accessSecret')
                    totpPass = app.utils.SecretEncrpt(accessKey,accessSecret);
                    app.utils.ajax({
                        'url' : basepath + 'api/mqtt/errorReport',
                        'method' : 'POST',
                        'data' : {
                            'developer_access' : accessKey,
                            'developer_pass' : totpPass,
                            'error' : errorData,
                        },
                        'success' : function(resp){
                            if(1 != resp.code){
                                app.logger.error(resp);
                            }
                        },
                        'error' : function(resp){
                            app.logger.error(resp);
                        }
                    });
				},
				// 发布消息
				publish : function(topic, message,options) {
					var MqttMessage = new Paho.MQTT.Message(message);
					MqttMessage.destinationName = topic;
					MqttMessage.qos = Utils.getOptionOrDefault(options,'qos',1);
					MqttMessage.retained = Utils.getOptionOrDefault(options,'retained',false);
					
					this.logger.debug('publish ' + topic);
					this.client.send(MqttMessage);
				},
				// 订阅消息
				subscribe : function(topic) {
					var _this = this;
					this.logger.debug('subscribe ' + topic);
					this.client.subscribe(topic, {
						qos : 1,
						invocationContext : {'topic':topic},
						onSuccess : function(obj){
							_this.logger.debug('subscribe onSuccess ' + obj.invocationContext.topic);
							_this.setSubscribed(obj.invocationContext.topic);
							_this.evt.fire('onSubscribed-' + obj.invocationContext.topic,[_this]);
						},
						onFailure : function(obj){
							_this.logger.error('subscribe onFailure ' + obj.invocationContext.topic);
							_this.evt.fire('onSubscribedFailure-' + obj.invocationContext.topic,[_this]);
						}
					});
				},
				// 设置订阅状态
				setSubscribed : function(topic){
					this.subscribeArray[topic] = 1;
				},
				// 设置取消订阅的状态
				setUnsubscribed : function(topic){
					this.subscribeArray[topic] = 0;
				},
				// 获取订阅状态
				isSubscribed : function(topic){
					if(1 == this.subscribeArray[topic]){
						return true;
					}
					return false;
				},
				// 订阅后触发
				subscribedThen : function(topic,callback){
					if(this.isSubscribed(topic)){
						callback(this);
					}else{
						this.evt.addHandler('onSubscribed-' + topic ,callback);
					}
				},
				// 取消订阅
				unsubscribe : function(topic) {
					if(this.isSubscribed(topic)){
						this.client.unsubscribe(topic);
						this.setUnsubscribed(topic);
					}
				},
				// 清除 Retained 消息
				clearRetainedMsg : function(topic) {
					this.publish(topic,'',{
						'retained' : true,
					});
				},
				// payload protobuf 解码
				decodePayload : function(headerOrCmdID, payloadBytes, options) {
					try {
						var decodeClass, isAES, removeHeaderLength = this
								.getEssentialConfig('protobuf.headerLength'), cmdID = typeof headerOrCmdID == 'object' ? headerOrCmdID.COMMAND_ID
								: headerOrCmdID;
						
						var decodeClassMap = this.getEssentialConfig('protobuf.decodeClassMap');
						decodeClass = decodeClassMap[cmdID] === undefined ? Simple.BaseResponse
								: decodeClassMap[cmdID];
						this.logger.debug('The cmdID is ',cmdID);
						if (undefined !== options) {
							decodeClass = options['options'] || decodeClass;
							removeHeaderLength = options['removeHeaderLength']
									|| removeHeaderLength;
							cmdID = options['cmdID'] || cmdID;
							isAES = options['isAES'] || false;
						}

						var payloadBytesBody = payloadBytes;
						if (removeHeaderLength) {
							// Get payload body
							payloadBytesBody = payloadBytes
									.slice(removeHeaderLength);
						}
						var decodeObj;
						Logger.debug('payloadBytesBody', payloadBytesBody);
						if (isAES === true) {
							// Convert 2 hex string
							var payloadHex = Utils
									.intArray2HexStr(payloadBytesBody);
							Logger.debug('payloadHexString', payloadHex);
							// Convert 2 base64 string
							var b64str = CryptoJS.enc.Base64
									.stringify(CryptoJS.enc.Hex
											.parse(payloadHex));
							// AES decrypt
							var AESCfg = this.getEssentialConfig('AES');
							var res1 = Utils.AESDecrypt(b64str, AESCfg.key,
									AESCfg.iv);
							Logger.debug('AESDecrypted Data', Utils
									.hex2IntArray(CryptoJS.enc.Hex
											.stringify(res1)));
							// Proto decode
							decodeObj = decodeClass.decodeHex(res1.toString());
						} else {
							decodeObj = decodeClass.decode(payloadBytesBody);
						}
						Logger.debug('Proto Data', decodeObj);
						return decodeObj;
					} catch (e) {
						Logger.warning('protobuf decode error', arguments);
					}
					return false;
				},
				// 组装 包头
				assembleHeader : function(headerLength, bodyLength, cmdID,
						seqID) {
					// if(undefined == seqID){
					// seqID = Utils.timestamp();
					// }
					var headerStruct = this
							.getEssentialConfig('protobuf.headerStruct'), preinstallHeaderData = this
							.getEssentialConfig('protobuf.headerData');
					var headerData = [ preinstallHeaderData.MAGIC_NUMBER,
							preinstallHeaderData.VERSION, bodyLength, cmdID,
							seqID ];
					var headerByteArray = new Uint8Array(headerLength), index = 0, i = 0;
					for ( var k in headerStruct) {
						var v = headerStruct[k];
						while (v--)
							headerByteArray[index++] = headerData[i] >> (v * 8) & 0xff;
						i++;
					}
					return headerByteArray;
				},
				// 分析包头
				analyzeHeader : function(payloadBytes) {
					var headerStruct = this
							.getEssentialConfig('protobuf.headerStruct'), headerData = this
							.getEssentialConfig('protobuf.headerData');
					var payloadArray = new Uint8Array(payloadBytes);
					var headerLength = 0;
					for ( var k in headerStruct) {
						headerLength += headerStruct[k];
					}
					if (payloadArray.length >= headerLength) {
						var headerBytes = payloadArray.slice(0, headerLength);
						if (headerBytes[0] == headerData.MAGIC_NUMBER) {
							var ret = {}, index = 0;
							for ( var k in headerStruct) {
								ret[k] = 0;
								var v = headerStruct[k];
								while (v--)
									ret[k] |= headerBytes[index++] << (v * 8);
							}
							return ret;
						}
					}
					return false;
				},
				// protobuf 数据打包
				protoDataPackage : function(protoDataArrayBuffer, cmdId, seqId) {
					if (protoDataArrayBuffer
							&& !(protoDataArrayBuffer instanceof ArrayBuffer)
							&& !(protoDataArrayBuffer instanceof Array)) {
						if (protoDataArrayBuffer['encode'] != undefined
								&& 'function' == typeof protoDataArrayBuffer['encode']) {
							protoDataArrayBuffer = protoDataArrayBuffer
									.encode().toArrayBuffer()
						} else {
							this.logger.error('Data Type Error',protoDataArrayBuffer);
							throw Error('Data Type Error');
						}
					}
					Logger.debug('protoDataArrayBuffer', protoDataArrayBuffer);
					var msgData = new Uint8Array(protoDataArrayBuffer);
					Logger.debug('payload Bytes', msgData);
					var payloadLength = msgData.length;
					var payloadByteLength = msgData.byteLength;
					var headerLength = this
							.getEssentialConfig('protobuf.headerLength'); // 包头字节长度
					var b = new Uint8Array(payloadLength + headerLength);
					b.set(msgData, headerLength);
					Logger.debug('protoDataPackage len', payloadByteLength
							+ headerLength);
					var header = this.assembleHeader(headerLength,
							payloadByteLength, cmdId, seqId);
					Logger.debug('header', header);
					b.set(header, 0);
					return b;
				},
				// protobuf 数据打包，带AES加密
				protoDataPackageWithAES : function(protoData, cmdId, seqId) {
					// Word Array
					var hexData = CryptoJS.enc.Hex.parse(protoData.encodeHex());
					// AES encrypt
					var AESCfg = this.getEssentialConfig('AES');
					var encryptData = Utils.AESEncrypt(hexData, AESCfg.key,
							AESCfg.iv);
					// Base64 Decode 2 Word Array
					var base64Words = CryptoJS.enc.Base64.parse(encryptData
							.toString());
					// Convert 2 hex String
					var hexEncryptedStr = CryptoJS.enc.Hex
							.stringify(base64Words);
					// Convert 2 int Array
					var intArray = Utils.hex2IntArray(hexEncryptedStr);
					// Convert 2 ArrayBuffer
					var u8ArrayBuffer = new Uint8Array(intArray).buffer;
					// Call package function
					return this.protoDataPackage(u8ArrayBuffer, cmdId, seqId);
				},
				// 发送指令到设备
				sendCmd2Dev : function(deviceId, cmdId, protoDataArrayBuffer,options) {
					var seq = Utils.getSequence();
					this.logger.debug('sendCmd2Dev Seq',seq);
					var b = this.protoDataPackage(protoDataArrayBuffer, cmdId,seq);
					Logger.info('sendCmd2Dev Header', this.analyzeHeader(b));
					this.publish("/" + deviceId, b);
					return seq;
				},
				// 回复指令消息
				sendCmdResp : function(seq, cmdId, protoDataArrayBuffer,options) {
					var b = this.protoDataPackage(protoDataArrayBuffer, cmdId,seq);
					Logger.info('sendCmdResp Header', this.analyzeHeader(b));
					this.publish("/" + this.getEssentialConfig('accessKey') + '/resp', b,options);
					return true;
				}
			};
			return SmellOpen;
		});
