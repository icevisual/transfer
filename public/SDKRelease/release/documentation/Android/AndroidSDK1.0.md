
SDK详解
====
#初始化部分

#### 1.1. 初始化部分流程步骤 <br>

	第一步:环境搭建	 配置AndroidManifest.xml，设置对应权限

	第二步:注册监听器  SciWifiSDK.sharedInstance().setListener(your_sdk_listener);

	第三步:启动 SciWifiSDK.sharedInstance().startWithAppAccess(your_context,
	"your_app_access","your_app_secret",null, null, false);

#### 1.2. 注册SDK通用监听器

注册SDK通用监听器是为了能让APP收到来自SciWifiSDK类的响应事件，包含了注册、登录、配置设备、绑定设备等回调接口。

该监听器是SDK使用中十分重要的一个监听器，与SciWifiSDK类相关的操作都会在这里会回调。

如果没有正确注册通用监听器，将无法正常使用SDK。注册监听时，

APP可以根据自己的需求实现回调接口。建议两种设置方式：

1)在每一个使用到的Activity中都实例化一次监听器并注册一次，且只实现需要的回调接口。

该种方式比较灵活，可在service中使用。但要注意必须每次打开activity都监听一次， 且无法多个Activity同时收到回调。

###### 调用示例
```Java
//实例化监听器
SciWifiSDKListener mListener = new SciWifiSDKListener() {
	//实现手机号注册用户回调
	Override
	public void didRegisterUser(SciWifiErrorCode result, String uid, String token){
		if (result == SciWifiErrorCode.Sci_SDK_SUCCESS) {
			// 注册成功，处理注册成功的逻辑
		} else {
			// 注册失败，处理注册失败的逻辑
		}
	}
};

public void onCreate() {
	super.onCreate();
	// 注册监听器
	SciWifiSDK.sharedInstance().setListener(mListener);
	// 调用SDK的手机号注册接口
	SciWifiSDK.sharedInstance().registerUser("HelloSciwits", "12345678");	
}
```

2)在一个基类中实例化一次监听器，并把回调抛出，子类继承基类，这就不需要每个子类都实例化一次监听器。该种方式通过继承的方式，可以多个Activity都收到回调。但该种方式无法在Service中使用。如无特别说明，文档中的范例都是使用该方法注册监听器。

###### 调用示例
```Java     
//创建基类，在基类中实例化和注册监听器
public class BaseActivity extends Activity {
	private SciWifiSDKListener mListener = new SciWifiSDKListener() {
		Override
		public void didRegisterUser(SciWifiErrorCode result, String uid, String token) {
			BaseActivity.this.didRegisterUser(result, uid, token);
		}
	};
	public void didRegisterUser(SciWifiErrorCode result, String uid, String token)
	{
		//实现逻辑
	}
	Override 
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState); 
		//每次启动activity都要注册一次sdk监听器，保证sdk状态能正确回调
		SciWifiSDK.sharedInstance().setListener(mListener);
	}
}
```

#### 1.3 初始化 sdk

    SDK启动时需要指定应用程序的AppAccess，开发者需要先在气味王国网站上为自己的APP申请一个AppAccess，

    请在应用的Application或者第一个启动的Activity的onCreate中调用该方法指定应用的APPAccess。

    该方法只需要调用一次。
    SDK的日志可以帮助开发者发现APP运行时发生的问题，SDK默认将所有日志信息输出到调试终端和日志文件中。
    如果手机有SD卡，则日志文件会保存在SD卡上，如果没有SD卡，就只保存在应用程序路径下。
    SD卡上的日志文件目录为手机SD卡路径下的SciWifiSDK/app_package_name/SciSDKLog。
    APP如果不希望在调试终端输出日志，可以通过日志级别设置接口，把日志输出级别修改为SciLogPrintNone。

###### 调用示例

```Java 
public void onCreate() {
   super.onCreate();
   SciWifiSDK.sharedInstance().setListener(mListener);
   SciWifiSDK.sharedInstance().startWithAppAccess(getApplicationContext(), "your_app_id"); 
   SciWifiSDKListener mListener = new SciWifiSDKListener() {
   @Override
   public void didNotifyEvent(SciEventType eventType, Object eventSource, 
		SciWifiErrorCode eventID, String eventMessage) {

		if (eventType == SciEventType.SciEventSDK) {
		   // SDK的事件通知
		   Log.i("SciWifiSDK", "SDK event happened: " + eventID + ", " + eventMessage);
		} else if (eventType == SciEventType.SciEventDevice) {
		   // 设备连接断开时可能产生的通知
		  SciWifiDevice mDevice = (SciWifiDevice)eventSource;
		   Log.i("SciWifiSDK", "device mac: " + mDevice.getMacAddress() 
		   + " disconnect caused by eventID: " + eventID + ", eventMessage: " + eventMessage);
		} else if (eventType == SciEventType.SciEventToken) {
		   //token失效通知
		   Log.i("SciWifiSDK", "token " + (String)eventSource + " expired: " + eventMessage);
		}
	}
   };
}
``` 
#用户部分

```Java 
//子类继承基类，实现基类的回调接口。
public class TestActivity extends BaseActivity {
	protected void onCreate(android.os.Bundle savedInstanceState) { 
		//调用父类方法
		super.onCreate(savedInstanceState);
		//调用用户注册方法
		SciWifiSDK.sharedInstance().registerUser("your_phone_number", "your_ password",
		“your_verify_code”, SciUserAccountType.SciUserPhone);
	}
	@Override
	public void didRegisterUser(SciWifiErrorCode result, String uid,  String token) {
		if (result == SciWifiErrorCode.Sci_SDK_SUCCESS) {
			// 注册成功
		} else {
			// 注册失败
		}
	}
}

```

