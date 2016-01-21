<?php
return [
    
    
    'calculation_precision' => 2, //基金额计算精度
    
    'token_expire' => 2592000, // token失效时间
    
    'sms' => [
        'expire' => 600, // 登录验证码失效时间(s)
        'num' => 5, // 每天可获取登录验证码的次数(次数)
        'period' => 60 // 获取验证码的时间间隔(s)
    ],
    'sms_content' => [
        'login' => '[1]（登录验证码，请于10分钟内完成验证操作）如非本人操作，请忽略本短信。'
    ]
];
