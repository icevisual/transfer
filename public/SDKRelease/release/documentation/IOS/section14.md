#检测设备是否存在

|参数       | 类型      | 允许空| 说明 |
|:----      |:------  |:----  |:--- |
|devicesName | NSString | 否    |设备名|
###调用示例
```c
    SJScentking *scentking  = [[SJScentking alloc]init];
    [scentking TestEquipmentWithDevicesName:devicesName success:^(NSURLSessionDataTask *task, id responseObject) {
        NSLog(@"成功");

    // 此为弹出秘钥弹窗
    [scentking SecretKeyAlertWithSelfVC:self];

    } failed:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"失败");
    }];
```

###BaseResponse结构说明
```json
{
    "code": 1,
    "msg": "OK",
    "data": {
        //binded 是1 表示该设备已绑定、是0 表示该设备未绑定
        "binded": 1
    }
}


{
    "code": 9003,
    "msg": "开发者 AccessKey 不能为空。",
    "data": []
}
```
