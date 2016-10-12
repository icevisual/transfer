<?php
namespace App\Extensions\Mqtt;

class MqttUtil
{
    const COLOR_BLACK = 30;
    const COLOR_RED = 31;
    const COLOR_GREEN = 32;
    const COLOR_YELLOW = 33;
    const COLOR_BLUE = 34;
    const COLOR_LIGHT_PURPLE = 35;
    const COLOR_LIGHT_BLUE = 36;

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
    
    public static function dumpByte($data,$msg = '')
    {
        $output = '';
        if($msg){
            $output .= "[ ".MqttUtil::colorString($msg,MqttUtil::COLOR_GREEN)." ] ";
        }
        for ($i = 0; $i < strlen($data); $i ++) {
            $output .= ' ' . ord($data[$i]);
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