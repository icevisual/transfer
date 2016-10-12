<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Extensions\Mqtt\BluerhinosMqtt;
use BinSoul\Net\Mqtt\Flow\OutgoingConnectFlow;
use BinSoul\Net\Mqtt\Packet\ConnectRequestPacket;
use sskaje\mqtt\MQTT;
use sskaje\mqtt\Debug;
use sskaje\mqtt\MessageHandler;
use App\Extensions\Mqtt\MqttUtil;

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
            'mac' => '获取设备MAC地址',
            'uptime' => '获取设备开机时间',
            'downtime' => '获取上次关机时间',
            'sleep' => '主机休眠',
            'wakeup' => '唤醒主机',
            'usedSeconds' => '获取瓶子使用总时间（秒）',
            // 'enableSmell' => '开启某个气味',
            'playSmell' => '播放气味'
        ]
        // 'setPower' => '设置播放功率',
        ;
        $prefix = [
            'SCI_req_',
            'SCI_resp_'
        ];
        $i = 0;
        foreach ($cmd as $k => $v) {
            echo "{$prefix[0]}{$k} = " . ($i * 2 + 1) . ";\t// {$cmd[$k]} request;" . PHP_EOL;
            echo "{$prefix[1]}{$k} = " . ($i * 2 + 2) . ";\t// {$cmd[$k]} response;" . PHP_EOL;
            $i ++;
        }
    }

    public function init()
    {
        $mqtt = new MQTT("tcp://192.168.5.21:1883/");
        
        $context = stream_context_create();
        $mqtt->setSocketContext($context);
        
        // Debug::Enable();
        
        // $mqtt->setAuth('sskaje', '123123');
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
        dump(MqttUtil::aesEncrypt($text,'XqCEMSzhsdWHfwhm','00000000000Pkcs7'));
        dump(base64_encode($crypttext));
        dump(trim($decode));
        dump(MqttUtil::dumpByte($crypttext));
        // dump(base64_encode(openssl_encrypt($text, 'aes-128-cbc', $key, OPENSSL_ZERO_PADDING, $iv)));
    }
    
    public function printAction()
    {
        MqttUtil::dumpByte('sadasdasd','I got a message');
    }

    public function testAction()
    {
        $this->template(function ($mqtt) {
            
            $topics['/0CRngr3ddpVzUBoeF'] = 2;
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
            $bodyLength = strlen($content);
            $cmdId = \Proto2\Scentrealm\Simple\SrCmdId::SCI_req_playSmell;
            $seq = 1;
            $header = [
                0xfe,
                0x01,
                $bodyLength >> 8,
                $bodyLength & 0xff,
                $cmdId >> 8 ,
                $cmdId & 0xff,
                $seq >> 8 ,
                $seq & 0xff
            ];
            dump($bodyLength);
            $hStr = '';
            foreach ($header as $v){
                $hStr .= chr($v);
            }
            $sss = $hStr.$content;
            MqttUtil::dumpByte($content,'Playload Body');
            MqttUtil::dumpByte($sss,'With Header');
            $mqtt->publish_async('/0CRngr3ddpVzUBoeF', $sss, 0, 0);
        });
    }
    
    
    public function publishAction()
    {
        $this->template(function ($mqtt) {
            // $content = file_get_contents(public_path('test/ADDRESS_BOOK_FILE.FILE'));
            // $content = file_get_contents(public_path('test/bin_person.person'));\
            
            $PlaySmell = new \Proto2\Scentrealm\Simple\PlaySmell();
            $when = new \Proto2\Scentrealm\Simple\PlayStartTime();
            
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
            
            $bodyLength = strlen($content);
            $cmdId = \Proto2\Scentrealm\Simple\SrCmdId::SCI_req_playSmell;
            $seq = 1;
            $header = [
                0xfe,
                0x01,
                $bodyLength >> 8,
                $bodyLength & 0xff,
                $cmdId >> 8 ,
                $cmdId & 0xff,
                $seq >> 8 ,
                $seq & 0xff
            ];
            $hStr = '';
            foreach ($header as $v){
                $hStr .= chr($v);
            }
            $sss = $hStr.$content;
            MqttUtil::dumpByte($content,'Playload Body');
            MqttUtil::dumpByte($sss,'With Header');
            
            file_put_contents(public_path('proto/PlayAction.mqtt.payload'), $content);
            
            $mqtt->publish_async('/0CRngr3ddpVzUBoeF', $sss, 0, 0);
        });
    }
    
    public function publishPersonAction()
    {
        $this->template(function ($mqtt) {
            
            $AddressBook = new \Proto2\Tutorial\AddressBook();
            $Person = new \Proto2\Tutorial\Person();
            
            $Person->setEmail('person@qq.com');
            $Person->setId(123);
            $Person->setName('person name');
            
            $Phone = new \Proto2\Tutorial\Person_PhoneNumber();
            $Phone->setNumber('18764548772');
            $Phone->setType(\Proto2\Tutorial\Person_PhoneType::MOBILE);
            
            $Person->appendPhone($Phone);
            
            $AddressBook->appendPerson($Person);
            
    
            $content = $AddressBook->serializeToString();
    
            file_put_contents(public_path('proto/Person.mqtt.data'), $content);
    
            $bodyLength = strlen($content);
            $cmdId = 2222;
            $seq = 1;
            $header = [
                0xfe,
                0x01,
                $bodyLength >> 8,
                $bodyLength & 0xff,
                $cmdId >> 8 ,
                $cmdId & 0xff,
                $seq >> 8 ,
                $seq & 0xff
            ];
            $hStr = '';
            foreach ($header as $v){
                $hStr .= chr($v);
            }
            $sss = $hStr.$content;
            MqttUtil::dumpByte($content);
            MqttUtil::dumpByte($sss);
            $mqtt->publish_async('/0CRngr3ddpVzUBoeF', $sss, 0, 0);
        });
    }
    
}


