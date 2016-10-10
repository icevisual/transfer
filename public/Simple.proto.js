syntax proto2;
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
    SCI_req_mac = 1;        //获取设备MAC地址 request;
    SCI_resp_mac = 2;       //获取设备MAC地址 response;
    SCI_req_uptime = 3;     //获取设备开机时间 request;
    SCI_resp_uptime = 4;    //获取设备开机时间 response;
    SCI_req_downtime = 5;   //获取上次关机时间 request;
    SCI_resp_downtime = 6;  //获取上次关机时间 response;
    SCI_req_sleep = 7;      //主机休眠 request;
    SCI_resp_sleep = 8;     //主机休眠 response;
    SCI_req_wakeup = 9;     //唤醒主机 request;
    SCI_resp_wakeup = 10;   //唤醒主机 response;
    SCI_req_usedSeconds = 11;       //获取瓶子使用总时间（秒） request;
    SCI_resp_usedSeconds = 12;      //获取瓶子使用总时间（秒） response;
    SCI_req_enableSmell = 13;       //开启某个气味 request;
    SCI_resp_enableSmell = 14;      //开启某个气味 response;
    SCI_req_playSmell = 15; //播放气味 request;
    SCI_resp_playSmell = 16;        //播放气味 response;
    SCI_req_setPower = 17;  //设置播放功率 request;
    SCI_resp_setPower = 18; //设置播放功率 response;
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
    required string bid = 1 ; // 气味瓶子ID
    repeated int32 time = 2; // 使用时间（秒）
}
// 获取瓶子使用总时间（秒） response结构
message UsedTimeResponse {
    required BaseResponse response = 1 ; // 返回成功与否
    repeated UsedTime usedTime = 2;
}
// 功率指令
message SetPower {
    required string bid = 1 ; // 气味瓶子ID
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
// 气味播放模式
enum SrPlayMode
{
    SOM_relative = 1; // 相对时间模式
    SOM_absolute = 2 ; // 绝对时间模式
}
// ====================================================
// 播放气味
message PlaySmell {
    required SrPlayMode mode = 1 [default = SOM_relative];// 播放模式
    required bytes smell = 2;// 播放的气味
    required int32 start = 3 ;//相对模式，表示多少秒后开始； 绝对模式，表示开始的时间戳
    optional int32 duration = 4;//播放时间，单位秒
    optional string end = 5;// 
    required bytes circulation = 6;// 是否循环,00不循环,01循环，10无限循环
    optional int32 cycleTime= 7;// 循环次数
}
message Who {
    required string bid = 1 ;// 气味ID，（瓶子ID）
}
message When {
    required int32 start = 3 ;//相对模式，表示多少秒后开始； 绝对模式，表示开始的时间戳
    optional string end = 5;// 
    required bytes circulation = 6;// 是否循环,00不循环,01循环，10无限循环
    optional int32 cycleTime= 7;// 循环次数
}
message How {
	required string bid = 1 ;// 气味ID，（瓶子ID）
    required int32 startTime = 2 ; // 开始时间，星期
    required int32 interval = 3 ;// 间歇时间 ，-- --
    
    required int32 duration = 4;//播放时间，单位秒
    required bytes circulation = 6;// 是否循环,00不循环,01循环，10无限循环
    optional int32 cycleTime = 7;// 循环次数
}
// 播放气味
message Play {
    required When when = 2 ;// 开始播放气味的时间
    required How how = 3 ; // 如何播放气味
}
//9 绝对时间播放气味00代表绝对时间后面跟具体日期时间 N代表第几组，最多10组，时间按顺序发送
//F600(绝对时间2个字节)+160616152549(时间6个字节)+气味4个字节(取最低位)+播放气味时间(2个字节,单位秒)+N(第几条命令)+55   //16个字节
//F6001606171400010000000100050155
message PlayAbsolute { * N
    required int32 time = 1 ;
    required string bid = 2 ;// 播放气味的时间
    required int32 duration = 3 ; // 时长
}
//10 相对时间播放气味  
//F501(2个字节)+气味(四个字节)+持续秒(2个字节)+55 
//
message PlayRelative {
    required int32 time = 1 ;
    required string bid = 2 ;// 播放气味的时间
    required int32 duration = 3 ; // 如何播放气味
}
//11.循环播放
//F401(2个字节)+卡号(4个字节）+开始时间（2个字节）+间歇时间(2个字节)+N(循环次数)+55
//F4010000000100050005050355
//
message PlayCycle {
    required string bid = 1 ;// 气味
    required int32 startTime = 2 ; // 开始时间
    required int32 sleepTime = 3 ;// 间歇时间
    required int32 duration = 4 ; // 如何播放气味
    required int32 circulation = 6 ; // 循环次数
}
//12.循环播放
//F301(2个字节)+卡号(4个字节)+星期几(1个字节）+时间(2个字节)+播放时间2个字节
//F301(2个字节)+卡号(4个字节)+星期几(1个字节）+时间(3个字节)+播放时间2个字节+INDEX(1个字节)+0x55
message PlayCycle1 {
    required string bid = 1 ;// 气味
    required int32 startTime = 2 ; // 开始时间，星期
    required int32 sleepTime = 3 ;// 间歇时间
    required int32 duration = 4 ; // 如何播放气味
    required int32 circulation = 6 ; // 循环次数
}

// 场景，
//  1.电影电视脚本                        长度两小时内，多等待，播放某种气味几秒，重复
//  2.游戏脚本                              触发式，播放某种气味几秒
//  3.安卓|IOS录制的播放脚本      多气味，切换频繁，可能会有小循环，如果作为专门的脚本录入入口，会涉及所有播放模式
//  4.仿真物品气味                        循环长时间播放
//  5.广告牌（午饭时间播放气味） 大循环（天、周、月），小循环（每隔几分钟）

// 相对，什么时候开始，怎么播放（播几秒，循环）
// 考虑多个动作组合整个过程
// 播放的场景
//    不循环
//        过几秒，播放几秒
//        具体时间，播放几秒
//    循环，参考crontab
//        次数循环
//        时间循环
//            每周循环，每月


