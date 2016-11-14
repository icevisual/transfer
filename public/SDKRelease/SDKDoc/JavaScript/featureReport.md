
## 获取设备组件信息 featureReport <a name="featureReport" id="featureReport" />

用于获取设备组件信息

####　参数解析

| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------|
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |

#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    var Simple = SDK.protoRoot;// 播放方式结构的根结构
    dev.featureReport(function(FeatureReportResponse,app){
    // FeatureReportResponse 为设备返回的执行结果对象，描述操作成功与否
    // app 为SDK本身
    },function(error,app){
    // error 为调用失败的错误信息
    }); 
```

#### FeatureReportResponse 结构

``` json
{
    'feature' : [//组件列表
        {
            'type' : Simple.SrFeatureType.SFT_SWITCH, //组件类型
            'attrs' : [
                {
                    'attr' : Simple.SrFeatureAttrType.SFAT_NAME,//组件属性
                    'value' : 'a switch'//组件属性值
                }
            ]
        },
        {
            'type' : Simple.SrFeatureType.SFT_SWITCH,
            'attrs' : [
                {
                    'attr' : Simple.SrFeatureAttrType.SFAT_NAME,
                    'value' : 'a switch'
                }
            ]
        }
    ]
}

```
