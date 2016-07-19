<?php
namespace App\Services\MyBank;

use App\Services\MyBank\SDK\Sign;

class BalanceQuery
{

    public static function main()
    {
        $function = 'ant.ebank.acount.balance.query';
        
        $form = [
            'function' => $function,
            'reqTime' => '2016-07-09 11:03:10.125',
            'reqMsgId' => CommonUtil::uuid(),
            'cardNo' => HttpsMain::$cardNo,
            'currencyCode' => HttpsMain::$currencyCode,
            'cashExCode' => 'CSH'
        ];
        
        $sign = Sign::sign("cardNo=" . $form['cardNo'] . "||currencyCode=" . $form['cardNo'] . "||cashExCode=" . $form['cardNo'], false);
        $form['sign'] = $sign;
        edump($form);
    }
}