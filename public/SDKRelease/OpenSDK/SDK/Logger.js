define(['Utils'],function(Utils) {
	//---------------------------------------------------  
	// 日期格式化  
	// 格式 YYYY/yyyy/YY/yy 表示年份  
	// MM/M 月份  
	// W/w 星期  
	// dd/DD/d/D 日期  
	// hh/HH/h/H 时间  
	// mm/m 分钟  
	// ss/SS/s/S 秒  
	//---------------------------------------------------  
	Date.prototype.Format = function(formatStr)   
	{   
	    var str = formatStr;   
	    var Week = ['日','一','二','三','四','五','六'];  
	  
	    str=str.replace(/yyyy|YYYY/,this.getFullYear());   
	    str=str.replace(/yy|YY/,(this.getYear() % 100)>9?(this.getYear() % 100).toString():'0' + (this.getYear() % 100));   
	  
	    str=str.replace(/MM/,this.getMonth()>9?this.getMonth().toString():'0' + this.getMonth());   
	    str=str.replace(/M/g,this.getMonth());   
	  
	    str=str.replace(/w|W/g,Week[this.getDay()]);   
	  
	    str=str.replace(/dd|DD/,this.getDate()>9?this.getDate().toString():'0' + this.getDate());   
	    str=str.replace(/d|D/g,this.getDate());   
	  
	    str=str.replace(/hh|HH/,this.getHours()>9?this.getHours().toString():'0' + this.getHours());   
	    str=str.replace(/h|H/g,this.getHours());   
	    str=str.replace(/mm/,this.getMinutes()>9?this.getMinutes().toString():'0' + this.getMinutes());   
	    str=str.replace(/m/g,this.getMinutes());   
	  
	    str=str.replace(/ss|SS/,this.getSeconds()>9?this.getSeconds().toString():'0' + this.getSeconds());   
	    str=str.replace(/s|S/g,this.getSeconds());   
	  
	    return str;   
	}   

	function now(){
	    return (new Date()).Format('yyyy-MM-dd HH:mm:ss');
	}
	
	return {
		logLevel : 'debug',
		setLevel : function(level){
			this.logLevel = level;
		},
		debug : function() {
			this.log('debug', arguments);
		},
		info : function() {
			this.log('info', arguments);
		},
		notice : function() {
			this.log('notice', arguments);
		},
		warning : function() {
			this.log('warning', arguments);
		},
		error : function() {
			this.log('error', arguments);
		},
		levelCompare : function(maxLevel, nowLevel) {
			var level = {
				'debug' : 1,
				'info' : 2,
				'notice' : 3,
				'warning' : 4,
				'error' : 5,
			};
			if (!level[nowLevel] || !level[maxLevel]) {
				return false;
			}
			return level[nowLevel] >= level[maxLevel];
		},
		log : function(level, data) {
			if (data.length > 0
					& this.levelCompare(this.logLevel, level)) {
				var dateStr = "[" + Utils.now() + "] " + level + " : ";
				var output;
				if (typeof data[0] == "string") {
					data[0] = (dateStr += data[0]);
					output = data;
				} else {
					output = new Array();
					output.push(dateStr);
					for ( var i in data) {
						output.push(data[i]);
					}
				}
				return console.log.apply(console, output);
				for ( var i in output) {
					console.log(output[i]);
				}
			}
		}
	};
});
