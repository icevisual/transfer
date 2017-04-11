
## 获取设备上的气味 smellList <a name="smellList" id="smellList" />

用于获取瓶子使用时间

####　参数解析

| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------| 
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |

#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    dev.smellList(function(sequence,SmellListResponse,app){
    // SmellListResponse 为设备返回的包含瓶子使用信息的对象
    // app 为SDK本身
    },function(error,app){
    // error 为调用失败的错误信息
    }); 
```

#### SmellListResponse 结构说明

``` json
{
    'response' : {// 成功失败
        'code' : 1,
        'msg' : 'error msg'
    },
    'smell_list' : [// 气味列表
        '000000a1',
        '000000a2',
        '000000a3',
        '000000a4',
    ]
}
```
