<?php
namespace AntFinancial\Api;

use AntFinancial\BaseApi;
use AntFinancial\Sdk\HttpsMain;

class BalanceQuery extends BaseApi
{
    
    protected $function = 'ant.ebank.acount.balance.query';
    
    /**
     * 参数验证规则
     *
     * @var unknown
     */
    protected $parameterValidate = [
        'cardNo' => '',
        'currencyCode' => '',
        'cashExCode' => ''
    ];
    
    public static function Main() {
        
        $api = new static;
        
        $params = [
            'cardNo' => HttpsMain::$cardNo,
            'currencyCode' => HttpsMain::$currencyCode,
            'cashExCode' => 'CSH',
        ];
        
        $result = $api->run($params);
        
        $api->console($result);
    }

}