<?php
namespace AntFinancial\Api;

use AntFinancial\BaseApi;
use AntFinancial\Sdk\HttpsMain;

class SmsCodeSend extends BaseApi
{
    
    protected $function = 'ant.ebank.auth.smscode.send';
    
    protected $parameterValidate = [
        'bizNo' => '',
        'bizName' => '',
        'certNo' => '',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function resultFormat($return){
        return $return['bizNo'];
    }
    
    public static function Main() {
        
        $api = new static;
        
        $params = [
            'bizNo' => $api->getReqMsgId(),
            'bizName' => 'P_INNER_TRANSFER',
            'certNo' => HttpsMain::$certNo,
        ];
        
        $result = $api->run($params);
        
        $api->console($result);
    }

}