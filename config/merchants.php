<?php
return [
    /**
     * 前置机开放端口
     */
    'port' => '8989',
    /**
     * 前置机IP
     */
    'ip_address' => '60.190.243.130',
    /**
     * 登录用户名
     */
    'login_name' => '王勇W',
    
    /**
     * 查询交易结果模式
     * = sync 同步，直接等待6秒后调用结果查询接口
     * = schedule 每分钟查询，走队列
     * = curl 通过curl等待一秒
     * = fsockopen
     */
    'check_result_mode' => 'sync',
    
    // 'port' => '8080',
    // 'ip_address' => 'getOnlineIp',
    // 'login_name' => '银企直连专用集团1',
    
    'GetAccInfo' => [
        'SDKACINFX' => [
            'BBKNBR' => '', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
            'C_BBKNBR' => '杭州', // 分行名称 Z(1,62) 附录A.1 是
            'ACCNBR' => '571908952410602'
        ] // 账号 C(1,35) 否

    ],
    
    'DCPAYMENT' => [
        'SDKPAYRQX' => [
            'BUSCOD' => 'N02031', // 业务类别 C(6) N02031:直接支付N02041:直接集团支付
            'BUSMOD' => '00001'
        ] // 业务模式编号 C(5) 默认为00001
,
        'DCOPDPAYX' => [
            'DBTACC' => '571908952410602', // 否 付方帐号 企业用于付款的转出帐号，该帐号的币种类型必须与币种字段相符。
            'DBTBBK' => '57', // 否 付方开户地区代
            'CCYNBR' => '10', // 否 币种代码
            'STLCHN' => 'N', // 否 结算方式代码 只对跨行交易有效 N：普通 F：快速
            'BNKFLG' => 'Y'
        ]
    ] // 否 系统内外标志 Y：招行；N：非招行；
,
    
    /**
     * 直接支付配置信息
     */
    'AgentRequest' => [ // 4.2直接代发代扣 参数
        'SDKATSRQX' => [
            'BUSCOD' => 'N03020', // 业务类别 C(6) N03010:代发工资N03020:代发N03030:代扣 否 默认为代发工资: N03010
            'BUSMOD' => '00001', // 业务模式编号 C(5) 否 编号和名称填写其一，填写编号则名称无效。可通过前置机或者查询可经办的业务模式信息（ListMode）获得，必须使用无审批的业务模式
            'MODALS' => '', // 业务模式名称 Z(62)
            'C_TRSTYP' => '代发劳务收入', // 交易代码名称 Z 附录A.45 否 为空时默认BYSA：代发工资，代发和代扣时必填，可通过4.1获得可以使用的交易代码，也可以通过前置机获取。
            'TRSTYP' => 'BYBC', // 交易代码 C(4)
            'EPTDAT' => '', // 期望日期 D 可 不用填写，不支持期望日直接代发
            'DBTACC' => '571908952410602', // 转出账号/转入账号 C(35) 否 代发为转出账号；代扣为转入账号
            'BBKNBR' => '59', // 分行代码 C(2) 附录A.1 否 代码和名称不能同时为空。同时有值时BBKNBR有效。
            'BANKAREA' => '', // 分行名称 附录A.1
                              // 'SUM' => '12.19', // 总金额 M 否
                              // 'TOTAL' => '1', // 总笔数 N(4) 否
            'CCYNBR' => '10', // 币种代码 N(2) 附录A.3 可 默认10：人民币 同时有值时CCYNBR有效。
            'CURRENCY' => '人民币', // 币种名称 Z(1,10) 附录A.3
                                 // 'YURREF' => 'I' . createSerialNum(), // 业务参考号 C(1,30) 否
            'MEMO' => '代发劳务收入'
        ]
    ], // 用途 Z(1,42) 否
    
    /**
     * 网银贷记配置
     */
    'NTIBCOPR' => [ // 网银互联2网银贷记
        'NTFCH1' => 1, // 通知方式一 手机短信 开关
        'NTFCH2' => 0, // 通知方式二 邮箱 开关
        
        'NTIBCOPRX' => [
            'BBKNBR' => 'CB', // 付款账号银行号 C(2) 否
            'ACCNBR' => '571908952410602', // 付款账号 C(35) 否 我行账号
            'CNVNBR' => '0000001136', // 协议号 C(10) 否 贷记内部协议号
            'CCYNBR' => '10', // 币种 C(2) 附录 A.3 否
            'TRSTYP' => 'C210', // 业务类型编码 C(4) 附录A.49 否
            'TRSCAT' => '09001'
        ], // 业务种类编码 C(5) 否
        'NTOPRMODX' => [
            'BUSMOD' => '00001'
        ]
    ]
];


