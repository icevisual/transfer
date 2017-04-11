syntax="proto3";
package com.scentrealm;

// 简化微信蓝牙协议，使用其包头用头结构，
// struct BpFixHead
// {
//     unsigned char bMagicNumber;
//     unsigned char bVer; //版本号
//     unsigned short nLength;//包体总长度
//     unsigned short nCmdId;// 命令号 enum SrCmdId
//     unsigned int32 nSeq;// 指令序号（时间戳 + 随机数）
// };
// eg. fe（MagicNumber）01（版本号）00 3b（总长度） 2711（命令号）5805 bb0d（Seq）
// ${DevID}指代设备的唯一ID，会录入数据库
// 设备订阅/${DevID} topic，用于接收指令，
// /${DevID}/resp  作为消息返回 topic
// Response 的 nSeq 与 Request 的 nSeq 相同

// 指令集 ，包头 nCmdId字段
enum SrCmdId {
    // Just a place holder
    SCI_CMD_NONE = 0;

    // Request CMD
    SCI_REQ_SLEEP = 10001;  // 主机休眠 request;
    SCI_REQ_WAKEUP = 10002; // 唤醒主机 request;
    SCI_REQ_USEDSECONDS = 10003;    // 获取瓶子使用总时间（秒） request;
    SCI_REQ_PLAYSMELL = 10004;      // 播放气味 request;
    SCI_REQ_GETDEVATTR = 10005;     // 获取设置 request;
    SCI_REQ_SETDEVATTR = 10006;     // 修改设置 request;
    SCI_REQ_DEVICE_MODEL = 10007;   // 设备设备模型信息获取 request;
    SCI_REQ_STOP_PLAY = 10008;   // 设备停止播放某个气味 request;
    SCI_REQ_REMOVE_TASK = 10009;   // 移除设备任务 request;
    SCI_REQ_SET_CONTROL_ATTR = 10010;   // 设置控件属性 request;

    SCI_REQ_DEV_WIFI_LIST = 10011;   // 获取设备附近的 wifi 信号列表 request;
    SCI_REQ_SET_DEV_WIFI = 10012;   // 设置设备 wifi 用户名密码 request;
    SCI_REQ_WIFI_CONN_REPORT = 10013;   // 设置设备连接 wifi 结果上报 request;
    SCI_REQ_NET_CONN_REPORT = 10014;   // 设置设备连接网络结果上报 request;

    SCI_REQ_GET_SMELL_LIST = 10015;   // 获取设备的气味 request;
    
    SCI_REQ_STOP_ALL = 10016;   // 设备停止播放所有气味 request;
    SCI_REQ_CURRENT_PLAY = 10017;   // 获取设备当前播放信息 request;

    // Response CMD
    SCI_RESP_SLEEP = 20001; // 主机休眠 response;
    SCI_RESP_WAKEUP = 20002;        // 唤醒主机 response;
    SCI_RESP_USEDSECONDS = 20003;   // 获取瓶子使用总时间（秒） response;
    SCI_RESP_PLAYSMELL = 20004;     // 播放气味 response;
    SCI_RESP_GETDEVATTR = 20005;    // 获取设置 response;
    SCI_RESP_SETDEVATTR = 20006;    // 修改设置 response;
    SCI_RESP_DEVICE_MODEL = 20007;  // 设备设备模型信息获取 response;
    SCI_RESP_STOP_PLAY = 20008;   // 设备停止播放某个气味 response;
    SCI_RESP_REMOVE_TASK = 20009;   // 移除设备任务 response;
    SCI_RESP_SET_CONTROL_ATTR = 20010;   // 设置控件属性 response;

    SCI_RESP_DEV_WIFI_LIST = 20011;   // 设置设备附近的 wifi 信号列表 response;
    SCI_RESP_SET_DEV_WIFI = 20012;   // 设置设备 wifi 用户名密码 response;
    SCI_RESP_WIFI_CONN_REPORT = 20013;   // 设置设备连接 wifi 结果上报 response;
    SCI_RESP_NET_CONN_REPORT = 20014;   // 设置设备连接网络结果上报 response;

