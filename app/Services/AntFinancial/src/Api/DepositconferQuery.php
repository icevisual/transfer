<?php
namespace AntFinancial\Api;

use AntFinancial\BaseApi;

class DepositconferQuery extends BaseApi
{
    
    protected $function = 'ant.ebank.depositconfer.query';
    
    /**
     * 参数验证规则
     *
     * @var unknown
     */
    protected $parameterValidate = [
    ];
    
    public static function Main() {
        
        $api = new static;
        
        $params = [
        ];
        
        $result = $api->run($params);
        
        $api->console($result);
    }

}