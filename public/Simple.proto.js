syntax="proto2"; 
package Proto2.Scentrealm.Simple;

// 简化微信蓝牙协议，使用其包头用头结构，
// struct BpFixHead
// {
//     unsigned char bMagicNumber; 
//     unsigned char bVer; //版本号
//     unsigned short nLength;//包体总长度
//     unsigned short nCmdId;// 命令号 enum SrCmdId
//     unsigned short nSeq;// 指令序号
// };
// eg. fe（MagicNumber）01（版本号）00 3b（总长度） 2711（命令号）0001（Seq）
// ${DevID}指代设备的唯一ID，会录入数据库
// 设备订阅/${DevID} topic，用于接收指令，
// /${DevID}/resp  作为消息返回 topic

// 指令集 ，包头 nCmdId字段
enum SrCmdId
{
	SCI_req_mac = 1;        // 获取设备MAC地址 request;
	SCI_resp_mac = 2;       // 获取设备MAC地址 response;
	SCI_req_uptime = 3;     // 获取设备开机时间 request;
	SCI_resp_uptime = 4;    // 获取设备开机时间 response;
	SCI_req_downtime = 5;   // 获取上次关机时间 request;
	SCI_resp_downtime = 6;  // 获取上次关机时间 response;
	SCI_req_sleep = 7;      // 主机休眠 request;
	SCI_resp_sleep = 8;     // 主机休眠 response;
	SCI_req_wakeup = 9;     // 唤醒主机 request;
	SCI_resp_wakeup = 10;   // 唤醒主机 response;
	SCI_req_usedSeconds = 11;       // 获取瓶子使用总时间（秒） request;
	SCI_resp_usedSeconds = 12;      // 获取瓶子使用总时间（秒） response;
	SCI_req_enableSmell = 13;       // 开启某个气味 request;
	SCI_resp_enableSmell = 14;      // 开启某个气味 response;
	SCI_req_setPower = 15;  // 设置播放功率 request;
	SCI_resp_setPower = 16; // 设置播放功率 response;
}
// 返回码
enum SrErrorCode
{
    SEC_success = 0;// 成功
    SEC_error = -1; // 错误
}
// 基础返回格式，简单数据的返回使用该结构返回，如获取mac，获取开机时间等
message BaseResponse {
    required SrErrorCode code = 1 ; // 返回成功与否
    repeated string data = 2;// 返回数据
}
// 瓶子已用时间
message UsedTime {
    required string bottle = 1 ; // 气味瓶子ID
    repeated int32 time = 2; // 使用时间（秒）
}
// 获取瓶子使用总时间（秒） response结构
message UsedTimeResponse {
    required BaseResponse response = 1 ; // 返回成功与否
    repeated UsedTime usedTime = 2;
}
// 功率指令
message SetPower {
    required string bottle = 1 ; // 气味瓶子ID
    required int32 time = 2; // 时间
    required int32 power = 3; // 功率
}
// 设置播放功率
message SetPowerRequest {
    required SetPower setting = 1 ; // 功率指令
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
// 时间点
message TimePoint {
	required SrTimeMode mode = 1;// 播放模式
	required int32 value = 2;// 粗略时间
	optional int32 endValue = 3;// 时间范围，结束点，用以描述周一到周五等情况
}
// 开始时间
message PlayStartTime {
	required SrTimeMode mode = 1;// 播放模式
	repeated TimePoint startAt = 2;// 开始时间，整个数组组成一个时间，
    required bytes circulation = 3;// 是否循环,00不循环,01循环，10无限循环
    optional int32 cycleTime = 4;// 循环次数
}
// 一个播放动作
message PlayAction {
	required string bottle = 1; // 瓶子ID
    required int32 beforeStart = 2; // 几秒后开始
    required int32 duration = 3; // 播放时间，单位秒
    required bytes circulation = 4; // 是否循环,00不循环,01循环，10无限循环
    optional int32 interval = 5; // 循环间歇时间 ，-- --
    optional int32 cycleTime = 6; // 循环次数
}
// 播放气味
message PlaySmell {
    required PlayStartTime when = 1 ;// 开始播放气味的时间
    repeated PlayAction play = 2 ; // 如何播放气味
}

// 场景，
//  1.电影电视脚本                        长度两小时内，多等待，播放某种气味几秒，重复
//  2.游戏脚本                              触发式，播放某种气味几秒
//  3.安卓|IOS录制的播放脚本      多气味，切换频繁，可能会有小循环，如果作为专门的脚本录入入口，会涉及所有播放模式
//  4.仿真物品气味                        循环长时间播放
//  5.广告牌（午饭时间播放气味） 大循环（天、周、月），小循环（每隔几分钟）

