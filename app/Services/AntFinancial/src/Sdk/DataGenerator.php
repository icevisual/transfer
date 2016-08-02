<?php
namespace AntFinancial\Sdk;

use AntFinancial\Sdk\HttpsMain;
use AntFinancial\Sdk\Util;

class DataGenerator
{

    public function dataSourceName()
    {
        $array = [
            '张三',
            '李四',
            '王五',
            '镇流',
            '飞远',
            '佚名',
            '古荡',
        ];
        $content = $this->arrayRandom($array);
//         $content = randomChineseName(2);
        return iconv(detect_encoding($content), 'UTF-8', $content);
    }

    public function dataSourceCard()
    {
        $str = file_get_contents(ant_test_path('cards'));
        static $_cached = [];
        $array = preg_split('/\s+/', trim($str));
        
        $ret = '';
        
        $i = 0 ;
        
        $max = 500;
        while (isset($_cached[$ret = $this->arrayRandom($array)])){
        	$i ++;
        	if($i > $max) break; 
        }
        $_cached[$ret] = 1;
        return $ret;
    }

    public function dataSourcenBank()
    {
        $bank = '中国银行';
        $content = randomChineseName(2);
        return iconv(detect_encoding($bank), 'UTF-8', $bank);
        $array = HttpsMain::getBanks();
        return $this->arrayRandom($array);
    }

    public function arrayRandom($array, $num = 1)
    {
        return $array[array_rand($array, $num)];
    }

    public function dataSourceNameCardBank($num = 0)
    {
        $count = $num ? $num : mt_rand(10, 20) ;
        $ret = [];
        for ($i = 0; $i < $count; $i ++) {
            $ret[] = [
                'card_no' => $this->dataSourceCard(),
                'truename' => $this->dataSourceName(),
                'bank_name' => $this->dataSourcenBank(),
                'amount' => mt_rand(100,999),
                'bank_no' => '',
                'payee_name' => '',
                'identity' => '',
                'phone' => '',
                'alipay_no' => '',
                'note' => '',
                'pay_id' => Util::formatBizNo($i + 1,16)
            ];
        }
        return $ret;
    }
    
    public function getCachedDs($bizNo,$num = 0){
        $ret = \Cache::get($bizNo);
        if(!$ret){
            $ret = $this->dataSourceNameCardBank($num);
            \Cache::put($bizNo, $ret, 20);
        }
        return $ret ;
    }
    
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new static;
        }
        return self::$instance;
    }
    
    private  static $instance = false;
    
    public static function run()
    {
        $instance = self::getInstance();
        return $instance->dataSourceNameCardBank();
    }
    
    
}
