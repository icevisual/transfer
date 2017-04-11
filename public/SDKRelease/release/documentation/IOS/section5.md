# 通过蓝牙配置设备Wi-Fi

### 调用示例
|参数       | 类型      | 允许空| 说明 |
|:----      |:------  |:----  |:--- |
|SSID | NSString | 否    |wifi SSID|
|pwd | NSString | 否     |wifi密码|

``` c
    SJScentking *scentking = [[SJScentking alloc]init];
    [scentking setDevWifiWithName:SSID Pwd:pwd];
```
