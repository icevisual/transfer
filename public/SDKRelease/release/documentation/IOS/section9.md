# 获取搜索到的蓝牙设备信息

### 1.获取搜索到的所有智能设备
##### 示例:

```c
    SJScentking *scentking = [[SJScentking alloc]init];
    NSArray *peripheralArray = [scentking returnAllScanPeripherals];
```

### 2.获取搜索到的所有智能设备名字
##### 示例:

```c
    SJScentking *scentking = [[SJScentking alloc]init];
    NSArray *peripheralNameArray = [scentking returnAllScanPeripheralNames];
```

### 3.获取搜索到的所有智能设备信号
##### 示例:

```c
    SJScentking *scentking = [[SJScentking alloc]init];
    NSArray *peripheralSignalsArray = [scentking returnAllScanPeripheralSignals];
```
