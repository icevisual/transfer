
## 获取设备模型信息 deviceModelReport <a name="deviceModelReport" id="deviceModelReport" />

用于获取设备模型信息

####　参数解析

| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------|
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |

#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    var Simple = SDK.protoRoot;// 播放方式结构的根结构
    dev.deviceModelReport(function(sequence,DeviceModelResponse,app){
    // DeviceModelResponse 为设备返回的执行结果对象，描述操作成功与否
    // app 为SDK本身
    },function(error,app){
    // error 为调用失败的错误信息
    }); 
```

#### DeviceModelResponse 结构

``` json
{
    'response': {
        'code': Simple.SrErrorCode.SEC_SUCCESS,
        'msg': 'error msg'
    },
    'cmd' : [
        SrCmdId.SCI_REQ_SLEEP // 支持的指令列表
    ],
    'controls': [ // 设备模型中可控组件列表
         {
             'switch' : { // 开关
                 'name' : 'switch',
                 'status' : true 
             },
         },
         {
             'spin' : { // 旋钮
                 'name' : 'spin',
                 'angle' : 22
             }
         }
    ]
}
                
                

```
