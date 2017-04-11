# 物联网连接
|参数       | 类型      | 允许空| 说明 |
|:----      |:------  |:----  |:--- |
|deviceAccess | NSString | 否    |设备的device_access|
|title | NSString | 否    |设备名|

### 示例

    SJScentking *scentking = [[SJScentking alloc]init];
    [scentking connectWiFi:[NSString stringWithFormat:@"/%@",deviceAccess] title:title];
