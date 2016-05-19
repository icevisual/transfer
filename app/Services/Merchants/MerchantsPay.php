<?php
namespace App\Services\Merchants;

class MerchantsPay
{

    /**
     *
     * @var \App\Services\Merchants\FBSdkService
     */
    protected $_FBSdkService;

    /**
     * 前置机URL
     *
     * @var unknown
     */
    protected $_FBSdkManagerUrl;

    /**
     * 前置机端口
     *
     * @var unknown
     */
    protected $_FBSdkManagerPort;

    public function __construct()
    {
        $this->_FBSdkService = FBSdkService::getInstance();
    }

    /**
     *
     * @return \App\Services\Merchants\FBSdkService
     */
    public function getFBSdkService()
    {
        return $this->_FBSdkService;
    }

    public function getAccBalence()
    {
        $ACC_array = [];
        // 直接支付 可查询/可经办的账户列表
        $DCPAYMNT_accs = $this->_FBSdkService->ListAccount('00001','N02031');
        // 跨行支付 可查询/可经办的账户列表
        $NTIBCOPR_accs = $this->_FBSdkService->ListAccount('00001','N31010');
        // $NTIBCOPR_accs 的BBKNBR = CB
        foreach ([$NTIBCOPR_accs,$DCPAYMNT_accs] as $k => $_accs){
            if ($_accs) {
                if (isset($_accs['NTQACLSTZ'][0])) {
                    foreach ($_accs['NTQACLSTZ'] as $key => $value){
                        $ACC_array[$value['ACCNBR']] = $value['BBKNBR'];
                    }
                } else {
                    $ACC_array[$_accs['NTQACLSTZ']['ACCNBR']] = $_accs['NTQACLSTZ']['BBKNBR'];
                }
            }
        }
        foreach ($ACC_array as $key => $value){
            $SDKACINFX[] = [
                'ACCNBR' => $key,
                'BBKNBR' => $value
            ];
        }
        $result = $this->_FBSdkService->D22_GetAccInfo($SDKACINFX);
        $data = $result['NTQACINFZ'];
        if(!isset($data[0])){
            $data = [$data];
        }
        \DB::beginTransaction();
        foreach ($data as $k => $record){
            $record['log_id'] = $result['log_id'];
            $record['created_at'] = date('Y-m-d H:i:s');
            \App\Models\PaymentAcc::create($record);
        }
        \DB::commit();
    }

