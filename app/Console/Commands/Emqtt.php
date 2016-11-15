<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use sskaje\mqtt\MQTT;
use sskaje\mqtt\Debug;
use sskaje\mqtt\MessageHandler;
use App\Extensions\Mqtt\MqttUtil;
use App\Services\Common\TOTPService;

class Emqtt extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'em {action=test} {msg=hello}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'EMQ test';

    /**
     *
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    protected $channel = null;

    /**
     *
     * @var \PhpAmqpLib\Connection\AMQPStreamConnection
     */
    protected $connection = null;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $action = $this->argument('action');
        $funcName = strtolower($action) . 'Action';
        if (method_exists($this, $funcName)) {
            call_user_func([
                $this,
                $funcName
            ]);
        } else {
            $this->error(PHP_EOL . 'No Action Found');
        }
    }

    public function generAction()
    {
        $cmd = [
            'SLEEP' => '主机休眠',
            'WAKEUP' => '唤醒主机',
            'USEDSECONDS' => '获取瓶子使用总时间（秒）',
            'PLAYSMELL' => '播放气味',
            'GETDEVATTR' => '获取设置',
            'SETDEVATTR' => '修改设置',
            'FEATUREREPORT' => '设备特性上报，可控组件上报'
        ];
        $prefix = [
            'SCI_REQ_',
            'SCI_RESP_'
        ];
        $i = 0;
        foreach ($cmd as $k => $v) {
            echo "{$prefix[0]}{$k} = " . ($i * 2 + 1) . ";\t// {$cmd[$k]} request;" . PHP_EOL;
            echo "{$prefix[1]}{$k} = " . ($i * 2 + 2) . ";\t// {$cmd[$k]} response;" . PHP_EOL;
            $i ++;
        }
    }
    
    public function gCmdMapAction()
    {
        $cmd = [
            'SLEEP' => '主机休眠',
            'WAKEUP' => '唤醒主机',
            'USEDSECONDS' => '获取瓶子使用总时间（秒）',
            'PLAYSMELL' => '播放气味',
            'GETDEVATTR' => '获取设置',
            'SETDEVATTR' => '修改设置',
            'FEATUREREPORT' => '设备特性上报，可控组件上报'
        ];
        $prefix = [
            'SCI_REQ_',
            'SCI_RESP_'
        ];
        $i = 0;
        foreach ($cmd as $k => $v) {
            // dcm[SrCmdId.{$prefix[0]}{$k}] = SrCmdId.{$prefix[1]}{$k};
            echo "lcm[SrCmdId.{$prefix[0]}{$k}] = SrCmdId.{$prefix[1]}{$k};" . PHP_EOL;
            $i ++;
        }
        // lcm
    }

    public function init()
    {
        // $mqtt = new MQTT("tcp://192.168.5.21:1883/",'PHP-client-1');
        $mqtt = new MQTT("tcp://120.26.109.169:1883/", 'PHP-client-1');
        
        $context = stream_context_create();
        $mqtt->setSocketContext($context);
        
        // Debug::Enable();
        $mqtt->setAuth('test123', '123132');
        $mqtt->setKeepalive(36);
        $connected = $mqtt->connect();
        if (! $connected) {
            die("Not connected\n");
        }
        
        $this->connection = $mqtt;
    }

    public function template(callable $function)
    {
        $this->init();
        call_user_func_array($function, [
            $this->connection
        ]);
    }

    public function aesAction()
    {
        $text = "123456dsfdfas789";
        $key = md5("XqCEMSzhsdWHfwhm"); // md5($text); //key的长度必须16，32位,这里直接MD5一个长度为32位的key
        $iv = '00000000000Pkcs7';
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CBC, $iv);
        $decode = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $crypttext, MCRYPT_MODE_CBC, $iv);
        dump(MqttUtil::aesEncrypt($text, 'XqCEMSzhsdWHfwhm', '00000000000Pkcs7'));
        dump(base64_encode($crypttext));
        dump(trim($decode));
        dump(MqttUtil::dumpByte($crypttext));
        // dump(base64_encode(openssl_encrypt($text, 'aes-128-cbc', $key, OPENSSL_ZERO_PADDING, $iv)));
    }
    
    
    public function tes5t($str){
        return $str == TOTPService::base32_decode(MqttUtil::base32_encode($str));
    }

    public function printAction()
    {
//         edump($this->tes5t('aaaaaaaaaa'));

        $initkey = 'aaaaaaaaaa';
        
        $k =  TOTPService::get_otp(MqttUtil::base32_encode($initkey));
        dump($k);
//         $ret =  TOTPService::verify_key(MqttUtil::base32_encode('HNKGRV2O2oeK7W2jtmFC'), $k);
//         dump($ret);
//         $initkey = 'HNKGRV2O2oeK7W2jtmFC1';
//         $k =  TOTPService::get_otp(MqttUtil::base32_encode($initkey));
        
//         dump($k);
//         $ret =  TOTPService::verify_key(MqttUtil::base32_encode('HNKGRV2O2oeK7W2jtmFC'), $k);
//         dump($ret);
        exit;
        
//         $Logger = new \SmellOpen\Core\Logger();
//         $msg = 'this is a test message ';
//         \SmellOpen\Core\Logger::info($msg);
//         \SmellOpen\Core\Logger::debug($msg);
//         \SmellOpen\Core\Logger::warning($msg);
//         \SmellOpen\Core\Logger::error($msg);
//         exit;
        
        $Core = new \SmellOpen\Core\Core([]);
//         SDK.connect({
//             'accessKey' : 'IAzDhpyc0z9yGFajKp2P',
//             'accessSecret' : 'HNKGRV2O2oeK7W2jtmFC',
//             'logLevel' : 'info',
//         });
//         SMSDK = SDK;
        
        $dev = $Core->usingDevice('TCeOp0gzzrWhAMoOa3Mm');
        
        $dev->sleep();
        
        exit;
        
        
        
        
        
        dump( toFix(pow(2.25 /2 ,1/2) ) );
        
        exit;
        
        
        $str =<<<EOL
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
EOL;
        
        $data = explode("\r\n", $str);
        foreach($data as $k => $v){
            $match = preg_replace('/(SDST_)|( = \d+;\s*)|(\s)/', '', $v);
            $match  = explode("//", $match);
//             dump($match);
            
            echo "'{$match[0]}' : '',//{$match[1]}".PHP_EOL;
            
        }
        dump($data);
        
        exit;
        
        
        dump(dechex(time()));
        
        
        dump(MqttUtil::base32_encode('Mangdk2222'));
        
//         JVQW4Z3ENM
        dump(TOTPService::base32_decode('JVQW4Z3ENMZDEMRS'));
        exit;
        $K =  (str_random(18));
        dump( $K);
        dump( strlen($K));
        $InitalizationKey = "LFLFMU2SGVCUIUCZKBMEKRKLIQ"; // Set the inital key
//         $InitalizationKey = "sdfsdfdsfsdfs"; // Set the inital key
        
        $TimeStamp = TOTPService::get_timestamp();
        $secretkey = TOTPService::base32_decode($InitalizationKey); // Decode it into binary
        $otp = TOTPService::oath_hotp($secretkey, $TimeStamp); // Get current token
        echo ("secretkey: $secretkey\n");
        echo ("Init key: $InitalizationKey\n");
        echo ("Timestamp: $TimeStamp\n");
        echo ("One time password: $otp\n");
        
        // Use this to verify a key as it allows for some time drift.
        
        $result = TOTPService::verify_key($InitalizationKey, "123456");
        
        dump($result);
        $result = TOTPService::verify_key($InitalizationKey, $otp);
        
        dump($result);
        
        exit();
        
        $ctx = hash_init('sha256', HASH_HMAC, 'key');
        hash_update($ctx, 'Message');
        $result = hash_final($ctx);
        dump($result);
        dump(base64_encode($result));
        
        dump(hash_hmac('sha1', 'Message', 'key'));
        dump(base64_encode(hash_hmac('sha512', 'Message', 'key')));
        // $ret = MqttUtil::assemblePayload('123123', \Proto2\Scentrealm\Simple\SrCmdId::SCI_resp_playSmell);
        
        return;
        dump($ret);
        dump((time()));
        dump(dechex(time()));
        MqttUtil::dumpByte('sadasdasd', 'I got a message');
    }

    public function testAction()
    {
        $this->template(function ($mqtt) {
            
            $topics['/TCeOp0gzzrWhAMoOa3Mm'] = 1;
            $topics['/TCeOp0gzzrWhAMoOa3Mm/resp'] = 1;
            $topics['/testIAzDhpyc0z9yGFajKp2P'] = 1;
            
            //
            // $mqtt->unsubscribe(array_keys($topics));
            
            $mqtt->subscribe($topics);
            
            // #$mqtt->unsubscribe(array_keys($topics));
            $callback = new \App\Extensions\Mqtt\MySubscribeCallback();
            
            $mqtt->setHandler($callback);
            
            $mqtt->loop();
        });
    }

    public function pubfileAction()
    {
        $this->template(function ($mqtt) {
            $content = file_get_contents(public_path('proto/PlayAction.mqtt.data'));
            $content = file_get_contents(public_path('proto/PlaySmellBack.data'));
            
            $sss = MqttUtil::assemblePayload($content, \Proto2\Scentrealm\Simple\SrCmdId::SCI_resp_playSmell);
            
            $mqtt->publish_async('/0CRngr3ddpVzUBoeF', $sss, 0, 0);
        });
    }

    public function publishAction()
    {
        $this->template(function ($mqtt) {
            // $content = file_get_contents(public_path('test/ADDRESS_BOOK_FILE.FILE'));
            // $content = file_get_contents(public_path('test/bin_person.person'));\
            
            $PlaySmell = new \Proto2\Scentrealm\Simple\PlaySmell();
            
            $startAt = new \Proto2\Scentrealm\Simple\TimePoint();
            $startAt->setMode(\Proto2\Scentrealm\Simple\SrTimeMode::STM_weekday);
            $startAt->setValue(1);
            $startAt->setEndValue(5);
            
            $startAt1 = new \Proto2\Scentrealm\Simple\TimePoint();
            $startAt1->setMode(\Proto2\Scentrealm\Simple\SrTimeMode::STM_daytime);
            $startAt1->setValue(41400);
            $startAt1->setEndValue(45000);
            
            $startAt2 = new \Proto2\Scentrealm\Simple\TimePoint();
            $startAt2->setMode(\Proto2\Scentrealm\Simple\SrTimeMode::STM_daytime);
            $startAt2->setValue(63000);
            $startAt2->setEndValue(66600);
            
            $PlaySmell->setCycleMode(\Proto2\Scentrealm\Simple\SrCycleMode::SCM_cycle_no);
            $PlaySmell->setCycleTime(0);
            $PlaySmell->appendStartAt($startAt);
            $PlaySmell->appendStartAt($startAt1);
            $PlaySmell->appendStartAt($startAt2);
            
            $PlayTrace = new \Proto2\Scentrealm\Simple\PlayTrace();
            
            $PlayAction = new \Proto2\Scentrealm\Simple\PlayAction();
            $PlayAction->setBottle("0000000001");
            $PlayAction->setDuration(2);
            $PlayAction->setPower(5);
            $PlayAction1 = new \Proto2\Scentrealm\Simple\PlayAction();
            $PlayAction1->setBottle("0000000002");
            $PlayAction1->setDuration(3);
            $PlayAction1->setPower(5);
            $PlayAction2 = new \Proto2\Scentrealm\Simple\PlayAction();
            $PlayAction2->setBottle("0000000003");
            $PlayAction2->setDuration(2);
            $PlayAction2->setPower(7);
            $PlayAction3 = new \Proto2\Scentrealm\Simple\PlayAction();
            $PlayAction3->setBottle("");
            $PlayAction3->setDuration(2);
            $PlayAction3->setPower(0);
            $PlayAction4 = new \Proto2\Scentrealm\Simple\PlayAction();
            $PlayAction4->setBottle("");
            $PlayAction4->setDuration(3);
            $PlayAction4->setPower(0);
            
            $PlayTrace->appendActionId(0);
            $PlayTrace->appendActionId(3);
            $PlayTrace->appendActionId(1);
            $PlayTrace->appendActionId(4);
            $PlayTrace->appendActionId(2);
            
            $PlayTrace->setBeforeStart(0);
            $PlayTrace->setCycleMode(\Proto2\Scentrealm\Simple\SrCycleMode::SCM_cycle_yes);
            $PlayTrace->setCycleTime(278);
            $PlayTrace->setInterval(0);
            
            $PlaySmell->appendPlay($PlayAction);
            $PlaySmell->appendPlay($PlayAction1);
            $PlaySmell->appendPlay($PlayAction2);
            $PlaySmell->appendPlay($PlayAction3);
            $PlaySmell->appendPlay($PlayAction4);
            
            $PlaySmell->appendTrace($PlayTrace);
            
            $content = $PlaySmell->serializeToString();
            
            file_put_contents(public_path('proto/PlayAction.mqtt.data'), $content);
            
            $sss = MqttUtil::assemblePayload($content, \Proto2\Scentrealm\Simple\SrCmdId::SCI_resp_playSmell);
            
            file_put_contents(public_path('proto/PlayAction.mqtt.payload'), $sss);
            
            $mqtt->publish_async('/0CRngr3ddpVzUBoeF', $sss, 0, 0);
        });
    }
}


