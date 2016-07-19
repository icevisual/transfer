<?php
namespace App\Services\MyBank;

class CommonUtil
{

    public static function uuid(){
        list ($m, $t) = microtime();
        return md5($m . mt_rand() . $t);
    }
    
}
