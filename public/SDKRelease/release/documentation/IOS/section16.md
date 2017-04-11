# 请求已绑定设备


### 调用示例
``` c
    SJScentking *scentking = [[SJScentking alloc]init];
    [scentking listBindedDevicesSuccess:^(NSURLSessionDataTask *task, id responseObject) {
        NSLog(@"成功");
    } failed:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"失败");
    }];
```
### BaseResponse结构说明
``` json
    {
        "code": 1,
        "msg": "OK",
        "data": [
            {
            "device_name": "5-vr",
            "device_access": "9c32626fc323",
            "online": 1  //已绑定 1 未绑定 0 
            }
        ]
    }

    {
        "code": 9009,
        "msg": "未找到开发者"
    }
```
