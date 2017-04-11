
## 获取设备属性 getDevAttr <a name="getDevAttr" id="getDevAttr" />

用于获取设备属性

####　参数解析

| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------|
| attrs          | array         | 是          | 要获取的属性，为空表示获取所有属性 | 
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |

#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    var Simple = SDK.protoRoot;// 播放方式结构的根结构
    dev.getDevAttr([
        Simple.SrDevAttrType.SDST_DEVICEID,
        Simple.SrDevAttrType.SDST_DEVICENAME,
    ],function(sequence,DevAttrs,app){
    // DevAttrs 为设备返回的执行结果对象，描述操作成功与否
    // app 为SDK本身
    },function(error,app){
    // error 为调用失败的错误信息
    }); 
```

#### attrs 结构

``` json
[
    Simple.SrDevAttrType.SDST_DEVICEID,
    Simple.SrDevAttrType.SDST_DEVICENAME,
]
```
或者
``` json
[
    'deviceid',
    'devicename',
]
```

#### DevAttrs 结构

``` json
{
    attrs : [
        {
            'attr' : SrDevAttrType.SDST_DEVICEID,// 设备类型枚举
            'value' : 'TCeOp0gzzrWhAMoOa3Mm'
        },
        {
            'attr' : SrDevAttrType.SDST_DEVICENAME,
            'value' : '测试设备0001'
        },
    ]
}
```

枚举相关信息请查阅[枚举列表](/Enum-list.md)
