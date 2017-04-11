define(['Utils'],function(Utils) {
	var simpleRoot = Utils.loadProtoString('syntax="proto3";package com.scentrealm;enum SrCmdId {SCI_CMD_NONE = 0;SCI_REQ_SLEEP = 10001;SCI_REQ_WAKEUP = 10002;SCI_REQ_USEDSECONDS = 10003;SCI_REQ_PLAYSMELL = 10004;SCI_REQ_GETDEVATTR = 10005;SCI_REQ_SETDEVATTR = 10006;SCI_REQ_DEVICE_MODEL = 10007;SCI_REQ_STOP_PLAY = 10008;SCI_REQ_REMOVE_TASK = 10009;SCI_REQ_SET_CONTROL_ATTR = 10010;SCI_REQ_DEV_WIFI_LIST = 10011;SCI_REQ_SET_DEV_WIFI = 10012;SCI_REQ_WIFI_CONN_REPORT = 10013;SCI_REQ_NET_CONN_REPORT = 10014;SCI_REQ_GET_SMELL_LIST = 10015;SCI_REQ_STOP_ALL = 10016;SCI_REQ_CURRENT_PLAY = 10017;SCI_RESP_SLEEP = 20001;SCI_RESP_WAKEUP = 20002;SCI_RESP_USEDSECONDS = 20003;SCI_RESP_PLAYSMELL = 20004;SCI_RESP_GETDEVATTR = 20005;SCI_RESP_SETDEVATTR = 20006;SCI_RESP_DEVICE_MODEL = 20007;SCI_RESP_STOP_PLAY = 20008;SCI_RESP_REMOVE_TASK = 20009;SCI_RESP_SET_CONTROL_ATTR = 20010;SCI_RESP_DEV_WIFI_LIST = 20011;SCI_RESP_SET_DEV_WIFI = 20012;SCI_RESP_WIFI_CONN_REPORT = 20013;SCI_RESP_NET_CONN_REPORT = 20014;SCI_RESP_GET_SMELL_LIST = 20015;SCI_RESP_STOP_ALL = 20016;SCI_RESP_CURRENT_PLAY = 20017;SCI_PUSH = 30000;}enum SrErrorCode {SEC_NONE = 0;SEC_SUCCESS = 10000;SEC_ACCEPT = 10001;SEC_ERROR = 20000;SEC_REJECT = 20001;}message BaseResponse {SrErrorCode code = 1 ;string msg = 2;}message BaseRequest {}message UsedTimeResponse {message UsedTime {string bottle = 1 ;int32 time = 2;}BaseResponse response = 1 ;repeated UsedTime used_time = 2;}enum SrTimeMode {STM_NONE = 0;STM_RELATIVE = 1;STM_ABSOLUTE = 2;STM_DAYTIME = 3;STM_WEEKDAY = 4;STM_MONTHDAY = 5;STM_MONTH = 6;STM_YEAR = 7;}enum SrCycleMode {SCM_NONE = 0;SCM_CYCLE_NO = 1;SCM_CYCLE_YES = 2;SCM_CYCLE_INFINITE = 3;}message TimePoint {SrTimeMode mode = 1;int32 value = 2;int32 end_value = 3;}message PlayRequest {message PlayAction {string bottle = 1;int32 duration = 2;int32 power = 3;}message PlayTrace {repeated int32 action_id = 1;int32 before_start = 2;SrCycleMode cycle_mode = 3;int32 interval = 4;int32 cycle_time = 5;}BaseRequest request = 1 ;SrCycleMode cycle_mode = 2;repeated TimePoint start_at = 3;int32 cycle_time = 4;repeated PlayAction actions = 5 ;repeated PlayTrace trace = 6 ;}message StopPlayRequest {BaseRequest request = 1;repeated string bottles = 2;}enum SrRemoveTaskMode{SRT_NONE = 0;SRT_STOP_CANCEL = 1;SRT_STOP_NOTCANCEL = 2;SRT_NOTSTOP_CANCEL = 3;}message RemoveTaskRequest {BaseRequest request = 1;message RemoveTaskOption {int32 stop_seq = 1;SrRemoveTaskMode mode = 2;}repeated RemoveTaskOption stop_tasks = 2;}enum SrDevAttrType {SDST_NONE = 0;SDST_DEVICEID = 1;SDST_DEVICENAME = 2;SDST_DEVICETYPE = 3;SDST_MAC = 4;SDST_WIFISSID = 5;SDST_WIFIPWD = 6;SDST_NETCONNECTSTATE = 7;SDST_BLECONNECTSTATE = 8;SDST_LOGSTATE = 9;SDST_DATETIME = 10;SDST_UPTIME = 11;SDST_DOWNTIME = 12;}message DevAttrOption {SrDevAttrType attr = 1;string value = 2;}message GetDevAttrsRequest {BaseRequest request = 1 ;repeated SrDevAttrType attrs = 2;}message GetDevAttrsResponse {BaseResponse response = 1 ;repeated DevAttrOption attrs = 2;}message SetDevAttrsRequest {BaseRequest request = 1 ;repeated DevAttrOption attrs = 2;}message DeviceModelResponse {BaseResponse response = 1;repeated SrCmdId support_cmds = 2;repeated AppControlSet controls = 3;}message AppControlSet {int32 identity = 1;string name = 2;oneof controller {AppControlSwitch switchs = 11;AppControlSlider slider = 12;AppControlSpin spin = 13;AppControlSwitchSlider switch_slider = 14;}}enum SrSwitchStatus {SSS_NONE = 0;SSS_ON = 1;SSS_OFF = 2;}message AppControlSwitch {SrSwitchStatus status = 1;}message AppControlSlider {int32 _min = 1;int32 _max = 2;int32 _step = 3;int32 value = 4;}message AppControlSpin {int32 _step = 1;int32 angle = 2;int32 angular_speed = 3;}message AppControlSwitchSlider {SrSwitchStatus status = 1;int32 _min = 2;int32 _max = 3;int32 _step = 4;int32 value = 5;}message SetControlAttrRequest {message SetControlAttr {int32 identity = 1;string attr = 2;string value = 3;}BaseRequest request = 1;repeated SetControlAttr settings = 2;}message DevWifiListResponse {BaseResponse response = 1;repeated string wifissid_list = 2;}message SetDevWifiRequest {string wifissid = 1;string wifipwd = 2;}message SmellListResponse {BaseResponse response = 1 ;repeated string smell_list = 2;}message CurrentPlayRespones {BaseResponse response = 1 ;string bottle = 2;int32 remain_second  = 3;int32 total_second = 4;}');
	var Simple = simpleRoot.com.scentrealm;
	return Simple;
});