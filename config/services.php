<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    
    /**
     *云通讯配置项
     */
    'yuntongxun' => array(
        '_ACCOUNT_SID_'            => '8a48b5514f73ea32014f8195a47b14b6',    //主帐号
        '_ACCOUNT_TOKEN_'          => 'a0bc7537cba642a49e4c204fc6bebd6d',    //主帐号Token
        '_APP_ID_'                 => '8aaf070856bbb3c50156c0c2dd970567',    //应用Id
        '_SERVER_IP_'              => 'app.cloopen.com',             //请求地址，格式如下，不需要写https://
        '_SERVER_PORT_'            => '8883',                               //请求端口
        '_SOFT_VERSION_'           => '2013-12-26',                         //REST版本号
    ),

];
