#IOS SDK 使用说明

##### 1.导入第三方库
```c
    pod 'ReactiveCocoa'
    pod 'AFNetworking', '~> 3.1.0'
    pod 'FMDB'
    pod 'MQTTKit', '~> 0.1.0'
    pod 'ProtocolBuffers', '~> 1.9.11'
    pod 'FCUUID', '~> 1.3.1'
```
##### 2.引入 CoreBluetooth.framework 
##### 3.将 libSJScentKingSDK 文件添加到项目中
##### 4.注册蓝牙和Wi-Fi所需要的通知 示例：

```c
    //WIFI设备连接成功通知
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(connectWiFiSucess:) name:ConnectWiFiSucess object:nil];
    //WiFi返回数据通知
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(deliveryObj:) name:WiFiDeliveryDataNotify object:nil];
    //蓝牙返回数据通知
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(deliveryData:) name:BluetoothDeliveryDataNotify object:nil];
    //搜索设备完成通知
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(searchBluetoothDevicesComplete:) name:SearchBluetoothDevicesComplete object:nil];
    //蓝牙连接中通知
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(onStartConnectToBluetooth:) name:OnStartConnectToBluetooth object:nil];
    //蓝牙未打开通知
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(onCallbackBluetoothPowerOff:) name:OnCallbackBluetoothPowerOff object:nil];
    //蓝牙已打开通知
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(bluetoothPowerOn:) name:kBluetoothPowerOnNotify object:nil];
    //蓝牙设备已连接通知
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(onCallbackConnectToBluetoothSuccessfully:) name:OnCallbackConnectToBluetoothSuccessfully object:nil];
    //蓝牙设备连接超时通知
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(onCallbackConnectToBluetoothTimeout:) name:OnCallbackConnectToBluetoothTimeout object:nil];
```

##### 5.实现注册的通知的方法
##### 6.物联网发送指令返回数据可以用物联网返回数据通知进行接受、蓝牙发送指令返回数据可以用物联网返回数据通知进行接受 示例:
```c
    - (void)deliveryObj:(NSNotification *)notify{
        //物联网返回数据
    }
    -(void)deliveryData:(NSNotification *)notify{
        //蓝牙返回数据
    }
```
##### 7.要是使用物联网请去 http://open.qiweiwangguo.com 去注册用户 并登录 然后在开发中心->API接口文档->用户的 密钥管理 将Access Key和 Secret Key 分别复制到项目的SJScentking.plist  中
