<?php
namespace App\Http\Controllers;

class MerchantsPayTestController extends Controller
{

    protected $FBSdk;

    public function __construct()
    {
        
        set_time_limit(0);
        
        ini_set('memory_limit', '128M');
        $config = [
            'ip_address' => '60.190.243.140',
            'port' => '8989',
            'login_name' => '商务人力',
        ];
        $config = [];
        $this->FBSdk = \App\Services\Merchants\FBSdkService::getInstance($config);
    }
    
    public function getCombination(){
        $this->combinationTest();
    }
    
    
    public function combinationTest(){
        set_time_limit(60);
        $FBSdk = $this->FBSdk;
        $BUSCOD_Array = [
            'N01010' => '账务查询',
            'N03010' => '代发代扣',
            'N03020' => '代发工资',
        ];
        
        foreach ($BUSCOD_Array as $BUSCOD => $desc){
            // Get
            $NTQMDLSTZ = $FBSdk->D16_ListMode('N03010');
            $BUSMOD = '00001';
            if(isset($NTQMDLSTZ['NTQMDLSTZ'][0])){
                $BUSMOD = $NTQMDLSTZ['NTQMDLSTZ'][0]['BUSMOD'];
            }else{
                $BUSMOD = $NTQMDLSTZ['NTQMDLSTZ']['BUSMOD'];
            }
            dump($desc);
            dump($FBSdk->D21_ListAccount($BUSMOD,$BUSCOD));
        }
    }
    
    

    protected function getD42Resource()
    {
        return [
            [
                'ACCNBR' => '6222620170003931032', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
                'CLTNAM' => '金燕林', // 户名 Z(1,62) 否
                'BNKFLG' => 'N', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
                'EACBNK' => '交通银行', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
                'EACCTY' => '杭州'
            ], // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
            [
                'ACCNBR' => '623061571013816231', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
                'CLTNAM' => '金燕林', // 户名 Z(1,62) 否
                'BNKFLG' => 'N', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
                'EACBNK' => '杭州银行', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
                'EACCTY' => '杭州'
            ], // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
            [
                'ACCNBR' => '6228580699005714769', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
                'CLTNAM' => '金燕林', // 户名 Z(1,62) 否
                'BNKFLG' => 'N', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
                'EACBNK' => '浙江省农村信用社', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
                'EACCTY' => '杭州'
            ]
        ] // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填

        ;
    }

    protected function getD36Resource()
    {
        // 郑慧 13588346134 6214835711623411 招商银行股份有限公司
        // 杨扬 15158133652 6214855710279726 招商银行股份有限公司
        return [
//             [
//                 'CRTACC' => '6222600170008404351' ,// 收方帐号 N（35） 否 收款企业的转入帐号，该帐号的币种类型必须与币种字段相符。
//                 'CRTNAM' => '金燕林' ,// 收方帐户名 Z（62） 否 收款方企业的转入帐号的帐户名称。
//                 'BRDNBR' => '', // 收方行号 C(30) 可 人行自动支付收方联行号
//                 'BNKFLG' => 'N', // 系统内外标志 Y：招行；N：非招行； 否
//                 'CRTBNK' => '交通银行', // 收方开户行 Z（62） 跨行支付（BNKFLG=N）必填 可
//                 'CTYCOD' => '57', // 城市代码 C(4) 附录A.18 CRTFLG不为Y时行内支付必填。 可 行内支付填写，为空则不支持收方识别功能。
//                 'CRTADR' => '杭州', // 收方行地址 Z(62) 跨行支付（BNKFLG=N）必填；CRTFLG不为Y时行内支付必填。 可 例如：广东省深圳市南山区
//                 'CRTFLG' => '', // 收方信息不检查标志 C(1) Y: 行内支付不检查城市代码和收方行地址 默认为Y。 可
//                 'NTFCH1' => '', // 收方电子邮件 C（36） 可 收款方的电子邮件地址，用于交易 成功后邮件通知。
//                 'NTFCH2' => '18767135775', // 收方移动电话 C（16） 可 收款方的移动电话，用于交易 成功后短信通知。
//                 'CRTSQN' => 'UID00001', // 收方编号 C（20） 可 用于标识收款方的编号。非受限收方模式下可重复。
//                 'TRSTYP' => '', // 业务种类 C(6) 100001=普通汇兑 101001=慈善捐款 101002 =其他 默认100001 可
//                 'RCVCHK' => '1', // 行内收方账号户名校验 C(1) 1：校验 空或者其他值：不校验 可 如果为1，行内收方账号与户名不相符则支付经办失败。
//                 'RSV28Z' => ''
                
//             ], // 交通银行
            [
                'CRTACC' => '6214855710279726', // 收方帐号 N（35） 否 收款企业的转入帐号，该帐号的币种类型必须与币种字段相符。
                'CRTNAM' => '杨扬'
            ], // 收方帐户名 Z（62） 否 收款方企业的转入帐号的帐户名称。
            [
                'CRTACC' => '6214835711623411', // 收方帐号 N（35） 否 收款企业的转入帐号，该帐号的币种类型必须与币种字段相符。
                'CRTNAM' => '郑慧'
            ]
        ]; // 收方帐户名 Z（62） 否 收款方企业的转入帐号的帐户名称。

        
    }

