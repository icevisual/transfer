# 解绑设备

### 调用示例

|参数       | 类型      | 允许空| 说明 |
|:----      |:------  |:----  |:--- |
|accessKey | NSString | 否    |设备的 AccessKey|

``` c
    SJScentking *scentking  = [[SJScentking alloc]init];
    [_scentking removeAccessKey:accessKey success:^(NSURLSessionDataTask *task, id responseObject) {
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
    "data": []
    }

    {
    "code": 9003,
    "msg": "开发者密码错误",
    "data": []
    }

    {
    "code": 9005,
    "msg": "尚未绑定",
    "data": []
    }

    {
    "code": 9003,
    "msg": "设备 AccessKey 不能为空。",
    "data": []
    }
```