    /**
     * net bank interconnection for one
     *
     * @param array $paymentData
     *            <pre> * N
     *            'SQRNBR' => '0000000001', // 流水号 C(10) 否 批次内唯一，批量经办时用作响应结果与请求的对应字段。
     *            'TRSAMT' => '0.01', // 金额 M 否
     *            'YURREF' => 'I201512231125', // 业务参考号 C(30) 否 成功和在途的业务唯一
     *            'CRTSQN' => 'RCV0000001', // 收方编号 C(20) 可 UID0000001
     *            'NTFCH1' => '18767135775', // 通知方式一 C(40) 是
     *            'NTFCH2' => 'jinyanlin@renrenfenqi.com', // 通知方式二 C(40) 是
     *            'CDTNAM' => '金燕林', // 收款人户名 Z(100) 否
     *            'CDTEAC' => '6222600170008404351', // 收款人账号 C(35) 否
     *            'CDTBRD' => '301290000007', // 收款行行号 C(12) 否
     *            'RMKTXT' => '测试网银互联',
     *            'RSV30Z' => ''
     *            </pre>
     */
    public function netBankInterconnectionPayOne(array $paymentData)
    {
        $NTIBCOPRX = [];
        $config = (array) \Config::get('merchants.NTIBCOPR');
        $NTIBCOPRX_config = $config['NTIBCOPRX'];
        $BUSMOD_config = $config['NTOPRMODX']['BUSMOD'];
        $NTFCH1_config = $config['NTFCH1'];
        $NTFCH2_config = $config['NTFCH2'];
        
        $data = $paymentData;
        runValidator($data, [
            'TRSAMT' => 'required|numeric', // 金额 M 否
            'YURREF' => 'required|max:30', // 业务参考号 C(30) 否 成功和在途的业务唯一
            'NTFCH1' => 'sometimes|email', // 通知方式一 C(40) 是
            'NTFCH2' => 'sometimes|mobile', // 通知方式二 C(40) 是
            'CDTNAM' => 'required', // 收款人户名 Z(100) 否
            'CDTEAC' => 'required|numeric', // 收款人账号 C(35) 否
            'CDTBRD' => 'required|numeric', // 收款行行号 C(12) 否
            'CRTSQN' => 'required'
        ]);
        // TODO: check NTIBCOPRX ，限制可传入的键值
        $data['SQRNBR'] = str_pad(1, 10, '0', STR_PAD_LEFT);
        $map = [
            'uid' => $data['CRTSQN'],
            'order_id' => $data['YURREF']
        ];
        // $data['CRTSQN'] = 'UID' . str_pad($data['CRTSQN'], 8,'0', STR_PAD_LEFT);
        if (! $NTFCH1_config) {
            unset($data['NTFCH1']);
        }
        if (! $NTFCH2_config) {
            unset($data['NTFCH2']);
        }
        $NTIBCOPRX = $data + $NTIBCOPRX_config;
        
        $config = (array) \Config::get('merchants.NTIBCOPR.NTIBCOPRX');
        $BUSMOD = \Config::get('merchants.NTIBCOPR.NTOPRMODX.BUSMOD');
        try {
            $result = $this->_FBSdkService->NTIBCOPR($BUSMOD_config, $NTIBCOPRX);
            // $result['log_id']
            $fbsdkLog = \App\Models\Mcpay\FbsdkLog::getLastCreatedObject();
            $fbsdkLog && \App\Models\Mcpay\McpayDetail::doImport($fbsdkLog);
            
            $return = $result['NTOPRRTNZ'];
            // NTOPRDRTZ
            if (isset($return['REQNBR']) && $return['REQSTS'] == 'BNK' && substr($return['ERRCOD'], 0, 3) == 'SUC' && ! isset($return['ERRTXT'])) {
                // 银行已受理该业务,添加查询业务
                if (isset($result['NTOPRDRTZ'])) {
                    // 等待后获取结果，问题：网络原因，长连接不妥，一次查询不保证得到结果
                    \Config::get('merchants.check_result_mode');
                    __async_curl('/v1/withdrawResultSelect', [
                        'wait' => $result['NTOPRDRTZ']['RTNTIM'],
                        'REQNBR' => $return['REQNBR'],
                        'uid' => $map['uid'],
                        'order_id' => $map['order_id']
                    ]);
                    return true;
                } else {
                    // QUESTION
                    throw new \App\Exceptions\NotifyException('Logic Error 1');
                }
            } else {
                // 失败
                if (isset($result['NTOPRDRTZ'])) {
                    // QUESTION
                    throw new \App\Exceptions\NotifyException('Logic Error 2');
                } else {
                    \App\Models\User\UserWithdraw::userWithdrawFailed($map['uid'], $map['order_id'], $return['ERRTXT']);
                }
            }
            return false;
        } catch (\App\Services\Merchants\FBSdkException $e) {
            // 请求报错
            // 程序问题
            // 请求错误
            \App\Models\User\UserWithdraw::userWithdrawFailed($map['uid'], $map['order_id'], $e->getMessage());
            throw $e;
            [
                'INFO' => [
                    'DATTYP' => '2',
                    'ERRMSG' => 'FBCN026: 字符串类型字段BUSMOD长度不合法',
                    'FUNNAM' => 'NTIBCOPR',
                    'LGNNAM' => '银企直连专用集团1',
                    'RETCOD' => '-3'
                ]
            ];
        }
        return false;
    }

