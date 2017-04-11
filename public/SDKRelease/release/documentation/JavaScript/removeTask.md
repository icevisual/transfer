
## 取消设备任务 removeTask <a name="removeTask" id="removeTask" />

用于取消设备任务

####　参数解析

| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------|
| stop_tasks          | array         | 是          | 要停止的任务 | 
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |

#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    var Simple = SDK.protoRoot;// 播放方式结构的根结构
    dev.removeTask( [{
        'stop_seq' : 140155251,
        'mode' : Simple.SrRemoveTaskMode.SRT_STOP_NOTCANCEL,
    }],function(sequence,BaseResponse,app){
    // BaseResponse 为设备返回的执行结果对象，描述操作成功与否
    // app 为SDK本身
    },function(error,app){
    // error 为调用失败的错误信息
    }); 
```

#### stop_tasks 结构
``` javascript
[{
    'stop_seq' : 140155251, // 
    'mode' : Simple.SrRemoveTaskMode.SRT_STOP_NOTCANCEL,
}]
```

枚举相关信息请查阅[枚举列表](/Enum-list.md)

