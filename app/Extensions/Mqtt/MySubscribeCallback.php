<?php
namespace App\Extensions\Mqtt;

use sskaje\mqtt\MQTT;
use sskaje\mqtt\MessageHandler;
use sskaje\mqtt\Message\PUBLISH;

class MySubscribeCallback extends MessageHandler
{

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
        $packed = substr($msg, $headerLength);
        
        $AuthRequest = new \Proto2\Scentrealm\AuthRequest();
        $obj = $AuthRequest->parseFromString($packed);
        
        dump($obj);
        
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