    public function NTEBPINF_process($RTNTIM, $REQNBR, $uid, $order_id)
    {
        \LRedis::SETEX(__FUNCTION__ . time(), 60, json_encode(get_defined_vars()));
        sleep(intval($RTNTIM));
        $NTEBPINF_result = $this->_FBSdkService->NTEBPINF($REQNBR);
        /**
         * 14
         * <RJCRSN>BDM0022户名不一致</RJCRSN>
         * <RTNNAR>RJ90BDM0022户名不一致</RTNNAR>
         *
         * <RJCRSN>该业务暂不受理此账号</RJCRSN>
         * <RTNNAR>RJ08该业务暂不受理此账号</RTNNAR>
         * 01
         * <RTNNAR>KPIC071业务类型非法(D200)</RTNNAR>
         * <RTNNAR>CSAC054 户口571908952410602透支(CSEACCK1RI)</RTNNAR>
         */
        
        /**
         * '00' => '初始',
         * '01' => '已取消',
         * '02' => '经办中',
         * '10' => '已发送',
         * '11' => '已拒绝',
         * '12' => '已成功',
         * '13' => '已撤销',
         * '14' => '已失败',
         * '15' => '贷记已冲账',
         * '16' => '收到 URL',
         * '18' => '已挂账',
         * '20' => '回执已发送',
         * '21' => '已拒绝 ( 提回 )',
         * '22' => '已成功 ( 提回 )',
         * '23' => '已撤销 ( 提回 )',
         * '24' => '已失败 ( 提回 )',
         * '25' => '校验通过',
         * '26' => '发出 URL',
         * '27' => '经办中（ URL ）',
         * '28' => '已挂账 ( 提回 )',
         * '29' => '已扣款'
         */
        // 'RJCCOD' => '' ,// 拒绝码 C(4)
        // 'RJCRSN' => '' ,// 拒绝原因 Z(175)
        // 'RTNNAR' => '' ,// 结果摘要 Z(120)
        
        if ($NTEBPINF_result['NTEBPINFZ']['YURREF'] != $order_id) {
            throw new \App\Exceptions\NotifyException('Question:order_id and YURREF not match');
        }
        $_code = [
            'success' => [
                '12'
            ],
            'failed' => [
                '01',
                '11',
                '14',
                '13'
            ],
            'delay' => [
                '00',
                '01',
                '02'
            ]
        ];
        if (in_array($NTEBPINF_result['NTEBPINFZ']['TRXSTS'], $_code['success'])) {
            \App\Models\User\UserWithdraw::userWithdrawSuccess($uid, $order_id);
        } else 
            if (in_array($NTEBPINF_result['NTEBPINFZ']['TRXSTS'], $_code['failed'])) {
                \App\Models\User\UserWithdraw::userWithdrawFailed($uid, $order_id, $NTEBPINF_result['NTEBPINFZ']['RJCRSN']);
            } else 
                if (in_array($NTEBPINF_result['NTEBPINFZ']['TRXSTS'], $_code['delay'])) {
                    $RTNTIM = intval($RTNTIM / 2);
                    if ($RTNTIM) {
                        $this->NTEBPINF_process($RTNTIM, $REQNBR, $uid, $order_id);
                    } else {
                        throw new \Exception('Notify Error');
                    }
                } else {
                    throw new \App\Exceptions\NotifyException("Unexpected TRXSTS:{$NTEBPINF_result['NTEBPINFZ']['TRXSTS']}");
                }
    }

