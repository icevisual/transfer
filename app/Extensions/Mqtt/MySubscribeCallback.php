<?php
namespace App\Extensions\Mqtt;

use sskaje\mqtt\MQTT;
use sskaje\mqtt\MessageHandler;
use sskaje\mqtt\Message\PUBLISH;

class MySubscribeCallback extends MessageHandler
{

    public function aesDecrypt($content){
        $key = md5("XqCEMSzhsdWHfwhm"); // md5($text); //key的长度必须16，32位,这里直接MD5一个长度为32位的key
        $iv = '00000000000Pkcs7';
        $decode = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $content, MCRYPT_MODE_CBC, $iv);
        return trim($decode);
    }
    
    public function dumpByte($string){
        $output = '';
        for($i = 0 ; $i < strlen($string) ; $i ++){
            $output .= ' '.ord($string[$i]);
        }
        echo $output.PHP_EOL;
    }
    
    public function decodeProtoData($msg,$class,$headerLength = 0,$aes = false){
        $packed = $msg;
        if($headerLength > 0 ){
            $packed = substr($msg, $headerLength);
            dump("Received Body Bytes");
            $this->dumpByte($packed);
        }
        if($aes){
            $decryptedPack = $this->aesDecrypt($packed);
            dump("aesDecrypt Body Bytes");
            $this->dumpByte($decryptedPack);
            $obj = $class->parseFromString($decryptedPack);
            return $obj;
        }else{
            $obj = $class->parseFromString($packed);
            return $obj;
        }
    }
    
    public function publish(MQTT $mqtt, PUBLISH $publish_object)
    {
        
        printf(
            "\e[32mI got a message\e[0m:(msgid=%d, QoS=%d, dup=%d, topic=%s) \e[32m%s\e[0m\n",
            $publish_object->getMsgID(),
            $publish_object->getQos(),
            $publish_object->getDup(),
            $publish_object->getTopic(),
            $publish_object->getMessage()
        );
        
        
        $msg = $publish_object->getMessage();
        

        $headerLength = 8 ;
        $headerBytes = [];
        $packed = '';
//         $bytes[] = ord($msg[$i]);
        for($i = 0 ; $i < strlen($msg) ; $i ++){
            if($i < $headerLength){
                $headerBytes[] = ord($msg[$i]);
            }else{
//                 \Proto2\Scentrealm::class
            }
        }
        dump("Received Bytes");
        $this->dumpByte($msg);
        if($headerBytes[0] == 0xfe){
            $class = new \Proto2\Scentrealm\Simple\PlaySmell();
            $ret = $this->decodeProtoData($msg, $class,$headerLength);
            dump($obj);
            $class->dump();
        }
        
    }
}
