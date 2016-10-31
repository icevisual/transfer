define([ 'protoDemo' ],
		function(simpleData) {
			var Api = function(app, deviceId) {
				var Utils = app.utils;
				var Logger = app.logger;
				var Simple = app.protoRoot;
				app.subscribe('/' + deviceId + '/resp');
				return {
					deviceId : deviceId,
					disconnect : function() {
						app.unsubscribe('/' + deviceId + '/resp');
					},
					defaultCallback : function(deviceId, reqCmd, respCmd, seq,
							callback) {
						// Send publish & waiting for subscribe msg of specific
						// seq
//						app.subscribe('/' + deviceId + '/resp');
						if (undefined === callback) {
							app.evt.addHandler(respCmd + '/' + seq, function(header,
									message, app) {
								var decode = null;
								if (header.BODY_LENGTH > 0) {
									decode = app.decodePayload(header,
											message.payloadBytes);
								}
								app.logger.info('reqCmd,header,message,decode',reqCmd, header,
										message, decode);
							});
						} else if (typeof callback == 'function') {
							app.evt.addHandler(respCmd, callback);
						}
						return true;
					},
					/**
					 * 设备休眠
					 * 
					 * @param callback
					 *            {function} nullable 成功回调
					 */
					sleep : function(callback) {
						var seq = app.sendCmd2Dev(deviceId,
								Simple.SrCmdId.SCI_req_sleep);
						this.defaultCallback(deviceId,
								Simple.SrCmdId.SCI_req_sleep,
								Simple.SrCmdId.SCI_resp_sleep, seq, callback);
						return true;
					},
					/**
					 * 唤醒设备
					 * 
					 * @param callback
					 *            {function} nullable 成功回调
					 */
					wakeup : function(callback) {
						app
								.sendCmd2Dev(deviceId,
										Simple.SrCmdId.SCI_req_wakeup);
					},
					/**
					 * 获取设备中各个瓶子的使用时间
					 * 
					 * @param callback
					 *            {function} nullable 成功回调
					 */
					usedSeconds : function(callback) {
						var seq = app.sendCmd2Dev(deviceId,
								Simple.SrCmdId.SCI_req_usedSeconds);
						this.defaultCallback(deviceId,
								Simple.SrCmdId.SCI_req_usedSeconds,
								Simple.SrCmdId.SCI_resp_usedSeconds, seq,
								callback);
						return true;
					},
					/**
					 * 设备播放气味
					 * 
					 * @param how
					 *            {Object} 如何播放气味 Simple.PlaySmell 对象
					 * @param callback
					 *            {function} nullable 成功回调
					 */
					playSmell : function(how, callback) {
						var seq = app.sendCmd2Dev(deviceId,
								Simple.SrCmdId.SCI_req_playSmell, how);
						this.defaultCallback(deviceId,
								Simple.SrCmdId.SCI_req_playSmell,
								Simple.SrCmdId.SCI_resp_playSmell, seq,
								callback);
						return true;
					},
					/**
					 * 获取设备属性
					 * 
					 * @param attrs
					 *            {Array|Object} nullable 要获取的属性内容，不填返回所有
					 * @param callback
					 *            {function} nullable 成功回调
					 */
					getDevAttr : function(attrs, callback) {
						var SrDevAttrType = Simple.SrDevAttrType;
						attrs = attrs
								|| [ SrDevAttrType.SDST_deviceID,
										SrDevAttrType.SDST_deviceName,
										SrDevAttrType.SDST_deviceType, ];
						for ( var i in attrs) {
							if (!/\d+/.test(attrs[i])) {
								attrs[i] = app.utils.EnumGetValue('SDST_',
										attrs[i]);
							}
						}
						var req = new Simple.GetDevAttrsRequest({
							'attrs' : attrs
						});
						var seq = app.sendCmd2Dev(deviceId,
								Simple.SrCmdId.SCI_req_getDevAttr, req);
						this.defaultCallback(deviceId,
								Simple.SrCmdId.SCI_req_getDevAttr,
								Simple.SrCmdId.SCI_resp_getDevAttr, seq,
								callback);
						return true;
					},
					/**
					 * 设置设备属性
					 * 
					 * @param attrSettings
					 *            {Array|Object} nullable
					 *            要设置的属性内容，为数组下标表示SrDevAttrType，为对象Key表示SrDevAttrType内部去prefix的字符串
					 * @param callback
					 *            {function} nullable 成功回调
					 */
					setDevAttr : function(attrSettings, callback) {
						var attrs = [];
						for ( var key in attrSettings) {
							if (/\d+/.test(key)) {
								attrs.push({
									'attr' : key,
									'value' : attrSettings[key]
								});
							} else {
								attrs.push({
									'attr' : app.utils.EnumGetValue('SDST_',
											key),
									'value' : attrSettings[key]
								});
							}
						}
						var req = new Simple.DevAttrs({
							'attrs' : attrs
						});
						var seq = app.sendCmd2Dev(deviceId,
								Simple.SrCmdId.SCI_req_setDevAttr, req);
						this.defaultCallback(deviceId,
								Simple.SrCmdId.SCI_req_setDevAttr,
								Simple.SrCmdId.SCI_resp_setDevAttr, seq,
								callback);
						return true;
					},
					/**
					 * 获取设备组件信息
					 * 
					 * @param callback
					 *            {function} nullable 成功回调
					 */
					featureReport : function(callback) {
						var seq = app.sendCmd2Dev(deviceId,
								Simple.SrCmdId.SCI_req_featureReport,
								simpleData);
						this.defaultCallback(deviceId,
								Simple.SrCmdId.SCI_req_featureReport,
								Simple.SrCmdId.SCI_resp_featureReport, seq,
								callback);
						return true;
					}
				};
			};
			return Api;
		});