#配置设备入网部分

#### 3.1. 设备配置流程步骤

	第一步:搜索蓝牙连接设备

	第二步:配置wifi名称、密码

	第三步:通过蓝牙4.0发送配置wifi指令

	第四步:获取蓝牙数据进行分包数据处理

	第五步:解析蓝牙分包处理回调结果渲染UI

#### 3.2. BLE4.0配置

BLELink使用BLE4.0方式，连上蓝牙后发出配置成功指令，通知手机配置已完成之后验证密钥是否通过算是添加成功。

如果一分钟内未收到BleLink蓝牙或无法正确连上蓝牙，将退出首页。

###### 调用示例
```Java
//首先通过设备蓝牙4.0与手机连上
//配置设备入网，发送要配置的wifi名称、密码
//分包解析抽象工具类
BluetoothBufferParseTool bluetoothBufferParseTool=
  new BluetoothBufferParseTool(mMessageChildHandler);
bluetoothBufferParseTool.setIsrunflg(true);

//输入wifi ssid与wifi密码发送配置指令
CmdCenter.getInstance(this).setBleAirlinkCmd("your_ssid", "your_ssidPsw");
 /**The handler.
   //连接蓝牙wifi网络配置提示、倒计时提示
   TIMER_TEXT,
   //蓝牙设备已断开处理
   DISCONNECT,
   //云端配置提示
   CLOUD_TEXT,
   //倒计时开始
   START_TIMER
   //配置成功
   SUCCESSFUL
   //配置失败
   FAILED
*/
//回调配置
private Handler handler = new Handler() {
     @SuppressWarnings({"deprecation", "static-access"})
     public void handleMessage(Message msg) {
            super.handleMessage(msg);
            handler_key key = handler_key.values()[msg.what];
            switch (key) {
                case TIMER_TEXT:
                    //连接蓝牙wifi网络配置状态
                    break;
                case CLOUD_TEXT:
                   //云端配置结果
                    break;
                case START_TIMER:
                    isStartTimer();
                    break;
                case DISCONNECT:
                    //蓝牙设备已断开
                    // 返回
                    setResult(RESULT_OK);
                    finish();
                    break;
                case SUCCESSFUL:
                    //配置成功提示

		    //弹出对话框验证设备密钥是否通过
		    //SciWifiSDK.sharedInstance().verifyDeviceSecretKey("your_uid", "your_token",
		    //"your_app_access","your_app_secret");
                    break;
                case FAILED:
                    //配置失败结果提示

                    // 清除储存wifi配置返回结果数据
                    // 配置失败
                    Toast.makeText(this, msg.obj.toString(),Toast.LENGTH_SHORT).show();
                    setResult(RESULT_OK);
                    finish();
                    break;
                default:
                    break;

            }
        }

};
/**蓝牙回调结果*/
HolloBluetooth.OnHolloBluetoothCallBack bleCallBack = 
	new HolloBluetooth.OnHolloBluetoothCallBack() {
      @Override
      public void OnHolloBluetoothState(int state) {
          Log.d(TAG, "连接状态..." + state);
          if (state == HolloBluetooth.HOLLO_BLE_DISCONNECTED) {
             Log.d(TAG, "蓝牙连接已断开...");
             handler.sendEmptyMessage(handler_key.DISCONNECT.ordinal());
          }
      }
      @Override
      public void OnReceiveData(final byte[] recvData) {
          if (recvData != null) {
             Log.d(TAG,"收到数据:" + ConvertData.bytesToHexString(recvData, false));
             //分包数据开始收集工具类
             bluetoothBufferParseTool.parseRecvDataBuffer(recvData);
          }
      }
};
/**解析蓝牙分包回调数据*/
public MessageChildHandler mMessageChildHandler;
public class MessageChildHandler extends Handler {
        /** The Constant RESP. */
        protected static final int RESP_WIFI_SET = 5;
        protected static final int RESP_WIFI_RESULT = 6;
        protected static final int RESP_WIFI_CONFIG_RESULT = 7;
        public MessageChildHandler() {
            super();
        }
        @SuppressWarnings("unchecked")
        public void handleMessage(Message msg) {
            ConcurrentHashMap<String, Object> hashMap;
            Message message = new Message();
            switch (msg.what) {
                case RESP_WIFI_SET:
                    hashMap=(ConcurrentHashMap<String, Object>) msg.obj;
                    //解析wifi设备结果
                    try {
                        SrErrorCode srErrorCode = (SrErrorCode) hashMap.get("ErrorCode");
                        if (srErrorCode == SrErrorCode.SEC_SUCCESS) {
                            //已连接设备
                            handler.sendEmptyMessage(handler_key.TIMER_TEXT.ordinal());
                        } else if (srErrorCode == SrErrorCode.SEC_ERROR) {
                            //未连接设备
                            handler.sendEmptyMessage(handler_key.TIMER_TEXT.ordinal());
                        } else if (srErrorCode == SrErrorCode.SEC_REJECT) {
                            //连接设备拒绝
                            handler.sendEmptyMessage(handler_key.TIMER_TEXT.ordinal());
                        }
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                    break;
                case RESP_WIFI_RESULT:
                    hashMap=(ConcurrentHashMap<String, Object>) msg.obj;
                    //解析连接云端结果
                    SrErrorCode srErrorCode = (SrErrorCode) hashMap.get("ErrorCode");
                    if (srErrorCode == SrErrorCode.SEC_SUCCESS) {
                       //设备连接云端成功
                       handler.sendEmptyMessage(handler_key.CLOUD_TEXT.ordinal());
                    } else if (srErrorCode == SrErrorCode.SEC_ERROR) {
                       //设备连接云端失败
                       handler.sendEmptyMessage(handler_key.CLOUD_TEXT.ordinal());
                    } else if (srErrorCode == SrErrorCode.SEC_REJECT) {
                       //设备连接云端拒绝
                       handler.sendEmptyMessage(handler_key.CLOUD_TEXT.ordinal());
                    }
                    break;
                case RESP_WIFI_CONFIG_RESULT:
                    hashMap=(ConcurrentHashMap<String, Object>) msg.obj;
                    //解析网络配置结果
                    if (timer != null) {
                       timer.cancel();
                    }
		    //存储唯一access名称;
                    //存储唯一access;
                    SrErrorCode srErrorCode = (SrErrorCode) hashMap.get("ErrorCode");
                    if (srErrorCode == SrErrorCode.SEC_SUCCESS) {
                        //网络配置成功
			handler.sendEmptyMessage(handler_key.SUCCESSFUL.ordinal());
                    } else if (srErrorCode == SrErrorCode.SEC_ERROR) {
                        //网络配置失败
			handler.sendEmptyMessage(handler_key.FAILED.ordinal());
                    } else if (srErrorCode == SrErrorCode.SEC_REJECT) {
                        //网络配置拒绝
			handler.sendEmptyMessage(handler_key.FAILED.ordinal());
                    }
                    break;
            }
	}
}
```
#### 3.3 蓝牙4.0 (nRF Toolbox for BLE) 设备搜索

