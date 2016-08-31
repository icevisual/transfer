<?php
namespace App\Http\Controllers\Iot;

use App\Http\Controllers\Controller;
use Iot\Request\V20160530 as Iot;

class IotController extends Controller
{
    
    public function server(){
        $message = \Input::get('data');
        $messageJsonArray = json_decode($message,1);
        $data = [
            'message' => array_get($messageJsonArray, 'message'),//message
            'topic' => array_get($messageJsonArray, 'topic'),//topic
            'sign' => array_get($messageJsonArray, 'sign'),//sign= md5_32(productKey+(message)+productSecret)
            'messageId' => array_get($messageJsonArray, 'messageId'),//messageId
            'appKey' => array_get($messageJsonArray, 'appKey'),//appKey
            'deviceId' => array_get($messageJsonArray, 'deviceId'),//deviceId
        ];
        $sql = createInsertSql('op_topic_msg', $data);
        \DB::insert($sql);
    }
    
    
    public function index(){
        
    }
    
}