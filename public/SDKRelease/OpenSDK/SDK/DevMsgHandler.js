define(
		[ 'Utils', 'logger', 'protocolStruct', 'device' ],
		function(Utils, Logger, Simple, Dev) {
			var handler = function(SmellOpen) {

				var map = Utils.array_reverse(Simple.SrCmdId);

				var MsgHandler = {
					SCI_req_sleep : function(header, message) {
						Logger.debug('Got SCI_req_sleep');
						var resp = new Simple.BaseResponse(
								Simple.SrErrorCode.SEC_success,
								[ 'SCI_req_sleep' ]);
						SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
								Simple.SrCmdId.SCI_resp_sleep, resp,{'retained' : true});
					},
					SCI_req_wakeup : function(header, message) {
						Logger.debug('Got SCI_req_wakeup');
						var resp = new Simple.BaseResponse(
								Simple.SrErrorCode.SEC_success,
								[ 'SCI_req_wakeup' ]);
						SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
								Simple.SrCmdId.SCI_resp_wakeup, resp);
					},
					SCI_req_usedSeconds : function(header, message) {
						var resp = new Simple.UsedTimeResponse({
							'response' : {
								'code' : Simple.SrErrorCode.SEC_success,
								'data' : [ 'SCI_req_usedSeconds' ]
							},
							'usedTime' : [ {
								'bottle' : '000000001',
								'time' : 10001
							}, {
								'bottle' : '000000002',
								'time' : 10002
							}, {
								'bottle' : '000000003',
								'time' : 10003
							} ]
						});
						SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
								Simple.SrCmdId.SCI_resp_usedSeconds, resp);
					},
					SCI_req_playSmell : function(header, message, app) {
						var decode = app.decodePayload(header,
								message.payloadBytes);
						app.logger.info('SCI_req_playSmell', decode);

						var resp = new Simple.BaseResponse({
							'code' : Simple.SrErrorCode.SEC_accept,
							'data' : [ 'SCI_req_playSmell' ]
						});
						SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
								Simple.SrCmdId.SCI_resp_playSmell, resp);

						// run smell play
						// resp = new Simple.BaseResponse({
						// 'code' : Simple.SrErrorCode.SEC_success,
						// 'data' : [ 'SCI_req_playSmell' ]
						// });
						// SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
						// Simple.SrCmdId.SCI_resp_playSmell, resp);

					},
					SCI_req_getDevAttr : function(header, message, app) {
						// // 设备属性类型
						// enum SrDevAttrType
						// {
						// SDST_deviceID = 1; // 设备唯一标识
						// SDST_deviceName = 2;// 设备名字
						// SDST_deviceType = 3;// 设备类别
						// SDST_mac = 4; // MAC
						// SDST_wifiSsid = 5; // wifi ssid
						// SDST_wifiPwd = 6;// wifi 密码
						// SDST_netConnectState = 7;// 网络连接状态
						// SDST_bleConnectState = 8;// 蓝牙连接状态
						// SDST_logState = 9;// 日志开启状态
						// SDST_datetime = 10;// 时间
						// SDST_uptime = 11;// 设备上次开机时间
						// SDST_downtime = 12;// 设备上次关机时间
						// }
						var decode = app.decodePayload(header,
								message.payloadBytes);
						var attrs = [];

						for ( var i in decode.attrs) {
							var attrName = app.utils.EnumGetKey('SDST_',
									decode.attrs[i]);
							attrs.push({
								'attr' : decode.attrs[i],
								'value' : Dev.getAttr(attrName)
							});
						}

						// in Simple {'prefix_name':int_value}
						// Get id by name
						// Get name by id
						// Type , Prefix ,Map

						var resp = new Simple.DevAttrs({
							'attrs' : attrs
						});
						SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
								Simple.SrCmdId.SCI_resp_getDevAttr, resp);

					},
					SCI_req_setDevAttr : function(header, message, app) {
						var decode = app.decodePayload(header,
								message.payloadBytes);
						app.logger.info('decode Obj', decode);
						var attrs = [];
						for ( var i in decode.attrs) {
							var attrName = app.utils.EnumGetKey('SDST_',
									decode.attrs[i]['attr']);
							var ret = Dev.setAttr(attrName,
									decode.attrs[i]['value']);
							app.logger.info('decode attrName,ret', attrName,
									ret);
						}

						var resp = new Simple.BaseResponse({
							'code' : Simple.SrErrorCode.SEC_success,
							'data' : [ 'SCI_req_setDevAttr' ]
						});
						SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
								Simple.SrCmdId.SCI_resp_setDevAttr, resp);
					},
					SCI_req_featureReport : function(header, message, app) {
						var SrFeatureType = Simple.SrFeatureType;
						var SrFeatureAttrType = Simple.SrFeatureAttrType;
						var FeatureReportResponse = new Simple.FeatureReportResponse(
								{
									'feature' : [ {
										'type' : SrFeatureType.SFT_switch,
										'attrs' : [
												{
													'attr' : SrFeatureAttrType.SFAT_name,
													'value' : '测试开关0001'
												},
												{
													'attr' : SrFeatureAttrType.SFAT_name,
													'value' : '测试开关0002'
												} ]
									} ]
								});

						var features = Dev.getFeature();
						var obj = {
							'feature' : [],
						};
						for ( var i in features) {
							var fea = {
								'type' : app.utils.EnumGetValue('SFT_',
										features[i].type),
								'attrs' : [],
							};
							for ( var j in features[i].attrs) {
								fea.attrs
										.push({
											'attr' : app.utils.EnumGetValue(
													'SFAT_', j),
											'value' : features[i].attrs[j]
										});
							}
							obj.feature.push(fea);
						}
						var FeatureReportResponse = new Simple.FeatureReportResponse(
								obj);
						SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
								Simple.SrCmdId.SCI_resp_featureReport,
								FeatureReportResponse);
					}
				};

				return {
					onConnect : function() {
						var accessKey = SmellOpen.getEssentialConfig('accessKey');
						Logger.info("onConnect,clientId = " + accessKey);
						SmellOpen.subscribe("/" + accessKey);
						SmellOpen.setConnected();
						SmellOpen.evt.fire('onServerConnect',[SmellOpen]);
					},
					onConnectionLost : function(responseObject) {
						Logger.debug('onConnectionLost', responseObject);
						if (responseObject.errorCode !== 0) {
							Logger.debug("onConnectionLost:"
									+ responseObject.errorMessage);
						}
					},
					onMessageDelivered : function(message) {
						Logger
								.debug('onMessageDelivered.payloadBytes '
										+ message.destinationName,
										message.payloadBytes);
						// console.log('onMessageDelivered.payloadString',message.payloadString);
					},
					onMessageArrived : function(message) {
						// 读取内容，分析指令

						// Logger.debug("onMessageArrived message.qos "
						// + message.qos);
						// Logger.debug("onMessageArrived message.duplicate "
						// + message.duplicate);

						Logger.debug("onMessageArrived From Topic "
								+ message.destinationName);
						var headerInfo = SmellOpen
								.analyzeHeader(message.payloadBytes);
						if (false === headerInfo) {
							Logger.debug('Header Not Match');
							Logger
									.debug('payloadString',
											message.payloadString);
							Logger.debug('payloadBytes', message.payloadBytes);
						} else {
							Logger.debug('Header Found', headerInfo);
							Logger.debug('Dev EVENT');

							var handleFunctionName = map[headerInfo.COMMAND_ID];
							if (undefined != handleFunctionName
									&& undefined != MsgHandler[map[headerInfo.COMMAND_ID]]) {
								MsgHandler[map[headerInfo.COMMAND_ID]].apply(
										null,
										[ headerInfo, message, SmellOpen ]);
							}
						}
					}
				};
			}
			return handler;
		});
