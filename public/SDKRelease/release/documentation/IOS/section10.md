# 获取设备模型信息

## 物联网获取
|参数       | 类型      | 允许空| 说明 |
|:----      |:------  |:----  |:--- |
|topic      | NSString| 否     |设备的订阅号,即设备的/(device_access)|

### 示例

    SJScentking *scentking = [[SJScentking alloc]init];
    [scentking getDeviceModelInformationToWiFiWithDeviceTopic:topic];


## 蓝牙获取

### 示例

    SJScentking *scentking = [[SJScentking alloc]init];
    [scentking getDeviceModelInformationToBlue];

### BaseResponse结构说明
``` json
{
     name = wifiDeliverryDataNotify;
     userInfo = {
        typeIndex = 20007;
        userInfo =     {
            code = 10000;
            data =         (
            {
                identity = 1;
                name = AppControlSwitch;
                switch =                 {
                    status = 2;
                };
            },
            {
                identity = 2;
                name = AppControlSlider;
                slider =                 {
                    max = 10;
                    min = 1;
                    step = 2;
                    value = 2;
                };
            },
            {
                identity = 3;
                name = AppControlSpin;
                spin =                 {
                    angle = 22;
                    angularSpeed = 1;
                    step = 1;
                };
            },
            {
                identity = 4;
                name = AppControlSwitchSlider;
                "switch_slider" =                 {
                    max = 10;
                    min = 1;
                    status = 1;
                    step = 2;
                    value = 2;
                };
            });
        msg = "SCI_REQ_DEVICE_MODEL";
        };
    }
}
```