    SCI_RESP_GET_SMELL_LIST = 20015;   // 获取设备的气味 response;
    
    SCI_RESP_STOP_ALL = 20016;   // 设备停止播放所有气味 response;
    SCI_RESP_CURRENT_PLAY = 20017;   // 获取设备当前播放信息 response;
    

    // Push Request , No Response
    SCI_PUSH = 30000;
}
// 返回码
enum SrErrorCode {
    SEC_NONE = 0;// 无状态
    SEC_SUCCESS = 10000;// 任务完成
    SEC_ACCEPT = 10001;// 接受任务
    SEC_ERROR = 20000; // 出现错误
    SEC_REJECT = 20001;// 拒绝任务
}
// 基础返回格式，简单数据的返回使用该结构返回，如获取mac，获取开机时间等
message BaseResponse {
    SrErrorCode code = 1 ; // 返回成功与否
    string msg = 2;// 返回数据
}

// 公共请求
message BaseRequest {

}
// 获取瓶子使用总时间（秒） response结构
message UsedTimeResponse {
    // 瓶子已用时间
    message UsedTime {
        string bottle = 1 ; // 气味瓶子ID
        int32 time = 2; // 使用时间（秒）
    }
    BaseResponse response = 1 ; // 返回成功与否
    repeated UsedTime used_time = 2;
}
// 时间模式
enum SrTimeMode {
    STM_NONE = 0;
    STM_RELATIVE = 1; // 相对时间模式，对应几秒后
    STM_ABSOLUTE = 2; // 固定时间点，对应yyyy-MM-dd HH:mm:ss
    STM_DAYTIME = 3; // 今日第几秒
    STM_WEEKDAY = 4; // 周几
    STM_MONTHDAY = 5; // 某月的第几天
    STM_MONTH = 6; // 月份
    STM_YEAR = 7; // 年份
}
// 循环模式
enum SrCycleMode {
    SCM_NONE = 0;
    SCM_CYCLE_NO = 1; //不循环
    SCM_CYCLE_YES = 2; //循环
    SCM_CYCLE_INFINITE = 3; //无限循环
}
// 时间点
message TimePoint {
    SrTimeMode mode = 1;// 播放模式
    int32 value = 2;// 粗略时间
    int32 end_value = 3;// 时间范围，结束点，用以描述周一到周五等情况
}
// 播放气味请求
message PlayRequest {
    // 一个播放动作，描述如何播放一个气味
    message PlayAction {
        string bottle = 1; // 瓶子ID
        int32 duration = 2; // 播放时间，单位秒
        int32 power = 3; // 播放功率
    }
    // 播放痕迹，描述如何组合播放动作
    message PlayTrace {
        repeated int32 action_id = 1; // 动作ID，对应PlayAction的数组下标
        int32 before_start = 2; // 几秒后开始
        SrCycleMode cycle_mode = 3; // 循环模式
        int32 interval = 4; // 循环间歇时间
        int32 cycle_time = 5; // 循环次数
    }

    BaseRequest request = 1 ;

    SrCycleMode cycle_mode = 2;// 循环模式
    repeated TimePoint start_at = 3;// 开始时间，整个数组组成一个时间，
    int32 cycle_time = 4;// 循环次数

    repeated PlayAction actions = 5 ; // 播放动作数组
    repeated PlayTrace trace = 6 ; // 播放痕迹
}

// 设备停止某个瓶子的播放动作
message StopPlayRequest {
    BaseRequest request = 1;
    repeated string bottles = 2;  // 要停止播放的气味瓶子编号
}

// 移除任务的模式
enum SrRemoveTaskMode{
    SRT_NONE = 0;
    SRT_STOP_CANCEL = 1;// 停止本次并取消该任务
    SRT_STOP_NOTCANCEL = 2; // 停止当前在运行的该任务，但是不取消
    SRT_NOTSTOP_CANCEL = 3; // 不停止正在运行的该任务，取消未运行的
}

