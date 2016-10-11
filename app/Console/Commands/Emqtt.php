<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Extensions\Mqtt\BluerhinosMqtt;
use BinSoul\Net\Mqtt\Flow\OutgoingConnectFlow;
use BinSoul\Net\Mqtt\Packet\ConnectRequestPacket;
use sskaje\mqtt\MQTT;
use sskaje\mqtt\Debug;
use sskaje\mqtt\MessageHandler;

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

    public function aesDecrypt($content)
    {
        $key = md5("XqCEMSzhsdWHfwhm"); // md5($text); //key的长度必须16，32位,这里直接MD5一个长度为32位的key
        $iv = '00000000000Pkcs7';
        $content = base64_decode($content);
        $decode = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $content, MCRYPT_MODE_CBC, $iv);
        return trim($decode);
    }

    public function aesEncrypt($content)
    {
        $key = md5("XqCEMSzhsdWHfwhm"); // md5($text); //key的长度必须16，32位,这里直接MD5一个长度为32位的key
        $iv = '00000000000Pkcs7';
        $decode = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $content, MCRYPT_MODE_CBC, $iv);
        return base64_encode($decode);
    }

    public function dumpByte($string)
    {
        $output = '';
        for ($i = 0; $i < strlen($string); $i ++) {
            $output .= ' ' . ord($string[$i]);
        }
        echo $output . PHP_EOL;
    }

    public function pt($msg, $colr)
    {
        echo "\e[" . $colr . "m" . $msg . "\e[0m" . PHP_EOL;
    }

    public function base64Decode($encode)
    {
        $base = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
        $segmentsLength = strlen($encode) / 4;
        $map = array_flip(str_split($base, 1));
        $ret = "";
        for ($i = 0; $i < $segmentsLength; $i ++) {
            $str = substr($encode, $i * 4, 4);
            if ($i == $segmentsLength - 1) {
                $str = trim($str, '=');
                if (strlen($str) == 2) {
                    $a = chr($map[$str[0]] << 2 | $map[$str[1]] >> 4);
                } else 
                    if (strlen($str) == 3) {
                        $a = "12";
                        $a{0} = chr($map[$str[0]] << 2 | $map[$str[1]] >> 4);
                        $a{1} = chr(($map[$str[1]] & 0x0f) << 4 | $map[$str[2]] >> 2);
                    } else {
                        $a = "123";
                        $a{0} = chr($map[$str[0]] << 2 | $map[$str[1]] >> 4);
                        $a{1} = chr(($map[$str[1]] & 0x0f) << 4 | $map[$str[2]] >> 2);
                        $a{2} = chr(($map[$str[2]] & 0x01) << 6 | $map[$str[3]]);
                    }
            } else {
                $a = "123";
                $a{0} = chr($map[$str[0]] << 2 | $map[$str[1]] >> 4);
                $a{1} = chr(($map[$str[1]] & 0x0f) << 4 | $map[$str[2]] >> 2);
                $a{2} = chr(($map[$str[2]] & 0x01) << 6 | $map[$str[3]]);
            }
            $ret .= $a;
        }
        
        return $ret;
    }

    function c_base64_encode($src)
    {
        static $base = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
        
        // //将原始的3个字节转换为4个字节
        $slen = strlen($src);
        $smod = ($slen % 3);
        $snum = floor($slen / 3);
        
        $desc = array();
        
        for ($i = 0; $i < $snum; $i ++) {
            // //读取3个字节
            $_arr = array_map('ord', str_split(substr($src, $i * 3, 3)));
            
            // /计算每一个base64值
            $_dec0 = $_arr[0] >> 2;
            $_dec1 = (($_arr[0] & 3) << 4) | ($_arr[1] >> 4);
            $_dec2 = (($_arr[1] & 0xF) << 2) | ($_arr[2] >> 6);
            $_dec3 = $_arr[2] & 63;
            
            $desc = array_merge($desc, array(
                $base[$_dec0],
                $base[$_dec1],
                $base[$_dec2],
                $base[$_dec3]
            ));
        }
        
        if ($smod == 0)
            return implode('', $desc);
            
            // /计算非3倍数字节
        $_arr = array_map('ord', str_split(substr($src, $snum * 3, 3)));
        $_dec0 = $_arr[0] >> 2;
        // /只有一个字节
        if (! isset($_arr[1])) {
            $_dec1 = (($_arr[0] & 3) << 4);
            $_dec2 = $_dec3 = "=";
        } else {
            // /2个字节
            $_dec1 = (($_arr[0] & 3) << 4) | ($_arr[1] >> 4);
            $_dec2 = $base[($_arr[1] & 7) << 2];
            $_dec3 = "=";
        }
        $desc = array_merge($desc, array(
            $base[$_dec0],
            $base[$_dec1],
            $_dec2,
            $_dec3
        ));
        return implode('', $desc);
    }

    public function printAction()
    {
        $str = "sdfdfgfhhgdfhdjh";
        dump($str);
        $encode = base64_encode($str);
        dump($encode);
        dump($this->c_base64_encode($str));
        dump($this->base64Decode($encode));
        
        exit();
        
        $color = [
            30 => 'BLACK',
            'RED',
            'GREEN',
            'YELLOW',
            'BLUE',
            'LIGHT_PURPLE',
            'LIGHT_BLUE'
        ];
        
        for ($i = 30; $i < 38; $i ++)
            $this->pt("Color Number " . $i, $i);
    }

    public function aesAction()
    {
        $text = "123456dsfdfas789";
        $key = md5("XqCEMSzhsdWHfwhm"); // md5($text); //key的长度必须16，32位,这里直接MD5一个长度为32位的key
        $iv = '00000000000Pkcs7';
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CBC, $iv);
        $decode = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $crypttext, MCRYPT_MODE_CBC, $iv);
        dump($this->aesEncrypt($text));
        dump(base64_encode($crypttext));
        dump(trim($decode));
        dump($this->dumpByte($crypttext));
        // dump(base64_encode(openssl_encrypt($text, 'aes-128-cbc', $key, OPENSSL_ZERO_PADDING, $iv)));
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

    public function publishAction()
    {
        $this->template(function ($mqtt) {
            // $content = file_get_contents(public_path('test/ADDRESS_BOOK_FILE.FILE'));
            // $content = file_get_contents(public_path('test/bin_person.person'));\
            
            $PlaySmell = new \Proto2\Scentrealm\Simple\PlaySmell();
            $when = new \Proto2\Scentrealm\Simple\PlayStartTime();
            
            $startAt = new \Proto2\Scentrealm\Simple\TimePoint();
            $startAt->setMode(\Proto2\Scentrealm\Simple\SrTimeMode::STM_absolute);
            $startAt->setValue(295);
            $startAt->setEndValue(296);
            
            $startAt1 = new \Proto2\Scentrealm\Simple\TimePoint();
            $startAt1->setMode(\Proto2\Scentrealm\Simple\SrTimeMode::STM_monthday);
            $startAt1->setValue(11);
            $startAt1->setEndValue(12);
            
            $when->setCycleMode(\Proto2\Scentrealm\Simple\SrCycleMode::SCM_cycle_no);
            $when->setCycleTime(0);
            $when->appendStartAt($startAt);
            $when->appendStartAt($startAt1);
            
            $PlaySmell->setWhen($when);
            
            $PlayAction = new \Proto2\Scentrealm\Simple\PlayAction();
            
            $PlayAction->setBottle("0000000b1");
            $PlayAction->setBeforeStart(222);
            $PlayAction->setCycleMode(\Proto2\Scentrealm\Simple\SrCycleMode::SCM_cycle_no);
            $PlayAction->setCycleTime(0);
            $PlayAction->setDuration(2222);
            $PlayAction->setInterval(0);
            $PlayAction->setPower(5);
            
            $PlaySmell->appendPlay($PlayAction);
            
            $content = $PlaySmell->serializeToString();
            
            // var len = SmellOpen.utils.ten2sixteen(payloadByteLength + headerLength);
            // var cmd = SmellOpen.utils.ten2sixteen(cmdId);
            // var seq = SmellOpen.utils.ten2sixteen(seqId);
            // var header = [ 0xfe, 0x01, len[0], len[1], cmd[0], cmd[1], seq[0],
            // seq[1] ];
            $bodyLength = strlen($content);
            $cmdId = \Proto2\Scentrealm\Simple\SrCmdId::SCI_req_playSmell;
            $seq = 1;
            $header = [
                0xfe,
                0x01,
                $bodyLength >> 4,
                $bodyLength & 0x0f,
                $cmdId >> 4 ,
                $cmdId & 0x0f,
                $seq >> 4 ,
                $seq & 0x0f
            ];
            $hStr = '';
            foreach ($header as $v){
                $hStr .= $v;
            }
            $sss = $hStr.$content;
            
            dumpByte($sss);
            $mqtt->publish_async('/0CRngr3ddpVzUBoeF', $sss, 0, 0);
        });
    }
}


