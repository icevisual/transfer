# 添加设备

|参数       | 类型      | 允许空| 说明 |
|:----      |:------  |:----  |:--- |
|devicesName | NSString | 否    |设备名|
|secretKey | NSString | 否    |设备secretKey|

### 调用示例
```c
    SJScentking *scentking = [[SJScentking alloc]init];
    [scentking addSevicesGetDataDevicesName:devicesName SecretKey:secretKey success:^(NSURLSessionDataTask *task, id responseObject) {
        NSLog(@"成功");
    } failed:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"失败");
    }];
```

### BaseResponse结构说明
```json
    {
        "code": 1,
        "msg": "OK",
        "data": {
            "access_key": "9c32626fc323"
        }
    }

    {
        "code": 9003,
        "msg": "设备密码错误",
        "data": []
    }

    {
    "code": 9003,
    "msg": "开发者 AccessKey 不能为空。"
    }
```
