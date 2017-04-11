
# JavaScript SDK 使用说明

## SDK 初始化

OpenSDK JS 版需用 requirejs 载入，下面是载入 SDK 以及初始化配置的示例代码:
用 requireJS 载入

``` html
    <script data-main="OpenSDK/main" src="js/require.js"></script>
```
用 script 标签载入

``` html
    <script type="text/javascript" src="js/require.js"></script>
    <script type="text/javascript" src="OpenSDK.min.js"></script>
    <script type="text/javascript">
    require(['SDK'],function(SDK){
        SDK.connect({
            'accessKey' : 'IAzDhpyc0z9yGFajKp2P', //  Open平台开发者accessKey
            'accessSecret' : 'HNKGRV2O2oeK7W2jtmFC',//  Open平台开发者accessSecret
            'logLevel' : 'info',// deb8ug info notice warning error 日志级别
        });
        var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
        dev.getDevAttr(['mac']); // 获取设备Mac地址
    });
    </script>
```
RequireJS 调用示例

``` javascript
require.config({
    paths : {
        'SDK' : 'OpenSDK.min',
    },
});
require([ 'SDK'], function(SDK) {
    SDK.connect({
        'accessKey' : 'IAzDhpyc0z9yGFajKp2P',//  Open平台开发者accessKey
        'accessSecret' : 'HNKGRV2O2oeK7W2jtmFC',//  Open平台开发者accessSecret
        'logLevel' : 'info',// deb8ug info notice warning error 日志级别
    });
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
     // 获取设备Mac地址
    dev.getDevAttr(['mac'],function(){
    // 成功回调
        console.log(arguments);
    },function(){
    // 失败回调
        console.log(arguments);
    });
    return SDK;
})
```

## 关于 API 回调函数

所有API都会有两个回调函数，分别为 onSuccess 和 onError，onSuccess 包含三个参数（sequence，decode，app），
分别表示 请求序列号、返回数据和 SDK 本身；onError 包含两个参数（error，app），表示错误信息和 SDK 本身


# API列表 

| 名称           | 方法                                  | 
|:----------------- |:--------------------------------------| 
| 设置设备休眠      | [sleep(onSuccess,onError)](/JavaScript/sleep.md)               |
| 唤醒设备          | [wakeup(onSuccess,onError)](/JavaScript/wakeup.md)            |
| 释放设备资源      | [disconnect()](/JavaScript/disconnect.md)          |
| 获取瓶子使用时间  | [usedSeconds(onSuccess,onError)](/JavaScript/usedSeconds.md)       | 
| 设备播放气味      | [playSmell(how, onSuccess,onError)](/JavaScript/playSmell.md)           | 
| 获取设备属性      | [getDevAttr(attrs, onSuccess,onError)](/JavaScript/getDevAttr.md)          | 
| 设置设备属性      | [setDevAttr(attrSettings, onSuccess,onError)](/JavaScript/setDevAttr.md)          |
| 获取设备模型信息  | [deviceModelReport(onSuccess,onError)](/JavaScript/featureReport.md)         |
| 停止播放气味      | [stopPlay(bottles,onSuccess,onError)](/JavaScript/stopPlay.md)         |
| 取消设备任务      | [removeTask(stop_tasks,onSuccess,onError)](/JavaScript/removeTask.md)         |
| 设置控件属性      | [setControlAttr(settings,onSuccess,onError)](/JavaScript/setControlAttr.md)         |


