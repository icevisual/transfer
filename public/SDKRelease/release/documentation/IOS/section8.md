# 物联网发送字符串指令

|参数       | 类型      | 允许空| 说明 |
|:----      |:------  |:----  |:--- |
|message | NSString | 否    |16进制字符串|
|topic | NSString | 否    |接收设备的订阅号|

### 调用示例
```c
    SJScentking *scentking = [[SJScentking alloc]init];
    [scentking sendWIFIDataWithStr:message Topic:topic];
```
