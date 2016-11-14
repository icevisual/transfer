syntax="proto2"; 
package Proto2.Scentrealm.Simple;

// 简化微信蓝牙协议，使用其包头用头结构，
// struct BpFixHead
// {
//     unsigned char bMagicNumber; 
//     unsigned char bVer; //版本号
//     unsigned short nLength;//包体总长度
//     unsigned short nCmdId;// 命令号 enum SrCmdId
//     unsigned int32 nSeq;// 指令序号（时间戳）
// };
// eg. fe（MagicNumber）01（版本号）00 3b（总长度） 2711（命令号）5805 bb0d（Seq）
// ${DevID}指代设备的唯一ID，会录入数据库
// 设备订阅/${DevID} topic，用于接收指令，
// /${DevID}/resp  作为消息返回 topic
// Response 的 nSeq 与 Request 的 nSeq 相同

// 指令集 ，包头 nCmdId字段
enum SrCmdId
{
	SCI_REQ_SLEEP = 1;      // 主机休眠 REQUEST;
	SCI_RESP_SLEEP = 2;     // 主机休眠 RESPONSE;
	SCI_REQ_WAKEUP = 3;     // 唤醒主机 REQUEST;
	SCI_RESP_WAKEUP = 4;    // 唤醒主机 RESPONSE;
	SCI_REQ_USEDSECONDS = 5;        // 获取瓶子使用总时间（秒） REQUEST;
	SCI_RESP_USEDSECONDS = 6;       // 获取瓶子使用总时间（秒） RESPONSE;
	SCI_REQ_PLAYSMELL = 7;  // 播放气味 REQUEST;
	SCI_RESP_PLAYSMELL = 8; // 播放气味 RESPONSE;
	SCI_REQ_GETDEVATTR = 9; // 获取设置 REQUEST;
	SCI_RESP_GETDEVATTR = 10;       // 获取设置 RESPONSE;
	SCI_REQ_SETDEVATTR = 11;        // 修改设置 REQUEST;
	SCI_RESP_SETDEVATTR = 12;       // 修改设置 RESPONSE;
	SCI_REQ_FEATUREREPORT = 13;     // 设备特性上报，可控组件上报 REQUEST;
	SCI_RESP_FEATUREREPORT = 14;    // 设备特性上报，可控组件上报 RESPONSE;
}
// 返回码
enum SrErrorCode
{
    SEC_SUCCESS = 1;// 任务完成
    SEC_ACCEPT = 2;// 接受任务
    SEC_ERROR = -1; // 出现错误
    SEC_REJECT = -2;// 拒绝任务
}
// 基础返回格式，简单数据的返回使用该结构返回，如获取mac，获取开机时间等
message BaseResponse 
{
    required SrErrorCode code = 1 ; // 返回成功与否
    repeated string data = 2;// 返回数据
}
// 瓶子已用时间
message UsedTime 
{
    required string bottle = 1 ; // 气味瓶子ID
    required int32 time = 2; // 使用时间（秒）
}
// 获取瓶子使用总时间（秒） response结构
message UsedTimeResponse 
{
    required BaseResponse response = 1 ; // 返回成功与否
    repeated UsedTime usedTime = 2;
}
// 时间模式
enum SrTimeMode
{
    STM_RELATIVE = 1; // 相对时间模式，对应几秒后
    STM_ABSOLUTE = 2; // 固定时间点，对应yyyy-MM-dd HH:mm:ss
    STM_DAYTIME = 3; // 今日第几秒
    STM_WEEKDAY = 4; // 周几
    STM_MONTHDAY = 5; // 某月的第几天
    STM_MONTH = 6; // 月份
    STM_YEAR = 7; // 年份
}
// 循环模式
enum SrCycleMode
{
	SCM_CYCLE_NO = 1; //不循环
	SCM_CYCLE_YES = 2; //循环
	SCM_CYCLE_INFINITE = 3; //无限循环
}
// 时间点
message TimePoint 
{
	required SrTimeMode mode = 1;// 播放模式
	required int32 value = 2;// 粗略时间
	required int32 endValue = 3;// 时间范围，结束点，用以描述周一到周五等情况
}
// 一个播放动作，描述如何播放一个气味
message PlayAction {
	required string bottle = 1; // 瓶子ID
    required int32 duration = 2; // 播放时间，单位秒
    optional int32 power = 3; // 播放功率
}
// 播放痕迹，描述如何组合播放动作
message PlayTrace
{
	repeated int32 actionId = 1; // 动作ID，对应PlayAction的数组下标
	required int32 beforeStart = 2; // 几秒后开始
    required SrCycleMode cycleMode = 3; // 循环模式
    optional int32 interval = 4; // 循环间歇时间
    optional int32 cycleTime = 5; // 循环次数
}
// 播放气味请求
message PlayRequest 
{
    required SrCycleMode cycleMode = 1;// 循环模式
    repeated TimePoint startAt = 2;// 开始时间，整个数组组成一个时间，
    optional int32 cycleTime = 3;// 循环次数
    
    repeated PlayAction actions = 4 ; // 播放动作数组
    repeated PlayTrace trace = 5 ; // 播放痕迹
}
// 设备属性类型
enum SrDevAttrType
{
	SDST_DEVICEID = 1; // 设备唯一标识
	SDST_DEVICENAME = 2;// 设备名字
	SDST_DEVICETYPE = 3;// 设备类别
	SDST_MAC = 4; // MAC
	SDST_WIFISSID = 5; // wifi ssid
	SDST_WIFIPWD = 6;// wifi 密码
	SDST_NETCONNECTSTATE = 7;// 网络连接状态
	SDST_BLECONNECTSTATE = 8;// 蓝牙连接状态
	SDST_LOGSTATE = 9;// 日志开启状态
	SDST_DATETIME = 10;// 时间
	SDST_UPTIME = 11;// 设备上次开机时间
	SDST_DOWNTIME = 12;// 设备上次关机时间
}
// 设备属性信息
message DevAttrOption 
{
	required SrDevAttrType attr = 1;
	required string value = 2;
}
// 获取设备属性信息的返回的结果，设置属性的请求体
message DevAttrs 
{
	repeated DevAttrOption attrs = 1;
}
// 获取设备属性请求，如果要所有信息，就讲包体置空
message GetDevAttrsRequest 
{
	repeated SrDevAttrType attrs = 1;
}
// 设备组件类别
enum SrFeatureType
{
	SFT_SWITCH = 1;//开关（数量、分别叫什么、开还是关）
	SFT_SCROLLBAR = 2;// 滚动条（数量、分别叫什么、最大值、步幅）
	SFT_TEXTBOX = 3;//文本框（数量、分别叫什么、默认文本信息）
	SFT_ROTATION = 4;//旋转（数量、分别叫什么）
}
//设备组件属性类别
enum SrFeatureAttrType
{
	SFAT_NAME = 1;// 名字
	SFAT_NUM = 2;// 数量
	SFAT_STATE = 3;// 开关状态
	SFAT_MAX = 4;// 最大值
	SFAT_MIN = 5;// 最小值
	SFAT_STEP = 6;// 步幅
	SFAT_DEFAULT = 7;// 默认值
	SFAT_ANGLE = 8; // 旋转角度
	SFAT_ANGULAR_SPEED = 9;// 旋转角速度
}
// 设备属性
message FeatureAttr 
{
	required SrFeatureAttrType attr = 1;// 组件属性类别
	required string value = 2;// 属性值
}
// 设备组件信息上报
message FeatureReport 
{
	required SrFeatureType type = 1;// 组件类别
	repeated FeatureAttr attrs = 2;// 组件属性
}
//设备组件信息上报Response
message FeatureReportResponse 
{
	repeated FeatureReport feature = 1;// 组件
}
// 设置名称

// 场景，
//  1.电影电视脚本                        长度两小时内，多等待，播放某种气味几秒，重复
//  2.游戏脚本                              触发式，播放某种气味几秒
//  3.安卓|IOS录制的播放脚本      多气味，切换频繁，可能会有小循环，如果作为专门的脚本录入入口，会涉及所有播放模式
//  4.仿真物品气味                        循环长时间播放
//  5.广告牌（午饭时间播放气味） 大循环（天、周、月），小循环（每隔几分钟）
// 			每周1-5 中午 11:30 - 12:30 和 17:30 - 18:30--  ---  --(12s) 循环
// 			