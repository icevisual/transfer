define(
		[ 'Paho', 'Utils', 'logger', 'event', 'msgHandler', 'protocolStruct',
				'protoDemo', 'CmdApi' ],
		function(Paho, Utils, Logger, Event, Handlers, Simple, simpleData,
				CmdApi) {
			var SmellOpen = {
				defaults : {
					'accessKey' : 'IAzDhpyc0z9yGFajKp2P',
					'accessSecret' : 'HNKGRV2O2oeK7W2jtmFC',
					'logLevel' : 'debug',
					'mqtt' : {
						// 'hostname' : '120.26.109.169',
						'hostname' : '192.168.5.21',
						'port' : '8083',
					},
					'AES' : {
						'key' : 'XqCEMSzhsdWHfwhm',
						'iv' : '00000000000Pkcs7',
					},
					'protobuf' : {
						'headerLength' : 10,
						'headerStruct' : {
							'MAGIC_NUMBER' : 1,
							'VERSION' : 1,
							'BODY_LENGTH' : 2,
							'COMMAND_ID' : 2,
							'SEQUENCE_NUMBER' : 4,
						},
						'headerData' : {
							'MAGIC_NUMBER' : 0xfe,
							'VERSION' : 0x01,
						}
					}
				},
				getEssentialConfig : function(key, defaultValue) {
					var ret = this.getConfig(key, defaultValue);
					if (undefined == ret) {
						throw Error('Essential Config Not Found!');
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
				client : null,
				configs : null,
				evt : null,
				utils : null,
				logger : null,
				protoRoot : null,
				serverConnected : false,
				initialize : function(cfg) {
					this.loadConfigs(cfg);
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
				},
				connect : function(cfg) {

					this.initialize(cfg);

					var mqttConfig = this.getEssentialConfig();
					Logger.debug('SDK Config', mqttConfig);
					// Create a client instance
					this.client = new Paho.MQTT.Client(
							mqttConfig.mqtt.hostname,
							Number(mqttConfig.mqtt.port), mqttConfig.accessKey);

					var hdls = new Handlers(this);

					// set callback handlers

					this.client.onConnectionLost = hdls.onConnectionLost;
					this.client.onMessageArrived = hdls.onMessageArrived;
					this.client.onMessageDelivered = hdls.onMessageDelivered;
					// connect the client
					this.client.connect({
						onSuccess : hdls.onConnect,
						onFailure : function() {
							Logger.error('onConnectFailure', arguments);
						},
						userName : mqttConfig.accessKey,
						password : Utils.SecretEncrpt(mqttConfig.accessKey,
								mqttConfig.accessSecret),
					});
					return this;
				},
				appendOnconnectEvent : function(callback){
					this.evt.addHandler('onServerConnect',callback);
				},
				run : function(callback){
					if(this.isConnected()){
						callback(this);
					}else{
						this.appendOnconnectEvent(callback);
					}
				},
				disconnect : function() {
					this.client.disconnect();
				},
				setConnected : function(){
					this.serverConnected = true;
				},
				isConnected : function(){
					return this.serverConnected
				},
				usingDevice : function(deviceID) {
					// TODO : check the device is avaliable
					return new CmdApi(this, deviceID);
				},
				publish : function(topic, message,options) {
					var MqttMessage = new Paho.MQTT.Message(message);
					MqttMessage.destinationName = topic;
					MqttMessage.qos = Utils.getOptionOrDefault(options,'qos',1);
					MqttMessage.retained = Utils.getOptionOrDefault(options,'retained',false);
//					this.client.send(MqttMessage);
					this.run(function(app){
						app.client.send(MqttMessage);
					});
				},
				subscribe : function(topic) {
					this.run(function(app){
						app.client.subscribe(topic, {
							qos : 1
						});
					});
//					this.client.subscribe(topic, {
//						qos : 2
//					});
				},
				clearRetainedMsg : function(topic) {
					this.publish(topic,'',{
						'retained' : true,
					});
				},
				unsubscribe : function(topic) {
					this.client.unsubscribe(topic);
				},
				decodePayload : function(headerOrCmdID, payloadBytes, options) {
					try {
						var decodeClass, isAES, removeHeaderLength = this
								.getEssentialConfig('protobuf.headerLength'), cmdID = typeof headerOrCmdID == 'object' ? headerOrCmdID.COMMAND_ID
								: headerOrCmdID;

						var decodeClassMap = [];
						decodeClassMap[Simple.SrCmdId.SCI_resp_usedSeconds] = Simple.UsedTimeResponse;
						decodeClassMap[Simple.SrCmdId.SCI_req_playSmell] = Simple.PlaySmell;
						decodeClassMap[Simple.SrCmdId.SCI_req_getDevAttr] = Simple.GetDevAttrsRequest;
						decodeClassMap[Simple.SrCmdId.SCI_resp_getDevAttr] = Simple.DevAttrs;
						decodeClassMap[Simple.SrCmdId.SCI_req_setDevAttr] = Simple.DevAttrs;
						decodeClassMap[Simple.SrCmdId.SCI_resp_featureReport] = Simple.FeatureReportResponse;

						decodeClass = decodeClassMap[cmdID] === undefined ? Simple.BaseResponse
								: decodeClassMap[cmdID];

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
				protoDataPackage : function(protoDataArrayBuffer, cmdId, seqId) {
					if (protoDataArrayBuffer
							&& !(protoDataArrayBuffer instanceof ArrayBuffer)
							&& !(protoDataArrayBuffer instanceof Array)) {
						if (protoDataArrayBuffer['encode'] != undefined
								&& 'function' == typeof protoDataArrayBuffer['encode']) {
							protoDataArrayBuffer = protoDataArrayBuffer
									.encode().toArrayBuffer()
						} else {
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
				sendCmd2Dev : function(deviceId, cmdId, protoDataArrayBuffer,options) {
					var seq = Utils.timestamp();
					var b = this.protoDataPackage(protoDataArrayBuffer, cmdId,
							seq);
					Logger.info('sendCmd2Dev Header', this.analyzeHeader(b));
					this.publish("/" + deviceId, b);
					return seq;
				},
				sendCmdResp : function(seq, cmdId, protoDataArrayBuffer,options) {
					var b = this.protoDataPackage(protoDataArrayBuffer, cmdId,
							seq);
					Logger.info('sendCmdResp Header', this.analyzeHeader(b));
					this.publish("/" + this.getEssentialConfig('accessKey')
							+ '/resp', b,options);
					return true;
				}
			};
			return SmellOpen;
		});