    protected function getI2Resource()
    {
        
        // 金燕林 623061571013816231 杭州银行 313331000014
        // 金燕林 6228580699005714769 浙江省农村信用社 402331000007
        return [
            
            [
                'CDTNAM' => '金燕林', // 收款人户名 Z(100) 否
                'CDTEAC' => '6222620170003931032', // 收款人账号 C(35) 否
                'CDTBRD' => '301290000007'
            ], // 交通银行
            [
                'CDTNAM' => '金燕林', // 收款人户名 Z(100) 否
                'CDTEAC' => '623061571013816231', // 收款人账号 C(35) 否
                'CDTBRD' => '313331000014'
            ], // 杭州银行
            [
                'CDTNAM' => '金燕林', // 收款人户名 Z(100) 否
                'CDTEAC' => '6228580699005714769', // 收款人账号 C(35) 否
                'CDTBRD' => '402331000007'
            ]
        ]; // 浙江省农村信用社

        
    }

    
    public function getD21()
    {
//         dump($this->FBSdk->D21_ListAccount('00002','N03020'));
//         dump($this->FBSdk->D21_ListAccount('00002','N03010'));
        dump($this->FBSdk->D21_ListAccount('00001'));
//         571908952410801
    }
    
    public function getD16()
    {
        dump($this->FBSdk->D16_ListMode('N03010'));
    }
    
    public function getD14()
    {
        dump($this->FBSdk->D14_GetNewNotice());
    }
    
    public function getD18()
    {
        dump($this->FBSdk->D18_GetHisNotice([
            'BGNDAT' => '20160318', //否 开始日期 开始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20160320', //否 结束日期
//             'MSGTYP' => 'NCBCHOPR', //可 消息类型
            'MSGNBR' => '', //可 消息号
        ]));
    }
    
    public function getD22()
    {
        //571908952410602 代发代扣
        //571908952410801
//         edump($this->FBSdk->D22_GetAccInfo([
//             'BBKNBR' => '57', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
//             'C_BBKNBR' => '', // 分行名称 Z(1,62) 附录A.1 是
//             'ACCNBR' => '571907650010808'
//         ]) // 账号 C(1,35) 否
//         );//571908795110802
        edump($this->FBSdk->D22_GetAccInfo([
            'BBKNBR' => '54', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
            'C_BBKNBR' => '', // 分行名称 Z(1,62) 附录A.1 是
            'ACCNBR' => '574905966010901'
        ]) // 账号 C(1,35) 否
        );//571908795110802
        
        
        dump($this->FBSdk->D22_GetAccInfo([
            'BBKNBR' => '57', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
            'C_BBKNBR' => '', // 分行名称 Z(1,62) 附录A.1 是
            'ACCNBR' => '571908952410602'
        ]) // 账号 C(1,35) 否
);//571908795110802
        
        dump($this->FBSdk->D22_GetAccInfo([
            'BBKNBR' => '57', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
            'C_BBKNBR' => '', // 分行名称 Z(1,62) 附录A.1 是
            'ACCNBR' => '571908795110802'
        ]) // 账号 C(1,35) 否
        );//571908795110802
        dump($this->FBSdk->D22_GetAccInfo([
            'BBKNBR' => '57', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
            'C_BBKNBR' => '', // 分行名称 Z(1,62) 附录A.1 是
            'ACCNBR' => '571908939010802'
        ]) // 账号 C(1,35) 否
        );//571908795110802
    }
    
