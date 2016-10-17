<?php
namespace App\Extensions\Mqtt;

use Proto2\Scentrealm\Simple\SrCmdId;

class MqttUtil
{
    /**
     * Console.color
     * @var unknown
     */
    const COLOR_BLACK = 30;
    const COLOR_RED = 31;
    const COLOR_GREEN = 32;
    const COLOR_YELLOW = 33;
    const COLOR_BLUE = 34;
    const COLOR_LIGHT_PURPLE = 35;
    const COLOR_LIGHT_BLUE = 36;
    
    /**
     * Header.const
     * @var unknown
     */
    const PROTO_HEADER_LENGRH = 8;
    const PROTO_HEADER_MAGIC_NUMBER = 0xfe;
    const PROTO_HEADER_VERSION = 0x01;
    
    /**
     * CmdID & Class Map
     * @var unknown
     */
    public static $CmdIDMap = [
        SrCmdId::SCI_req_mac => '',
        SrCmdId::SCI_resp_mac => '',
        SrCmdId::SCI_req_uptime => '',
        SrCmdId::SCI_resp_uptime => '',
        SrCmdId::SCI_req_downtime => '',
        SrCmdId::SCI_resp_downtime => '',
        SrCmdId::SCI_req_sleep => '',
        SrCmdId::SCI_resp_sleep => '',
        SrCmdId::SCI_req_wakeup => '',
        SrCmdId::SCI_resp_wakeup => '',
        SrCmdId::SCI_req_usedSeconds => '',
        SrCmdId::SCI_resp_usedSeconds => '',
        SrCmdId::SCI_req_playSmell => \Proto2\Scentrealm\Simple\PlaySmell::class,
        SrCmdId::SCI_resp_playSmell => '',
    ];
    
    
    /**
     * CmdID & Class Map
     * @param unknown $cmdID
     */
    public static function getDecodeClass($cmdID){
        
        $map = self::$CmdIDMap;
        return isset($map[$cmdID]) ? $map[$cmdID] : false;
    }
    

    public static function aesDecrypt($content, $key, $iv = '00000000000Pkcs7',$base64decode = true)
    {
        $key = md5($key); 
        $base64decode && $content = base64_decode($content);
        $decode = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $content, MCRYPT_MODE_CBC, $iv);
        return trim($decode);
    }

    public static function aesEncrypt($content, $key, $iv = '00000000000Pkcs7',$base64encode = true)
    {
        $key = md5($key); 
        $decode = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $content, MCRYPT_MODE_CBC, $iv);
        return $base64encode ? base64_encode($decode) : $decode;
    }

    public static function colorString($msg, $color)
    {
        return "\e[{$color}m{$msg}\e[0m";
    }
    
    /**
     * Output data Bytes in Dec Syntax
     * @param unknown $data
     * @param string $msg
     */
    public static function dumpHexDec($data,$msg = '')
    {
        $output = '';
        if($msg){
            $output .= "[ ".MqttUtil::colorString($msg,MqttUtil::COLOR_GREEN)." ] ".PHP_EOL;
        }
        for ($i = 0; $i < strlen($data); $i ++) {
            $output .= ' ' . sprintf('%3d',ord($data[$i]));
            if(($i + 1) % 10 == 0){
                $output .= "\t";
            }
            if(($i + 1) % 20 == 0){
                $output .= PHP_EOL;
            }
        }
        echo $output . PHP_EOL;
    }
    
    /**
     * Output data Bytes in Hex Syntax 
     * @param unknown $data
     * @param string $msg
     */
    public static function dumpByte($data,$msg = '')
    {
        $output = '';
        if($msg){
            $output .= "[ ".MqttUtil::colorString($msg,MqttUtil::COLOR_GREEN)." ] ".PHP_EOL;
        }
        for ($i = 0; $i < strlen($data); $i ++) {
            $output .= ' ' . sprintf('%02x',ord($data[$i]));
            if(($i + 1) % 10 == 0){
                $output .= "\t";
            }
            if(($i + 1) % 20 == 0){
                $output .= PHP_EOL;
            }
        }
        echo $output . PHP_EOL;
    }
    
    /**
     * decode ProtoData
     * @param unknown $msg 
     * @param unknown $class
     * @param number $headerLength
     * @param string $aes
     * @return unknown
     */
    public static function decodeProtoData($msg,$class,$headerLength = 0,$aes = false,$key = '', $iv = '' ){
        $packed = $msg;
        if($headerLength > 0 ){
            $packed = substr($msg, $headerLength);
            MqttUtil::dumpByte($packed,"Received Body Bytes");
        }
        
        try {
            if($aes){
                $decryptedPack = MqttUtil::aesDecrypt($packed,$key,$iv,false);
                MqttUtil::dumpByte($decryptedPack,"aesDecrypt Body Bytes");
                $obj = $class->parseFromString($decryptedPack);
                return $obj;
            }else{
                $obj = $class->parseFromString($packed);
                return $obj;
            }
        }catch (\Exception $e){
            dump("error decodeProtoData");
        }
        return false;
    }
    
    
    
    
    
    
}