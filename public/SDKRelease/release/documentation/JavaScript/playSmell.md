
## 设备播放气味 playSmell <a name="playSmell" id="playSmell" />

用于控制设备播放气味

####　参数解析


| 参数           | 类型          | 允许空      | 说明         |
|:-------------- |:--------------|:------------|:-------------| 
| how            | json          | 否          | 播放动作     |
| onSuccess      | function      | 是          | 成功回调函数 |
| onError        | function      | 是          | 失败回调函数 |



#### 调用示例

``` javascript
    var dev = SDK.usingDevice('TCeOp0gzzrWhAMoOa3Mm');// 申请占用设备
    var Simple = SDK.protoRoot;// 播放方式结构的根结构
    dev.playSmell({
        'cycleMode' : Simple.SrCycleMode.SCM_CYCLE_YES,// 循环模式
        'startAt' : [ { //播放开始时间，指明不同时间模式的值来确定一个播放时间
            'mode' : Simple.SrTimeMode.STM_WEEKDAY, // 时间模式
            'value' : 1, // 起始值
            'endValue' : 5 // 结束值
        }, {
            'mode' : Simple.SrTimeMode.STM_DAYTIME,
            'value' : 41400,
            'endValue' : 45000
        }, {
            'mode' : Simple.SrTimeMode.STM_DAYTIME,
            'value' : 63000,
            'endValue' : 66600
        } ],
        'cycleTime' : 0, // 循环次数
        'actions' : [ {// 动作列表 ，播放的原子工作
            'bottle' : '0000000001',//气味瓶子，为空字符串表示不播放气味
            'duration' : 2,//播放时间
            'power' : 5//播放功率
        }, {
            'bottle' : '0000000002',
            'duration' : 3,
            'power' : 7
        }, {
            'bottle' : '0000000003',
            'duration' : 2,
            'power' : 5
        }, {
            'bottle' : '0000000004',
            'duration' : 2,
            'power' : 0
        } ],
        'trace' : [ {//播放轨迹
            'actionId' : [ 0, 3, 1, 4, 2 ], // 播放动作顺序，数字为工作列表中动作的下标
            'beforeStart' : 0,// 开始前的等待时间（秒）
            'cycleMode' : Simple.SrCycleMode.SCM_CYCLE_YES,//循环模式
            'interval' : 0,// 循环间隔
            'cycleTime' : 278//循环次数
        } ],
    },function(sequence,decode, app){
        console.log(decode);
    },function(error, app){
        console.log(error);
    });
```

#### 播放方式how结构

``` json
{
    'cycleMode' : Simple.SrCycleMode.SCM_CYCLE_YES,// 循环模式
    'startAt' : [ { //播放开始时间，指明不同时间模式的值来确定一个播放时间
        'mode' : Simple.SrTimeMode.STM_WEEKDAY, // 时间模式
        'value' : 1, // 起始值
        'endValue' : 5 // 结束值
    }, {
        'mode' : Simple.SrTimeMode.STM_DAYTIME,
        'value' : 41400,
        'endValue' : 45000
    }, {
        'mode' : Simple.SrTimeMode.STM_DAYTIME,
        'value' : 63000,
        'endValue' : 66600
    } ],
    'cycleTime' : 0, // 循环次数
    'actions' : [ {// 动作列表 ，播放的原子工作
        'bottle' : '0000000001',//气味瓶子，为空字符串表示不播放气味
        'duration' : 2,//播放时间
        'power' : 5//播放功率
    }, {
        'bottle' : '0000000002',
        'duration' : 3,
        'power' : 7
    }, {
        'bottle' : '0000000003',
        'duration' : 2,
        'power' : 5
    }, {
        'bottle' : '0000000004',
        'duration' : 2,
        'power' : 0
    } ],
    'trace' : [ {//播放轨迹
        'actionId' : [ 0, 3, 1, 4, 2 ], // 播放动作顺序，数字为工作列表中动作的下标
        'beforeStart' : 0,// 开始前的等待时间（秒）
        'cycleMode' : Simple.SrCycleMode.SCM_CYCLE_YES,//循环模式
        'interval' : 0,// 循环间隔
        'cycleTime' : 278//循环次数
    } ],
}
```
枚举相关信息请查阅[枚举列表](/Enum-list.md)