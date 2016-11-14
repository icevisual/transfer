
## 获取瓶子使用时间 usedSeconds <a name="usedSeconds" id="usedSeconds" />

用于获取瓶子使用时间

####　参数解析

| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------| 
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |

#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    dev.usedSeconds(function(UsedTimeResponse,app){
    // UsedTimeResponse 为设备返回的包含瓶子使用信息的对象
    // app 为SDK本身
    },function(error,app){
    // error 为调用失败的错误信息
    }); 
```

#### BaseResponse结构说明

``` json
{
    'response' : {// 成功失败
        'code' : 1,
        'data' : ['error msg']
    },
    'usedTime' : [// 用时列表
        {
            'bottle' : '000000a1', // 气味瓶子编号
            'time' : 199999, // 已用时长，单位秒
        },
        {
            'bottle' : '000000a2',
            'time' : 199999,
        },
    ]
}
```