###### 调用示例
```Java 
//子类继承基类，实现基类的回调接口。
public class TestActivity extends BaseActivity {
	private boolean mScanning=true;
	// Stops scanning after 10 seconds.
	private static final long SCAN_PERIOD = 5*1000;
	protected void onCreate(android.os.Bundle savedInstanceState) { 
	    //调用父类方法
	    super.onCreate(savedInstanceState);
	    //获取蓝牙实例
	    HolloBluetooth mble = HolloBluetooth.getInstance(getApplicationContext());
	    // 判断本设备是否支持蓝牙ble，并连接本地蓝牙设备
	    if (!mble.isBleSupported() || !mble.connectLocalDevice()) {
		Toast.makeText(this,"BLE是不支持的设备",Toast.LENGTH_SHORT).show();
		this.finish();
		return;
	   }
	}
	Runnable cancelScan = new Runnable() {
		@Override
		public void run() {
		     mble.stopLeScan();
		     try {
		        Thread.sleep(500);
		     } catch (InterruptedException e) {
		       e.printStackTrace();
		     }
		    mble.startLeScan();
		    mHandler.postDelayed(cancelScan, SCAN_PERIOD);
		    invalidateOptionsMenu();
		}
	 };
	// enable = true表示蓝牙开始扫描，否则表示停止扫描
	private void scanLeDevice(final boolean enable) {
	   if (enable) {
		// SCAN_PERIOD 秒后停止扫描
		mHandler.postDelayed(cancelScan, SCAN_PERIOD);

		mScanning = true;
		mble.startLeScan(); // 开始蓝牙扫描
	   } else {
		// 取消停止扫描的线程
		mHandler.removeCallbacks(cancelScan);
		mScanning = false;
		mble.stopLeScan(); // 停止蓝牙扫描
	   }
	  invalidateOptionsMenu();
	}
	//扫描结果
	private BLeScanCallback mLeScanCallback = new BLeScanCallback() {
	    @Override
	    public void onBLeScan(BluetoothDevice device, int rssi, byte[] scanRecord) {     
	       byte[] temp = scanRecord;
	       //LogUtil.d(TAG, ConvertData.bytesToHexString(temp, false));
	       mLeDeviceListAdapter.addDevice(device, Integer.valueOf(rssi), ConvertData.bytesToHexString(temp, false));
	       mLeDeviceListAdapter.notifyDataSetChanged();
	   }
	};
	@Override
	protected void onResume()
	{
	   super.onResume();
	   //判断本地蓝牙是否已打开
	   if(!mble.isOpened()){
	      Intent openIntent = new Intent(BluetoothAdapter.ACTION_REQUEST_ENABLE);
	      startActivityForResult(openIntent, REQUEST_ENABLE_BT);
	      //Toast.makeText(this,"蓝牙未打开",Toast.LENGTH_SHORT).show();
	   }
	   //设置蓝牙扫描的回调函数
	   mble.setScanCallBack(mLeScanCallback);
	   //Initializes list view adapter.
           mLeDeviceListAdapter = new LeDeviceListAdapter(this);
           mListview.setListAdapter(mLeDeviceListAdapter);
	    //开始蓝牙扫描
	   scanLeDevice(true);	
	   //mScanBt.setText("停止扫描");
	}
	@Override
	protected void onPause()
	{
	   super.onPause();
	   scanLeDevice(false);		//停止蓝牙扫描
	   mLeDeviceListAdapter.clear();	//清空list
	}
	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data)
	{
	   //打开蓝牙结果
	   if(resultCode == REQUEST_ENABLE_BT && resultCode == Activity.RESULT_CANCELED){
		finish();
		return ;
	   }
	   super.onActivityResult(requestCode, resultCode, data);
	}
	@Override
	protected void onDestroy() {
	   Log.d(TAG,"onDestroy()....");
	   super.onDestroy();
	   mble.disconnectDevice();
	   Log.d(TAG, "destroy");
	   mble.disconnectLocalDevice();
       }
}
```
#### 3.4 蓝牙4.0 (nRF Toolbox for BLE) 连接设备

