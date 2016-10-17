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
        MqttUtil::info( sprintf(
            "(msgid=%d, QoS=%d, dup=%d, topic=%s)\n",
            $publish_object->getMsgID(),
            $publish_object->getQos(),
            $publish_object->getDup(),
            $publish_object->getTopic()
        ));
        $msg = $publish_object->getMessage();
        
        $header = MqttUtil::analyzeHeader($msg);
        MqttUtil::dumpHexDec($msg,"Received Bytes Dec");
        MqttUtil::dumpByte($msg,"Received Bytes");
        
        if(false === $header){
            MqttUtil::info('Header Not Match'); 
            MqttUtil::info($msg);
        }else{
            MqttUtil::dumpByte(substr($msg, 0,MqttUtil::PROTO_HEADER_LENGRH),"HeaderBytes Bytes");
            MqttUtil::dump($header);
            if(MqttUtil::isProtoAvaliable()){
            
                if($cmdID == \Proto2\Scentrealm\Simple\SrCmdId::SCI_req_playSmell){
                    $class = new \Proto2\Scentrealm\Simple\PlaySmell();
                }
                $ret = MqttUtil::decodeProtoData($msg, $class,$headerLength);
                MqttUtil::dump($ret);
                $class->dump();
            }
        }
    }
    
}
