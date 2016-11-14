define(function() {
	var Api = function(app, deviceId, options) {
		var Utils = app.utils,
			Logger = app.logger,
			Simple = app.protoRoot,
			SrCmdId = Simple.SrCmdId,
			SrDevAttrType = Simple.SrDevAttrType;
		// Subscribe Response Topic
		var defaultOptions = {
			'timeout' : 4,// Response Timeout Seconds
		};
		if (options) {
			for ( var i in options) {
				defaultOptions[i] = options[i];
			}
		}
		defaultOptions.responseTopic = '/' + deviceId + '/resp';
		app.connectedThen(function(){
			app.subscribe(defaultOptions.responseTopic);
		});
		return {
			deviceId : deviceId,
			options : defaultOptions,
			getOption : function(key) {
				return app.utils.getOptionOrDefault(this.options, key,
						undefined);
			},
			disconnect : function() {
				var topic = this.getOption('responseTopic');
				if(app.isSubscribeds(topic)){
					app.unsubscribe(topic);
				}
			},
			setCallback : function(reqCmd, respCmd, seq, onSuccess,onError) {
				// Send publish & waiting for subscribe msg of specific seq
				var eventSeq = respCmd + '/' + seq,emptyFunction =  function(){},
				  	successCallback = app.utils.validateCallback(onSuccess) ? onSuccess : emptyFunction,
				  	failureCallback = app.utils.validateCallback(onError)? onError : emptyFunction;
				// Decode Response Data And Fire SuccessCallback
				var error = {
					'msg' : 'Response Decode Error',
					'type' : 'ResponseDecodeError',
				};
				var responseHandler = function(header, message, app) {
					var decode = null;
					if (header.BODY_LENGTH > 0) {
						decode = app.decodePayload(header,message.payloadBytes);
					}
					if (false === decode) {
						app.logger.error('Response Data Decode Error');
						failureCallback(error,app);
						return;
					}
					app.logger.info('reqCmd,header,message,decode',reqCmd, header,message, decode);
					if(successCallback){
						successCallback(decode,app);
					}
				}
				app.logger.debug('eventSeq',eventSeq);
				app.evt.addHandler(eventSeq,responseHandler);
				// Set time out handler
				var rid = setTimeout(function() {
					app.logger.error('req ' + reqCmd + ' seq ' + seq + ' timeout');
					error = {
						'msg' : 'Respones Time Out',
						'type' : 'ResponesTimeOut',
					};
					failureCallback(error, app);
				}, 1000 * this.getOption('timeout'));
				// Clear Timeout handler
				app.evt.addHandler(eventSeq, function() {
					clearTimeout(rid);
				});
				return true;
			},
			invokeApi : function(opts){
				var resposeTopic = this.getOption('responseTopic');
				var _this = this;
				app.subscribedThen(resposeTopic,function(){
					reqData = opts.dataPacking();
					var respCmdMap = app.getEssentialConfig('protobuf.listenCmdMap');
					var seq = app.sendCmd2Dev(deviceId, opts.reqCmdID,reqData);
					_this.setCallback(opts.reqCmdID,respCmdMap[opts.reqCmdID], seq, opts.onSuccess,opts.onError);
				});
				return true;
			},
			/**
			 * 设备休眠
			 * 
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			sleep : function(onSuccess,onError) {
				return this.invokeApi({
					'reqCmdID' : SrCmdId.SCI_REQ_SLEEP, 
					'dataPacking' : function(){
						return [];
					},
					'onSuccess' : onSuccess,
					'onError' : onError
				});
			},
			/**
			 * 唤醒设备
			 * 
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			wakeup : function(onSuccess,onError) {
				return this.invokeApi({
					'reqCmdID' : SrCmdId.SCI_REQ_WAKEUP, 
					'dataPacking' : function(){
						return [];
					},
					'onSuccess' : onSuccess,
					'onError' : onError
				});
			},
			/**
			 * 获取设备中各个瓶子的使用时间
			 * 
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			usedSeconds : function(onSuccess,onError) {
				return this.invokeApi({
					'reqCmdID' : SrCmdId.SCI_REQ_USEDSECONDS, 
					'dataPacking' : function(){
						return [];
					},
					'onSuccess' : onSuccess,
					'onError' : onError
				});
			},
			/**
			 * 设备播放气味
			 * 
			 * @param how {Object} 如何播放气味 Simple.PlaySmell 对象
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			playSmell : function(how, onSuccess,onError) {
				return this.invokeApi({
					'reqCmdID' : SrCmdId.SCI_REQ_PLAYSMELL, 
					'dataPacking' : function(){
						if( (how instanceof Simple.PlayRequest) === false && typeof how == 'object'){
							how = new Simple.PlayRequest(how);
						}
						return how;
					},
					'onSuccess' : onSuccess,
					'onError' : onError
				});
			},
			/**
			 * 获取设备属性
			 * 
			 * @param attrs {Array|Object} nullable 要获取的属性内容，不填返回所有
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			getDevAttr : function(attrs, onSuccess,onError) {
				return this.invokeApi({
					'reqCmdID' : SrCmdId.SCI_REQ_GETDEVATTR, 
					'dataPacking' : function(){
						attrs = attrs
						|| [ SrDevAttrType.SDST_DEVICEID,
								SrDevAttrType.SDST_DEVICENAME,
								SrDevAttrType.SDST_DEVICETYPE, ];
						for ( var i in attrs) {
							if (!/\d+/.test(attrs[i])) {
								attrs[i] = app.utils.EnumGetValue('SDST_', attrs[i]);
							}
						}
						var req = new Simple.GetDevAttrsRequest({
							'attrs' : attrs
						});
						return req;
					},
					'onSuccess' : onSuccess,
					'onError' : onError
				});
			},
			/**
			 * 设置设备属性
			 * 
			 * @param attrSettings {Array|Object} nullable 要设置的属性内容，为数组下标表示SrDevAttrType，为对象Key表示SrDevAttrType内部去prefix的字符串
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			setDevAttr : function(attrSettings, onSuccess,onError) {
				return this.invokeApi({
					'reqCmdID' : SrCmdId.SCI_REQ_SETDEVATTR, 
					'dataPacking' : function(){
						var attrs = [];
						for ( var key in attrSettings) {
							if (/\d+/.test(key)) {
								attrs.push({
									'attr' : key,
									'value' : attrSettings[key]
								});
							} else {
								attrs.push({
									'attr' : app.utils.EnumGetValue('SDST_', key),
									'value' : attrSettings[key]
								});
							}
						}
						var req = new Simple.DevAttrs({
							'attrs' : attrs
						});
						return req;
					},
					'onSuccess' : onSuccess,
					'onError' : onError
				});
			},
			/**
			 * 获取设备组件信息
			 * 
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			featureReport : function(onSuccess,onError) {
				return this.invokeApi({
					'reqCmdID' : SrCmdId.SCI_REQ_FEATUREREPORT, 
					'dataPacking' : function(){
						return [];
					},
					'onSuccess' : onSuccess,
					'onError' : onError
				});
			}
		};
	};
	return Api;
});
