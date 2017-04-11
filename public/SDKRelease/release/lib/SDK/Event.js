define(['Utils'],function(Utils) {
	var MsgEvent = function() {
		this.handlers = {};
		this.evtRet = {};
	}
	MsgEvent.prototype = {
		addHandler : function(type, handler,options) {
			if (typeof this.handlers[type] == "undefined") {
				this.handlers[type] = [];
			}
			this.handlers[type].push({
				handler : handler,
				options : options,
			});
		},
		fire : function(type, params) {
			if (this.handlers[type] instanceof Array) {
				var handlers = this.handlers[type], ret = null;
				var handlersObj ;
				handlers.reverse();
				while(handlersObj = handlers.pop()){
					ret = handlersObj['handler'].apply(null, params);
					if (false === ret) {
						break;
					}
				}
				this.evtRet[type] = ret;
			}
		},
		loopFire : function(type, params) {
            if (this.handlers[type] instanceof Array) {
                var handlers = this.handlers[type], ret = null;
                var handlersObj ;
                var newHandlerArray = [];
                handlers.reverse();
                while(handlersObj = handlers.pop()){
                    ret = handlersObj['handler'].apply(null, params);
                    newHandlerArray.push(handlersObj);
                    if (false === ret) {
                        break;
                    }
                }
                this.evtRet[type] = ret;
                this.handlers[type] = newHandlerArray;
            }
        },
		clearHandler : function(type) {
			if (this.handlers[type] instanceof Array) {
				this.handlers[type] = [];
			}
		},
		removeHandler : function(type, handler) {
			if (this.handlers[type] instanceof Array) {
				var handlers = this.handlers[type];
				for (var i = 0, len = handlers.length; i < len; i++) {
					if (handlers[i] === handler) {
						break;
					}
				}
				handlers.splice(i, 1);
			}
		}
	};
	var evt = new MsgEvent();
	return evt;
});