    public function getD23()
    {
        
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        
        $date = date('Ymd');
        $res = $this->FBSdk->D23_GetTransInfo([
            'BBKNBR' => '57',//分行号 N(2) 附录A.1 可 分行号和分行名称不能同时为空
            'C_BBKNBR' => '',// 分s行名称 Z(1,62) 附录A.1 可
            'ACCNBR' => '571908952410602',//账号 C(1,35) 否
            'BGNDAT' => '20160323',//起始日期 D 否
            'ENDDAT' => '20160323',//结束日期 D 否 与结束日期的间隔不能超过100天
            'LOWAMT' => '100',//最小金额 M 可 默认0.00
            'HGHAMT' => '',//最大金额 M 可 默认9999999999999.99
            'AMTCDR' => 'C',//借贷码 C(1) C：收入 D：支出 可
        ]) ;
        
        
        foreach ($res['NTQTSINFZ'] as $k => $v){
            dump($v['TRSAMT']);
        }
        
        edump($res);
//         "RPYNAM" => "杨扬"
//             "RSV30Z" => "**"
//                 "RSV31Z" => "10"
//                     "RSV50Z" => SimpleXMLElement {#214}
//                     "TRSAMT" => "-0.01"
//                         "TRSAMTD" => "0.01"

        $payData = [
        ];
        //             NTQTSINFZ
        foreach ($res['NTQTSINFZ'] as $k => $v){
            $name = $v['RPYNAM'];
            $amount = $v['TRSAMTD'];
            if(!isset($payData[$name])){
                $payData[$name] = $amount;
            }else{
                $payData[$name] += $amount;
            }
        }
        edump($payData);
        
    }
    
    

    public function getD33()
    {
        $today = date('Ymd');
        edump($this->FBSdk->D33_GetPaymentInfo([
            'BUSCOD' => 'N02031', // 业务类别 C(6) 附录A.4 可
            'BGNDAT' => $today, // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => $today, // 结束日期 C(8) 否
            'DATFLG' => 'A', // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            'MINAMT' => '0.00', // MINAMT 最小金额 M 可 空时表示0.00
            'MAXAMT' => '99999999.99', // MAXAMT 最大金额 M 可 空时表示9999999999999.99
//             'YURREF' => 'D20160310', // 业务参考号 C(1,30) 可
            'RTNFLG' => '', // 业务处理结果 C(1) 附录A.6 可 空表示查询所有结果的数据
            'OPRLGN' => ''// 经办用户 可
        ]));
    }
    
    public function getD39()
    {
        $REQNBR = \Input::get('n','0563991733');
        dump($this->FBSdk->D39_NTSTLINF($REQNBR));
    }
    
