define('device',[ 'protocolStruct' ], function(Simple) {
	var obj = {
		attrs : {
			'DEVICEID' : 'TCEOP0GZZRWHAMOOA3MM',// 设备唯一标识
			'DEVICENAME' : '测试设备0001',// 设备名字
			'DEVICETYPE' : 'T1000',// 设备类别
			'DEVICESTATUS' : 'AWAKE',// 设备状态(已唤醒|休眠中) AWAKE SLEEP
			
			'MAC' : 'F8-A9-63-57-0F-CE',// MAC
			'WIFISSID' : 'RENRENWIFI',// WIFISSID
			'WIFIPWD' : 'RENREN2016',// WIFI密码
			'NETCONNECTSTATE' : 'OFF',// 网络连接状态
			'BLECONNECTSTATE' : 'OFF',// 蓝牙连接状态
			'LOGSTATE' : 'OFF',// 日志开启状态
			'DATETIME' : '2016-10-25 12:12:12',// 时间
			'UPTIME' : '2016-10-25 12:12:12',// 设备上次开机时间
			'DOWNTIME' : '2016-10-25 12:12:12',// 设备上次关机时间
		},
		attrsName : {
		    'DEVICEID' : '设备唯一标识',// 设备唯一标识
            'DEVICENAME' : '设备名字',// 设备名字
            'DEVICETYPE' : '设备类别',// 设备类别
            'DEVICESTATUS' : '设备状态',// 设备状态(已唤醒|休眠中) AWAKE SLEEP
            'MAC' : 'MAC 地址',// MAC
            'WIFISSID' : 'WIFISSID',// WIFISSID
            'WIFIPWD' : 'WIFI密码',// WIFI密码
            'NETCONNECTSTATE' : '网络连接状态',// 网络连接状态
            'BLECONNECTSTATE' : '蓝牙连接状态',// 蓝牙连接状态
            'LOGSTATE' : '日志开启状态',// 日志开启状态
            'DATETIME' : '时间',// 时间
            'UPTIME' : '设备上次开机时间',// 设备上次开机时间
            'DOWNTIME' : '设备上次关机时间',// 设备上次关机时间
		},
		bottles : [{
            'bottle': '000000001',
            'time': 1
        },
        {
            'bottle': '000000002',
            'time': 2
        },
        {
            'bottle': '000000003',
            'time': 3
        },
        {
            'bottle': '000000004',
            'time': 3
        },
        {
            'bottle': '000000005',
            'time': 3
        },
        {
            'bottle': '000000006',
            'time': 3
        }],
		model : {
			'support_cmds' : [
				Simple.SrCmdId.SCI_REQ_SLEEP,
				Simple.SrCmdId.SCI_REQ_WAKEUP,
				Simple.SrCmdId.SCI_REQ_USEDSECONDS,
				Simple.SrCmdId.SCI_REQ_PLAYSMELL,
				Simple.SrCmdId.SCI_REQ_GETDEVATTR,
				Simple.SrCmdId.SCI_REQ_SETDEVATTR,
				Simple.SrCmdId.SCI_REQ_DEVICE_MODEL,
				Simple.SrCmdId.SCI_REQ_STOP_PLAY,
				Simple.SrCmdId.SCI_REQ_REMOVE_TASK,
				Simple.SrCmdId.SCI_REQ_SET_CONTROL_ATTR,
			],
			'controls': [
	             {
	                 'identity': 1,
	                 'name': 'AppControlSwitch',
	                 'switch': {
	                     'status': Simple.SrSwitchStatus.SSS_OFF
	                 }
	             },
	             {
	                 'identity': 2,
	                 'name': 'AppControlSlider',
	                 'slider': {
	                     '_min': 1,
	                     '_max': 10,
	                     '_step': 2,
	                     'value': 2,
	                 }
	             },
	             {
	                 'identity': 3,
	                 'name': 'AppControlSpin',
	                 'spin': {
	                     'angle': 22,
	                     '_step' : 1,
	                     'angular_speed' : 1
	                 }
	             },
	             {
	                 'identity': 4,
	                 'name': 'AppControlSwitchSlider',
	                 'switch_slider': {
	                     'status': Simple.SrSwitchStatus.SSS_ON,
	                     '_min': 1,
	                     '_max': 10,
	                     '_step': 2,
	                     'value': 2,
	                 }
	             },
	             {
                     'identity': 5,
                     'name': 'AppControlSwitch',
                     'switch': {
                         'status': Simple.SrSwitchStatus.SSS_OFF
                     }
                 },
                 {
                     'identity': 6,
                     'name': 'AppControlSlider',
                     'slider': {
                         '_min': 1,
                         '_max': 10,
                         '_step': 2,
                         'value': 2,
                     }
                 },
                 {
                     'identity': 7,
                     'name': 'AppControlSpin',
                     'spin': {
                         'angle': 22,
                         '_step' : 1,
                         'angular_speed' : 1
                     }
                 },
                 {
                     'identity': 8,
                     'name': 'AppControlSwitchSlider',
                     'switch_slider': {
                         'status': Simple.SrSwitchStatus.SSS_ON,
                         '_min': 1,
                         '_max': 10,
                         '_step': 2,
                         'value': 2,
                     }
                 },
                 {
                     'identity': 9,
                     'name': 'AppControlSwitch',
                     'switch': {
                         'status': Simple.SrSwitchStatus.SSS_OFF
                     }
                 },
                 {
                     'identity': 10,
                     'name': 'AppControlSlider',
                     'slider': {
                         '_min': 1,
                         '_max': 10,
                         '_step': 2,
                         'value': 2,
                     }
                 },
                 {
                     'identity': 11,
                     'name': 'AppControlSpin',
                     'spin': {
                         'angle': 22,
                         '_step' : 1,
                         'angular_speed' : 1
                     }
                 },
                 {
                     'identity': 12,
                     'name': 'AppControlSwitchSlider',
                     'switch_slider': {
                         'status': Simple.SrSwitchStatus.SSS_ON,
                         '_min': 1,
                         '_max': 10,
                         '_step': 2,
                         'value': 2,
                     }
                 },
                 {
                     'identity': 13,
                     'name': 'AppControlSwitch',
                     'switch': {
                         'status': Simple.SrSwitchStatus.SSS_OFF
                     }
                 },
                 {
                     'identity': 14,
                     'name': 'AppControlSlider',
                     'slider': {
                         '_min': 1,
                         '_max': 10,
                         '_step': 2,
                         'value': 2,
                     }
                 },
                 {
                     'identity': 15,
                     'name': 'AppControlSpin',
                     'spin': {
                         'angle': 22,
                         '_step' : 1,
                         'angular_speed' : 1
                     }
                 },
                 {
                     'identity': 16,
                     'name': 'AppControlSwitchSlider',
                     'switch_slider': {
                         'status': Simple.SrSwitchStatus.SSS_ON,
                         '_min': 1,
                         '_max': 10,
                         '_step': 2,
                         'value': 2,
                     }
                 }
	         ],
		},
		getUsedTime : function(){
		    return this.bottles;
		},
		getModel : function(){
			return this.model;
		},
		getAttr : function(key) {
			return this.attrs[key] ? this.attrs[key] : false;
		},
		setAttr : function(key, value) {
			if (this.attrs[key] != undefined) {
				this.attrs[key] = value;
				return true;
			}
			return false;
		},
		getDeviceSmellList : function(){
		    var ret = [];
		    for(var i in this.bottles){
		        ret.push(this.bottles[i]['bottle']);
		    }
		    return ret;
		},
		actions : {
			netConnect : function() {
				console.log('--netConnect--');
			},
			playSmell : function(decode) {
				console.log('--playSmell--');
			},
			sleep : function() {
			    obj.attrs.DEVICESTATUS = 'SLEEP';
				console.log('--sleep--');
			},
			wakeup : function() {
			    obj.attrs.DEVICESTATUS = 'AWAKE';
				console.log('--wakeup--');
			},
			setControlAttr : function(decode){
			    var map = {};
			    var settingCount = 0;
			    for(var i in decode.settings){
			        if(! map['id-' + decode.settings[i]['identity']] ){
			            map['id-' + decode.settings[i]['identity']] = {};
			        }
                    map['id-' + decode.settings[i]['identity']] [ decode.settings[i]['attr'] ] = decode.settings[i]['value'];
                    settingCount ++;
			    }
			    for(var i in obj.model.controls){
			        var identity = obj.model.controls[i]['identity'];
			        if(undefined !== map['id-' + identity ] ){
			            var cnKey = 'slider';
			            for(var ckey in obj.model.controls[i] ){
			                if( ! ('identity' === ckey || 'name' === ckey) ){
			                    cnKey = ckey;
			                    break ;
			                }
			            }
			            for(var attr in map['id-' + identity ]){
			                // name 属性作为公共属性，放在外面
			                if('name' === attr){
			                    obj.model.controls[i] [attr] = map['id-' + identity ][ attr ];
			                    continue;
			                }
	                        var originValue = obj.model.controls[i] [cnKey] [ attr ] ;
	                        // 未找到属性 
	                        if(undefined === originValue){
	                            console.log('Attr Not Found  ' + attr);
	                            continue;
	                        }
	                        // 设值的类型转换
	                        var targetValue = map['id-' + identity ][ attr ];
	                        if(typeof originValue == 'number'){
	                            targetValue = parseInt(targetValue);
	                        }else if(typeof originValue == 'string'){
	                            targetValue = targetValue + '';
	                        }else if(typeof originValue == 'boolean'){
	                            if('true' === targetValue){
	                                targetValue = true;
	                            }else if('false' === targetValue){
	                                targetValue = false;
	                            }else if(parseInt(targetValue) > 0){
	                                targetValue = true;
	                            }else {
	                                targetValue = false;
	                            }
	                        }
	                        // 
	                        obj.model.controls[i] [cnKey] [ attr ] = targetValue;
	                        console.log('identity = ' + identity + ' attr = ' + attr + ' [' + originValue + ' => ' + targetValue + ']');
	                        settingCount --;
			            }
			            delete map['id-' + identity ];
			        }
                }
			    console.log(map);
			    console.log(decode);
			    console.log('--setControlAttr--');
			    
			    // 指令内容无法 全部执行
			    if(settingCount > 0){
			        console.log('--Identity Can Not Found List--');
			        for(var i in map){
			            console.log(i,map[i]);
			        }
			        console.log('--END List--');
			    }
			    
			    return settingCount ;
			}
		}
	};
	return obj;
});