###### 调用示例
```Java 
String cDeviceAddress;//设备地址
//连接蓝牙设备
if(mble.connectDevice(cDeviceAddress, bleCallBack){
   Log.d(TAG, "设备链接成功....");
}else{
   Log.d(TAG, "设备链接失败....");
}
//连接回调结果
HolloBluetooth.OnHolloBluetoothCallBack bleCallBack = 
	new HolloBluetooth.OnHolloBluetoothCallBack() {

        @Override
        public void OnHolloBluetoothState(int state) {
            if (state == HolloBluetooth.HOLLO_BLE_DISCONNECTED) {
		Log.d(TAG, "蓝牙已断开");
            }
        }
        @Override
        public void OnReceiveData(byte[] recvData) {
            Log.d(TAG, "接收：\r\n      " + 
	    ConvertData.bytesToHexString(recvData, false));
        }
};
if(mble.wakeUpBle()) {
  //设备唤醒成功
  Log.d(TAG, "设备唤醒成功....");
}else{
 Log.d(TAG, "设备唤醒失败....");
}
```

#设备发现和订阅部分
#### 4.1. 设备发现和订阅流程步骤

	第一步:设置SDK监听    SciWifiSDK.sharedInstance().setListener(mListener);

	第二步:发现、绑定设备列表

			//获取已绑定设备列表（获取绑定设备才需要调用）
			SciWifiSDK.sharedInstance().getDeviceList();

			//实现已绑定的回调
			public void didDiscovered(SciWifiErrorCode result, java.util.List<SciWifiDevice> deviceList) {
			}

	第三步:设置设备订阅监听	                

			//获取设备列表中的设备
			mWifiDevice.setListener(mWifiDeviceListener);

	第四步:订阅设备	
			//订阅设备
			mWifiDevice.setSubscribe(this, true);	

			//用于设备订阅回调
			public void didReceiveData(SciWifiErrorCode result, SciWifiDevice device,ConcurrentHashMap<String, Object> dataMap, int sn) {}

#### 4.2. 设备发现
APP设置好监听，启动SDK后，就可以收到SDK的设备列表推送。

用户登录后，SDK会主动把用户已绑定的设备列表上报给APP，绑定设备在不同的手机上登录帐号都可获取到。

如果APP想要刷新绑定设备列表，可以调用绑定设备列表接口，SDK会把筛选后的设备列表返回给APP。

SDK提供设备列表绑定缓存，设备列表中的设备对象在整个APP生命周期中一直有效。
    
###### 调用示例

```Java
// 使用缓存的设备列表刷新UI
List<SciWifiDevice> devices = SciWifiSDK.sharedInstance().getDeviceList();
//接收设备列表变化上报，刷新UI
SciWifiSDKListener mListener = new SciWifiSDKListener() {
	@Override
	public  void didDiscovered(SciWifiErrorCode result, List<SciWifiDevice> deviceList) {
		// 提示错误原因
		if(result != SciWifiErrorCode.Sci_SDK_SUCCESS) {
		    Log.d("", "result: " + result.name());
		}
		// 显示变化后的设备列表
		Log.d("", "discovered deviceList: " + deviceList);
		devices = deviceList;
	}
};
```

```Java
// 主动刷新绑定设备列表、指定筛选的设备productKey
List<String> pks = new ArrayList<String> ();
pks.add("your_productKey");
SciWifiSDK.sharedInstance().getBoundDevices("your_uid", "your_token", pks);
```

#### 4.2.1 手动添加设备。

###### 调用示例
```Java
SciWifiSDK.sharedInstance().setListener(mListener);
SciWifiSDK.sharedInstance().verifyDeviceName("your_third_uid", "your_third_token","deviceName");
// 实现回调
SciWifiSDKListener sciWifiSDKListener = new SciWifiSDKListener() {
	/** 验证设备用户是否存在 */
        public void didVerifyDeviceName(SciWifiErrorCode result,int binded,String deviceName) {
	   if (result == SciWifiErrorCode.SCI_SDK_SUCCESS) {
	     if (binded== 0) {//0未绑定 1已绑定 未绑定输入密钥
               
             }else{

	     }
	   }else if(SciWifiErrorCode.SCI_SDK_HTTP_ANSWER_PARAM_ERROR==result){
	       //服务器返回错误信息
	      log.d("",result.getResultMessage());
	   }else {
	     //返回其他错误信息
	   }
        }

};
```

#### 4.2.2 验证设备密钥是否通过

###### 调用示例
```Java
SciWifiSDK.sharedInstance().setListener(mListener);
SciWifiSDK.sharedInstance().verifyDeviceSecretKey("your_third_uid", "your_third_token"
	,"deviceName","secretkey");
//实现回调
SciWifiSDKListener sciWifiSDKListener = new SciWifiSDKListener() {
     /** 验证设备名称与设备密钥是否通过 */
     public void didVerifyDeviceSecretKey(SciWifiErrorCode result,String deviceName,
		String deviceSecretKey) {

        if (result == SciWifiErrorCode.SCI_SDK_SUCCESS) {
	  //秘钥通过、添加新设备成功
        }else if(SciWifiErrorCode.SCI_SDK_HTTP_ANSWER_PARAM_ERROR==result){
	  //服务器返回错误信息
	  log.d("",result.getResultMessage());
	}else {
	  //返回其他错误信息
	}
    }
};
```

#### 4.3. 设置设备的监听器

	在设备列表中得到设备对象，为其设置设备监听器，以便于刷新设备UI。

	APP根据自己的需要实现相应的回调。

#### 4.4. 设备订阅和绑定
	APP得到设备列表后，给设备设置监听后，可以订阅设备。

	APP可以通过手动绑定的方式完成绑定。绑定成功的设备，需要订阅后才能使用。

	无论是手动绑定还是自动绑定，设备的别名信息，都需要在设备绑定成功后再设置。

	解除订阅的设备，连接会被断开，不能再继续下发控制指令了。

