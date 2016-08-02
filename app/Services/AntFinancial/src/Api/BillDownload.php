<?php
namespace AntFinancial\Api;

use AntFinancial\BaseApi;

class BillDownload extends BaseApi
{
    
    protected $function = 'ant.ebank.bill.download.apply';
    
    /**
     * 参数验证规则
     *
     * @var unknown
     */
    protected $parameterValidate = [
        'tradeNo' => '',
    ];
    
    
    public static function Main() {
        
        $api = new static;
        
        $params = [
            'tradeNo' => '2016022610130010110000000011170000003704',
        ];
        
        $result = $api->run($params);
        
        $api->console($result);
    }

}