// 设备取消播放任务（一系列播放动作）
message RemoveTaskRequest {
    BaseRequest request = 1;
    message RemoveTaskOption {
        int32 stop_seq = 1; // 要停止的 PlayRequest 请求 nSeq 序列号
        SrRemoveTaskMode mode = 2; // 模式
    }
    repeated RemoveTaskOption stop_tasks = 2;
}

// 设备属性类型
enum SrDevAttrType {
    SDST_NONE = 0;
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
message DevAttrOption {
    SrDevAttrType attr = 1;
    string value = 2;
}
// 获取设备属性请求，如果要所有信息，就讲包体置空
message GetDevAttrsRequest {
    BaseRequest request = 1 ;
    repeated SrDevAttrType attrs = 2;
}
// 获取设备属性返回
message GetDevAttrsResponse {
    BaseResponse response = 1 ; // 返回成功与否
    repeated DevAttrOption attrs = 2;
}
// 设置设备属性请求体
message SetDevAttrsRequest {
    BaseRequest request = 1 ;
    repeated DevAttrOption attrs = 2;
}

// 属性名之前加入下划线“_”来表示该属性是只读的
// 设备模型上报 response
message DeviceModelResponse {
    BaseResponse response = 1;
    repeated SrCmdId support_cmds = 2; // 设备支持的指令集合
    repeated AppControlSet controls = 3;// 设备的控件集合
}
// 控件集合
message AppControlSet {
    int32 identity = 1;// 控件唯一标识符
    string name = 2;// 控件名称
    oneof controller {
        AppControlSwitch switchs = 11; // 开关
        AppControlSlider slider = 12; // 滑动条
        AppControlSpin spin = 13; // 旋转器
        AppControlSwitchSlider switch_slider = 14; // 滑动条 + 开关
    }
}

// 开关状态
enum SrSwitchStatus {
    SSS_NONE = 0;// 无
    SSS_ON = 1;// 开
    SSS_OFF = 2;// 关
}

// 开关
message AppControlSwitch {
    SrSwitchStatus status = 1;// 开关状态 true 开，false 关
}
// 滑动条
message AppControlSlider {
    int32 _min = 1;  // 最小值
    int32 _max = 2;  // 最大值
    int32 _step = 3; // 步幅
    int32 value = 4;// 当前滚动值
}
// 旋转器
message AppControlSpin {
    int32 _step = 1; // 旋转最小角度
    int32 angle = 2;// 当前角度
    int32 angular_speed = 3; // 角速度 
}
// 滑动条 + 开关
message AppControlSwitchSlider {
    SrSwitchStatus status = 1;// 开关状态 true 开，false 关
    int32 _min = 2;  // 最小值
    int32 _max = 3;  // 最大值
    int32 _step = 4; // 步幅
    int32 value = 5;// 当前滚动值
}
// 设置控件属性请求，将控件的值描述为控件的一个可修改属性，
// 通过控制控件属性来达到控制控件的效果，也可以用于设置控件的名字
message SetControlAttrRequest {
    // 设置控件属性信息
    message SetControlAttr {
        int32 identity = 1; // 控件标识符，指明设置的控件
        string attr = 2;  // 控件属性名，指明要设置的属性
        string value = 3; // 属性值
    }
    BaseRequest request = 1;
    repeated SetControlAttr settings = 2; // 控制控件内容
}

// 获取设备周边 wifi 信号
message DevWifiListResponse {
    BaseResponse response = 1;
    repeated string wifissid_list = 2;
}
// 设置 wifi 用户名密码，
message SetDevWifiRequest {
    string wifissid = 1;
    string wifipwd = 2;
}

// 获取设备中的气味列表
message SmellListResponse {
    BaseResponse response = 1 ; // 返回成功与否
    repeated string smell_list = 2;
}


// 获取设备当前播放信息 response , bottle 置空表示当前没有播放任务
message CurrentPlayRespones {
    BaseResponse response = 1 ; // 返回成功与否
    string bottle = 2; // 瓶子ID
    int32 remain_second  = 3; // 剩余播放时间，单位秒
    int32 total_second = 4; // 需播放总时间，单位秒
}