#### 4.4.1. 设备订阅
	所有通过SDK得到的设备，都可以订阅，订阅结果通过回调返回。
	订阅成功的设备，要在其网络状态变为可控时才能查询状态和下发控制指令。

###### 调用示例

```Java
//以设备列表中的第一个设备实例为例，为其设置监听
SciWifiDevice mDevice = null;
	for (int i = 0; i < deviceList.size(); i++) {
		mDevice = deviceList[0];
		mDevice.setListener(mListener);
		mDevice.setSubscribe(true);
		break;
	}
SciWifiDeviceListener mListener = new SciWifiDeviceListener() {
		@Override
		public  void didSetSubscribe(SciWifiErrorCode result,
		SciWifiDevice device, boolean isSubscribed) {
		if (result == SciWifiErrorCode.Sci_SDK_SUCCESS) {
		   //订阅或解除订阅成功
		} else {
		   // 失败
		}
	}
};
```
#######4.4.2. 非局域网设备绑定（Wifi设备不需要远程绑定） 废弃

#######APP可以通过设备的mac、productKey、productSecret完成设备的绑定,APP通过扫码方式绑定。

#######GPRS设备、蓝牙设备等都是无法通过Wifi局域网发现的设备，都属于非局域网设备。
```Java
/*SciWifiSDK.sharedInstance().setListener(mListener);
SciWifiSDK.sharedInstance().bindRemoteDevice("your_uid", "your_token", "your_device_mac",
"your_device_product_key", "your_product_secret"); 
// 实现回调
SciWifiSDKListener mListener = new SciWifiSDKListener() {
	@Override
	public void didBindDevice(SciWifiErrorCode result, String did) {
		if (result == SciWifiErrorCode.Sci_SDK_SUCCESS) {
		   // 绑定成功
		} else {
		   // 绑定失败
		}
	}
};*/
```

#### 4.4.3 设备解绑

已绑定的设备可以解绑，解绑需要APP调用接口完成操作，SDK不支持自动解绑。

对于已订阅的设备，解绑成功时会被解除订阅，同时断开设备连接，设备状态也不会再主动上报了。

设备解绑后，APP刷新绑定设备列表时就得不到该设备了。

###### 调用示例

```Java
SciWifiSDK.sharedInstance().setListener(mListener);
SciWifiSDK.sharedInstance().unbindDevice("your_uid", "your_token", "your_device_did"); 
// 实现回调
SciWifiSDKListener mListener = new SciWifiSDKListener() {
	@Override
	public void didUnbindDevice(SciWifiErrorCode result, String did) {
		if (result == SciWifiErrorCode.SCI_SDK_SUCCESS) {
		    // 解绑成功
		}else {
		   // 解绑失败
		}
	}
};
```

#设备控制部分

SDK通过序列化值对方式进行设备控制和状态接收。SDK接收到APP下发的指令字后，对应解析为设备可识别的数据，发送给设备。

反之，SDK收到设备回复或上报的数据后，对应解析为字典键值对上报给APP。

智能设备需正确烧写了GAgent固件和气味王国串口通讯协议。APP发送的指令必须符合数据点定义。

#### 5.1. 设备控制流程步骤

第一步:设置设备监听   获取设备列表中的设备,设置设备监听 mDevice.setListener(mWifiDeviceListener);

第二步:APP控制设备
		对照数据点定义，操作指令名称与下发值改变，放入byte[]

		下发指令转换byte[]: byte[] totlbuf = DatumCode.getInstance().sendDataSetControl(number, key, identity, attr, value, value2);//发送编号,改变key(JsonKeys.KEY_ON_OFF),控件标识符(int),控件属性名(String),修改值(Object),修改值2(Object)

		下发操作指令: mDevice.sendData(totlbuf);	

第三步:设备状态变化

		接收设备状态上报:

		public void didReceiveData(SciWifiErrorCode result, SciWifiDevice device,
		ConcurrentHashMap<String, Object> dataMap, int sn) {}

		从data中读取状态、故障、错误等信息

		APP刷新界面状态

#### 5.2. 发送控制指令

     设备订阅变成可控状态后，APP可以发送操作指令。

     操作指令是字典格式，键值对为数据点名称和值。

     操作指令的确认回复，通过didReceiveData回调返回。

     APP下发操作指令时可以指定sn，通过回调参数中的sn能够对应到下发指令是否发送成功了。

     但回调参数dataMap有可能是空字典，这取决于设备回复时是否携带当前数据点的状态。

     如果APP下发指令后只关心是否有设备状态上报，那么下发指令的sn可填0，这时回调参数sn也为0。

###### 调用示例

```Java
//mDevice是从设备列表中获取到的设备实体对象，设置监听
mDevice.setListener(mListener);
// 订阅设备并变为可控状态后，执行开灯动作
int sn = 5;
ConcurrentHashMap<String, Object> command = new ConcurrentHashMap<String, Object> ();
command.put("LED_OnOff", true);
mDevice.write(command, sn);
// 实现回调
SciWifiDeviceListener mListener = new SciWifiDeviceListener() {
	@Override
	public  void didReceiveData(SciWifiErrorCode result, SciWifiDevice device,
		ConcurrentHashMap<String, Object> dataMap, int sn) {
		if (result == SciWifiErrorCode.Sci_SDK_SUCCESS) {
			if (sn == 5) {
				// 命令序号相符，开灯指令执行成功
			} else {
				// 其他命令的ack或者数据上报
			}
		} else {
			// 操作失败
		}
	}
};
```

#### 5.3. 接收设备状态
    设备订阅变成可控状态后，APP可以随时收到设备状态的主动上报，仍然通过didReceiveData回调返回。
    设备上报状态时，回调参数sn为0，回调参数dataMap为设备上报的状态。