    /**
     *
     * @param unknown $paymentData
     *            <pre>
     *            'TRSAMT' => '' ,// 交易金额 M 否 该笔业务的付款金额。
     *            'YURREF' => '', // 业务参考号 C（30） 否 用于标识该笔业务的编号，也可使用银行缺省值（单笔支付），批量支付须由企业提供。直联必须用企业提供
     *            'BUSNAR' => '', // 业务摘要 Z（200） 可 用于企业付款时填写说明或者备注。
     *            'CRTACC' => '', // 收方帐号 N（35） 否 收款企业的转入帐号，该帐号的币种类型必须与币种字段相符。
     *            'CRTNAM' => '', // 收方帐户名 Z（62） 否 收款方企业的转入帐号的帐户名称。
     *            'NTFCH1' => '', // 收方电子邮件 C（36） 可 收款方的电子邮件地址，用于交易 成功后邮件通知。
     *            'NTFCH2' => '', // 收方移动电话 C（16） 可 收款方的移动电话，用于交易 成功后短信通知。
     *            'CRTSQN' => '', // 收方编号 C（20） 可 用于标识收款方的编号。非受限收方模式下可重复。
     *            'TRSTYP' => '', // 业务种类 C(6) 100001=普通汇兑 101001=慈善捐款 101002 =其他 默认100001 可
     *            'RCVCHK' => '',// 行内收方账号户名校验 C(1) 1：校验 空或者其他值：不校验 可 如果为1，行内收方账号与户名不相符则支付经办失败。
     *            'NUSAGE' => '',// 用途 Z（62） 否 对应对账单中的摘要NARTXT
     *            </pre>
     */
    public function DCPayment($paymentData)
    {
        $data = $paymentData;
        runValidator($data, [
            'TRSAMT' => 'required|numeric', // 金额 M 否
            'YURREF' => 'required|max:30', // 业务参考号 C(30) 否 成功和在途的业务唯一
            'NTFCH1' => 'sometimes|email', // 通知方式一 C(40) 是
            'NTFCH2' => 'sometimes|mobile', // 通知方式二 C(40) 是
            'CRTNAM' => 'required', // 收款人户名 Z(100) 否
            'CRTACC' => 'required|numeric', // 收款人账号 C(35) 否
            'CRTSQN' => 'required'
        ]);
        $map = [
            'uid' => $data['CRTSQN'],
            'order_id' => $data['YURREF']
        ];
        $config = (array) \Config::get('merchants.DCPAYMENT');
        $DCOPDPAYX_config = $config['DCOPDPAYX'];
        $SDKPAYRQX_config = $config['SDKPAYRQX'];
        $DCOPDPAYX = $DCOPDPAYX_config + $data;
        
        try {
            $result = $this->_FBSdkService->DCPayment($SDKPAYRQX_config, $DCOPDPAYX);
            $fbsdkLog = \App\Models\FbsdkLog::getLastCreatedObject();
            $fbsdkLog && \App\Models\McpayDetail::doImport($fbsdkLog);
            $return = $result['NTQPAYRQZ'];
            if (isset($return['REQNBR']) && $return['REQSTS'] == 'NTE' && substr($return['ERRCOD'], 0, 3) == 'SUC' && ! isset($return['ERRTXT'])) {
                \Config::get('merchants.check_result_mode');
                __async_curl('/v1/withdrawResultSelect', [
                    'wait' => 40,
                    'REQNBR' => $return['REQNBR'],
                    'uid' => $map['uid'],
                    'order_id' => $map['order_id'],
                    'direct' => 1
                ]); // 直接支付

                return true;
            } else {
                // 失败
                \App\Models\User\UserWithdraw::userWithdrawFailed($map['uid'], $map['order_id'], $return['ERRTXT']);
            }
            return false;
        } catch (\App\Services\Merchants\FBSdkException $e) {
            // 请求报错
            // 程序问题
            // 请求错误
            \App\Models\User\UserWithdraw::userWithdrawFailed($map['uid'], $map['order_id'], $e->getMessage());
            throw $e;
            [
                'INFO' => [
                    'DATTYP' => '2',
                    'ERRMSG' => 'FBCN026: 字符串类型字段BUSMOD长度不合法',
                    'FUNNAM' => 'NTIBCOPR',
                    'LGNNAM' => '银企直连专用集团1',
                    'RETCOD' => '-3'
                ]
            ];
        }
    }

