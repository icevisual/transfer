<?php
namespace AntFinancial\Api;

use AntFinancial\BaseApi;
use AntFinancial\Sdk\HttpsMain;

class FundHisTransQuery extends BaseApi
{
    
    protected $function = 'ant.ebank.fund.histranquery';
    
    /**
     * 参数验证规则
     *
     * @var unknown
     */
    protected $parameterValidate = [
        'queryStartDate' => '',
        'queryEndDate' => '',
        'pageSize' => '',
        'pageIndex' => '',
        'fundChannel' => '',
        'cardNo' => '',
        'transTypes' => '',
        'transStatuses' => '',
    ];
    
    public static function Main() {
        
        $api = new static;
        
        $params = [
            'queryStartDate' => '2016-02-01',
            'queryEndDate' => '2016-02-29',
            'pageSize' => '10',
            'pageIndex' => '1',
            'fundChannel' => HttpsMain::$channel,
            'cardNo' => HttpsMain::$cardNo,
            'transTypes' => '11,12,13,14,15',
            'transStatuses' => 'inprocess,success,failure',
        ];
        
        $result = $api->run($params);
        
        $api->console($result);
    }

}