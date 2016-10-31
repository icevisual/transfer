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

// 指令集 ，包头 nCmdId字段
enum SrCmdId
{
	SCI_req_sleep = 1;      // 主机休眠 request;
	SCI_resp_sleep = 2;     // 主机休眠 response;
	SCI_req_wakeup = 3;     // 唤醒主机 request;
	SCI_resp_wakeup = 4;    // 唤醒主机 response;
	SCI_req_usedSeconds = 5;        // 获取瓶子使用总时间（秒） request;
	SCI_resp_usedSeconds = 6;       // 获取瓶子使用总时间（秒） response;
	SCI_req_playSmell = 7;  // 播放气味 request;
	SCI_resp_playSmell = 8; // 播放气味 response;
	SCI_req_getDevAttr = 9; // 获取设置 request;
	SCI_resp_getDevAttr = 10;       // 获取设置 response;
	SCI_req_setDevAttr = 11;        // 修改设置 request;
	SCI_resp_setDevAttr = 12;       // 修改设置 response;
	SCI_req_featureReport = 13;     // 设备特性上报，可控组件上报 request;
	SCI_resp_featureReport = 14;    // 设备特性上报，可控组件上报 response;

}
// 返回码
enum SrErrorCode
{
    SEC_success = 1;// 任务完成
    SEC_accept = 2;// 接受任务
    SEC_error = -1; // 出现错误
    SEC_reject = -2;// 拒绝任务
}
// 基础返回格式，简单数据的返回使用该结构返回，如获取mac，获取开机时间等
message BaseResponse {
    required SrErrorCode code = 1 ; // 返回成功与否
    repeated string data = 2;// 返回数据
}
// 瓶子已用时间
message UsedTime {
    required string bottle = 1 ; // 气味瓶子ID
    required int32 time = 2; // 使用时间（秒）
}
// 获取瓶子使用总时间（秒） response结构
message UsedTimeResponse {
    required BaseResponse response = 1 ; // 返回成功与否
    repeated UsedTime usedTime = 2;
}
// 播放气味请求
message PlayRequest {
    required string cmdSeq = 3 ; // 指令序列号
    required PlaySmell play = 4; // 指令内容
}
// 时间模式
enum SrTimeMode
{
    STM_relative = 1; // 相对时间模式，对应几秒后
    STM_absolute = 2; // 固定时间点，对应yyyy-MM-dd HH:mm:ss
    STM_daytime = 3; // 今日第几秒
    STM_weekday = 4; // 周几
    STM_monthday = 5; // 某月的第几天
    STM_month = 6; // 月份
    STM_year = 7; // 年份
}
// 循环模式
enum SrCycleMode
{
	SCM_cycle_no = 1; //不循环
	SCM_cycle_yes = 2; //循环
	SCM_cycle_infinite = 3; //无限循环
}
// 时间点
message TimePoint {
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
message PlayTrace{
	repeated int32 actionId = 1; // 动作ID，对应PlayAction的数组下标
	required int32 beforeStart = 2; // 几秒后开始
    required SrCycleMode cycleMode = 3; // 循环模式
    optional int32 interval = 4; // 循环间歇时间
    optional int32 cycleTime = 5; // 循环次数
}
// 播放气味
message PlaySmell {
    required SrCycleMode cycleMode = 1;// 循环模式
    repeated TimePoint startAt = 2;// 开始时间，整个数组组成一个时间，
    optional int32 cycleTime = 3;// 循环次数
    
    repeated PlayAction actions = 4 ; // 播放动作数组
    repeated PlayTrace trace = 5 ; // 播放痕迹
}

// 设备属性类型
enum SrDevAttrType
{
	SDST_deviceID = 1; // 设备唯一标识
	SDST_deviceName = 2;// 设备名字
	SDST_deviceType = 3;// 设备类别
	SDST_mac = 4; // MAC
	SDST_wifiSsid = 5; // wifi ssid
	SDST_wifiPwd = 6;// wifi 密码
	SDST_netConnectState = 7;// 网络连接状态
	SDST_bleConnectState = 8;// 蓝牙连接状态
	SDST_logState = 9;// 日志开启状态
	SDST_datetime = 10;// 时间
	SDST_uptime = 11;// 设备上次开机时间
	SDST_downtime = 12;// 设备上次关机时间
}

// 设备属性信息
message DevAttrOption {
	required SrDevAttrType attr = 1;
	required string value = 2;
}
// 获取设备属性信息的返回的结果，设置属性的请求体
message DevAttrs {
	repeated DevAttrOption attrs = 1;
}

// 获取设备属性请求，如果要所有信息，就讲包体置空
message GetDevAttrsRequest {
	repeated SrDevAttrType attrs = 1;
}

// 设备组件类别
enum SrFeatureType
{
	SFT_switch = 1;//开关（数量、分别叫什么、开还是关）
	SFT_scrollBar = 2;// 滚动条（数量、分别叫什么、最大值、步幅）
	SFT_textbox = 3;//文本框（数量、分别叫什么、默认文本信息）
	SFT_rotation = 4;//旋转（数量、分别叫什么）
}
//设备组件属性类别
enum SrFeatureAttrType
{
	SFAT_name = 1;// 名字
	SFAT_num = 2;// 数量
	SFAT_state = 3;// 开关状态
	SFAT_max = 4;// 最大值
	SFAT_min = 5;// 最小值
	SFAT_step = 6;// 步幅
	SFAT_default = 7;// 默认值
	SFAT_angle = 8; // 旋转角度
	SFAT_angular_speed = 9;// 旋转角速度
}
// 设备属性
message FeatureAttr {
	required SrFeatureAttrType attr = 1;// 组件属性类别
	required string value = 2;// 属性值
}

// 设备组件信息上报
message FeatureReport {
	required SrFeatureType type = 1;// 组件类别
	repeated FeatureAttr attrs = 2;// 组件属性
}

//设备组件信息上报Response
message FeatureReportResponse {
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

