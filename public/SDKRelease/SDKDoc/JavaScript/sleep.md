
## 设置设备休眠 sleep <a name="sleep" id="sleep" />

用于设置设备休眠

####　参数解析

| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------| 
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |

#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    dev.sleep(function(BaseResponse,app){
    // BaseResponse 为设备返回的执行结果对象，描述操作成功与否
    // app 为SDK本身
    },function(error,app){
    // error 为调用失败的错误信息
    }); 
```
#### BaseResponse结构说明

``` json
{
    'code' : Simple.SrErrorCode.SEC_SUCCESS,// 1任务完成 2接受任务 -1出现错误 -2拒绝 
    'data' : [
        'error msg'
    ]
}
```