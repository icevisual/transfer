<?php
namespace AntFinancial\Api;

use AntFinancial\BaseApi;
use AntFinancial\Sdk\HttpsMain;
use AntFinancial\Sdk\Util;

class BatchPay extends BaseApi
{
    
    protected $function = 'ant.ebank.transfer.batchpay.confirm';
    
    /**
     * 参数验证规则
     *
     * @var unknown
     */
    protected $parameterValidate = [
        'bizNo' => '',
        'smsCode' => '',
        'fileName' => '',
        'totalCount' => '',
        'totalAmount' => '',
        'currencyCode' => '',
        'remark' => '',
        'companyCardNo' => '',
    ];

    public static function Main() {
        
        $api = new static;
        
        if(HttpsMain::$sendSms){
            $SmsCodeSend = new SmsCodeSend();
            $bizNo = $SmsCodeSend->run([
                'bizNo' => $SmsCodeSend->getReqMsgId(),
                'bizName' => 'P_CROSS_TRANSFER',
                'certNo' => HttpsMain::$certNo,
            ]);
        }else{
            $bizNo = Util::timestampBizNo();
        }
        
        $params = [
            'bizNo' => $bizNo,
            'smsCode' => HttpsMain::$smsCode,
            'fileName' => 'h2h_batchPay_226610000051657147614_00000020160207163159613065788659.xls',
            'totalCount' => 20,
            'totalAmount' => Util::formatMoney(0.20),
            'currencyCode' => HttpsMain::$currencyCode,
            'remark' => '代发工资',
            'companyCardNo' => HttpsMain::$cardNo,
        ];
        
        $result = $api->run($params);
        
        $api->console($result);
    }

}