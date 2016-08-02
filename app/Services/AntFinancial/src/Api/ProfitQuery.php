<?php
namespace AntFinancial\Api;

use AntFinancial\BaseApi;
use AntFinancial\Sdk\HttpsMain;

class ProfitQuery extends BaseApi
{
    
    protected $function = 'ant.ebank.fund.profitquery';
    
    /**
     * 参数验证规则
     *
     * @var unknown
     */
    protected $parameterValidate = [
        'fundChannel' => '',
        'cardNo' => ''
    ];
    
    public static function Main() {
        
        $api = new static;
        
        $params = [
            'fundChannel' => HttpsMain::$channel,
            'cardNo' => HttpsMain::$cardNo
        ];
        
        $result = $api->run($params);
        
        $api->console($result);
    }

}