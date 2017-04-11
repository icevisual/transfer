
## 设置控件属性 setControlAttr <a name="setControlAttr" id="setControlAttr" />

用于设置控件属性

####　参数解析

| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------|
| settings          | array         | 是          | 要设置的属性 | 
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |

#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    var Simple = SDK.protoRoot;// 播放方式结构的根结构
    dev.setControlAttr( [{
        'identity' : 12, // 控件 ID
        'attr' : 'value', // 控件属性名称
        'value' : '12' // 设置的值
    }],function(sequence,BaseResponse,app){
    // BaseResponse 为设备返回的执行结果对象，描述操作成功与否
    // app 为SDK本身
    },function(error,app){
    // error 为调用失败的错误信息
    }); 
```

#### settings 结构
``` javascript
[{
    'identity' : 12, // 控件 ID
    'attr' : 'value', // 控件属性名称
    'value' : '12' // 设置的值
}]
```


