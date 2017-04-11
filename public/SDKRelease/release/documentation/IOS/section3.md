# 蓝牙连接及状态

|参数       | 类型        | 允许空 | 说明               |
|:----     |:------     |:----  |:---                |
|peripheral|CBPeripheral| 否     |蓝牙设备 CBPeripheral|
|title     | NSString   | 否     |蓝牙设备 名称|
|num       | NSNumber   | 是     |蓝牙设备信号强度 RSIS  |

### 示例
```c
    //蓝牙连接
    SJScentking *scentking = [[SJScentking alloc]init];
    [scentking connectBlue:peripheral title:title RSIS:num];

    //蓝牙连接状态
    SJScentking *scentking = [[SJScentking alloc]init];
    BOOL isConnected = [scentking isConnected];
    if(isConnected == YES){
        //是连接
    }else{
        //未连接
    }
```
