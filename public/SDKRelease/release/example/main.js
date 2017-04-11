require.config({
    paths : {
        'SDK' : '../dist/OpenSDK-v1.0.0.min',
        'jQuery' : '../dist/jquery-1.9.1.min',
        'Vue' : '../dist/vue.min',
    },
    shim : {
        'jQuery' : {
            exports : '$'
        },
    }
});

var SMSDK ,dev,ret,cnm,dt;
require([ 'jQuery','SDK','Vue' ], function($,SDK,Vue) {
    // TODO : 级联select ，自定义组件，数据来源url
    var Utils = require('Utils');
    
    
    
    var defaultConfig = {
        'accessKey' : 'TCeOp0gzzrWhAMoOa3Mm',
        'accessSecret' : 'POVX1lgIvo8q1KHYpoD9'
    };
    var catchedConfig = {
        'accessKey' : localStorage.getItem('accessKey'),
        'accessSecret' : localStorage.getItem('accessSecret'),
        'env' : localStorage.getItem('env')
    };
    if(catchedConfig.accessKey){
        defaultConfig.accessKey = catchedConfig.accessKey;
    }
    if(catchedConfig.accessSecret){
        defaultConfig.accessSecret = catchedConfig.accessSecret;
    }
    if(catchedConfig.env){
        defaultConfig.env = catchedConfig.env;
    }
    var formTableConfig = {
        'isServerConnected' : false,
        'deviceInstance' : {
            'currentDevice' : false,
            'instance' : {},
            'apicache' : {
                'smellList' : {
                    
                }
            },
            'flags' : {}
        },
        'logic' : {
            'showConnect' : true,
            'showReconnect' : false,
            'showBindDevice' : false,
            'showLoopPlay' : false
        },
        'loopPlayList' : {
            'expectedLoopTime' : 1,
            'interval' : 1,
            'loopTime' : 1,
            'cacheLoopData' : true,
            'list' : [],
            'flags' : {},
            'timer' : {}
        },
        'errorLogList' : [],
        'bindedDevice' : [],
        'connectConfig' : {
            'test' : {
                'basepath' : 'http://test.open.qiweiwangguo.com/',
                'hostname' : '121.41.33.141', // 测试
                'port' : '8083',
            },
            'local' : {
                'basepath' : 'http://open.smell.com/',
                'hostname' : '192.168.5.61', // 测试
                'port' : '8083',
            },
            'production' : {
                'basepath' : 'http://open.qiweiwangguo.com/',
                'hostname' : '120.26.109.169', // 正式
                'port' : '8083',
            }
        },
        'bind-dev-fields' : {
            'deviceName' : {
                'name' : '设备名称',
                'type' : 'input',
                'attrs' : {
                    'type' : 'text',
                },
                'value' : '5-vr',
            },
            'deviceSecret' : {
                'name' : '设备秘钥',
                'type' : 'input',
                'attrs' : {
                    'type' : 'password',
                },
                'value' : '',
            },
        },
        'fields' : {
            'accessKey' : {
                'name' : '开发者 AccessKey',
                'validate' : {
                    'rules' : 'required|max:20',
                    'message' : {
                        'require' : '请填写 开发者 AccessKey'
                    },
                },
                'type' : 'input',
                'attrs' : {
                    'type' : 'text',
                    'default' : 'asdda'
                },
                'value' : defaultConfig.accessKey,
            },
            'accessSecret' : {
                'name' : '开发者 AccessSecret',
                'validate' : {
                    'rules' : 'required|max:20',
                    'message' : {
                        'require' : '请填写 开发者 AccessSecret'
                    },
                },
                'type' : 'input',
                'attrs' : {
                    'type' : 'text',
                },
                'value' : defaultConfig.accessSecret,
            },
            'logLevel' : {
                'name' : '日志级别',
                'type' : 'select',
                'value' : 'debug',
                'attrs' : {
                    'multiple' : 'multiple',
                },
                'data' : [
                   {
                       'value' : 'debug',
                       'text' : 'debug',
                   },
                   {
                       'value' : 'info',
                       'text' : 'info',
                   },
                   {
                       'value' : 'notice',
                       'text' : 'notice',
                   },
                   {
                       'value' : 'warning',
                       'text' : 'warning',
                   },
                   {
                       'value' : 'error',
                       'text' : 'error',
                   }
                ],
            },
            'env' : {
                'name' : '环境',
                'type' : 'select',
                'value' : defaultConfig.env,
                'data' : [
                    {
                        'value' : 'local',
                        'text' : '本地(192.168.5.61:8083)',
                    },      
                    {
                        'value' : 'test',
                        'text' : '测试(121.41.33.141:8083)',
                    },
                    {
                        'value' : 'production',
                        'text' : '正式(120.26.109.169:8083)',
                    }
                ]
            },
        }
    };
    dt = formTableConfig;
    // 组件的参数为 Object ，如何传值，当前用 data 函数 和 变量作用域做
    var determineDataSet = function(dataSelector){
        var map = {
            'bind-dev' : 'formTableConfig["bind-dev-fields"]',
            'server-connect' : 'formTableConfig.fields',
        };
        if(map[dataSelector]){
            return {
                'fields' : eval(map[dataSelector])
            };
        }
        return false;
    }
    
    Vue.component('common-form',{
        template : '\
    <table class="dev-cnt-tb" >\
        <caption>{{caption}}</caption>\
        <template v-for="(item,index) in fields" >\
            <tr>\
                <td>{{item.name}}</td>\
                <td>\
                    <template v-if="\'input\' == item.type">\
                        <input v-model="item.value" :type="item.attrs.type" :name="index" >\
                    </template>\
                    <template v-if="\'select\' == item.type">\
                        <select v-model="item.value" :name="index">\
                            <option v-for="option in item.data" :value="option.value" >{{ option.text }}</option>\
                        </select>\
                    </template>\
                </td>\
            </tr>\
        </template>\
        <tr>\
            <td>&nbsp;</td><td>\
            <button class="dev-cnt-btn" v-on:click="btnclick">连接</button>\
            <button class="dev-cnt-btn" v-on:click="clearCache">清除缓存</button>\
            </td>\
        </tr>\
    </table>\
',
        'props' : ['dataSelector','caption'],
        'data' : function () {
            return determineDataSet(this.dataSelector);
            if(this.dataSelector == 'bind-dev'){
                return {
                    'fields' : formTableConfig['bind-dev-fields']
                };
            }
            return {
                'fields' : formTableConfig.fields
            };
        },
        'methods' : {
            'clearCache' : function(){
                localStorage.clear();
            },
            'doFormValidate' : function(formSelector,inputData){
                var _form = $(formSelector);
                var fields = inputData.fields;
                var ret = {};
                for(var key in fields){
                    var _valField = _form.find(fields[key].type + '[name='+key+']');
                    if(!_valField.val()){
                        _valField.focus();
                        return false;
                    }
                    ret[key] = _valField.val();
                }
                return ret;
            },
            'btnclick' : function(){
                this.$emit('btnclick',this.doFormValidate(this.$el,this.$data));
            },
        },
    });
    // 连接之后 [获取已绑定设备，绑定设备，列举所有设备]
    var vm = new Vue({
        'el' : '#connect-container',
        'data' : formTableConfig,
        'filters' : {
            switchStatus : function (value) {
                return value == 1 ? 'ON' : 'OFF'
            }
        },
        'watch' : {
            isServerConnected: function (newQuestion) {
                console.log('isServerConnected changed',newQuestion);
            }
        },
        'computed' : {
            currentDeviceAPI: function () {
                console.log('computed currentDeviceAPI');
                var currentDevice = this.deviceInstance['currentDevice'];
                if(currentDevice && this.deviceInstance['flags'][currentDevice]){
                    var currentDeviceInstance = this.deviceInstance['instance'][currentDevice];
                    var ret = [];
                    for(var key in currentDeviceInstance){
                        if(typeof currentDeviceInstance[key] == 'function'){
                            if(key.charAt(0) != '_'){
                                ret.push(key);
                            }
                        }
                    }
                    return ret;
                }
                return [];
            },
            currentDevice : function(){
                var currentDeviceAccess = this.deviceInstance['currentDevice'];
                for(var i = 0 ; i < this.bindedDevice.length ; i ++){
                    if(this.bindedDevice[i]['device_access'] == currentDeviceAccess){
                        return this.bindedDevice[i]['device_name'] + '(' + currentDeviceAccess + ')';
                    }
                }
                return '未连接';
            }
        },
        'mounted' : function(){
            $('.dev-init').removeClass('hide');
        },
        'methods' : {
            mqttConnect : function(formRet){
                var ret = formRet;// this.formValidate('.dev-cnt-tb');
                if(false === ret){
                    return ;
                }
                SDK.connect({
                    'accessKey' : ret.accessKey,
                    'accessSecret' : ret.accessSecret,
                    'logLevel' : ret.logLevel,
                    'mqtt' : this.connectConfig[ret.env],
                    'apiTimeout' : 15,
                    'event' : {
                        'onSuccess' : function(){
                            
                            
                            localStorage.setItem('accessKey',ret.accessKey);
                            localStorage.setItem('accessSecret',ret.accessSecret);
                            localStorage.setItem('env',ret.env);
                            
                            
                            vm.isServerConnected = true;
                            
                            vm.logic.showConnect = false;
                            vm.logic.showReconnect = true;
//                            console.log(SDK.protoRoot.SrDevAttrType);
                        },
                        'onFailure' :function(){
                            vm.isServerConnected = false;
                            alert('连接失败');
                        },
                        'onConnectionLost' : function(){
                            vm.isServerConnected = false;
                            alert('失去连接');
                        }
                    }
                });
                SMSDK = SDK;
            },
            btnClicked : function(formRet){
                if(false === formRet){
                    return ;
                }
                if( ! vm.isServerConnected){
                    this.mqttConnect(formRet);
                }else {
                    vm.logic.showConnect = false;
                    vm.logic.showReconnect = true;
                }
            },
            listBindDevice : function(){
                vm.isServerConnected && Utils.ajax({
                    'url' : this.connectConfig[this.fields.env.value].basepath + 'api/mqtt/listBindedDevices',
                    'data' : {
                        'developer_access' : this.fields.accessKey.value,
                        'developer_pass' : Utils.SecretEncrpt(this.fields.accessKey.value,this.fields.accessSecret.value), 
                    },
                    'success' : function(response){
                        console.log(response);
                        for(var i = 0 ; i < response.data.length ; i ++ ){
                            response.data[i]['connected'] = false;
                        }
                        vm.bindedDevice = response.data;
                    }
                });
            },
            showBindDeviceFrom : function(){
                if(this.logic.showBindDevice){
                    this.logic.showBindDevice = false;
                }else{
                    this['bind-dev-fields']['deviceName']['value'] = '';
                    this['bind-dev-fields']['deviceSecret']['value'] = '';
                    this.logic.showBindDevice = true;
                }
            },
            bindDeviceBtnClick : function(ret){
                if( false !== ret ){
                    return this.bindDevice(ret.deviceName,ret.deviceSecret);
                }
            },
            bindDevice : function(deviceName,deviceSecret){
                return SDK.bindDevice(deviceName,deviceSecret,{
                    'onSuccess' : function(resp,app){
                        app.logger.info('bindDevice ' + resp.data.access_key + ' onSuccess');
                        vm.logic.showBindDevice = false;
                    },
                    'onFailure' : function(resp,app){
                        app.logger.info('bindDevice onFailure :' + resp.msg);
                    }
                });
            },
            unbindDevice : function(deviceAccess){
                return confirm('是否确认取消绑定') && SDK.unbindDevice(deviceAccess,{
                    'onSuccess' : function(resp,app){
                        app.logger.info('unbindDevice ' + deviceAccess + ' onSuccess');
//                        vm.logic.showBindDevice = false;
                    },
                    'onFailure' : function(resp,app){
                        app.logger.info('unbindDevice onFailure :' + resp.msg);
                    }
                });
            },
            deviceConnect : function(deviceAccess){
                if(vm.deviceInstance['flags'][deviceAccess] === true){
                    vm.deviceInstance['currentDevice'] = deviceAccess;
                    dev = vm.deviceInstance['instance'][deviceAccess];
                    return ;
                }
                vm.deviceInstance['instance'][deviceAccess] = SDK.usingDevice(deviceAccess,{
                    'onSuccess' : function(app){
                        // connect status
                        vm.deviceInstance['flags'][deviceAccess] = true;
                        // current 对策device
                        vm.deviceInstance['currentDevice'] = deviceAccess;
                        for(var i = 0 ; i < vm.bindedDevice.length ; i ++){
                            if(vm.bindedDevice[i]['device_access'] == deviceAccess){
                                vm.bindedDevice[i]['connected'] = true;
                                break;
                            }
                        }
                        app.logger.info('deviceConnect ' + deviceAccess + ' onSuccess');
                    },
                    'onFailure' : function(app){
                        vm.deviceInstance['flags'][deviceAccess] = false;
                        app.logger.info('deviceConnect ' + deviceAccess + ' onFailure');
                        alert('设备连接失败');
                    }
                });
            },
            showDeviceSmellList : function(){
                if(false !== vm.deviceInstance['currentDevice']){
                    // 未连接设备
                    var currentAccess = vm.deviceInstance['currentDevice'];
                    var currentDevice = vm.deviceInstance['instance'][currentAccess];
                    currentDevice.smellList(function(sequence,decode,app){
                        app.logger.info(decode);
                        var bottles = vm.loadCachedLoopData();
                        var defaultPlaySeconds = 30;// 每个瓶子默认播放时间
                        
                        var smellList = {};
                        for(var i in decode.smell_list ){
                            smellList[decode.smell_list[i]] = {
                                 'bottle' : decode.smell_list[i],// 瓶子ID
                                 'isPlaying' : false, // 是否正在播放标记
                                 'timer' : false, // 计数器
                                 'processor' : '', // 播放中的动画
                                 'isLoopPlay' : true,// 是否参与循环播放
                                 'playSeconds' : defaultPlaySeconds // 默认播放时间
                            };
                            
                            if(false !== bottles && bottles[decode.smell_list[i]] ){
                                smellList[decode.smell_list[i]]['isLoopPlay'] = bottles[decode.smell_list[i]]['isLoopPlay'];
                                smellList[decode.smell_list[i]]['playSeconds'] = bottles[decode.smell_list[i]]['playSeconds'];
                            }
                        }
                        vm.deviceInstance['apicache']['smellList'] = smellList;
                        
                        vm.logic.showLoopPlay = true;
                        
                        
                        
                        // if loopData is Cached,confirm if you want to load play config from cache
                        // clear cached data if choose not
                    },function(error,app){
                        app.logger.error(error);
                    });
                }else{
                    alert('设备未连接');
                }
            },
            isDeviceConnected : function(deviceAccess){
                return vm.deviceInstance['flags'][deviceAccess] === true;
            },
            playSmellAction : function(bottle,time){
                var bottleCfg = vm.deviceInstance['apicache']['smellList'][bottle];
                bottleCfg['isPlaying'] = true;
                bottleCfg['processor'] = '';
                bottleCfg['timer'] = setInterval(function(){
                    bottleCfg['processor'] += '>';
                    time --;
                    if(time <= 0) {
                        bottleCfg['isPlaying'] = false;
                        setTimeout(function(){
                            bottleCfg['processor'] = '';
                        },500);
                        clearInterval(bottleCfg['timer']);
                    }
                },1000);
            },
            stopPlaySmellAction : function(bottle){
                var bottleCfg = vm.deviceInstance['apicache']['smellList'][bottle];
                if(bottleCfg['isPlaying']){
                    bottleCfg['isPlaying'] = false;
                    bottleCfg['processor'] = '';
                    clearInterval(bottleCfg['timer']);
                }
                return false;
            },
            firePlayBtn : function(bottle){
                $('.usl').find('input[name="time-' + bottle + '"]').parent().find('button:eq(0)').click();
            },
            getShouldPlayingSeconds : function(bottle){
                
                return vm.deviceInstance['apicache']['smellList'][bottle]['playSeconds'];
                
                var seconds = $('.usl').find('input[name="time-' + bottle + '"]').val();
                return parseInt(seconds);
            },
            setPlayingSeconds : function(bottle,seconds){
                var seconds = $('.usl').find('input[name="time-' + bottle + '"]').val(seconds);
            },
            stopLoopPlay : function(){
                this.loopPlayList.list = [];
                clearTimeout(this.loopPlayList['timer']);
            },
            loopPlay : function(){
                
                loopTime = parseInt(this.loopPlayList.loopTime);
                if(loopTime <= 0){
                    return alert('循环次数需大于 0');
                }
                
                var bottleCfg = vm.deviceInstance['apicache']['smellList'];
                var loopPlayList = this.loopPlayList;
                for(var i in bottleCfg){
                    if(bottleCfg[i]['isLoopPlay'] === true){
                        loopPlayList.list.push(i);
                        loopPlayList.flags[i] = 0;
                    }
                }
                console.log(loopPlayList);
                var this$1 = this;
                
                loopPlayList.expectedLoopTime = loopPlayList.loopTime;
                
                var lp = function(){
                    // 找到本次要触发的按钮，再得到下次的时间，以再下次触发下次的按钮
                    var shouldPlayingSeconds = 0;
                    var bottle = loopPlayList.list.shift();
                    var loopTime = loopPlayList.loopTime;
                    if(undefined !== bottle){
                        // 获取播放时间
                        shouldPlayingSeconds = this$1.getShouldPlayingSeconds(bottle);
                        shouldPlayingSeconds = parseInt(shouldPlayingSeconds) + parseFloat(this$1.loopPlayList.interval);
                        // 播放 N 秒气味
                        this$1.firePlayBtn(bottle);
                        if(shouldPlayingSeconds > 0){
                            loopPlayList['timer'] = setTimeout(lp,shouldPlayingSeconds * 1000);
                        }
                    }else{
                        loopPlayList.loopTime --;
                        if(loopPlayList.loopTime > 0 ){
                            this$1.loopPlay();
                        }else{
                            this$1.clearCachedLoopData();
                        }
                    }
                }
                loopPlayList['timer'] = setTimeout(lp,0);
                
                if(loopPlayList.cacheLoopData){
                    // cache loop info & play seconds
                    this.cacheLoopData();
                }
            },
            getCachedLoopData : function(){
                var json = localStorage.getItem('cachedDataLoopPlayList');
                if(json){
                    return JSON.parse(json);
                }
                return false;
            },
            clearCachedLoopData : function(){
                localStorage.removeItem('cachedDataLoopPlayList');
            },
            loadCachedLoopData : function(){
                var cachedData = this.getCachedLoopData();
                if(cachedData){
                    if(confirm('是否从缓存读取？')){
                        var cachedData = this.getCachedLoopData();
                        this.loopPlayList['expectedLoopTime'] = cachedData.loopPlayList.expectedLoopTime;
                        this.loopPlayList['interval'] = cachedData.loopPlayList.interval;
                        this.loopPlayList['loopTime'] = cachedData.loopPlayList.loopTime;
                        return cachedData['bottles'];
                    }else{
//                        this.clearCachedLoopData();
                    }
                }
                return false;
            },
            cacheLoopData : function(){
                var cachedData = {
                    'bottles' : {}
                };
                for(var bottle in vm.deviceInstance['apicache']['smellList']){
                    var seconds = this.getShouldPlayingSeconds(bottle);
                    cachedData['bottles'][bottle] = {
                        'isLoopPlay' : vm.deviceInstance['apicache']['smellList'][bottle]['isLoopPlay'],// 是否参与循环播放
                        'playSeconds' : vm.deviceInstance['apicache']['smellList'][bottle]['playSeconds'] // 默认播放时间   
                    };
                }
                cachedData['loopPlayList'] = {
                    'expectedLoopTime' : this.loopPlayList['expectedLoopTime'],
                    'interval' : this.loopPlayList['interval'],
                    'loopTime' : this.loopPlayList['loopTime'],
                }
                var json = JSON.stringify(cachedData);
                localStorage.setItem('cachedDataLoopPlayList',json);
            },
            playSmell : function(bottle){
                if(false !== vm.deviceInstance['currentDevice']){
                    // 未连接设备
                    var currentAccess = vm.deviceInstance['currentDevice'];
                    var currentDevice = vm.deviceInstance['instance'][currentAccess];
                    var _time = $('input[name='+'time-' + bottle+']');
                    var time  = parseInt(_time.val());
                    if( ! (time > 0 && time <= 100) ){
                        alert('请输入 1 - 100 的整数');
                        return _time.focus();
                    }
                    // Stop All Playing Smell Animation 
                    for(var i in vm.deviceInstance['apicache']['smellList']){
                        var bottleCfg = vm.deviceInstance['apicache']['smellList'][i];
                        if(bottleCfg['isPlaying']){
                            vm.stopPlaySmellAction(i);
                        }
                    }
                    
                    
                    currentDevice.playSmell({
                        'actions' : [ {
                            'bottle' : bottle + '',
                            'duration' : time,
                            'power' : 5
                        }]
                    },function(sequence,decode,app){
                        if(app.protoRoot.SrErrorCode.SEC_SUCCESS == decode.code){
                            vm.playSmellAction(bottle,time);
                        }else if(app.protoRoot.SrErrorCode.SEC_ACCEPT == decode.code){       
                            vm.deviceInstance
                            vm.playSmellAction(bottle,time);
                        }else {
                            app.logger.error(decode.msg);
                            alert(decode.msg);
                        }
                    },function(sequence,error,app){
                        
                        // 记录错误日志，
                        // developer infor
                        // 
                        var deviceAccess = vm.deviceInstance['currentDevice'],deviceName = '';
                        for(var i = 0 ; i < vm.bindedDevice.length ; i ++){
                            if(vm.bindedDevice[i]['device_access'] == deviceAccess){
                                deviceName = vm.bindedDevice[i]['device_name'];
                                break;
                            }
                        }
                        
                        var logData = {
                            "datetime" : Utils.now(),
                            "errortype" : error.type,
                            "errormsg" : error.msg,
                            "developer_access" : vm.fields.accessKey.value,
                            "device_name" : deviceName,
                            "device_access" : deviceAccess,
                            "api_name" : "playSmell",
                            "req_seq" : sequence,
                            "req_timeout" : app.getEssentialConfig('apiTimeout'),
                            "req_params" : {
                                'actions' : [ {
                                    'bottle' : bottle + '',
                                    'duration' : time,
                                    'power' : 5
                                }]
                            },
                            "loopPlayConfig" : vm.loopPlayList
                        };
                        
                        cnm = logData;
                        app.errorReport(logData);
                        
                        vm.showError('[' + Utils.now() + ']['+sequence+']' + error.msg);
                        app.logger.error(error,app);
                    });
                }else{
                    alert('设备未连接');
                }
            },
            showError : function(msg){
                this.errorLogList.unshift(msg);
            },
            stopPlay : function(bottle){
                if(false !== vm.deviceInstance['currentDevice']){
                    // TODO: this bottle is playing
                    var bottleCfg = vm.deviceInstance['apicache']['smellList'][bottle];
                    if(!bottleCfg['isPlaying']){
                        return false;
                    }
                    var currentAccess = vm.deviceInstance['currentDevice'];
                    var currentDevice = vm.deviceInstance['instance'][currentAccess];
                    currentDevice.stopPlay([bottle],function(sequence,decode,app){
                        if(app.protoRoot.SrErrorCode.SEC_SUCCESS == decode.code){
                            vm.stopPlaySmellAction(bottle);
                        }else if(app.protoRoot.SrErrorCode.SEC_ACCEPT == decode.code){
                            vm.stopPlaySmellAction(bottle);
                        }else {
                            app.logger.error(decode.msg);
                            alert(decode.msg);
                        }
                    },function(error,app){
                        alert(error.msg);
                        app.logger.error(error,app);
                    });
                }else{
                    alert('设备未连接');
                }
            },
            stopAll : function(){
                if(false !== vm.deviceInstance['currentDevice']){
                    var currentAccess = vm.deviceInstance['currentDevice'];
                    var currentDevice = vm.deviceInstance['instance'][currentAccess];
                    currentDevice.stopAll(function(sequence,decode,app){
                        if(app.protoRoot.SrErrorCode.SEC_SUCCESS == decode.code){
                            for(var i in vm.deviceInstance['apicache']['smellList']){
                                var bottleCfg = vm.deviceInstance['apicache']['smellList'][i];
                                if(bottleCfg['isPlaying']){
                                    vm.stopPlaySmellAction(i);
                                }
                            }
                        }else {
                            app.logger.error(decode.msg);
                            alert(decode.msg);
                        }
                    },function(error,app){
                        alert(error.msg);
                        app.logger.error(error,app);
                    });
                }else{
                    alert('设备未连接');
                }
            },
            getCurrentPlay : function(){
                if(false !== vm.deviceInstance['currentDevice']){
                    var currentAccess = vm.deviceInstance['currentDevice'];
                    var currentDevice = vm.deviceInstance['instance'][currentAccess];
                    currentDevice.currentPlay(function(sequence,decode,app){
                        if(decode.bottle){
                            alert('当前播放的瓶子 ' + decode.bottle + 
                                    '\n总时间（秒） ' + decode.total_second + 
                                    '\n剩余（秒）' + decode.remain_second);
                        }
                        app.logger.info(decode);
                    },function(error,app){
                        alert(error.msg);
                        app.logger.error(error,app);
                    });
                }else{
                    alert('设备未连接');
                }
            },
            playSmellManual :function(){
                var smellID = $('input[name=manual-smell-id]').val();
                var playTime = $('input[name=manual-play-time]').val();
                playTime = parseInt(playTime);
                if(!smellID || !playTime){
                    alert("请填写参数");
                    return ;
                }
                
                if(smellID && playTime && false !== vm.deviceInstance['currentDevice']){
                    // 未连接设备
                    var currentAccess = vm.deviceInstance['currentDevice'];
                    var currentDevice = vm.deviceInstance['instance'][currentAccess];
                    currentDevice.playSmell({
                        'actions' : [ {
                            'bottle' : smellID + '',
                            'duration' : playTime,
                            'power' : 5
                        }]
                    },function(sequence,decode,app){
                        alert("Playing");
                    },function(sequence,error,app){
                        app.logger.error(error,app);
                    });
                }else{
                    alert('设备未连接');
                }
                
                
            }
        }
    });
})