    public function NTSTLINF_process($RTNTIM, $REQNBR, $uid, $order_id)
    {
        \LRedis::SETEX(__FUNCTION__ . time(), 60, json_encode(get_defined_vars()));
        sleep(intval($RTNTIM));
        $NTSTLINF_result = $this->_FBSdkService->NTSTLINF($REQNBR);
        
        // $NTSTLINF_result = $this->_FBSdkService->GetPaymentInfo([
        // 'BUSCOD' => 'N02031',//业务类别 C(6) 附录A.4 可
        // 'BGNDAT' => '20151201',//起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
        // 'ENDDAT' => '20151230',//结束日期 C(8) 否
        // 'YURREF' => $order_id,//业务参考号 C(1,30) 可
        // ]);
        
        $NTSTLINF_result = $NTSTLINF_result['NTQPAYQYZ'];
        if ($NTSTLINF_result['YURREF'] != $order_id) {
            throw new \App\Exceptions\NotifyException('Question:order_id and YURREF not match');
        }
        if ($NTSTLINF_result['REQSTS'] == 'FIN') {
            if ($NTSTLINF_result['RTNFLG'] == 'S') {
                \App\Models\User\UserWithdraw::userWithdrawSuccess($uid, $order_id);
            } else {
                \App\Models\User\UserWithdraw::userWithdrawFailed($uid, $order_id, $NTSTLINF_result['RTNNAR']);
            }
        } else {
            $next_RTNTIM = $RTNTIM + 0.1;
            if (intval($RTNTIM) == intval($next_RTNTIM)) {
                $this->NTSTLINF_process($next_RTNTIM, $REQNBR, $uid, $order_id);
            } else {
                throw new \Exception('Notify Error');
            }
        }
    }

    /**
     * 我方向存款用户打款,支持多个,代发
     *
     * @param array $paymentData
     *            <pre>
     *            [
     *            'ACCNBR' => '6225885910000108', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
     *            'CLTNAM' => 'Judy Zeng', // 户名 Z(1,62) 否
     *            'TRSAMT' => '12.19', // 金额 M 否
     *            'BNKFLG' => '', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
     *            'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
     *            'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
     *            'TRSDSP' => '测试1219',// 注释 Z(1,20) 可 代扣：如果签订有代扣协议，则必须填写与代扣协议一致的合作方账号（该号为扣款方的客户标识ID）
     *            ] * n
     *            </pre>
     * @param array $setting            
     */
    public function payment($paymentData, $YURREF = '', array $setting = [])
    {
        // 'YURREF' => 'I' . createSerialNum()
        $SDKATDRQX_ = [
            'ACCNBR' => '收款账号', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
            'CLTNAM' => '户名', // 户名 Z(1,62) 否
            'TRSAMT' => '金额', // 金额 M 否
            'BNKFLG' => '系统内标志', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
            'EACBNK' => '他行户口开户行', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
            'EACCTY' => '他行户口开户地', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
            'TRSDSP' => '注释'
        ]; // 注释 Z(1,20) 可 代扣：如果签订有代扣协议，则必须填写与代扣协议一致的合作方账号（该号为扣款方的客户标识ID）
        
        if (! isset($paymentData[0])) {
            $paymentData = [
                $paymentData
            ];
        }
        $SDKATSRQX = $setting + (array) \Config::get('merchants.AgentRequest.SDKATSRQX');
        $SDKATSRQX['SUM'] = 0;
        $SDKATSRQX['TOTAL'] = 0;
        $SDKATSRQX['YURREF'] = $YURREF ? $YURREF : 'I' . createSerialNum();
        foreach ($paymentData as $v) {
            // TODO: check SDKATDRQX
            $SDKATSRQX['SUM'] += $v['TRSAMT'];
            $SDKATSRQX['TOTAL'] ++;
            $SDKATDRQX[] = $v;
        }
        $result = $this->_FBSdkService->AgentRequest($SDKATSRQX, $SDKATDRQX);
        // 大量错误，需记录并联系程序员
        // 结果检查
        return $result['NTREQNBRY']['REQNBR'];
    }
}