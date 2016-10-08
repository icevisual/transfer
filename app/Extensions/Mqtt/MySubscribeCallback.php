<?php
namespace App\Extensions\Mqtt;

use sskaje\mqtt\MQTT;
use sskaje\mqtt\MessageHandler;
use sskaje\mqtt\Message\PUBLISH;

class MySubscribeCallback extends MessageHandler
{

    public function aesDecrypt($content){
        $key = md5("1231231231231232"); // md5($text); //key的长度必须16，32位,这里直接MD5一个长度为32位的key
        $iv = '00000000000Pkcs7';
        $decode = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $content, MCRYPT_MODE_CBC, $iv);
        return trim($decode);
    }
    
    
    public function publish(MQTT $mqtt, PUBLISH $publish_object)
    {
        
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
        
        if($headerBytes[0] == 0xfe){
            $packed = substr($msg, $headerLength);
            
            $decryptedPack = $this->aesDecrypt($packed);
            
            $AuthRequest = new \Proto2\Scentrealm\AuthRequest();
            $obj = $AuthRequest->parseFromString($decryptedPack);
            dump($obj);
            $AuthRequest->dump();
        }
        
//         ord($string)
        
        printf(
            "\e[32mI got a message\e[0m:(msgid=%d, QoS=%d, dup=%d, topic=%s) \e[32m%s\e[0m\n",
            $publish_object->getMsgID(),
            $publish_object->getQos(),
            $publish_object->getDup(),
            $publish_object->getTopic(),
            $publish_object->getMessage()
        );
    }
}
