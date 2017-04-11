require.config({
    urlArgs: "bust=" +  (new Date()).getTime(),
    paths : {
//        'SDK' : '../lib/Controller-SDK',
        'SDK' : '/demo/dist/OpenSDK-v1.0.0.min',
        'Vue' : '../../js/vue.min',
        'jQuery' : '/plugins/jQuery/jQuery-2.1.4.min', 
        'bootstrap' : '/bootstrap/js/bootstrap.min',
        'jQuery.slimscroll' : '/plugins/slimScroll/jquery.slimscroll.min',
        'fastclick' : '/plugins/fastclick/fastclick.min',
        'ALTApp' : '/dist/js/app.min',
        
        'HorizontalForm' : '/dist/js/Components/HorizontalForm',
        'CommonTableList' : '/dist/js/Components/CommonTableList',
        'SmellPlayList' : '/dist/js/Components/SmellPlayList',
        'DeviceList' : '/dist/js/Components/DeviceList',
        
        'Utilsc' : '/dist/js/Utils',
    },
    shim : {
        'jQuery' : {
            exports : '$'
        },
        'jQuery.slimscroll' : ['jQuery'],
        'bootstrap' : ['jQuery'],
        'ALTApp' : ['jQuery','jQuery.slimscroll','bootstrap','fastclick'],
    }
});
define('initialize',[
    'Vue','jQuery',
    'HorizontalForm','CommonTableList','SmellPlayList','DeviceList',
    'ALTApp','Utilsc'],function(Vue,$) {
    var EVue = Vue.extend({
        'methods' : {
            'removeTableRow' : function(key,row){
                this.pageConfig[key].data.list.splice(row,1);
            },
            'isMarked' : function(runtimeKey,key){
                if(undefined === key){
                    return (undefined !== this.runtime[runtimeKey] && false !== this.runtime[runtimeKey]);
                }
                return (undefined !== this.runtime[runtimeKey][key] && false !== this.runtime[runtimeKey][key]);
            },
            'markWithKey' : function(runtimeKey,key,data){
                if(undefined === data){
                    this.runtime[runtimeKey][key] = true;
                }else{
                    this.runtime[runtimeKey][key] = data;
                }
            },
            'mark' : function(runtimeKey,data){
                if(undefined === data){
                    this.runtime[runtimeKey] = true;
                }else{
                    this.runtime[runtimeKey] = data;
                }
            },
            'getMarkedData' : function(runtimeKey,key){
                if(undefined === key){
                    return this.runtime[runtimeKey];
                }
                return this.runtime[runtimeKey][key];
            },
            'unmark' : function(runtimeKey,key){
                if(undefined === key){
                    return this.runtime[runtimeKey] = false;
                }
                this.runtime[runtimeKey][key] = false;
            }, 
            'formReset' : function(formTag){
                var fields = this.pageConfig[formTag].fields;
                for(var i in fields){
                    if(undefined !== fields[i]['default']){
                        fields[i].value = fields[i]['default'];
                    }else{
                        fields[i].value = '';
                    }
                }
            },
            'formFieldReset' : function(formTag,field,defaultValue){
                if(undefined === this.pageConfig[formTag].fields[field]){
                    return false;
                }
                var defaultVal = this.pageConfig[formTag].fields[field]['default'];
                defaultVal = undefined === defaultValue ? defaultVal : defaultValue;
                if(undefined === defaultVal){
                    this.pageConfig[formTag].fields[field].value = '';
                }else{
                    this.pageConfig[formTag].fields[field].value = defaultVal;
                }
            },
            'formSelectInit' : function(formTag,field,data){
                this.pageConfig[formTag].fields[field].data = data;
            },
            'appendFormSelect' : function(formTag,field,data){
                if(data instanceof Array ){
                    for(var i in data){
                        this.pageConfig[formTag].fields[field].data.push(data[i]); 
                    }
                }else{
                    this.pageConfig[formTag].fields[field].data.push(data);
                }
            },
            'appendTableData' : function(tableTag,data){
                this.pageConfig[tableTag].data.list.push(data);
            },
            'getTableData' : function(tableTag,row){
                if(undefined === row){
                    return this.pageConfig[tableTag].data.list;
                }
                return this.pageConfig[tableTag].data.list[row];
            },
            'updateTableRow' : function(tableTag,row,data){
                for(var i in data){
                    this.pageConfig[tableTag].data.list[row][i] = data[i];
                }
            }
        }
    });
    return EVue;
});
var VVM ;
require(['initialize','SDK'], function(EVue,SDK) {
    var $ = require('jQuery'),
        Vue = require('Vue'),
        Utils = require('Utilsc');
//    Utils.cacheSet('connectCache',{
//        'access_key' : ret.access_key,
//        'access_secret' : ret.access_secret,
//        'env' : ret.env,
//    });
    var connectCache = Utils.cacheGet('connectCache');
    var defaultConfig = {
        'accessKey' : 'NF3DyoBL8bjT6sjkM9a5',
        'accessSecret' : 'ADJSnMGU2riyhOWagkVb',
        'env' : 'production'
    };
    var catchedConfig = {
        'accessKey' : connectCache.accessKey,
        'accessSecret' : connectCache.accessSecret,
        'env' : connectCache.env,
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
        
        
    
    var runtimeData = {
        'topTitle' : '连接服务器',
        'windowStack' :[],
        'status' : {
            'isServerConnected' : false,
        },
        'currentWindow' : 'connect_form',
        'currentDeviceAccess' : '',
        'currentDeviceName' : '',
        'currentRequestPlaySmell' : '',
        'deviceInstance' : {},
        'deviceConnectStatus' : {},
        'envMap' : {
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
    };
    var pageConfig = {
        "runtime" : runtimeData,
        "connect_form": {
            "attrs": {
                "caption": "服务器连接",
                "formColor": "box-info",
                "buttons": [
                    {
                        "name": "连接服务器",
                        "class": "btn-info",
                        "event": "validate"
                    },
                    {
                        "name": "清除缓存",
                        "class": "btn-warning",
                        "event": "reset"
                    },
                    {
                        "name": "新环境",
                        "class": "btn",
                        "event": "newenv"
                    }
                ],
                "action": {
                    "uri": "/api/attr/6",
                    "method": "PUT",
                    "success": {
                        "redirect": "/attrs"
                    }
                }
            },
            "fields": {
                "access_key": {
                    "name": "AccessKey/AccessSecret，以‘/’拼接",
                    "type": "input",
                    "attrs": {
                        "type": "text",
                        "placeholder": "开发者 AccessKey"
                    },
                    "value": defaultConfig.accessKey
                },
                "access_secret": {
                    "name": "开发者 AccessSecret",
                    "type": "input",
                    "attrs": {
                        "type": "text",
                        "placeholder": "开发者 AccessSecret"
                    },
                    "value": defaultConfig.accessSecret
                },
                "env": {
                    "name": "服务器",
                    "type": "select",
                    "value": defaultConfig.env,
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
                }
            }
        },
        "new_env_form": {
            "attrs": {
                "caption": "新建服务器",
                "formColor": "box-info",
                "buttons": [
                    {
                        "name": "Save",
                        "class": "btn-info",
                        "event": "validate"
                    },
                    {
                        "name": "Back",
                        "class": "btn",
                        "event": "back"
                    }
                ],
                "action": {
                    "uri": "/api/attr/6",
                    "method": "PUT",
                    "success": {
                        "redirect": "/attrs"
                    }
                }
            },
            "fields": {
                "name": {
                    "name": "名称",
                    "type": "input",
                    "attrs": {
                        "type": "text",
                        "placeholder": "名称"
                    },
                    "value": "New Env"
                },
                "address": {
                    "name": "IP 地址",
                    "type": "input",
                    "attrs": {
                        "type": "text",
                        "placeholder": "IP 地址"
                    },
                    "value": "127.0.0.1"
                },
                "port": {
                    "name": "端口",
                    "type": "input",
                    "attrs": {
                        "type": "text",
                        "placeholder": "端口"
                    },
                    "value": "8083"
                },
            }
        },
        "device_list" : [{
            "name" : "HaierUE4001",
            "access" : "HaierUE4001",
            "statuss" : false,
            "statusc" : false
        },{
            "name" : "HaierUE4002",
            "access" : "HaierUE4002",
            "statuss" : false,
            "statusc" : false
        },{
            "name" : "HaierUE4003",
            "access" : "HaierUE4003",
            "statuss" : false,
            "statusc" : false
        }],
        "smell_list" : []
    };
    var defaultSmellList = [{
        "name" : "01 号气味",
        "id" : "01"
    },{
        "name" : "02 号气味",
        "id" : "02"
    },{
        "name" : "03 号气味",
        "id" : "03"
    },{
        "name" : "04 号气味",
        "id" : "04"
    }];
 
    var loadSmellList = function(sourceData){
        var smell_list = [];
        for(var i in sourceData){
            var name = '',id = '';
            if(typeof(sourceData[i]) == 'string'){
                name = sourceData[i];
                id = sourceData[i];
            }else{
                name = sourceData[i].name;
                id = sourceData[i].id;
            }
            smell_list.push({
                "name" : name,
                "id" : id,
                "isPlaying" : false,
                "defaultPlaySeconds" : "10",
                "hasError" : false,
                "progress" : {
                    "style" : {"width" : "0%"},
                    "timer" : 0,
                    "cur" : 0
                },
                "playSeconds" : "10"
            });
        }
        return smell_list;
    }
    pageConfig.smell_list = loadSmellList(defaultSmellList);
    
    var vm = new EVue({
        'el' : '#formDemo',
        'data' : {
            'pageConfig' : pageConfig ,
        },
        'mounted' : function(){
            console.log("mounted");
            this.pushWindow(this.pageConfig.runtime.currentWindow);
            console.log(this.pageConfig.runtime.windowStack);
        },
        'methods' : {
            'isShowWindow' : function(formTag){
                return this.pageConfig.runtime.currentWindow == formTag;
            },
            'setTopTitle' : function(title){
                this.pageConfig.runtime.topTitle = title;
            },
            'pushWindow' : function(formTag){
                var windowStack = this.pageConfig.runtime.windowStack ;
                var lastWindow = windowStack[windowStack.length - 1];
                if(lastWindow != formTag){
                    windowStack.push(formTag);
                }
            },
            'popWindow' : function(formTag){
                var windowStack = this.pageConfig.runtime.windowStack ;
                if(windowStack.length >= 2){
                    windowStack.pop();
                    return windowStack[windowStack.length - 1];
                }
                return false;
            },
            'changeWindow' : function(formTag){
                this.pushWindow(formTag);
                this.pageConfig.runtime.currentWindow = formTag;
                this.autoChangeTitle();
                console.log(this.pageConfig.runtime.windowStack);
            },
            'backForword' : function(){
                console.log(this.pageConfig.runtime.windowStack);
                var last = this.popWindow();
                console.log('last = ' + last);
                if(false !== last){
                    this.pageConfig.runtime.currentWindow = last;
                    this.autoChangeTitle();
                }
            },
            'autoChangeTitle' : function(){
                switch(this.pageConfig.runtime.currentWindow){
                    case 'connect_form':
                        vm.setTopTitle('连接服务器');
                        break;
                    case 'device_list':
                        vm.setTopTitle('设备列表');
                        break;
                    case 'new_env_form':
                        vm.setTopTitle('新增环境');
                        break;
                    case 'smell_list':
                        var currentAccess = vm.getRuntime('currentDeviceAccess');
                        var currentName = '';
                        for(var i in vm.pageConfig.device_list){
                            if(currentAccess == vm.pageConfig.device_list[i]['access']){
                                currentName = vm.pageConfig.device_list[i]['name'];
                                break;
                            }
                        }
                        vm.setTopTitle(currentName + '('+currentAccess+')');
                        break;
                    default:;
                }
            },
            'AddNewEnv' : function(formRet,el){
                var runtime = this.pageConfig.runtime;
                if(formRet && undefined === runtime.envMap[formRet.name]){
                    
                    runtime.envMap[formRet.name] = {
                        'basepath' : '',
                        'hostname' : formRet.address, 
                        'port' : formRet.port
                    };
                    
                    this.appendFormSelect('connect_form','env',{
                        'value' : formRet.name,
                        'text' : formRet.name + '(' + formRet.address + ':' + formRet.port + ')',
                    });
                    this.formReset('new_env_form');
                    this.changeWindow('connect_form');
                }
                return false;
            },
            'updateDeviceList' : function(remoteInfo,deviceListScope){
                var deviceScope = {};
                for(var i in deviceListScope){
                    deviceScope[deviceListScope[i]['access']] = i;
                }
                for(var i in remoteInfo){
                    var access = remoteInfo[i]['device_access'];
                    if(undefined !== deviceScope[access]){
                        deviceListScope[deviceScope[access]]['statuss'] = remoteInfo[i]['online'] ? true: false;
                    }
//                    remoteList[remoteInfo[i]['device_access']] = {
//                        "name" :   remoteInfo[i]['device_name'],
//                        "access" : remoteInfo[i]['device_access'],
//                        "statuss" : remoteInfo[i]['online'] ? true: false ,
//                        "statusc" : false  
//                    }
                }
            },
            'listBindDevice' : function(onSuccess){
                var runtime = this.pageConfig.runtime;
                var connect_form = this.pageConfig.connect_form;
                if(this.getRuntimeStatus('isServerConnected')){
                    var basepath = runtime.envMap[connect_form.fields.env.value].basepath;
                    var url =  basepath + 'api/mqtt/listBindedDevices';
                    if(basepath){
                        Utils.ajax({
                            'url' : url,
                            'data' : {
                                'developer_access' : connect_form.fields.access_key.value,
                                'developer_pass' : SDK.utils.SecretEncrpt(connect_form.fields.access_key.value,connect_form.fields.access_secret.value), 
                            },
                            'success' : function(response){
                                if(Utils.apiReqSuccess(response)){
                                    vm.updateDeviceList(Utils.apiReqData(response),vm.pageConfig.device_list);
                                    onSuccess();
                                }
                                console.log(response);
                            },
                            'error' : function () {
                                for(var i in vm.pageConfig.device_list){
                                    vm.pageConfig.device_list[i]['statuss'] = 3;
                                }
                                alert("服务器连接失败，无法读取设备在线状态");
                                onSuccess();
                            }
                        });
                    }else{
                        for(var i in vm.pageConfig.device_list){
                            vm.pageConfig.device_list[i]['statuss'] = 3;
                        }
                        onSuccess();
                    }
                }
            },
            'reqEmqAPI' : function(){
//                'api/clients',
//                'api/subscriptions'
                Utils._ajax({
                    url : 'http://121.41.33.141:18083/api/clients',
                    headers: {
                        Authorization: "Basic YWRtaW46cHVibGlj"
                    },
                    beforeSend: function(request) {
                        request.setRequestHeader("Authorization", "Basic YWRtaW46cHVibGlj");
                    },
                    type: "get",
                    dataType : "jsonp",
                    success: function (data) {
                        console.log(data);
                    }
                });
            },
            'connectServer' : function(formRet,el){
//                this.reqEmqAPI();
//                return;
                var ret = formRet;// this.formValidate('.dev-cnt-tb');
                if(false === ret){
                    return ;
                }
                var accessWithSecret = ret.access_key.split('/');
                if(accessWithSecret.length == 2){
                    ret.access_key = accessWithSecret[0];
                    ret.access_secret = accessWithSecret[1];
                };
                SDK.connect({
                    'accessKey' : ret.access_key,
                    'accessSecret' : ret.access_secret,
                    'logLevel' : 'debug',
                    'mqtt' : this.pageConfig.runtime.envMap[ret.env],
                    'apiTimeout' : 10,
                    'event' : {
                        'onSuccess' : function(){
                            console.log('Server Connect Success');
                            vm.setRuntimeStatus('isServerConnected',true);
                            
                            vm.pageConfig.connect_form.fields.access_key.value = ret.access_key;
                            vm.pageConfig.connect_form.fields.access_secret.value = ret.access_secret;
                            
                            vm.listBindDevice(function(){
                                vm.changeWindow('device_list');
                            });
                            
                            Utils.cacheSet('connectCache',{
                                'accessKey' : ret.access_key,
                                'accessSecret' : ret.access_secret,
                                'env' : ret.env,
                            });
                            
//                            localStorage.setItem('accessKey',ret.accessKey);
//                            localStorage.setItem('accessSecret',ret.accessSecret);
//                            localStorage.setItem('env',ret.env);
                        },
                        'onFailure' :function(){
                            vm.setRuntimeStatus('isServerConnected',false);
                            alert('连接失败');
                        },
                        'onConnectionLost' : function(){
                            vm.setRuntimeStatus('isServerConnected',false);
                            alert('失去连接');
                        }
                    }
                });
                return false;
            },
            'setRuntimeStatus' : function(key,value){
                this.pageConfig.runtime.status[key] = value;
            },
            'getRuntimeStatus' : function(key){
                return this.pageConfig.runtime.status[key];
            },
            'setRuntime' : function(key,value){
                this.pageConfig.runtime[key] = value;
            },
            'getRuntime' : function(key){
                return this.pageConfig.runtime[key];
            },
            'setDeviceConnected' : function(deviceAccess){
                this.pageConfig.runtime.deviceConnectStatus[deviceAccess] = true;
                for(var i in this.pageConfig.device_list){
                    if(deviceAccess == this.pageConfig.device_list[i]['access']){
                        this.pageConfig.device_list[i]['statusc'] = true;
                        break;
                    }
                }
            },
            'isDeviceConnected' : function(deviceAccess){
                return true === this.pageConfig.runtime.deviceConnectStatus[deviceAccess];
            },
            'getCurrentDeviceInstance' : function(){
                var deviceAccess = this.getRuntime('currentDeviceAccess');
                return this.pageConfig.runtime['deviceInstance'][deviceAccess];
            },
            'ConnectDevice' : function(deviceAccess,onSuccess){
                if(!this.getRuntimeStatus('isServerConnected')){
                    alert("服务器未连接");
                    return ;
                }
                if(this.isDeviceConnected(deviceAccess)){
                    console.log(deviceAccess + " is Connected ");
                    vm.changeWindow('smell_list');
                    return ;
                }
                console.log("Connecting " + deviceAccess);
                this.pageConfig.runtime['deviceInstance'][deviceAccess] = SDK.usingDevice(deviceAccess,{
                    'onSuccess' : function(app){
                        vm.setRuntime('currentDeviceAccess',deviceAccess);
                        vm.setDeviceConnected(deviceAccess);
                        
                        vm.showDeviceSmellList(function(smell_list){
                            vm.$children[3].refreshSmellList(smell_list);
                            vm.changeWindow('smell_list');
                        },function(){
                            vm.changeWindow('smell_list');
                        });
                        
                        onSuccess(deviceAccess);
                        app.logger.info('deviceConnect ' + deviceAccess + ' onSuccess');
                    },
                    'onFailure' : function(app){
                        alert('设备连接失败');
                    }
                });
            },
            'playSmell' : function(smellID,time,onSuccess,onError){
                console.log(arguments);
                
                var currentRequestPlaySmell = vm.pageConfig.runtime.currentRequestPlaySmell;
                
//                if(smellID == currentRequestPlaySmell){
//                    return false;
//                }
                
                vm.pageConfig.runtime.currentRequestPlaySmell = smellID;
                
                var isRequesting = true;
                var deviceAccess = this.getRuntime('currentDeviceAccess');
                var instance = this.getCurrentDeviceInstance();
                
                if(this.getRuntimeStatus('isServerConnected') && this.isDeviceConnected(deviceAccess) && instance){
                    instance.playSmell({
                        'actions' : [ {
                            'bottle' : smellID + '',
                            'duration' : parseInt(time),
                            'power' : 5
                        }]
                    },function(sequence,decode,app){
                        vm.pageConfig.runtime.currentRequestPlaySmell = false;
                        if(app.protoRoot.SrErrorCode.SEC_SUCCESS == decode.code){
                   //         onSuccess(smellID);
                        }else if(app.protoRoot.SrErrorCode.SEC_ACCEPT == decode.code){       
                    //        onSuccess(smellID);
                        }else {
                    //        onError(smellID,decode.msg);
                            // show errormsg
                            app.logger.error(decode.msg);
                        //    alert(decode.msg);
                        }
                    },function(sequence,error,app){
                        vm.pageConfig.runtime.currentRequestPlaySmell = false;
                        app.logger.error(error,app);
                  //      onError(smellID,error.msg);
                    });
                }else{
                    alert('设备未连接');
                }
                onSuccess(smellID);
            },
            'stopPlay' : function(smellID,onSuccess,onError){
                console.log(arguments);
                var deviceAccess = this.getRuntime('currentDeviceAccess');
                var instance = this.getCurrentDeviceInstance();
                if(this.getRuntimeStatus('isServerConnected') && this.isDeviceConnected(deviceAccess) && instance){
                    // TODO: this bottle is playing
                    instance.stopPlay([smellID],function(sequence,decode,app){
                        if(app.protoRoot.SrErrorCode.SEC_SUCCESS == decode.code){
              //              onSuccess(smellID);
                        }else if(app.protoRoot.SrErrorCode.SEC_ACCEPT == decode.code){
             //               onSuccess(smellID);
                        }else {
          //                  onError(smellID,decode.msg);
                            app.logger.error(decode.msg);
                        }
                    },function(sequence,error,app){
            //            onError(smellID,error.msg);
                        app.logger.error(error,app);
                    });
                    onSuccess(smellID);
                }else{
                    alert('设备未连接');
                }
                
            },
            'stopAll' : function(onSuccess){
                var deviceAccess = this.getRuntime('currentDeviceAccess');
                var instance = this.getCurrentDeviceInstance();
                if(this.getRuntimeStatus('isServerConnected') && this.isDeviceConnected(deviceAccess) && instance){
                    instance.stopAll(function(sequence,decode,app){
                        if(app.protoRoot.SrErrorCode.SEC_SUCCESS == decode.code){
              //              onSuccess();
                        }else {
                            app.logger.error(decode.msg);
              //              alert(decode.msg);
                        }
                    },function(sequence,error,app){
              //          alert(error.msg);
                        app.logger.error(error,app);
                    });
                }else{
                    alert('设备未连接');
                }
                onSuccess();
            },
            'cacheClear' : function(){
                confirm('是否确定清除缓存？') && Utils.cacheClear();
            },
            'reflushDevice' : function(){
                this.listBindDevice(function(){
                    
                });
            },
            'showDeviceSmellList' : function(onSuccess,onError){
                var deviceAccess = this.getRuntime('currentDeviceAccess');
                var instance = this.getCurrentDeviceInstance();
                if(this.getRuntimeStatus('isServerConnected') && this.isDeviceConnected(deviceAccess) && instance){
                    instance.smellList(function(sequence,decode,app){
                        if(app.protoRoot.SrErrorCode.SEC_SUCCESS == decode.response.code){
                            var smell_list = loadSmellList(decode.smell_list);
                            console.log(smell_list);
                            onSuccess(smell_list);
                        }else {
                            app.logger.error(decode.msg);
                            onError && onError();
                            alert(decode.msg);
                        }
                    },function(sequence,error,app){
                        onError && onError();
                        app.logger.error(error,app);
                    });
                }else{
                    alert('设备未连接');
                }
            },
            'loadDefaultSmellList' : function(onSuccess){
                onSuccess(loadSmellList(defaultSmellList));
//                console.log('loadDefaultSmellList',defaultSmellList);
            },
            'refreshSmellList' : function(onSuccess){
                this.showDeviceSmellList(onSuccess);
                console.log('refreshSmellList');
            }
        }
    });
    VVM = vm;
})