###### 调用示例

```Java
SciWifiDeviceListener mListener = new SciWifiDeviceListener() {
	@Override
	public void didReceiveData(SciWifiErrorCode result, SciWifiDevice device,
		ConcurrentHashMap<String, Object> dataMap, int sn) {
		if (result == SciWifiErrorCode.Sci_SDK_SUCCESS) {
			// 已定义集合类型的设备数据点，有布尔、数值和枚举型数据集合 
			if (dataMap.get("data") != null) {
				ConcurrentHashMap<String, Object> map = 
				(ConcurrentHashMap<String, Object>) dataMap.get("data");
				//数据点，打印对应的key和value 
				List<ControllInfoData>  controllData=
				(ArrayList<ControllInfoData>)hashMap.get("data");
				for (int i = 0; i < controDataist.size(); i++) {
				    ControllInfoData customDragData=controDataist.get(i);
				    //customDragData.controlType  判断控件类型
				    System.out.println("customDragData:"+customDragData.toString());
				}
			}
			// 扩展已定义的设备发生故障后该字段有内容，没有发生故障则没内容
			if (dataMap.get("faults") != null) {
				ConcurrentHashMap<String, Object> map =  
				(ConcurrentHashMap<String, Object>)dataMap.get("faults");
				StringBuilder sb = new StringBuilder();
				for (String key : map.keySet()) {
					sb.append(key + "  :" +  map.get(key) + "\r\n");
					Toast.makeText(DeviceControlActivity.this,sb.toString(),
					Toast.LENGTH_SHORT).show();
				}
			}
			//扩展已定义的设备发生报警后该字段有内容，没有发生报警则没内容
			if (dataMap.get("alerts") != null) {
				ConcurrentHashMap<String, Object> map =  
				(ConcurrentHashMap<String, Object>)dataMap.get("alerts");
				StringBuilder sb = new StringBuilder();
				for (String key : map.keySet()) {
					sb.append(key + "  :" +  map.get(key) + "\r\n");
					Toast.makeText(DeviceControlActivity.this,sb.toString(),
					Toast.LENGTH_SHORT).show();
				}
			}
			//透传数据，无数据点定义，适合开发者自行定义协议自行解析
			if (dataMap.get("binary") != null) {
				byte[] binary = (byte[]) dataMap.get("binary");
				Log.i("", "Binary data:"+ bytesToHex(binary, 0, binary.length));
			}
		}
	}
};
```

#### 5.4. 设备状态查询
设备订阅变成可控状态后，APP可以查询设备状态。
设备状态查询结果也通过didReceiveData回调返回，回调参数sn为0。回调参数dataMap为设备回复的状态。

###### 调用示例

```Java
//mDevice是从设备列表中获取到的设备实体对象，设置监听
mDevice.setListener(mListener);
mDevice.getDeviceStatus();
// 实现回调
SciWifiDeviceListener mListener = new SciWifiDeviceListener() {
	@Override
	public  void didReceiveData(SciWifiErrorCode result, SciWifiDevice device,
		ConcurrentHashMap<String, Object> dataMap, int sn) {
		if (result == SciWifiErrorCode.Sci_SDK_SUCCESS) {
		   // 数据解析与3.5.3相同
		} else {
		  // 查询失败
		}
	}
};
```

