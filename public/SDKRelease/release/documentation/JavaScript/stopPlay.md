
## 停止播放气味 stopPlay <a name="stopPlay" id="stopPlay" />

用于停止播放气味

####　参数解析

| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------|
| bottles          | array         | 是          | 要停止的瓶子 | 
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |

#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    var Simple = SDK.protoRoot;// 播放方式结构的根结构
    dev.stopPlay( [
                   '00000b1',// 瓶子的ID
                   '00000b2',
                   '00000b3',
    ],function(sequence,BaseResponse,app){
    // BaseResponse 为设备返回的执行结果对象，描述操作成功与否
    // app 为SDK本身
    },function(error,app){
    // error 为调用失败的错误信息
    }); 
```