    public function getD36()
    {
//         名字错误
//         "ERRCOD" => "CPB0089"
//             "ERRTXT" => "CPB0089 收方户口号与户名检查不一致6214855710279726"
//                 "REQSTS" => "FIN"
//                     "RTNFLG" => "F"
//                         "SQRNBR" => "0000000000"
//                             "YURREF" => "D2016031009581201"
//         "ERRCOD" => "CSAB051"
//             "ERRTXT" => "CSAB051 户口6214855710279722不存在(F3RTV3T1RP)"
//                 "REQSTS" => "FIN"
//                     "RTNFLG" => "F"
//                         "SQRNBR" => "0000000000"
//                             "YURREF" => "D2016031009593201"
                                
        $dataResource = $this->getD36Resource();
        
        for ($i = 0; $i < 1; $i ++) {
            $DCOPDPAYX[] = $dataResource[$i] + [
                'YURREF' => 'D' . date('YmdHis') . sprintf('%02d', $i + 1), // 业务参考号 C（30） 否 用于标识该笔业务的编号，也可使用银行缺省值（单笔支付），批量支付须由企业提供。直联必须用企业提供
                'EPTDAT' => '', // 期望日 D 默认为当前日期 可
                'EPTTIM' => '', // 期望时间 T 默认为‘000000’ 可
                'DBTACC' => '571908939010802', // 付方帐号 N（35） 否 企业用于付款的转出帐号，该帐号的币种类型必须与币种字段相符。
                'DBTBBK' => '57', // 付方开户地区代码 C（2） 附录A.1 否 付方帐号的开户行所在地区，如北京、上海、深圳等。付方开户地区和付方开户地区代码不能同时为空，同时有值时DBTBBK有效。
                'TRSAMT' => '0.01', // 交易金额 M 否 该笔业务的付款金额。
                'CCYNBR' => '10', // 币种代码 C(2) 附录A.3 否 币种代码和币种名称不能同时为空同时有值时CCYNBR有效。。币种暂时只支持10(人民币)
                'STLCHN' => 'F', // 结算方式代码 C(1) N：普通 F：快速 否 只对跨行交易有效
                'NUSAGE' => '测试薪资', // 用途 Z（62） 否 对应对账单中的摘要NARTXT
                'BUSNAR' => '测试薪资摘要', // 业务摘要 Z（200） 可 用于企业付款时填写说明或者备注。
                'CRTACC' => '' ,// 收方帐号 N（35） 否 收款企业的转入帐号，该帐号的币种类型必须与币种字段相符。
                'CRTNAM' => '' ,// 收方帐户名 Z（62） 否 收款方企业的转入帐号的帐户名称。
                'BRDNBR' => '', // 收方行号 C(30) 可 人行自动支付收方联行号
                'BNKFLG' => 'Y', // 系统内外标志 Y：招行；N：非招行； 否
                'CRTBNK' => '', // 收方开户行 Z（62） 跨行支付（BNKFLG=N）必填 可
                'CTYCOD' => '', // 城市代码 C(4) 附录A.18 CRTFLG不为Y时行内支付必填。 可 行内支付填写，为空则不支持收方识别功能。
                'CRTADR' => '', // 收方行地址 Z(62) 跨行支付（BNKFLG=N）必填；CRTFLG不为Y时行内支付必填。 可 例如：广东省深圳市南山区
                'CRTFLG' => '', // 收方信息不检查标志 C(1) Y: 行内支付不检查城市代码和收方行地址 默认为Y。 可
                'NTFCH1' => '', // 收方电子邮件 C（36） 可 收款方的电子邮件地址，用于交易 成功后邮件通知。
                'NTFCH2' => '18767135775', // 收方移动电话 C（16） 可 收款方的移动电话，用于交易 成功后短信通知。
                'CRTSQN' => 'UID00001', // 收方编号 C（20） 可 用于标识收款方的编号。非受限收方模式下可重复。
                'TRSTYP' => '', // 业务种类 C(6) 100001=普通汇兑 101001=慈善捐款 101002 =其他 默认100001 可
                'RCVCHK' => '1', // 行内收方账号户名校验 C(1) 1：校验 空或者其他值：不校验 可 如果为1，行内收方账号与户名不相符则支付经办失败。
                'RSV28Z' => ''
            ] // 保留字段 C(27) 可 虚拟户支付时，前10位填虚拟户编号；集团支付不支持虚拟户支付。
;
        }
        
        // $this->FBSdk->D42_AgentRequest($SDKATSRQX, $SDKATDRQX);
        
        edump($this->FBSdk->D36_DCPayment([
            'BUSCOD' => 'N02031',
            'BUSMOD' => '00001'
        ] , $DCOPDPAYX));
    }

