<?php
namespace AntFinancial\Api;

use AntFinancial\BaseApi;
use AntFinancial\Sdk\HttpsMain;
use AntFinancial\Sdk\Util;

class LineTrans extends BaseApi
{
    
    protected $function = 'ant.ebank.transfer.single.crosslinetrans';
    
    
    /**
     * 参数验证规则
     *
     * @var unknown
     */
    protected $parameterValidate = [
        'bizNo' => '',
        'smsCode' => '',
        'amt' => '',
        'currencyCode' => '',
        'payerCardNo' => '',
        'payerName' => '',
        'payeeAccountNo' => '',
        'payeeName' => '',
        'remark' => '',
    ];
    
    public static function Main() {
        
        $api = new static;
        
        $SmsCodeSend = new SmsCodeSend();
        $bizNo = $SmsCodeSend->run([
            'bizNo' => $SmsCodeSend->getReqMsgId(),
            'bizName' => 'P_INNER_TRANSFER',
            'certNo' => HttpsMain::$certNo,
        ]);
        $params = [
            'bizNo' => $bizNo,
            'smsCode' => HttpsMain::$smsCode,
            'amt' => Util::formatMoney("123"),
            'currencyCode' => HttpsMain::$currencyCode,
            'payerCardNo' => HttpsMain::$cardNo,
            'payerName' => '张三',
            'payeeAccountNo' => '8888886531660936',
            'payeeName' => '开发测试5',
            'remark' => '转账',
        ];
        
        
        $result = $api->run($params);
        
        $api->console($result);
    }

}