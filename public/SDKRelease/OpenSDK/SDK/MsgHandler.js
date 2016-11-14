define(function() {
	var handlers = function(app) {
		var Utils = app.utils, Logger = app.logger, Simple = app.protoRoot;
		return {
			onConnect : function() {
				var accessKey = app.getEssentialConfig('accessKey');
				Logger.info("onConnect,clientId = " + accessKey);
				
				var selfTopic = "/" + accessKey;
				app.subscribe(selfTopic);
				app.subscribedThen(selfTopic,function(){
					// check self subscribe status
					// set Connected
					// fire events
				});
				app.setConnected();
				app.evt.fire('onServerConnect',[app]);
			},
			onConnectionLost : function(responseObject) {
				Logger.info('onConnectionLost', responseObject);
				if (responseObject.errorCode !== 0) {
					Logger.info("onConnectionLost:"
							+ responseObject.errorMessage);
				}
			},
			onMessageDelivered : function(message) {
				Logger.info('onMessageDelivered.payloadBytes '
						+ message.destinationName, message.payloadBytes);
				// console.log('onMessageDelivered.payloadString',message.payloadString);
			},
			onMessageArrived : function(message) {
				// 读取内容，分析指令
				// Logger.debug("onMessageArrived message.qos " +
				// message.qos);
				// Logger.debug("onMessageArrived message.duplicate "
				// + message.duplicate);

				Logger.info("onMessageArrived From Topic "
						+ message.destinationName);
				var headerInfo = app.analyzeHeader(message.payloadBytes);
				if (false === headerInfo) {
					Logger.info('Header Not Match');
					Logger.info('PayloadString', message.payloadString);
					Logger.info('PayloadBytes', message.payloadBytes);
				} else {
					Logger.info('Header Found', headerInfo);
					var SrCmdId = Utils.EnumGetKey('SCI_',headerInfo.COMMAND_ID);
					if (SrCmdId !== undefined) {
						Logger.info('Got SrCmdId ', SrCmdId);
						Logger.info('Message payloadBytes', message.payloadBytes);
						app.evt.fire(headerInfo.COMMAND_ID + '/' + headerInfo.SEQUENCE_NUMBER, [ headerInfo,
								message, app ]);
					} else {
						Logger.warning('SrCmdId Not Match', SrCmdId);
					}
				}
			}
		};
	}
	return handlers;
});
