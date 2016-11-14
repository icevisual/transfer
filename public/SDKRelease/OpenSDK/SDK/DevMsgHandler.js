define(
		[ 'Utils', 'logger', 'protocolStruct', 'device' ],
		function(Utils, Logger, Simple, Dev) {
			var handler = function(SmellOpen) {

				var map = Utils.array_reverse(Simple.SrCmdId);

				var MsgHandler = {
					SCI_REQ_SLEEP : function(header, message) {
						Logger.debug('Got SCI_req_sleep');
						var resp = new Simple.BaseResponse(
								Simple.SrErrorCode.SEC_SUCCESS,
								[ 'SCI_req_sleep' ]);
						SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
								Simple.SrCmdId.SCI_RESP_SLEEP, resp
//								,{'retained' : true}
						);
					},
					SCI_REQ_WAKEUP : function(header, message) {
						Logger.debug('Got SCI_req_wakeup');
						var resp = new Simple.BaseResponse(
								Simple.SrErrorCode.SEC_SUCCESS,
								[ 'SCI_req_wakeup' ]);
						SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
								Simple.SrCmdId.SCI_RESP_WAKEUP, resp);
					},
					SCI_REQ_USEDSECONDS : function(header, message) {
						var resp = new Simple.UsedTimeResponse({
							'response' : {
								'code' : Simple.SrErrorCode.SEC_SUCCESS,
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
								Simple.SrCmdId.SCI_RESP_USEDSECONDS, resp);
					},
					SCI_REQ_PLAYSMELL : function(header, message, app) {
						var decode = app.decodePayload(header,
								message.payloadBytes);
						app.logger.info('SCI_req_playSmell', decode);

						var resp = new Simple.BaseResponse({
							'code' : Simple.SrErrorCode.SEC_ACCEPT,
							'data' : [ 'SCI_req_playSmell' ]
						});
						SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
								Simple.SrCmdId.SCI_RESP_PLAYSMELL, resp);
					},
					SCI_REQ_GETDEVATTR : function(header, message, app) {
						// // 设备属性类型
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
								Simple.SrCmdId.SCI_RESP_GETDEVATTR, resp);
					},
					SCI_REQ_SETDEVATTR : function(header, message, app) {
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
							'code' : Simple.SrErrorCode.SEC_SUCCESS,
							'data' : [ 'SCI_req_setDevAttr' ]
						});
						SmellOpen.sendCmdResp(header.SEQUENCE_NUMBER,
								Simple.SrCmdId.SCI_RESP_SETDEVATTR, resp);
					},
					SCI_REQ_FEATUREREPORT : function(header, message, app) {
						var SrFeatureType = Simple.SrFeatureType;
						var SrFeatureAttrType = Simple.SrFeatureAttrType;
						var FeatureReportResponse = new Simple.FeatureReportResponse(
								{
									'feature' : [ {
										'type' : SrFeatureType.SFT_SWITCH,
										'attrs' : [
												{
													'attr' : SrFeatureAttrType.SFAT_NAME,
													'value' : '测试开关0001'
												},
												{
													'attr' : SrFeatureAttrType.SFAT_NAME,
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
								Simple.SrCmdId.SCI_RESP_FEATUREREPORT,
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
							Logger.debug('handleFunctionName',handleFunctionName);
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
