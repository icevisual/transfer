<?php
namespace AntFinancial;

use AntFinancial\Api\BalanceQuery;
use AntFinancial\Api\BatchPay;
use AntFinancial\Api\BatchPayQuery;
use AntFinancial\Api\SmsCodeSend;
use AntFinancial\Services\BatchPayServices;
use AntFinancial\Sdk\SFTPUtil;
use AntFinancial\Sdk\AntFinancial\Sdk;

class Entrance
{

    public static function Main()
    {
//         SmsCodeSend::Main();
//         exit;
//         dump('---------');
        BatchPayServices::Main();
    }
}