
## 唤醒设备 wakeup <a name="wakeup" id="wakeup" />

用于唤醒设备

####　参数解析

| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------| 
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |

#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    dev.wakeup(function(sequence,BaseResponse,app){
    // BaseResponse 为设备返回的执行结果对象，描述操作成功与否
    // app 为SDK本身
    },function(error,app){
    // error 为调用失败的错误信息
    }); 
```