####　错误码枚举类型(SciWifiErrorCode)
| 参数      |	类型                                       | 说明			|
|:----------|:---------------------------------------------|:---------------------------| 
|errorCode  |	result		                           |				|
| 0   	    |	SCI_SDK_SUCCESS				   |成功回调函数		|
| 8001	    |	SCI_SDK_PARAM_FORM_INVALID                 |				|
| 8002	    |   SCI_SDK_CLIENT_NOT_AUTHEN		   |SDK正在启动			|
| 8003	    |   SCI_SDK_CLIENT_VERSION_INVALID		   |无效的SDK版本号		|
| 8004	    |   SCI_SDK_UDP_PORT_BIND_FAILED		   |				|
| 8005	    |   SCI_SDK_DAEMON_EXCEPTION		   |SDK后台服务发生异常		|
| 8006	    |   SCI_SDK_PARAM_INVALID			   |接口参数无效		|
| 8007	    |   SCI_SDK_APPACCESS_LENGTH_ERROR		   |AppACCESS长度错误		|
| 8008	    |   SCI_SDK_LOG_PATH_INVALID		   |SDK日志路径无效		|
| 8009	    |   SCI_SDK_LOG_LEVEL_INVALID		   |日志级别无效		|
| 8021	    |   SCI_SDK_DEVICE_CONFIG_SEND_FAILED	   |设备配网信息发送失败	|
| 8022	    |   SCI_SDK_DEVICE_CONFIG_IS_RUNNING	   |设备配网正在执行		|
| 8023	    |   SCI_SDK_DEVICE_CONFIG_TIMEOUT		   |				|
| 8024	    |   SCI_SDK_DEVICE_DID_INVALID		   |设备DID无效			|
| 8025	    |   SCI_SDK_DEVICE_MAC_INVALID		   |				|
| 8026	    |   SCI_SDK_SUBDEVICE_DID_INVALID		   |				|
| 8027	    |   SCI_SDK_DEVICE_PASSCODE_INVALID		   |设备验证码无效		|
| 8028	    |   SCI_SDK_DEVICE_NOT_CENTERCONTROL	   |				|
| 8029	    |   SCI_SDK_DEVICE_NOT_SUBSCRIBED		   |设备无响应			|
| 8030	    |   SCI_SDK_DEVICE_NO_RESPONSE		   |设备还未就绪		|
| 8031	    |   SCI_SDK_DEVICE_NOT_READY		   |设备还未绑定		|
| 8032	    |   SCI_SDK_DEVICE_NOT_BINDED		   |设备操作指令中包含无效指令	|
| 8033	    |   SCI_SDK_DEVICE_CONTROL_WITH_INVALID_COMMAND|设备操作失败		|
| 8034	    |   SCI_SDK_DEVICE_CONTROL_FAILED		   |				|
| 8035	    |   SCI_SDK_DEVICE_GET_STATUS_FAILED	   |				|
| 8036	    |   SCI_SDK_DEVICE_CONTROL_VALUE_TYPE_ERROR	   |				|
| 8037	    |   SCI_SDK_DEVICE_CONTROL_VALUE_OUT_OF_RANGE  |				|
| 8038	    |   SCI_SDK_DEVICE_CONTROL_NOT_WRITABLE_COMMAND|				|
| 8039	    |   SCI_SDK_BIND_DEVICE_FAILED		   |设备绑定失败		|
| 8040	    |   SCI_SDK_UNBIND_DEVICE_FAILED		   |设备解绑失败		|
| 8041	    |   SCI_SDK_DNS_FAILED			   |DNS解析失败			|
| 8042	    |   SCI_SDK_M2M_CONNECTION_SUCCESS		   |				|
| 8043	    |   SCI_SDK_SET_SOCKET_NON_BLOCK_FAILED	   |				|
| 8044	    |   SCI_SDK_CONNECTION_TIMEOUT		   |连接超时			|
| 8045	    |   SCI_SDK_CONNECTION_REFUSED		   |连接被拒绝			|
| 8046	    |   SCI_SDK_CONNECTION_ERROR		   |发生了连接错误		|
| 8047	    |   SCI_SDK_CONNECTION_CLOSED		   |连接被对端关闭		|
| 8048	    |   SCI_SDK_SSL_HANDSHAKE_FAILED		   |ssl握手失败			|
| 8049	    |   SCI_SDK_DEVICE_LOGIN_VERIFY_FAILED	   |设备登录验证失败		|
| 8050	    |   SCI_SDK_INTERNET_NOT_REACHABLE		   |手机外网无法访问		|
| 8096	    |   SCI_SDK_HTTP_ANSWER_FORMAT_ERROR	   |HTTP应答格式错误		|
| 8097	    |   SCI_SDK_HTTP_ANSWER_PARAM_ERROR		   |HTTP应答数据错误		|
| 8098	    |   SCI_SDK_HTTP_SERVER_NO_ANSWER		   |HTTP服务端无应答		|
| 8099	    |   SCI_SDK_HTTP_REQUEST_FAILED		   |HTTP请求失败		|
| 8100	    |   SCI_SDK_OTHERWISE			   |SDK另外错误			|
| 8101	    |   SCI_SDK_MEMORY_MALLOC_FAILED		   |				|
| 8102	    |   SCI_SDK_THREAD_CREATE_FAILED		   |				|
| 8150	    |   SCI_SDK_USER_ID_INVALID			   |登录用户ID无效		|
| 8151	    |   SCI_SDK_TOKEN_INVALID			   |登录token无效		|
| 8152	    |   SCI_SDK_GROUP_ID_INVALID		   |				|
| 8153	    |   SCI_SDK_GROUPNAME_INVALID		   |				|
| 8154	    |   SCI_SDK_GROUP_PRODUCTKEY_INVALID	   |				|
| 8155	    |   SCI_SDK_GROUP_FAILED_DELETE_DEVICE	   |				|
| 8156	    |   SCI_SDK_GROUP_FAILED_ADD_DEVICE		   |				|
| 8157	    |   SCI_SDK_GROUP_GET_DEVICE_FAILED		   |				|
| 8201	    |   SCI_SDK_DATAPOINT_NOT_DOWNLOAD		   |				|
| 8202	    |   SCI_SDK_DATAPOINT_SERVICE_UNAVAILABLE	   |				|
| 8203	    |   SCI_SDK_DATAPOINT_PARSE_FAILED		   |设备数据点解析失败		|
| 8300	    |   SCI_SDK_SDK_NOT_INITIALIZED		   |				|
| 8301	    |   SCI_SDK_APK_CONTEXT_IS_NULL		   |				|
| 8302	    |   SCI_SDK_APK_PERMISSION_NOT_SET		   |				|
| 8303	    |   SCI_SDK_CHMOD_DAEMON_REFUSED		   |		  |
| 8304	    |   SCI_SDK_EXEC_DAEMON_FAILED		   |		  |
| 8305	    |   SCI_SDK_EXEC_CATCH_EXCEPTION		   |		  |
| 8306	    |   SCI_SDK_APPACCESS_IS_EMPTY		   |AppACCESS为空，无法使用SDK	|
| 8307	    |   SCI_SDK_UNSUPPORTED_API			   |此API已废弃，不再提供支持	|
| 8308	    |   SCI_SDK_REQUEST_TIMEOUT			   |SDK接口执行超时		|
| 8309	    |   SCI_SDK_DAEMON_VERSION_INVALID		   |		  |
| 8310	    |   SCI_SDK_PHONE_NOT_CONNECT_TO_SOFTAP_SSID   |		  |
| 8311	    |   SCI_SDK_DEVICE_CONFIG_SSID_NOT_MATCHED	   |				|
| 8312	    |   SCI_SDK_NOT_IN_SOFTAPMODE		   |				|
| 8313	    |   SCI_SDK_CONFIG_NO_AVAILABLE_WIFI	   |				|
| 8314	    |   SCI_SDK_RAW_DATA_TRANSMIT		   |				|
| 8316	    |   SCI_SDK_START_SUCCESS			   |SDK启动成功			|
| 9001	    |	SCI_OPENAPI_MAC_ALREADY_REGISTERED	   |				|
| 9002	    |	SCI_OPENAPI_PRODUCT_KEY_INVALID		   |product key无效		|
| 9003	    |	SCI_OPENAPI_APPACCESS_INVALID		   |appAccess无效			|
| 9004	    |	SCI_OPENAPI_TOKEN_INVALID		   |token无效			|
| 9005	    |	SCI_OPENAPI_USER_NOT_EXIST		   |用户名不存在		|
| 9006	    |	SCI_OPENAPI_TOKEN_EXPIRED		   |token已过期			|
| 9007	    |	SCI_OPENAPI_M2M_ID_INVALID		   |				|
| 9008	    |	SCI_OPENAPI_SERVER_ERROR		   |服务器错误			|
| 9009	    |	SCI_OPENAPI_CODE_EXPIRED		   |验证码已过期		|
| 9010	    |	SCI_OPENAPI_CODE_INVALID		   |验证码无效			|
| 9011	    |	SCI_OPENAPI_SANDBOX_SCALE_QUOTA_EXHAUSTED  |		  |
| 9012	    |	SCI_OPENAPI_PRODUCTION_SCALE_QUOTA_EXHAUSTED|		  |
| 9013	    |	SCI_OPENAPI_PRODUCT_HAS_NO_REQUEST_SCALE   |		  |
| 9014	    |	SCI_OPENAPI_DEVICE_NOT_FOUND		   |设备找不到			|
| 9015	    |	SCI_OPENAPI_FORM_INVALID		   |		  |
| 9016	    |	SCI_OPENAPI_DID_PASSCODE_INVALID	   |		  |
| 9017	    |	SCI_OPENAPI_DEVICE_NOT_BOUND		   |设备未绑定			|
| 9018	    |	SCI_OPENAPI_PHONE_UNAVALIABLE		   |		  |
| 9019	    |	SCI_OPENAPI_USERNAME_UNAVALIABLE	   |		  |
| 9020	    |	SCI_OPENAPI_USERNAME_PASSWORD_ERROR	   |用户名或者密码错误		|
| 9021	    |	SCI_OPENAPI_SEND_COMMAND_FAILED		   |指令发送失败		|
| 9022	    |	SCI_OPENAPI_EMAIL_UNAVALIABLE		   |邮箱已注册			|
| 9023	    |	SCI_OPENAPI_DEVICE_DISABLED		   |设备已注销			|
| 9024	    |	SCI_OPENAPI_FAILED_NOTIFY_M2M		   |		  |
| 9025	    |	SCI_OPENAPI_ATTR_INVALID		   |属性无效			|
| 9026	    |	SCI_OPENAPI_USER_INVALID		   |		  |
| 9027	    |	SCI_OPENAPI_FIRMWARE_NOT_FOUND		   |找不到设备固件		|
| 9029	    |	SCI_OPENAPI_DATAPOINT_DATA_NOT_FOUND	   |找不到对应的设备数据点数据  |
| 9030	    |	SCI_OPENAPI_SCHEDULER_NOT_FOUND		   |		  |
| 9031	    |	SCI_OPENAPI_QQ_OAUTH_KEY_INVALID	   |QQ登录授权key无效		|
| 9032	    |	SCI_OPENAPI_OTA_SERVICE_OK_BUT_IN_IDLE	   |OTA升级服务闲置或被禁用	|
| 9033	    |	SCI_OPENAPI_BT_FIRMWARE_UNVERIFIED	   |BT固件未验证		|
| 9034	    |	SCI_OPENAPI_BT_FIRMWARE_NOTHING_TO_UPGRADE |BT固件不需要升级		|
| 9035	    |	SCI_OPENAPI_SAVE_KAIROSDB_ERROR		   |		  |
| 9036	    |	SCI_OPENAPI_EVENT_NOT_DEFINED		   |		  |
| 9037	    |	SCI_OPENAPI_SEND_SMS_FAILED		   |手机短信发送失败		  |
| 9038	    |	SCI_OPENAPI_APPLICATION_AUTH_INVALID	   |		  |
| 9039	    |	SCI_OPENAPI_NOT_ALLOWED_CALL_API	   |不允许使用已废弃的API		  |
| 9040	    |	SCI_OPENAPI_BAD_QRCODE_CONTENT		   |		  |
| 9041	    |	SCI_OPENAPI_REQUEST_THROTTLED		   |		  |
| 9042	    |	SCI_OPENAPI_DEVICE_OFFLINE		   |设备已离线		  |
| 9043	    |	SCI_OPENAPI_TIMESTAMP_INVALID		   |时间戳无效		  |
| 9044	    |	SCI_OPENAPI_SIGNATURE_INVALID		   |应用签名无效		  |
| 9045	    |	SCI_OPENAPI_DEPRECATED_API		   |API已废弃		  |
| 9999	    |	SCI_OPENAPI_RESERVED			   |保留的错误字		  |
| 10003	    |	SCI_SITE_PRODUCTKEY_INVALID		   |		  |
| 10011	    |	SCI_SITE_DATAPOINTS_NOT_MALFORME	   |		  |
| 5001	    |	SCI_PUSHAPI_BODY_JSON_INVALID		   |数据体无效		  |
| 5101	    |	SCI_PUSHAPI_CONNECTION_SUCCESS		   |mqtt 服务器连接成功		  |
| 5102	    |	SCI_PUSHAPI_CONNECTION_FAILURE		   |mqtt 服务器连接失败		  |
| 5300	    |	SCI_PUSHAPI_DATA_NOT_EXIST		   |解析数据不存在	  |


