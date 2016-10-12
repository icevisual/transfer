<?php
namespace App\Extensions\Mqtt;

use sskaje\mqtt\MQTT;
use sskaje\mqtt\MessageHandler;
use sskaje\mqtt\Message\PUBLISH;
use App\Extensions\Mqtt\MqttUtil;

class MySubscribeCallback extends MessageHandler
{

    public function publish(MQTT $mqtt, PUBLISH $publish_object)
    {
        
        printf(
            "\e[32m".now()."\e[0m:(msgid=%d, QoS=%d, dup=%d, topic=%s) \e[32m%s\e[0m\n",
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
        MqttUtil::dumpByte($msg);
        if($headerBytes[0] == 0xfe){
            $class = new \Proto2\Scentrealm\Simple\PlaySmell();
            $ret = $this->decodeProtoData($msg, $class,$headerLength);
            dump($ret);
            $class->dump();
        }
        
    }
}
