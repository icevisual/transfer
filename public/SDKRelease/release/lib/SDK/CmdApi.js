define(function() {
	var Api = function(app, deviceAccess, options) {
		var Utils = app.utils,
			Logger = app.logger,
			Simple = app.protoRoot,
			SrCmdId = Simple.SrCmdId,
			SrDevAttrType = Simple.SrDevAttrType;
		// Subscribe Response Topic
		var defaultOptions = {
			'timeout' : app.getEssentialConfig('apiTimeout',5),// Response Timeout Seconds
		};
		if (options) {
			for ( var i in options) {
				defaultOptions[i] = options[i];
			}
		}
		var deviceID = deviceAccess;
		var onSubscribedFailure = function(app){
		    app.logger.error('Failed to use device "' + deviceAccess + '"');
		},onSubscribedSuccess = function(app){
            app.logger.debug('Success to use device "' + deviceAccess + '"');
        };
		app.logger.debug('Attrmpt to Connect "' + deviceAccess + '"');
		if( defaultOptions.onFailure && app.utils.isFunction(defaultOptions.onFailure)){
		    onSubscribedFailure = defaultOptions.onFailure;
        }
		if( defaultOptions.onSuccess && app.utils.isFunction(defaultOptions.onSuccess)){
		    onSubscribedSuccess = defaultOptions.onSuccess;
        }
		defaultOptions.responseTopic = '/' + deviceID + '/resp';
		app.evt.addHandler('onSubscribedFailure-' + defaultOptions.responseTopic,function(app){
		    onSubscribedFailure(app);
		});
		app.evt.addHandler('onSubscribed-' + defaultOptions.responseTopic,function(app){
		    onSubscribedSuccess(app);
        });
		app.connectedThen(function(){
			app.subscribe(defaultOptions.responseTopic);
		});
		return {
			deviceID : deviceID,
			options : defaultOptions,
			_getOption : function(key) {
				return app.utils.getOptionOrDefault(this.options, key,
						undefined);
			},
			_setCallback : function(reqCmd, respCmd, seq, onSuccess,onError) {
				// Send publish & waiting for subscribe msg of specific seq
				// 以指令ID 加 包时序号（sequence）唯一确定请求，加入成功和失败事件的注册
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
						successCallback(header.SEQUENCE_NUMBER,decode,app);
					}
				}
				app.logger.debug('eventSeq',eventSeq);
				app.evt.addHandler(eventSeq,responseHandler);
				// Set time out handler
				var apiTimeout = this._getOption('timeout');
				var rid = setTimeout(function() {
					app.logger.error('req ' + reqCmd + ' seq ' + seq + ' timeout');
					error = {
						'msg' : 'Respones Time Out In ' + apiTimeout + ' seconds',
						'type' : 'ResponesTimeOut',
					};
					failureCallback(seq,error, app);
				}, 1000 * apiTimeout);
				// Clear Timeout handler
				app.evt.addHandler(eventSeq, function() {
					clearTimeout(rid);
				});
				return true;
			},
			// 触发API ，保证在订阅设备了 回复topic 后触发
			_invokeApi : function(opts){
				var resposeTopic = this._getOption('responseTopic');
				var _this = this;
				app.subscribedThen(resposeTopic,function(){
					reqData = opts.dataPacking();
					var respCmdMap = app.getEssentialConfig('protobuf.listenCmdMap');
					var seq = app.sendCmd2Dev(deviceID, opts.reqCmdID,reqData);
					_this._setCallback(opts.reqCmdID,respCmdMap[opts.reqCmdID], seq, opts.onSuccess,opts.onError);
				});
				return true;
			},
	        disconnect : function() {
                var topic = this._getOption('responseTopic');
                if(app.isSubscribed(topic)){
                    app.unsubscribe(topic);
                }
            },
			/**
			 * 设备休眠
			 * 
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			sleep : function(onSuccess,onError) {
				return this._invokeApi({
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
				return this._invokeApi({
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
				return this._invokeApi({
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
			 * {
    'cycleMode' : Simple.SrCycleMode.SCM_CYCLE_YES,// 循环模式
    'startAt' : [ { //播放开始时间，指明不同时间模式的值来确定一个播放时间
        'mode' : Simple.SrTimeMode.STM_WEEKDAY, // 时间模式
        'value' : 1, // 起始值
        'endValue' : 5 // 结束值
    }, {
        'mode' : Simple.SrTimeMode.STM_DAYTIME,
        'value' : 41400,
        'endValue' : 45000
    }, {
        'mode' : Simple.SrTimeMode.STM_DAYTIME,
        'value' : 63000,
        'endValue' : 66600
    } ],
    'cycleTime' : 0, // 循环次数
    'actions' : [ {// 动作列表 ，播放的原子工作
        'bottle' : '0000000001',//气味瓶子，为空字符串表示不播放气味
        'duration' : 2,//播放时间
        'power' : 5//播放功率
    }, {
        'bottle' : '0000000002',
        'duration' : 3,
        'power' : 7
    }, {
        'bottle' : '0000000003',
        'duration' : 2,
        'power' : 5
    }, {
        'bottle' : '0000000004',
        'duration' : 2,
        'power' : 0
    } ],
    'trace' : [ {//播放轨迹
        'actionId' : [ 0, 3, 1, 4, 2 ], // 播放动作顺序，数字为工作列表中动作的下标
        'beforeStart' : 0,// 开始前的等待时间（秒）
        'cycleMode' : Simple.SrCycleMode.SCM_CYCLE_YES,//循环模式
        'interval' : 0,// 循环间隔
        'cycleTime' : 278//循环次数
    } ],
}
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			playSmell : function(how, onSuccess,onError) {
				return this._invokeApi({
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
			 * [ SrDevAttrType.SDST_DEVICEID,
                  SrDevAttrType.SDST_DEVICENAME,
                  SrDevAttrType.SDST_DEVICETYPE, 
                 ]
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			getDevAttr : function(attrs, onSuccess,onError) {
				return this._invokeApi({
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
			 * {
                    'devicename' : 'test-device-name', // 属性名 和 属性值
                    'wifissid' : 'renren123'
                }
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			setDevAttr : function(attrSettings, onSuccess,onError) {
				return this._invokeApi({
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
						var req = new Simple.SetDevAttrsRequest({
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
			deviceModelReport : function(onSuccess,onError) {
				return this._invokeApi({
					'reqCmdID' : SrCmdId.SCI_REQ_DEVICE_MODEL, 
					'dataPacking' : function(){
						return [];
					},
					'onSuccess' : onSuccess,
					'onError' : onError
				});
			},
			/**
			 * 停止播放气味
			 * 
			 * @param bottles {array} notnull 要停止的瓶子
			 * [
                   '00000b1',// 瓶子的ID
                   '00000b2',
                   '00000b3',
               ]
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			stopPlay : function(bottles,onSuccess,onError){
				// 设备停止播放动作
				return this._invokeApi({
					'reqCmdID' : SrCmdId.SCI_REQ_STOP_PLAY, 
					'dataPacking' : function(){
						var req = {
							'bottles' : bottles
						};
						return new Simple.StopPlayRequest(req);
					},
					'onSuccess' : onSuccess,
					'onError' : onError
				});
			},
			/**
			 * 设备取消任务
			 * 
			 * @param stop_tasks {array} notnull 要停止的任务
			 * [{
                    'stop_seq' : 140155251,
                    'mode' : Simple.SrRemoveTaskMode.SRT_STOP_NOTCANCEL,
                }]
			 * @param onSuccess {function} nullable 成功回调
			 * @param onError {function} nullable 错误回调
			 */
			removeTask : function(stop_tasks,onSuccess,onError){
				// 设备取消任务
//				message RemoveTaskRequest {
//				    BaseRequest request = 1;
//				    message RemoveTaskOption {
//				        int32 stop_seq = 1; // 要停止的 PlayRequest 请求 nSeq 序列号
//				        SrRemoveTaskMode mode = 2; // 模式
//				    }
//				    repeated RemoveTaskOption stop_tasks = 2; 
//				}
				// 设备取消任务
				return this._invokeApi({
					'reqCmdID' : SrCmdId.SCI_REQ_REMOVE_TASK, 
					'dataPacking' : function(){
						var req = {
							'stop_tasks' : stop_tasks
						};
						return new Simple.RemoveTaskRequest(req);
					},
					'onSuccess' : onSuccess,
					'onError' : onError
				});
			},
			/**
             * 设置控件属性请求，将控件的值描述为控件的一个可修改属性，
             * 通过控制控件属性来达到控制控件的效果，也可以用于设置控件的名字
             * 
             * @param settings {array} notnull 要设置的属性
             * [{
                    'identity' : 12, // 控件 ID
                    'attr' : 'value', // 控件属性名称
                    'value' : '12' // 设置的值
                }]
             * @param onSuccess {function} nullable 成功回调
             * @param onError {function} nullable 错误回调
             */
			setControlAttr : function(settings,onSuccess,onError){
			 // 设置控件属性请求，将控件的值描述为控件的一个可修改属性，
			 // 通过控制控件属性来达到控制控件的效果，也可以用于设置控件的名字
//			    message SetControlAttrRequest {
//			        // 设置控件属性信息
//			        message SetControlAttr {
//			            int32 identity = 1; // 控件标识符，指明设置的控件
//			            string attr = 2;  // 控件属性名，指明要设置的属性
//			            string value = 3; // 属性值
//			        }
//			        BaseRequest request = 1;
//			        repeated SetControlAttr settings = 2; // 控制控件内容
//			    }
			    return this._invokeApi({
                    'reqCmdID' : SrCmdId.SCI_REQ_SET_CONTROL_ATTR, 
                    'dataPacking' : function(){
                        var req = {
                            'settings' : settings
                        };
                        return new Simple.SetControlAttrRequest(req);
                    },
                    'onSuccess' : onSuccess,
                    'onError' : onError
                });
			},
			/**
             * 获取设备的其为列表
             * 
             * @param onSuccess {function} nullable 成功回调
             * @param onError {function} nullable 错误回调
             */
            smellList : function(onSuccess,onError) {
                return this._invokeApi({
                    'reqCmdID' : SrCmdId.SCI_REQ_GET_SMELL_LIST, 
                    'dataPacking' : function(){
                        return [];
                    },
                    'onSuccess' : onSuccess,
                    'onError' : onError
                });
            },
            /**
             * 停止设备所有气味播放
             * 
             * @param onSuccess {function} nullable 成功回调
             * @param onError {function} nullable 错误回调
             */
            stopAll : function(onSuccess,onError) {
                return this._invokeApi({
                    'reqCmdID' : SrCmdId.SCI_REQ_STOP_ALL, 
                    'dataPacking' : function(){
                        return [];
                    },
                    'onSuccess' : onSuccess,
                    'onError' : onError
                });
            },
            /**
             * 获取设备当前播放信息
             * 
             * @param onSuccess {function} nullable 成功回调
             * @param onError {function} nullable 错误回调
             */
            currentPlay : function(onSuccess,onError) {
                return this._invokeApi({
                    'reqCmdID' : SrCmdId.SCI_REQ_CURRENT_PLAY, 
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