    public function getI20()
    {
        $dataResource = $this->getI2Resource();
        // 【测试】网银贷记批量支付失败测试，部分失败
        // 【
        // 直接失败，招行可检测内容的错误
        // 查询后失败，招行不可检测内容的错误
        // 】,
        mt_mark('start');
        $NTIBCOPRX = [];
        
        $dr = array_slice($dataResource, 0,1);
        
        $max = 1;
        $resss = [];
        foreach (range(0,$max) as $v){
            $resss = array_merge($resss,$dr);
        }
        
        for ($i = 0; $i < $max; $i ++) {
            $req = $resss[$i] + [
                'SQRNBR' => sprintf('%010d', $i + 1), // 流水号 C(10) 否 批次内唯一，批量经办时用作响应结果与请求的对应字段。
                'BBKNBR' => 'CB', // 付款账号银行号 C(2) 否
                'ACCNBR' => '571907650010808', // 付款账号 C(35) 否 我行账号
                //571908795110802
                //571908952410801
                'CNVNBR' => '0000001464', // 协议号 C(10) 否 贷记内部协议号
                'YURREF' => 'I' . date('YmdHis') . sprintf('%02d', $i + 1), // 业务参考号 C(30) 否 成功和在途的业务唯一
                'CCYNBR' => '10', // 币种 C(2) 附录 A.3 否
                'TRSAMT' => '0.01', // 金额 M 否
                'CRTSQN' => 'UID' . sprintf('%04d', random_int(10, 100)), // 收方编号 C(20) 可
                                                                          // 'NTFCH1' => 'jinyanlin@renrenfenqi.com', // 通知方式一 C(40) 是
                                                                          // 'NTFCH2' => '18767135775', // 通知方式二 C(40) 是
                'CDTNAM' => '金燕林', // 收款人户名 Z(100) 否
                'CDTEAC' => '6230615710138162312', // 收款人账号 C(35) 否
                'CDTBRD' => '313331000014', // 收款行行号 C(12) 否
                'TRSTYP' => 'C209', // 业务类型编码 C(4) 附录A.49 否
                'TRSCAT' => '01200', // 业务种类编码 C(5) 否
                'RMKTXT' => '', // 附言 Z(235) 是
                'RSV30Z' => ''
            ];
            $NTIBCOPRX[] = $req;
        }
        
        dump($this->FBSdk->I20_NTIBCOPR('00002', $NTIBCOPRX));
        dmt_mark('start','end');
        // edump($NTIBCOPRX);
//         dump($this->FBSdk->I20_NTIBCOPR('00001', $NTIBCOPRX)); // 保留字 30
                                                                   
        // 0538373685
                                                                   // 0538373686
    }

    
    public function getI11(){
        dump($this->FBSdk->I11_NTQEBCTL('N31010'));
    }
    
    public function getI13(){
        $data = [
            '0540194163',
            '0540194164',
            '0540194166'
        ];
        $data = '0563560104';
        dump($this->FBSdk->I13_NTEBPINF($data));
    }
    
    public function getI14()
    {
        $today = date('Ymd');
        
        $res = $this->FBSdk->I14_NTQNPEBP([
            'QRYACC' => '571908952410602', // 账号 C(35) 否
            'TRXDIR' => 'O', // 交易方向 C(1) I：提回 O：提出 否
            'MSGNBR' => '101', // 业务种类 C(3) 101：网银贷记 103：网银借记 105：第三方贷记 否
            'BGNDAT' => '20160321', // 交易起始日期 D 否 日期间隔不能超过100天
            'ENDDAT' => '20160321', // 交易结束日期 D 否
            'MINAMT' => '', // 最小金额 M
            'MAXAMT' => '', // 最大金额 M
            'YURREF' => 'I1603212027001153', // 业务参考号 C(30)
            // 'TRXSTS' => '12', // 交易状态 C(2) 附录 A.48
            'PYREAC' => '', // 付款人账号 C(35)
            'PYEEAC' => '', // 收款人账号 C(35)
            'CNVNBR' => ''
        ]);
        exit();
        edump($res); // 内部协议号 C(10)
        
        // I2016030911354401
        edump($this->FBSdk->I12_NTQRYEBP([
            'BUSCOD' => 'N31010', // 业务类型 C(6) N31010 网银贷记 N31011 网银借记 N31012 第三方贷记 N31013 跨行账户信息查询 可
            'BGNDAT' => $today, // 起始日期 D 否 日期间隔不能超过100天
            'ENDDAT' => $today, // 结束日期 D 否
            'MINAMT' => '', // 最小金额 M
            'MAXAMT' => '', // 最大金额 M
            'YURREF' => 'I20160309', // 业务参考号 C(30)
            'OPRLGN' => '', // 经办用户 Z(30)
            'AUTSTR' => '', // 请求状态 C(30) 附录A.5 可以组合取值，比如AUTSTR = 'AUTNTEWCF'
            'RTNSTR' => '', // 返回结果 C(30) 附录 A.6 可以组合取值，比如RTNSTR = 'SFBR'
            'CNVNBR' => ''
        ]) // 内部协议号 C(10)
);
        dump($this->FBSdk->I13_NTEBPINF('', '00024451735'));
        return;
        dump($this->FBSdk->I14_NTQNPEBP([
            'QRYACC' => '571908952410602', // 账号 C(35) 否
            'TRXDIR' => 'O', // 交易方向 C(1) I：提回 O：提出 否
            'MSGNBR' => '101', // 业务种类 C(3) 101：网银贷记 103：网银借记 105：第三方贷记 否
            'BGNDAT' => $today, // 交易起始日期 D 否 日期间隔不能超过100天
            'ENDDAT' => $today, // 交易结束日期 D 否
            'MINAMT' => '', // 最小金额 M
            'MAXAMT' => '', // 最大金额 M
                            // 'YURREF' => 'I2015122314224' ,// 业务参考号 C(30)
                            // 'TRXSTS' => '12', // 交易状态 C(2) 附录 A.48
            'PYREAC' => '', // 付款人账号 C(35)
            'PYEEAC' => '', // 收款人账号 C(35)
            'CNVNBR' => ''
        ])) // 内部协议号 C(10)
;
    }

    
   // "ACCNAM" => "仁穗(杭州)互联网金融服务有限公司"
    //         "ACCNBR" => "571908795110802"
    //             "BBKNBR" => "57"
    //                 "CCYNBR" => "10"
    //                     "M_ACCNBR" => "杭州, 571908795110802, 人民币"
    //                         "RELNBR" => "0000052289"
    //                             ]
    //                             1 => array:6 [▼
        //                                 "ACCNAM" => "仁穗互联网金融服务(深圳)有限公司"
        //                                 "ACCNBR" => "571908939010802"
        //                                 "BBKNBR" => "57"
        //                                 "CCYNBR" => "10"
        //                                 "M_ACCNBR" => "杭州, 571908939010802, 人民币"
        //                                 "RELNBR" => "0000052288"
    
    
    
