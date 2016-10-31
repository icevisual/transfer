define(function() {
	var handlers = function(SmellOpen) {
		var Utils = SmellOpen.utils, Logger = SmellOpen.logger, Simple = SmellOpen.protoRoot;
		return {
			onConnect : function() {
				var accessKey = SmellOpen.getEssentialConfig('accessKey');
				Logger.info("onConnect,clientId = " + accessKey);
				SmellOpen.subscribe("/" + accessKey);
				
				SmellOpen.setConnected();
				
				SmellOpen.evt.fire('onServerConnect',[SmellOpen]);
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
				var headerInfo = SmellOpen.analyzeHeader(message.payloadBytes);
				if (false === headerInfo) {
					Logger.info('Header Not Match');
					Logger.info('payloadString', message.payloadString);
					Logger.info('payloadBytes', message.payloadBytes);
				} else {
					Logger.info('Header Found', headerInfo);
					var SrCmdId = Utils.EnumGetKey('SCI_',
							headerInfo.COMMAND_ID);
					if (SrCmdId !== undefined) {
						Logger.info('Got SrCmdId ', SrCmdId);
						SmellOpen.evt.fire(headerInfo.COMMAND_ID + '/' + headerInfo.SEQUENCE_NUMBER, [ headerInfo,
								message, SmellOpen ]);
					} else {
						Logger.warning('SrCmdId Not Match', SrCmdId);
					}
				}
			}
		};
	}
	return handlers;
});
