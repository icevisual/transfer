<?php
namespace AntFinancial\Api;

use AntFinancial\BaseApi;

class FundQuery extends BaseApi
{
    
    protected $function = 'ant.ebank.fund.query';
    
    protected $parameterValidate = [
        'queryStartDate' => '',
        'queryEndDate' => '',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function resultFormat($return){
        return $this->base64JsonDecode($return['priceDetailInfoList']);
    }
    
    
    public static function Main() {
        
        $api = new static;
        
        $params = [
            'queryStartDate' => '2016-02-01',
            'queryEndDate' => '2016-02-29',
        ];
        
        $result = $api->run($params);
        
        $api->console($result);
    }

}