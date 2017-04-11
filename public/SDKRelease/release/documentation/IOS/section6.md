# 通过蓝牙设置控件属性请求

|参数      | 类型      | 允许空  | 说明 |
|:----    |:------   |:----   |:--- |
|identity | NSString | 否     |控件标识符，指明设置的控件|
|attr     | NSString | 否     |控件属性名，指明要设置的属性|
|value    | NSString | 否     |属性值|
|topic    | NSString | 否     |接收设备的订阅号|


说明: attr 对应的 '开关'属性名为: @"status"、'进度条'属性名为: @"value"、'方向'的属性名为: @"angle"

### 调用示例

#### 蓝牙调用

```c
    SJScentking *scentking = [[SJScentking alloc]init];
    [scentking setControlAttrToBlueWithIdentity:identity Attr:attr value:value];
```

#### 物联网调用

```c
    SJScentking *scentking = [[SJScentking alloc]init];
    [scentking setControlAttrToWiFiWithIdentity:identity Attr:attr value:value toTopic:topic];
```