    public function getI32()
    {
        // dump($this->FBSdk->I11_NTQEBCTL('N31010'));
        dump($this->FBSdk->I32_NTQRYCBQ([
            'PTCTYP' => '01', // 协议类型 C(2) 03=查询协议 02=授权支付协议 01=贷记协议 是 全部传空
            'QRYBBK' => 'CB', // 银行号 C(2) 否
            'QRYACC' => '574905966010901', // 查询账号 C(35) 否
            'CNVNBR' => '', // 内部协议编号 C(10) 是 输入为单笔查询
            'PTCNBR' => '', // 人行协议号 C(60) 是
            'PTCSTS' => '', // 协议状态 C(2) 是
            'BGNDAT' => '', // 协议生效起始日期 D 是
            'ENDDAT' => '', // 协议生效结束日期 D 是
            'EFTDAT' => '', // 协议失效起始日期 D 是
            'EFEDAT' => ''
        ]) // 协议失效结束日期 D 是
);
    }
    public function getD44(){
        $n = \Input::get('n','0564063519');
        dump($this->FBSdk->D44_GetAgentDetail($n));
    }
    
    public function getD49(){
        $n = \Input::get('n','0564063519');
        dump($this->FBSdk->D49_NTAGDINF($n));
    }
    
    
    public function getD43(){
    
        dump($this->FBSdk->D43_GetAgentInfo([
//             'BUSCOD' => 'N03010', // 业务代码 C(6) N03010：代发工资；N03020：代发；N03030：代扣 可
            'BGNDAT' => '20160422', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20160422', // 结束日期 C(8) 否
            'DATFLG' => 'A', // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            'YURREF' => 'D42146131661793794747', // 业务参考号 C(1,30) 可
            'OPRLGN' => '',// 经办用户 Z(30) 可
        ]));
    }
    
    
    public function getD41(){
        $n = \Input::get('n','N03020');
        dump($this->FBSdk->D41_QueryAgentList($n));
    }
    
