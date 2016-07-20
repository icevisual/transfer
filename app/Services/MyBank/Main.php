<?php
namespace App\Services\MyBank;

class Main
{

    public static function init()
    {
        defined('_MYBANK_ROOT_') or define('_MYBANK_ROOT_', __DIR__);
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        require_once (_MYBANK_ROOT_ . DS . 'PHPJavaBridge' . DS . 'Java.inc'); //
    }
}