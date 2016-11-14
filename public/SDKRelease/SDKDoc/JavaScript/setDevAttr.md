
## 设置设备属性 setDevAttr <a name="setDevAttr" id="setDevAttr" />

用于设置设备属性

####　参数解析

| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------|
| attrSettings   | object        | 是          | 要设置的属性 | 
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |

#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    var Simple = SDK.protoRoot;// 播放方式结构的根结构
    dev.setDevAttr({
        'devicename' : 'test-device-name'
    },function(BaseResponse,app){
    // BaseResponse 为设备返回的执行结果对象，描述操作成功与否
    // app 为SDK本身
    },function(error,app){
    // error 为调用失败的错误信息
    }); 
```

#### attrSettings 结构

``` json
{
    'devicename' : 'test-device-name', // 属性名 和 属性值
}
```
枚举相关信息请查阅[枚举列表](/Enum-list.md)