    public function getD42()
    {
        $dataResource = $this->getD42Resource();
            // [
            // 'CRTACC' => '6214855710279726', // 收方帐号 N（35） 否 收款企业的转入帐号，该帐号的币种类型必须与币种字段相符。
            // 'CRTNAM' => '杨扬'
            // ], // 收方帐户名 Z（62） 否 收款方企业的转入帐号的帐户名称。
        $dataResource = [
            [
                'ACCNBR' => '6214855710279726', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
                'CLTNAM' => '杨扬1', // 户名 Z(1,62) 否
                'BNKFLG' => '', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
                'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
                'EACCTY' => '',
                'TRSDSP' => '杨扬测试代发招行',
            ],
            [
                'ACCNBR' => '6222620170003931032', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
                'CLTNAM' => '金燕林1', // 户名 Z(1,62) 否
                'BNKFLG' => 'N', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
                'EACBNK' => '交通银行', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
                'EACCTY' => '杭州',
                'TRSDSP' => '金燕林测试代发招行'
            ]
        ];
        $SUM = 0;
        for ($i = 0; $i < 2; $i ++) {
            $SDKATDRQX[] = $dataResource[$i] + [
                'ACCNBR' => '6225885910000108', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
                'CLTNAM' => 'Judy Zeng', // 户名 Z(1,62) 否
                'TRSAMT' => '0.01', // 金额 M 否
                'BNKFLG' => 'N', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
                'EACBNK' => '交通银行', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
                'EACCTY' => '杭州', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
                'TRSDSP' => ''
            ] // 注释 Z(1,20) 可 代扣：如果签订有代扣协议，则必须填写与代扣协议一致的合作方账号（该号为扣款方的客户标识ID）
            ;
            $SUM += $SDKATDRQX[$i]['TRSAMT'];
        }
//       BYSA=代发工资
        $SDKATSRQX = [
            'BUSCOD' => 'N03010', // 业务类别 C(6) N03010:代发工资N03020:代发N03030:代扣 否 默认为代发工资: N03010
            'BUSMOD' => '00001', // 业务模式编号 C(5) 否 编号和名称填写其一，填写编号则名称无效。可通过前置机或者查询可经办的业务模式信息（ListMode）获得，必须使用无审批的业务模式
            'MODALS' => '', // 业务模式名称 Z(62)
            'C_TRSTYP' => '代发工资', // 交易代码名称 Z 附录A.45 否 为空时默认BYSA：代发工资，代发和代扣时必填，可通过4.1获得可以使用的交易代码，也可以通过前置机获取。
            'TRSTYP' => 'BYSA', // 交易代码 C(4)
            'EPTDAT' => '', // 期望日期 D 可 不用填写，不支持期望日直接代发
            'DBTACC' => '571908952410801', // 转出账号/转入账号 C(35) 否 代发为转出账号；代扣为转入账号
            'BBKNBR' => '57', // 分行代码 C(2) 附录A.1 否 代码和名称不能同时为空。同时有值时BBKNBR有效。
            'BANKAREA' => '', // 分行名称 附录A.1
            'SUM' => $SUM, // 总金额 M 否
            'TOTAL' => $i, // 总笔数 N(4) 否
            'CCYNBR' => '10', // 币种代码 N(2) 附录A.3 可 默认10：人民币 同时有值时CCYNBR有效。
            'CURRENCY' => '人民币', // 币种名称 Z(1,10) 附录A.3
            'YURREF' => 'D42' . createSerialNum(), // 业务参考号 C(1,30) 否
            'MEMO' => '测试代发招行', // 用途 Z(1,42) 否
            'DMANBR' => '', // 虚拟户编号 C(1,20) 可 记账宝使用
            'GRTFLG' => ''
        ] // 直连经办网银审批标志 C(1) Y：直连经办、网银审批；空或者其他值：直连经办、无需审批。 可 为Y时必须使用有审批岗的模式；不为Y时，必须使用无审批岗的模式。
        ;
        
        
        $res = $this->FBSdk->D42_AgentRequest($SDKATSRQX, $SDKATDRQX);
        dump($this->FBSdk->D44_GetAgentDetail($res['NTREQNBRY']['REQNBR']));
        dump($this->FBSdk->D43_GetAgentInfo([
        //             'BUSCOD' => 'N03010', // 业务代码 C(6) N03010：代发工资；N03020：代发；N03030：代扣 可
            'BGNDAT' => '20160422', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20160422', // 结束日期 C(8) 否
            'DATFLG' => 'A', // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            'YURREF' => $SDKATSRQX['YURREF'], // 业务参考号 C(1,30) 可
            'OPRLGN' => '',// 经办用户 Z(30) 可
        ]));
        edump($res);
    }
    
}



