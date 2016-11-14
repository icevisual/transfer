define([ 'protocolStruct' ], function(Simple) {
	var obj = {
		attrs : {
			'DEVICEID' : 'TCEOP0GZZRWHAMOOA3MM',// 设备唯一标识
			'DEVICENAME' : '测试设备0001',// 设备名字
			'DEVICETYPE' : 'T1000',// 设备类别
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

		// // 设备组件类别
		// enum SrFeatureType
		// {
		// SFT_switch = 1;//开关（数量、分别叫什么、开还是关）
		// SFT_scrollBar = 2;// 滚动条（数量、分别叫什么、最大值、步幅）
		// SFT_textbox = 3;//文本框（数量、分别叫什么、默认文本信息）
		// SFT_rotation = 4;//旋转（数量、分别叫什么）
		// }
		// //设备组件属性类别
		// enum SrFeatureAttrType
		// {
		// SFAT_name = 1;// 名字
		// SFAT_num = 2;// 数量
		// SFAT_state = 3;// 开关状态
		// SFAT_max = 4;// 最大值
		// SFAT_min = 5;// 最小值
		// SFAT_step = 6;// 步幅
		// SFAT_default = 7;// 默认值
		// SFAT_angle = 8; // 旋转角度
		// SFAT_angular_speed = 9;// 旋转角速度
		// }
		//		
		features : [ {
			'type' : 'switch',
			'attrs' : {
				'name' : '一号开关',
				'state' : 'off',
			}
		}, {
			'type' : 'scrollBar',
			'attrs' : {
				'name' : '一号开关',
				'max' : '20',
				'min' : '1',
				'step' : '2',
			}
		}, ],
		getFeature : function(){
			return this.features;
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
		actions : {
			netConnect : function() {
				console.log('--netConnect--');
			},
			playSmell : function() {
				console.log('--playSmell--');
			},
			sleep : function() {
				console.log('--sleep--');
			},
			wakeup : function() {
				console.log('--wakeup--');
			}
		}
	};
	return obj;
});
