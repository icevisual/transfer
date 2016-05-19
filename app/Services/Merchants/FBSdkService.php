<?php
namespace App\Services\Merchants;

class FBSdkService extends FBSdk
{

    public static $_instance = [];

    /**
     *
     * @param array $config<pre>
     *            'ip_address',
     *            'port',
     *            'login_name',
     *            </pre>
     * @throws FBSdkException
     * @return \App\Services\Merchants\FBSdkService
     */
    public static function getInstance(array $config = [], array $curlOption = [])
    {
        $default = [
            'ip_address' => \Config::get('merchants.ip_address'),
            'port' => \Config::get('merchants.port'),
            'login_name' => \Config::get('merchants.login_name')
        ];
        $cfg = $config + $default;
        $cfg = array_only($cfg, [
            'ip_address',
            'port',
            'login_name'
        ]);
        $key = sha1(json_encode($cfg));
        if (! isset(static::$_instance[$key])) {
            if (false === ip2long($cfg['ip_address'])) {
                if (function_exists($cfg['ip_address'])) {
                    $cfg['ip_address'] = call_user_func($cfg['ip_address']);
                } else {
                    throw new FBSdkException('IP 配置错误');
                }
            }
            static::$_instance[$key] = new static("http://{$cfg['ip_address']}:{$cfg['port']}", $cfg['login_name']);
        }
        
        if ($curlOption) {
            static::$_instance[$key]->setCurlOptions($curlOption);
        }
        return static::$_instance[$key];
    }

    /**
     * 4.9查询大批量代发代扣明细信息
     *
     * @param string $REQNBR            
     * @return \App\Services\Merchants\Ambigous <pre>
     *         'TRXSEQ' => '' ,// 交易序号 C(8) 否
     *         'ACCNBR' => '' ,// 帐号 C(35) 否
     *         'ACCNAM' => '' ,// 户名 Z(62) 否
     *         'TRSAMT' => '' ,// 金额 M 否
     *         'LGRAMT' => '' ,// 实际代扣金额 M 可 仅供代扣查询明细时根据实际情况返回
     *         'TRSDSP' => '' ,// 注释 Z(42) 可
     *         'STSCOD' => '' ,// 记录状态 C(1)
     *         'ERRCOD' => '' ,// 错误码 C(7) 可
     *         'ERRTXT' => '' ,// 错误信息 Z(192) 可
     *         'BNKFLG' => '' ,// 系统内标志 C(1) Y/N 可
     *         'EACBNK' => '' ,// 他行户口开户行 Z(62) 可
     *         'EACCTY' => '' ,// 他行户口开户地 Z(62) 可
     *         'FSTFLG' => '' ,// 他行快速标志 C(1) 可 Y:快速N:普通
     *         'RCVBNK' => '' ,// 他行户口联行号 C(12) 可
     *         'CPRACT' => '' ,// 客户代码 C(20) 可
     *         'CPRREF' => '' ,// 合作方流水号 C(20) 可
     *         </pre>
     */
    public function D49_NTAGDINF($REQNBR)
    {
        $sendArray = [
            'NTAGDINFY1' => [
                'REQNBR' => $REQNBR
            ]
        ];
        $result = $this->invokeApi('NTAGDINF', $sendArray);
        return $result; // isset($result['NTAGCDTLY1']) ? $result['NTAGCDTLY1'] : [];
    }

    
    /**
     * Judge Payprocess Status ,True For Over And False For Not
     * @param unknown $NTQATSQYZ
     * @return boolean
     */
    public function D43_judgement($NTQATSQYZ)
    {
        if (isset($NTQATSQYZ['REQSTA']) && $NTQATSQYZ['REQSTA'] == 'FIN' ) {
            return true;
        }
        return false;
    }

    /**
     * 4.3查询交易概要信息
     *
     * @param array $SDKATSQYX
     *            <pre>
     *            'BUSCOD' => 'N03020', // 业务代码 C(6) N03010：代发工资；N03020：代发；N03030：代扣 可
     *            'DATFLG' => 'A', // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
     *            'OPRLGN' => '',// 经办用户 Z(30) 可
     *            </pre>
     * @return \App\Services\Merchants\Ambigous
     */
    public function D43_GetAgentInfo_const()
    {
        return [
            'BUSCOD' => 'N03010', // 业务代码 C(6) N03010：代发工资；N03020：代发；N03030：代扣 可
            'DATFLG' => 'A', // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            'OPRLGN' => ''
        ]; // 经办用户 Z(30) 可

    }

    /**
     * 4.3查询交易概要信息
     *
     * @param array $SDKATSQYX
     *            <pre>
     *            'BUSCOD' => 'N03020', // 业务代码 C(6) N03010：代发工资；N03020：代发；N03030：代扣 可
     *            'BGNDAT' => '20151219', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
     *            'ENDDAT' => '20151219', // 结束日期 C(8) 否
     *            'DATFLG' => 'A', // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
     *            'YURREF' => 'I145051489665969178', // 业务参考号 C(1,30) 可
     *            'OPRLGN' => '',// 经办用户 Z(30) 可
     *            </pre>
     * @return array $NTQATSQYZ * n
     *         <pre>
     *         'REQNBR' => '' ,// 流程实例号 C(10) 用于查询交易明细信息。
     *         'BUSCOD' => '' ,// 业务代码 C(6) 附录A.4 否
     *         'BUSMOD' => '' ,// 业务模式编号 C(5) 可 可通过查询可经办的业务模式信息（ListMode）获得，从中选出无审批的业务模式，不允许使用需要审批的业务模式
     *         'OPRDAT' => '' ,// 经办日期 D
     *         'EPTDAT' => '' ,// 期望日期 D
     *         'EPTTIM' => '' ,// 期望时间 T
     *         'TRSTYP' => '' ,// 交易类型代码 C(4)
     *         'C_TRSTYP' => '' ,// 交易代码名称 可 业务类型是代发工资时为空，代发和代扣时非空，可通过4.1获得
     *         'ACCNBR' => '' ,// 转出账号 C(35) 否
     *         'ACCNAM' => '' ,// 转出账户名 Z(62)
     *         'BBKNBR' => '' ,// 转出账户所在分行代码 C(2) 附录A.1 可 代码和名称不能同时为空。
     *         'TOTAMT' => '' ,// 交易总金额 M 否
     *         'TRSNUM' => '' ,// 交易总笔数 N(4) 否
     *         'SUCNUM' => '' ,// 成功笔数 N(4)
     *         'SUCAMT' => '' ,// 成功金额 M
     *         'CCYNBR' => '' ,// 币种代码 N(2) 附录A.3 可
     *         'YURREF' => '' ,// 业务参考号 C(1,30) 否
     *         'NUSAGE' => '' ,// 用途 Z(1,30) 可 和代发代扣的MEMO字段对应
     *         'REQSTA' => '' ,// 业务请求状态 C(3) 附录A.5 否
     *         'RTNFLG' => '' ,// 业务处理结果 C(1) 附录A.6 可 当REQSTA=FIN且RTNFLG=S需检查RSV62Z第一位是否为’P’，是表示部分成功，否则表示全部成功。
     *         'ERRDSP' => '' ,// 错误描述 Z(92) 可
     *         'DMANBR' => '' ,// 虚拟户编号 C(20) 可
     *         'RSV62Z' => '' ,// 保留字 C(62) 可
     *         </pre>
     */
    public function D43_GetAgentInfo(array $SDKATSQYX)
    {
        $sendArray = [
            'SDKATSQYX' => $SDKATSQYX
        ];
        $result = $this->invokeApi('GetAgentInfo', $sendArray);
        return $result;
    }

    /**
     * 4.4查询交易明细信息
     *
     * @param unknown $REQNBR            
     * @return array $NTQATDQYZ
     *         <pre>
     *         'ACCNBR' => '' ,// 账号 C(1,35) 否
     *         'CLTNAM' => '' ,// 户名 Z(1,20) 否
     *         'TRSAMT' => '' ,// 金额 M 否
     *         'TRSDSP' => '' ,// 注释 Z(20) 可
     *         'STSCOD' => '' ,// 状态 C(1) S：成功；F：失败；C：撤消；I：数据录入 否 I为初始状态
     *         'ERRDSP' => '' ,// 结果描述 可
     *         'BNKFLG' => '' ,// 系统内标志 C(1) Y：开户行为招行；N：开户行为他行 可
     *         'EACBNK' => '' ,// 他行户口开户行 Z(1,62) 可
     *         'EACCTY' => '' ,// 他行户口开户地 Z(1,62) 可
     *         </pre>
     */
    public function D44_GetAgentDetail($REQNBR)
    {
        $sendArray = [
            'SDKATDQYX' => [
                'REQNBR' => $REQNBR
            ]
        ];
        $result = $this->invokeApi('GetAgentDetail', $sendArray);
        return $result;
    }
    
    /**
     * Judge Pay Status ,True For Success And False For Failure
     * @param unknown $NTQATSQYZ
     * @return boolean
     */
    public function D44_judgement($NTQATDQYZ)
    {
        if (isset($NTQATDQYZ['STSCOD']) && $NTQATDQYZ['STSCOD'] == 'S') {
            return true;
        }
        return false;
    }
    
    // GetAgentDetail
    
    /**
     * 3.9批量查询支付信息
     *
     * @param unknown $REQNBR            
     * @return NTQPAYQYZ * 1...500
     *         <pre>
     *         'BUSCOD' => '' ,// 业务代码 C(6) 附录A.4 否
     *         'BUSMOD' => '' ,// 业务模式 C(5) 否
     *         'DBTBBK' => '' ,// 付方开户地区代码 C(2) 附录A.1 否
     *         'DBTACC' => '' ,// 付方帐号 C(35) 否 企业用于付款的转出帐号，该帐号的币种类型与币种字段相符。
     *         'DBTNAM' => '' ,// 付方帐户名 C(58) 否 企业用于付款的转出帐号的户名
     *         'DBTBNK' => '' ,// 付方开户行 Z(62) 否 企业用于付款的转出帐号的开户行名称，如：招商银行北京分行。
     *         'DBTADR' => '' ,// 付方行地址 Z(62) 可 企业用于付款的转出帐号的开户行地址
     *         'CRTBBK' => '' ,// 收方开户地区代码 C(2) 附录A.1 可
     *         'CRTACC' => '' ,// 收方帐号 C(35) 否 收款企业的转入帐号，该帐号的币种类型与币种字段相符。
     *         'CRTNAM' => '' ,// 收方帐户名 Z(62) 否 收款方企业的转入帐号的帐户名称。
     *         'RCVBRD' => '' ,// 收方大额行号 C(12) 二代支付新增
     *         'CRTBNK' => '' ,// 收方开户行 Z(62) 可 收方帐号的开户行名称，如：招商银行北京分行。
     *         'CRTADR' => '' ,// 收方行地址 Z(62) 可 收方帐号的开户行地址。
     *         'GRPBBK' => '' ,// 母公司开户地区代码 C(2) 附录A.1 可
     *         'GRPACC' => '' ,// 母公司帐号 C(35) 可 企业所属母公司的帐号。只对集团支付有效。
     *         'GRPNAM' => '' ,// 母公司帐户名 Z(62) 可 企业所属母公司帐号的帐户名称。只对集团支付有效。
     *         'CCYNBR' => '' ,// 币种代码 N(2) 附录A.3 否
     *         'TRSAMT' => '' ,// 交易金额 M 否 该笔业务的付款金额。
     *         'EPTDAT' => '' ,// 期望日 D 可 企业银行客户端经办时指定的期望日期。
     *         'EPTTIM' => '' ,// 期望时间 T 可 企业银行客户端经办时指定的期望时间。只有小时数有效。
     *         'BNKFLG' => '' ,// 系统内外标志 C(1) “Y”表示系统内， “N”表示系统外 可 表示该笔业务是否为招行系统内的支付结算业务。
     *         'REGFLG' => '' ,// 同城异地标志 C(1) “Y”表示同城业务； “N”表示异地业务 可 表示该笔业务是否为同城业务。
     *         'STLCHN' => '' ,// 结算方式代码 C(1) N-普通；F-快速 可
     *         'NUSAGE' => '' ,// 用途 Z(28) 可
     *         'NTFCH1' => '' ,// 收方电子邮件 C(36) 可 收款方的电子邮件地址，用于邮件通知。
     *         'NTFCH2' => '' ,// 收方移动电话 C(16) 可 收款方的移动电话，用于短信通知。
     *         'OPRDAT' => '' ,// 经办日期 D 可 经办该笔业务的日期。
     *         'YURREF' => '' ,// 业务参考号 C(30) 否 用于标识该笔业务编号，企业银行编号+业务类型+业务参考号必须唯一。
     *         'REQNBR' => '' ,// 流程实例号 C(10) 可
     *         'BUSNAR' => '' ,// 业务摘要 Z(196) 可 用于企业付款时填写说明或者备注。
     *         'REQSTS' => '' ,// 业务请求状态代码 C(3) 附录A.5 否
     *         'RTNFLG' => '' ,// 业务处理结果代码 C(1) 附录A.6 可
     *         'OPRALS' => '' ,// 操作别名 Z(28) 可 待处理的操作名称。
     *         'RTNNAR' => '' ,// 结果摘要 Z(88) 可 支付结算业务处理的结果描述，如失败原因、退票原因等
     *         'RTNDAT' => '' ,// 退票日期 D 可
     *         'ATHFLG' => '' ,// 是否有附件信息 C(1) “Y”表示有附件，“N”表示无附件 可
     *         'LGNNAM' => '' ,// 经办用户登录名 Z(30) 可
     *         'USRNAM' => '' ,// 经办用户姓名 Z(30) 可
     *         'TRSTYP' => '' ,// 业务种类 C(6) 可 二代支付新增
     *         'FEETYP' => '' ,// 收费方式 C(1) N = 不收费 Y = 收费 可
     *         'RCVTYP' => '' ,// 收方公私标志 C(1) A=对公 P=个人 X=信用卡 可
     *         'BUSSTS' => '' ,// 汇款业务状态 C(1) A =待提出 C=已撤销 D =已删除 P =已提出 R=已退票 W=待处理（待确认） 可
     *         'TRSBRN' => '' ,// 受理机构 C(6) 可
     *         'TRNBRN' => '' ,// 转汇机构 C(6) 可
     *         'RSV30Z' => '' ,// 保留字段 C(30) 可 虚拟户支付时前十位为虚拟户编号
     *         </pre>
     */
    public function D39_NTSTLINF($REQNBR)
    {
        if (! is_array($REQNBR)) {
            $REQNBR = [
                $REQNBR
            ];
        }
        $sendArray = [];
        foreach ($REQNBR as $k => $v) {
            $sendArray['NTSTLINFX'][] = [
                'REQNBR' => $v
            ];
        }
        $result = $this->invokeApi('NTSTLINF', $sendArray);
        return $result;
    }

    public function D39_judgment($return)
    {}

    /**
     * 4.1查询交易代码
     *
     * @param unknown $BUSCOD
     *            N03020：代发；N03030：代扣
     * @return \App\Services\Ambigous
     */
    public function D41_QueryAgentList($BUSCOD)
    {
        $sendArray = [
            'SDKAGTTSX' => [
                'BUSCOD' => $BUSCOD
            ]
        ];
        $result = $this->invokeApi('QueryAgentList', $sendArray);
        return $result;
    }

    /**
     *
     * @return multitype:string <pre>
     *         [
     *         'BUSCOD' => 'N03010', // 业务类别 C(6) N03010:代发工资N03020:代发N03030:代扣 否 默认为代发工资: N03010
     *         'BUSMOD' => '00001', // 业务模式编号 C(5) 否 编号和名称填写其一，填写编号则名称无效。可通过前置机或者查询可经办的业务模式信息（ListMode）获得，必须使用无审批的业务模式
     *         'MODALS' => '', // 业务模式名称 Z(62)
     *         'C_TRSTYP' => '代发工资', // 交易代码名称 Z 附录A.45 否 为空时默认BYSA：代发工资，代发和代扣时必填，可通过4.1获得可以使用的交易代码，也可以通过前置机获取。
     *         'TRSTYP' => 'BYSA', // 交易代码 C(4)
     *         'EPTDAT' => '', // 期望日期 D 可 不用填写，不支持期望日直接代发
     *         'BANKAREA' => '', // 分行名称 附录A.1
     *         'CCYNBR' => '10', // 币种代码 N(2) 附录A.3 可 默认10：人民币 同时有值时CCYNBR有效。
     *         'CURRENCY' => '人民币', // 币种名称 Z(1,10) 附录A.3
     *         'MEMO' => '代发招行', // 用途 Z(1,42) 否
     *         'DMANBR' => '', // 虚拟户编号 C(1,20) 可 记账宝使用
     *         'GRTFLG' => ''
     *         ]
     *         </pre>
     */
    public function D42_AgentRequest_const()
    {
        return [
            'BUSCOD' => 'N03010', // 业务类别 C(6) N03010:代发工资N03020:代发N03030:代扣 否 默认为代发工资: N03010
            'BUSMOD' => '00001', // 业务模式编号 C(5) 否 编号和名称填写其一，填写编号则名称无效。可通过前置机或者查询可经办的业务模式信息（ListMode）获得，必须使用无审批的业务模式
            'MODALS' => '', // 业务模式名称 Z(62)
            'C_TRSTYP' => '代发工资', // 交易代码名称 Z 附录A.45 否 为空时默认BYSA：代发工资，代发和代扣时必填，可通过4.1获得可以使用的交易代码，也可以通过前置机获取。
            'TRSTYP' => 'BYSA', // 交易代码 C(4)
            'EPTDAT' => '', // 期望日期 D 可 不用填写，不支持期望日直接代发
                            // 'DBTACC' => '571908952410801', // 转出账号/转入账号 C(35) 否 代发为转出账号；代扣为转入账号
                            // 'BBKNBR' => '57', // 分行代码 C(2) 附录A.1 否 代码和名称不能同时为空。同时有值时BBKNBR有效。
            'BANKAREA' => '', // 分行名称 附录A.1
                              // 'SUM' => $SUM, // 总金额 M 否
                              // 'TOTAL' => $i, // 总笔数 N(4) 否
            'CCYNBR' => '10', // 币种代码 N(2) 附录A.3 可 默认10：人民币 同时有值时CCYNBR有效。
            'CURRENCY' => '人民币', // 币种名称 Z(1,10) 附录A.3
                                 // 'YURREF' => 'D42' . createSerialNum(), // 业务参考号 C(1,30) 否
            'MEMO' => '代发招行', // 用途 Z(1,42) 否
            'DMANBR' => '', // 虚拟户编号 C(1,20) 可 记账宝使用
            'GRTFLG' => ''
        ];
    }

    /**
     * 4.2直接代发代扣
     *
     * @param array $SDKATSRQX
     *            <pre>
     *            'BUSCOD' => 'N03020', // 业务类别 C(6) N03010:代发工资N03020:代发N03030:代扣 否 默认为代发工资: N03010
     *            'BUSMOD' => '', // 业务模式编号 C(5) 否 编号和名称填写其一，填写编号则名称无效。可通过前置机或者查询可经办的业务模式信息（ListMode）获得，必须使用无审批的业务模式
     *            'MODALS' => '', // 业务模式名称 Z(62)
     *            'C_TRSTYP' => '代发劳务收入', // 交易代码名称 Z 附录A.45 否 为空时默认BYSA：代发工资，代发和代扣时必填，可通过4.1获得可以使用的交易代码，也可以通过前置机获取。
     *            'TRSTYP' => 'BYBC', // 交易代码 C(4)
     *            'EPTDAT' => '', // 期望日期 D 可 不用填写，不支持期望日直接代发
     *            'DBTACC' => '', // 转出账号/转入账号 C(35) 否 代发为转出账号；代扣为转入账号
     *            'BBKNBR' => '59', // 分行代码 C(2) 附录A.1 否 代码和名称不能同时为空。同时有值时BBKNBR有效。
     *            'BANKAREA' => '', // 分行名称 附录A.1
     *            'SUM' => '12.19', // 总金额 M 否
     *            'TOTAL' => '1', // 总笔数 N(4) 否
     *            'CCYNBR' => '10', // 币种代码 N(2) 附录A.3 可 默认10：人民币 同时有值时CCYNBR有效。
     *            'CURRENCY' => '人民币', // 币种名称 Z(1,10) 附录A.3
     *            'YURREF' => 'I' . createSerialNum(), // 业务参考号 C(1,30) 否
     *            'MEMO' => '测试代发', // 用途 Z(1,42) 否
     *            'DMANBR' => '', // 虚拟户编号 C(1,20) 可 记账宝使用
     *            'GRTFLG' => '',// 直连经办网银审批标志 C(1) Y：直连经办、网银审批；空或者其他值：直连经办、无需审批。 可 为Y时必须使用有审批岗的模式；不为Y时，必须使用无审批岗的模式。
     *            
     *            </pre>
     * @param array $SDKATDRQX
     *            1-9998
     *            <pre>'ACCNBR' => '6225885910000108', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
     *            'CLTNAM' => 'Judy Zeng', // 户名 Z(1,62) 否
     *            'TRSAMT' => '12.19', // 金额 M 否
     *            'BNKFLG' => '', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
     *            'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
     *            'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
     *            'TRSDSP' => '测试1219',// 注释 Z(1,20) 可 代扣：如果签订有代扣协议，则必须填写与代扣协议一致的合作方账号（该号为扣款方的客户标识ID）
     *            </pre>
     * @return array $NTREQNBRY
     *         <pre>
     *         'REQNBR' => '0564197481', // 流程实例号 C(10)
     *         'Rsv50z' => 'BNK', // 保留字段 C(50)
     *         </pre>
     */
    public function D42_AgentRequest(array $SDKATSRQX, array $SDKATDRQX)
    {
        // FBS145033704654
        // 3.6直接支付
        $sendArray = [
            'SDKATSRQX' => $SDKATSRQX,
            'SDKATDRQX' => $SDKATDRQX
        ];
        // FBS145025909971
        $result = $this->invokeApi('AgentRequest', $sendArray);
        
        return $result;
    }

    /**
     * 3.6直接支付
     *
     * @param array $SDKPAYRQX
     *            <pre>
     *            'BUSCOD' => 'N02031',//业务类别 C(6) N02031:直接支付N02041:直接集团支付
     *            'BUSMOD' => '00001',//业务模式编号 C(5) 默认为00001
     *            </pre>
     * @param array $DCOPDPAYX
     *            <pre>
     *            'YURREF' => '' ,// 业务参考号 C（30） 否 用于标识该笔业务的编号，也可使用银行缺省值（单笔支付），批量支付须由企业提供。直联必须用企业提供
     *            'EPTDAT' => '' ,// 期望日 D 默认为当前日期 可
     *            'EPTTIM' => '' ,// 期望时间 T 默认为‘000000’ 可
     *            'DBTACC' => '' ,// 付方帐号 N（35） 否 企业用于付款的转出帐号，该帐号的币种类型必须与币种字段相符。
     *            'DBTBBK' => '' ,// 付方开户地区代码 C（2） 附录A.1 否 付方帐号的开户行所在地区，如北京、上海、深圳等。付方开户地区和付方开户地区代码不能同时为空，同时有值时DBTBBK有效。
     *            'TRSAMT' => '' ,// 交易金额 M 否 该笔业务的付款金额。
     *            'CCYNBR' => '' ,// 币种代码 C(2) 附录A.3 否 币种代码和币种名称不能同时为空同时有值时CCYNBR有效。。币种暂时只支持10(人民币)
     *            'STLCHN' => '' ,// 结算方式代码 C(1) N：普通 F：快速 否 只对跨行交易有效
     *            'NUSAGE' => '' ,// 用途 Z（62） 否 对应对账单中的摘要NARTXT
     *            'BUSNAR' => '' ,// 业务摘要 Z（200） 可 用于企业付款时填写说明或者备注。
     *            'CRTACC' => '' ,// 收方帐号 N（35） 否 收款企业的转入帐号，该帐号的币种类型必须与币种字段相符。
     *            'CRTNAM' => '' ,// 收方帐户名 Z（62） 否 收款方企业的转入帐号的帐户名称。
     *            'BRDNBR' => '' ,// 收方行号 C(30) 可 人行自动支付收方联行号
     *            'BNKFLG' => '' ,// 系统内外标志 Y：招行；N：非招行； 否
     *            'CRTBNK' => '' ,// 收方开户行 Z（62） 跨行支付（BNKFLG=N）必填 可
     *            'CTYCOD' => '' ,// 城市代码 C(4) 附录A.18 CRTFLG不为Y时行内支付必填。 可 行内支付填写，为空则不支持收方识别功能。
     *            'CRTADR' => '' ,// 收方行地址 Z(62) 跨行支付（BNKFLG=N）必填；CRTFLG不为Y时行内支付必填。 可 例如：广东省深圳市南山区
     *            'CRTFLG' => '' ,// 收方信息不检查标志 C(1) Y: 行内支付不检查城市代码和收方行地址 默认为Y。 可
     *            'NTFCH1' => '' ,// 收方电子邮件 C（36） 可 收款方的电子邮件地址，用于交易 成功后邮件通知。
     *            'NTFCH2' => '' ,// 收方移动电话 C（16） 可 收款方的移动电话，用于交易 成功后短信通知。
     *            'CRTSQN' => '' ,// 收方编号 C（20） 可 用于标识收款方的编号。非受限收方模式下可重复。
     *            'TRSTYP' => '' ,// 业务种类 C(6) 100001=普通汇兑 101001=慈善捐款 101002 =其他 默认100001 可
     *            'RCVCHK' => '' ,// 行内收方账号户名校验 C(1) 1：校验 空或者其他值：不校验 可 如果为1，行内收方账号与户名不相符则支付经办失败。
     *            'RSV28Z' => '' ,// 保留字段 C(27) 可 虚拟户支付时，前10位填虚拟户编号；集团支付不支持虚拟户支付。
     *            </pre>
     */
    public function D36_DCPayment(array $SDKPAYRQX, array $DCOPDPAYX)
    {
        // 3.6直接支付
        $sendArray = [
            'SDKPAYRQX' => $SDKPAYRQX,
            'DCOPDPAYX' => $DCOPDPAYX
        ];
        // FBS145025909971
        $result = $this->invokeApi('DCPAYMNT', $sendArray);
        return $result;
    }

    /**
     * Judge The Pay Result
     *
     * @param unknown $NTQPAYRQZ            
     * @return boolean
     */
    public function D36_judgment($NTQPAYRQZ)
    {
        if (isset($NTQPAYRQZ['REQNBR']) && $NTQPAYRQZ['REQSTS'] == 'NTE' && substr($NTQPAYRQZ['ERRCOD'], 0, 3) == 'SUC' && ! isset($NTQPAYRQZ['ERRTXT'])) {
            return true;
        }
        return false;
    }

    public function D36_judge_by_d33($NTQPAYQYZ)
    {
        if ($NTQPAYRQZ['RTNFLG'] == 'S' && $NTQPAYRQZ['REQSTS'] == 'FIN') {
            return true;
        }
        return false;
    }

    /**
     *
     * @return $DCOPDPAYX const <pre>
     *         'CCYNBR' => '10' ,// 币种代码 C(2) 附录A.3 否 币种代码和币种名称不能同时为空同时有值时CCYNBR有效。。币种暂时只支持10(人民币)
     *         'STLCHN' => 'F' ,// 结算方式代码 C(1) N：普通 F：快速 否 只对跨行交易有效
     *         'NUSAGE' => '工资代发' ,// 用途 Z（62） 否 对应对账单中的摘要NARTXT
     *         'BNKFLG' => 'Y' ,// 系统内外标志 Y：招行；N：非招行； 否
     *         'CRTFLG' => 'Y' ,// 收方信息不检查标志 C(1) Y: 行内支付不检查城市代码和收方行地址 默认为Y。 可
     *         'TRSTYP' => '100001' ,// 业务种类 C(6) 100001=普通汇兑 101001=慈善捐款 101002 =其他 默认100001 可
     *         'RCVCHK' => '1' ,// 行内收方账号户名校验 C(1) 1：校验 空或者其他值：不校验 可 如果为1，行内收方账号与户名不相符则支付经办失败。
     *        
     *         'BUSNAR' => '' ,// 业务摘要 Z（200） 可 用于企业付款时填写说明或者备注。
     *         'BRDNBR' => '' ,// 收方行号 C(30) 可 人行自动支付收方联行号
     *         'EPTDAT' => '' ,// 期望日 D 默认为当前日期 可
     *         'EPTTIM' => '' ,// 期望时间 T 默认为‘000000’ 可
     *         'CRTBNK' => '' ,// 收方开户行 Z（62） 跨行支付（BNKFLG=N）必填 可
     *         'CTYCOD' => '' ,// 城市代码 C(4) 附录A.18 CRTFLG不为Y时行内支付必填。 可 行内支付填写，为空则不支持收方识别功能。
     *         'CRTADR' => '' ,// 收方行地址 Z(62) 跨行支付（BNKFLG=N）必填；CRTFLG不为Y时行内支付必填。 可 例如：广东省深圳市南山区
     *         'NTFCH1' => '' ,// 收方电子邮件 C（36） 可 收款方的电子邮件地址，用于交易 成功后邮件通知。
     *         'NTFCH2' => '' ,// 收方移动电话 C（16） 可 收款方的移动电话，用于交易 成功后短信通知。
     *         'CRTSQN' => '' ,// 收方编号 C（20） 可 用于标识收款方的编号。非受限收方模式下可重复。
     *         'RSV28Z' => '' ,// 保留字段 C(27) 可 虚拟户支付时，前10位填虚拟户编号；集团支付不支持虚拟户支付。
     *         </pre>
     */
    public function D36_DCOPDPAYX_const()
    {
        return [
            'CCYNBR' => '10', // 币种代码 C(2) 附录A.3 否 币种代码和币种名称不能同时为空同时有值时CCYNBR有效。。币种暂时只支持10(人民币)
            'STLCHN' => 'F', // 结算方式代码 C(1) N：普通 F：快速 否 只对跨行交易有效
            'NUSAGE' => '工资代发', // 用途 Z（62） 否 对应对账单中的摘要NARTXT
            'BNKFLG' => 'Y', // 系统内外标志 Y：招行；N：非招行； 否
            'CRTFLG' => 'Y', // 收方信息不检查标志 C(1) Y: 行内支付不检查城市代码和收方行地址 默认为Y。 可
            'TRSTYP' => '100001', // 业务种类 C(6) 100001=普通汇兑 101001=慈善捐款 101002 =其他 默认100001 可
            'RCVCHK' => '1', // 行内收方账号户名校验 C(1) 1：校验 空或者其他值：不校验 可 如果为1，行内收方账号与户名不相符则支付经办失败。
            'BUSNAR' => '', // 业务摘要 Z（200） 可 用于企业付款时填写说明或者备注。
            'BRDNBR' => '', // 收方行号 C(30) 可 人行自动支付收方联行号
            'EPTDAT' => '', // 期望日 D 默认为当前日期 可
            'EPTTIM' => '', // 期望时间 T 默认为‘000000’ 可
            'CRTBNK' => '', // 收方开户行 Z（62） 跨行支付（BNKFLG=N）必填 可
            'CTYCOD' => '', // 城市代码 C(4) 附录A.18 CRTFLG不为Y时行内支付必填。 可 行内支付填写，为空则不支持收方识别功能。
            'CRTADR' => '', // 收方行地址 Z(62) 跨行支付（BNKFLG=N）必填；CRTFLG不为Y时行内支付必填。 可 例如：广东省深圳市南山区
            'NTFCH1' => '', // 收方电子邮件 C（36） 可 收款方的电子邮件地址，用于交易 成功后邮件通知。
            'NTFCH2' => '', // 收方移动电话 C（16） 可 收款方的移动电话，用于交易 成功后短信通知。
            'CRTSQN' => '', // 收方编号 C（20） 可 用于标识收款方的编号。非受限收方模式下可重复。
            'RSV28Z' => ''
        ]; // 保留字段 C(27) 可 虚拟户支付时，前10位填虚拟户编号；集团支付不支持虚拟户支付。
    }

    /**
     * 1.5取系统信息
     *
     * @param string $SYSTYP            
     * @return Array <pre>
     *         'LGNNAM' => '' ,// 用户登录名 否
     *         'USRNAM' => '' ,// 用户姓名 否
     *         'LGNTIM' => '' ,// 用户上次成功登录时间 否 日期+时间
     *         'USRTYP' => '' ,// 用户类型 "P"：系统管理员；"S"：普通用户 否
     *         'CORTYP' => '' ,// 用户所属公司的类型 "G"：集团企业；"N"：普通企业 否
     *         'CORNAM' => '' ,// 用户所属公司名称
     *         'GRPNAM' => '' ,// 用户所属集团公司 对普通企业，和CORNAM一致
     *         'ICCLGN' => '' ,// 是否证书卡用户 "Y"：是；"N"：不是
     *         'ICCNBR' => '' ,// 证书卡卡号
     *         </pre>
     */
    public function D15_GetSysInfo($SYSTYP = 'USRINF')
    {
        $sendArray = [
            'SDKSYINFX' => [
                'SYSTYP' => $SYSTYP
            ]
        ];
        $result = $this->invokeApi('GetSysInfo', $sendArray);
        return $result['NTQSYINFZ'];
    }

    /**
     * 1.6查询可经办的业务模式信息
     *
     * @param string $BUSCOD            
     * @return unknown
     */
    public function D16_ListMode($BUSCOD = 'N01010')
    {
        $sendArray = [
            'SDKMDLSTX' => [ // 业务类别 N01010 账务查询
                'BUSCOD' => $BUSCOD
            ]
        ];
        // FBS145025909971
        $result = $this->invokeApi('ListMode', $sendArray);
        return $result;
    }

    /**
     * 1.8查询历史通知
     *
     * @param array $FBDLRHMGX
     *            <pre>
     *            'BGNDAT' => '20151218', //否 开始日期 开始日期和结束日期的间隔不能超过100天
     *            'ENDDAT' => '20151230', //否 结束日期
     *            'MSGTYP' => 'NCBCHOPR', //可 消息类型
     *            'MSGNBR' => '', //可 消息号
     *            </pre>
     * @return unknown
     */
    public function D18_GetHisNotice(array $FBDLRHMGX)
    {
        $sendArray = [
            'FBDLRHMGX' => $FBDLRHMGX
        ];
        $result = $this->invokeApi('GetHisNotice', $sendArray);
        $result['C_MSGTYP'] = $this->DA16_MSGTYP;
        return $result;
    }

    /**
     * 2.1查询可查询/可经办的账户列表
     *
     * @param unknown $BUSMOD
     *            业务模式
     *            某个业务有哪些可经办的业务模式，可通
     *            过查询可经办的业务模式信息（ListMode）获得。
     *            账务查询（"BUSCOD=N01010"）时忽略该项
     * @return unknown <pre> Return false when Empty or
     *         NTQACLSTZ
     *         'ACCNAM' => '杭州仁创人力资源服务有限公司',//户名 Z（62） 否
     *         'ACCNBR' => '571908952410602',//帐号 C（35） 否
     *         'BBKNBR' => '57',//分行号 C（2,2） 否 招商银行分行代码（代码对照表请参照附录）
     *         'CCYNBR' => '10',//币种 C（2,2） 附录A.3 否 帐号币种代码（代码对照表请参照附录）
     *         'C_RELNBR' => '杭州仁创人力资源服务有限公司',//公司名称 Z（62） 否
     *         'RELNBR' => ''
     *         </pre>
     */
    public function D21_ListAccount($BUSMOD, $BUSCOD = 'N01010')
    {
        $sendArray = [
            'SDKACLSTX' => [
                'BUSCOD' => $BUSCOD, // 业务类别
                'BUSMOD' => $BUSMOD
            ]
        ]; // 业务模式
        
        try {
            $result = $this->invokeApi('ListAccount', $sendArray);
        } catch (FBSdkException $e) {
            $message = $e->getMessage();
            // NCB4118: -业务模式记录不存在
            if ($e->getCode() == - 9 && strpos($message, 'NCB4118') === 0) {
                return false;
            } else {
                throw $e;
            }
        }
        return $result;
    }

    /**
     * 2.2查询账户详细信息
     * 支持多账户查询。
     *
     * @param unknown $SDKACINFX
     *            <pre>
     *            'BBKNBR' => '', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
     *            'C_BBKNBR' => '',// 分行名称 Z(1,62) 附录A.1 是
     *            'ACCNBR' => '',// 账号 C(1,35) 否
     *            </pre>
     * @return unknown NTQACINFZ<pre>
     *         $resultExample = [
     *         "ACCBLV" => "20062.09",// ACCBLV 上日余额 M 否 当INTCOD='S'时，这个字段的值显示为"头寸额度（集团支付子公司余额）"是子公司的虚拟余额
     *         "ACCITM" => "10001",// ACCITM 科目 C（5,5） 否 科目代码
     *         "ACCNAM" => "银企直连专用账户9",// ACCNAM 注解 Z（62） 否 一般为户名
     *         "ACCNBR" => "591902896710201", // ACCNBR 帐号 C（35） 否
     *         "AVLBLV" => "18506.20", // AVLBLV 可用余额 M 可
     *         "BBKNBR" => "59", // BBKNBR 分行号 C（2,2） 否 招商银行分行代码（代码对照表请参照附录）
     *         "CCYNBR" => "10", // CCYNBR 币种 C（2,2） 否 帐号币种代码（代码对照表请参照附录）
     *         "C_CCYNBR" => "人民币",// C_CCYNBR 币种名称 Z(10) 可
     *         "C_INTRAT" => "0.3500000%", // C_INTRAT 年利率 C（11） 可
     *         "DPSTXT" => "在岸挂牌对", // DPSTXT 存期 Z（12） 定期时，取值：一天, 七天, 一个月,三个月, 六个月,一年,二年,三年,四年,五年
     *         "HLDBLV" => "0.00",// HLDBLV 冻结余额 M 可
     *         "LMTOVR" => "0.00",// LMTOVR 透支额度 M 可
     *         "ONLBLV" => "19895.68",// ONLBLV 联机余额 M 否
     *         "OPNDAT" => "20120928",// OPNDAT 开户日 D 否 8位数字
     *         "STSCOD" => "A"// STSCOD 状态 C（1） A=活动，B=冻结，C=关户 否
     *         ];
     *         INTCOD 利息码 C（1） S=子公司虚拟余额 可
     *         INTRAT 年利率 I 可
     *         MUTDAT 到期日 D 可 8位数字
     *         INTTYP 利率类型 C（3,3） A.35利率类型码
     *         </pre>
     */
    public function D22_GetAccInfo(array $SDKACINFX)
    {
        $sendArray = [
            'SDKACINFX' => $SDKACINFX
        ];
        $result = $this->invokeApi('GetAccInfo', $sendArray);
        // NTQACINFZ
        return $result;
    }

    /**
     * 2.3查询账户交易信息
     *
     * @param array $SDKTSINFX
     *            <pre>
     *            [
     *            'BBKNBR' => '',//分行号 N(2) 附录A.1 可 分行号和分行名称不能同时为空
     *            'C_BBKNBR' => '',// 分行名称 Z(1,62) 附录A.1 可
     *            'ACCNBR' => '',//账号 C(1,35) 否
     *            'BGNDAT' => '',//起始日期 D 否
     *            'ENDDAT' => '',//结束日期 D 否 与结束日期的间隔不能超过100天
     *            'LOWAMT' => '',//最小金额 M 可 默认0.00
     *            'HGHAMT' => '',//最大金额 M 可 默认9999999999999.99
     *            'AMTCDR' => '',//借贷码 C(1) C：收入 D：支出 可
     *            ]
     *            </pre>
     * @return unknown
     */
    public function D23_GetTransInfo(array $SDKTSINFX)
    {
        $sendArray = [
            'SDKTSINFX' => $SDKTSINFX
        ];
        $result = $this->invokeApi('GetTransInfo', $sendArray);
        return $result;
    }
    
    // 2.4查询账户历史余额
    // 2.5查询分行号信息
    // 2.6查询电子回单信息
    
    /**
     * 3.3查询支付结果
     *
     * @param array $SDKPAYQYX
     *            <pre>
     *            [
     *            'BUSCOD' => 'N02031',//业务类别 C(6) 附录A.4 可
     *            'BGNDAT' => '20151201',//起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
     *            'ENDDAT' => '20151230',//结束日期 C(8) 否
     *            'DATFLG' => 'A',//日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
     *            'MINAMT' => '0.00',//MINAMT 最小金额 M 可 空时表示0.00
     *            'MAXAMT' => '99999999.99',//MAXAMT 最大金额 M 可 空时表示9999999999999.99
     *            'YURREF' => 'FBS145033704654',//业务参考号 C(1,30) 可
     *            'RTNFLG' => '',//业务处理结果 C(1) 附录A.6 可 空表示查询所有结果的数据
     *            'OPRLGN' => '',//经办用户 可
     *            ]
     *            </pre>
     * @return array <pre>
     *         'ATHFLG' => 'N',//是否有附件信息 C（1） “Y”表示有附件，“N”表示无附件 可
     *         'BNKFLG' => 'Y',//系统内外标志 C（1） “Y”表示系统内，“N”表示系统外 可 表示该笔业务是否为招行系统内的支付结算业务。
     *         'BUSCOD' => 'N02031',//业务代码 C(6) 附录A.4 否
     *         'BUSMOD' => '00001',//业务模式 C(5) 否
     *         'BUSNAR' => '直接转发银行1',//业务摘要 Z（196） 可 用于企业付款时填写说明或者备注。
     *         'BUSSTS' => 'P',//汇款业务状态 C(1) A =待提出 C=已撤销 D =已删除 P =已提出 R=已退票 W=待处理（待确认） 可
     *         'CCYNBR' => '10',//币种代码 N(2) 附录A.3 否
     *         'CRTACC' => '591902896810209',//收方帐号 C（35） 否 收款企业的转入帐号，该帐号的币种类型与币种字段相符。
     *         'CRTADR' => '福建省福州市',//收方行地址 Z（62） 可 收方帐号的开户行地址。
     *         'CRTBBK' => '59',//收方开户地区代码 C(2) 附录A.1 可
     *         'CRTBNK' => '招商银行股份有限公司福州分行',//收方开户行 Z（62） 可 收方帐号的开户行名称，如：招商银行北京分行。
     *         'CRTNAM' => '银企直连专用账户10',//收方帐户名 Z（62） 否 收款方企业的转入帐号的帐户名称。
     *         'CRTREL' => '',//UNKNOWN
     *         'C_BUSCOD' => '直接支付',//业务类型 Z（12） 附录A.4 否
     *         'C_CCYNBR' => '人民币',//币种 Z（10） 附录A.3 否
     *         'C_CRTBBK' => '福州',//收方开户地区 Z（12） 附录A.1 可
     *         'C_CRTREL' => '',//收方公司名 Z（62） 可 收款方企业的公司名称
     *         'C_DBTBBK' => '福州',//付方开户地区 Z（12） 附录A.1 否
     *         'C_DBTREL' => '银企直连专用账户9',//付方公司名 Z（62） 可 付款企业的公司名称，只对内部转帐有效。
     *         'C_GRPBBK' => '',//母公司开户地区 Z（12） 附录A.1 可 企业所属母公司帐号的开户行所在地区，只对集团支付有效。
     *         'C_REQSTS' => '完成',//业务请求状态 Z（20） 附录A.5 否 支付结算业务请求目前所处的状态
     *         'C_RTNFLG' => '成功',//业务处理结果 Z（20） 附录A.6 可 支付结算业务处理的结果，只有REQSTS=FIN（完成），该字段才有意义
     *         'C_STLCHN' => '普通',//结算方式 Z（12） 快速、普通 可
     *         'DBTACC' => '591902896710903',//付方帐号 C（35） 否 企业用于付款的转出帐号，该帐号的币种类型与币种字段相符。
     *         'DBTADR' => '福建省福州市',//付方行地址 Z（62） 可 企业用于付款的转出帐号的开户行地址
     *         'DBTBBK' => '59',//付方开户地区代码 C(2) 附录A.1 否
     *         'DBTBNK' => '招商银行福州分行白马支行',//付方开户行 Z（62） 否 企业用于付款的转出帐号的开户行名称，如：招商银行北京分行。
     *         'DBTNAM' => '银企直连专用账户9',//付方帐户名 C（58） 否 企业用于付款的转出帐号的户名
     *         'DBTREL' => '0000008365',//UNKNOWN
     *         'EPTDAT' => '20151212',//期望日 D 可 企业银行客户端经办时指定的期望日期。
     *         'EPTTIM' => '000000',//期望时间 T 可 企业银行客户端经办时指定的期望时间。只有小时数有效。
     *         'FEETYP' => 'N',//收费方式 C(1) N = 不收费 Y = 收费 可
     *         'GRPBBK' => '',//母公司开户地区代码 C(2) 附录A.1 可
     *         'LGNNAM' => '银企直连专用集团2',//经办用户登录名 Z（30） 可
     *         'NUSAGE' => '直接转发银行1',//用途 Z（28） 可
     *         'OPRDAT' => '20151212',//经办日期 D 可 经办该笔业务的日期。
     *         'RCVBRD' => '59',//收方大额行号 C(12) 二代支付新增
     *         'RCVTYP' => 'A',//收方公私标志 C(1) A=对公 P=个人 X=信用卡 可
     *         'REGFLG' => 'Y',//同城异地标志 C（1） “Y”表示同城业务；“N”表示异地业务 可 表示该笔业务是否为同城业务。
     *         'REQNBR' => '0028771573',//流程实例号 C(10) 可
     *         'REQSTS' => 'FIN',//业务请求状态代码 C(3) 附录A.5 否
     *         'RTNFLG' => 'S',//业务处理结果代码 C(1) 附录A.6 可
     *         'STLCHN' => 'N',//结算方式代码 N-普通；F-快速 可
     *         'TRSAMT' => '9.67',//交易金额 M 否 该笔业务的付款金额。
     *         'TRSBRN' => '591575',//受理机构 C(6) 可
     *         'TRSTYP' => '100001',//业务种类 C(6) 可 二代支付新增
     *         'USRNAM' => '银企直连专用账户92',//经办用户姓名 Z（30） 可
     *         'YURREF' => '352003144990033386100000000000',//业务参考号 C（30） 否 用于标识该笔业务编号，企业银行编号+业务类型+业务参考号必须唯一。
     *         </pre>
     */
    public function D33_GetPaymentInfo(array $SDKPAYQYX)
    {
        $sendArray = [
            'SDKPAYQYX' => $SDKPAYQYX
        ];
        $result = $this->invokeApi('GetPaymentInfo', $sendArray);
        
        // TODO : 己方支付 YURREF 查询无结果
        return $result;
    }

    /**
     *
     * @return part of SDKPAYQYX <pre>
     *         'BUSCOD' => 'N02031',//业务类别 C(6) 附录A.4 可 N02031 直接支付
     *         'DATFLG' => 'A',//日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
     *         'MINAMT' => '0.00',//MINAMT 最小金额 M 可 空时表示0.00
     *         'MAXAMT' => '99999999.99',//MAXAMT 最大金额 M 可 空时表示9999999999999.99
     *         'RTNFLG' => '',//业务处理结果 C(1) 附录A.6 可 空表示查询所有结果的数据
     *         'OPRLGN' => '',//经办用户 可
     *         </pre>
     */
    public function D33_SDKPAYQYX_const()
    {
        return [
            'BUSCOD' => 'N02031', // 业务类别 C(6) 附录A.4 可 N02031 直接支付 N02040 集团支付 N02041 直接集团支付
            'DATFLG' => 'A', // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            'MINAMT' => '0.00', // MINAMT 最小金额 M 可 空时表示0.00
            'MAXAMT' => '99999999.99', // MAXAMT 最大金额 M 可 空时表示9999999999999.99
            'RTNFLG' => '', // 业务处理结果 C(1) 附录A.6 可 空表示查询所有结果的数据
            'OPRLGN' => ''
        ]; // 经办用户 可
    }

    /**
     * 1.4取新的通知
     *
     * @return \App\Services\Ambigous <pre>
     *         MSGTYP = NCDRTPAY-直接支付结果通知
     *         'ACCNAM' => '银企直连专用账户9',//帐户名称 Z（62） 否
     *         'ACCNBR' => '591902896710201',//帐号 C（35） 否
     *         'BBKNBR' => '59',//分行地区码 C（2,2） 否
     *         'CCYNBR' => '10',//币种代码 C（2） 否 如"10"代表人民币
     *         'EPTDAT' => '20151208',//期望日期 D 否
     *         'EPTTIM' => '000000',//期望时间 T 可
     *         'FLWTYP' => 'N03020',//业务类型 C（6） 否 N02031-直接支付；N02041-直接集团支付
     *         'MSGNBR' => '201512080022030430',//通知序号 C（18） 否 唯一标示一笔通知信息
     *         'MSGTYP' => 'NCDRTPAY',//通知类型 C（8） NCDRTPAY-直接支付结果通知 否
     *         'OPRDAT' => '20151208',//经办日期 D 否
     *         'REQNBR' => '0028771063',//流程实例号 C(10) 可
     *         'RTNDSP' => '无一笔成功入账',//业务结果描述 Z（92） 可
     *         'RTNFLG' => 'F',//业务请求结果 C（1） A.6 业务处理结果 否 如"成功"、"失败"、"退票"、"经办失败"等
     *         'TRSAMT' => '10.00',//交易金额 M 可
     *         'YURREF' => '2015120810000521',//业务参考号 C（30） 可 取值为企业银行客户端经办时录入的参考号，其他情况为空
     *        
     *         MSGTYP = NCCRTTRS-到帐通知；NCDBTTRS-付款通知
     *         'ACCNAM' => '银企直连专用账户9',//帐户名称 Z（62） 否
     *         'ACCNBR' => '591902896710201',//帐号 C（35） 否
     *         'AMTCDR' => 'C',//借贷码 C（1） 'C'代表贷方；'D'代表借方 否
     *         'BBKNBR' => '59',//分行号 N（2,2） 附录A.1 否 如"75"代表深圳
     *         'BLVAMT' => '147695.33',//余额 M 否
     *         'CCYNBR' => '10',//币种代码 C（2,2） 否 如"10"代表人民币
     *         'MSGNBR' => '201512080022030432',//通知序号 C（18） 否 唯一标示一笔通知信息
     *         'MSGTYP' => 'NCCRTTRS',//通知类型 C（8） NCCRTTRS-到帐通知；NCDBTTRS-付款通知； 否
     *         'NARTXT' => '代发余额退款',//摘要 Z（62） 可 有效长度为16字节。若为企业银行客户端经办的交易，则该字段为用途信息（4.0版代发代扣业务除外），若为其它渠道经办的交易，则该字段为交易的简单说明和注解。
     *         'SEQNBR' => 'K7876600000007C',//交易流水号 C（15） 可 银行会计系统交易流水号
     *         'TRSAMT' => '10.00',//交易金额 M 可
     *         'TRSANL' => 'AIGATR',//交易分析码 C（6） 可 1-2位取值含义件附录A.8，3-6位取值含义件附录A.9。建议：该字段取值后台没有统一标准，所以附录额A.8和A.9不易公开发表。如有客户需要区分不同交易，再根据具体情况提供取值范围。
     *         'TRSDAT' => '20150416',//交易日期 D 否
     *         'TRSSET' => 'K78766P416AAAAJ',//交易套号 C（15） 可
     *         'TRSTIM' => '192016',//交易时间 T 否
     *         'VALDAT' => '20150416',//起息日 D 可
     *         'YURREF' => '2015120810000521',//对方参考号 C（30） 可 取值为企业银行客户端经办时录入的参考号，其他情况为空
     *        
     */
    public function D14_GetNewNotice()
    {
        $result = $this->invokeApi('GetNewNotice');
        if (isset($result['NTQNTCGTZ'])) {
            // have new notice
            if (! isset($result['NTQNTCGTZ'][0])) {
                $result['NTQNTCGTZ'] = [
                    $result['NTQNTCGTZ']
                ];
            }
            foreach ($result['NTQNTCGTZ'] as $k => $v) {
                if (isset($v['MSGTYP'])) {
                    $result['NTQNTCGTZ'][$k]['C_MSGTYP'] = $this->DA16_MSGTYP[$v['MSGTYP']];
                }
            }
            return $result['NTQNTCGTZ'];
        } else {
            return [];
        }
    }

    /**
     * 4.5代发工资额度查询
     *
     * @param array $NTAGCLMTX1
     *            <pre>
     *            'ACCNBR' => '591902896710201',//账号 C(35) 否
     *            'BBKNBR' => '59',//分行号 C(2) 否
     *            </pre>
     * @return array <pre>
     *         'TTLLMT' => '' ,// 年度总额度 M 可
     *         'USELMT' => '' ,// 已用额度 M 可
     *         'REMLMT' => '' ,// 剩余额度 M 可
     *         'RSV50Z' => '' ,// 保留字段 C(50) 可
     *         </pre>
     */
    public function D45_NTAGCLMT(array $NTAGCLMTX1)
    {
        $sendArray = [
            'NTAGCLMTX1' => $NTAGCLMTX1
        ];
        $result = $this->invokeApi('NTAGCLMT', $sendArray);
        
        return $result['NTAGCLMTZ1'];
    }

    /**
     * 4.7大批量代发经办
     *
     * @param array $NTAGCAGCX1
     *            * 1
     *            <pre>
     *            'BEGTAG' => 'Y' ,// 批次开始标志 C(1) 否 必须为’Y’或’N’，’Y’表示批次开始，续传批次固定赋值为’N’
     *            'ENDTAG' => 'Y' ,// 批次结束标志 C(1) 否 必须为’Y’或’N’，’Y’表示批次结束，非结束批次固定赋值为’N’
     *            'REQNBR' => '' ,// 流程实例号 C(10) 可 第一次上传时必须为空；续传时不能为空，所有续传次数流程实例号必须为同一个；主机校验该字段值与批次开始、结束标志的匹配性
     *            'TTLAMT' => '21.59' ,// 总金额 M 否 批次总金额，代发代扣系统要求第一次就要必输
     *            'TTLCNT' => '1' ,// 总笔数 F(8,0) 否 批次总笔数，代发代扣系统要求第一次就要必输
     *            'TTLNUM' => '1' ,// 总次数 F(3,0) 否 该批次数据计划分多少次上传完，代发代扣系统要求第一次就要必输
     *            'CURAMT' => '21.59' ,// 本次金额 M 否
     *            'CURCNT' => '1' ,// 本次笔数 F(8,0) 否
     *            'CNVNBR' => '' ,// 合作方协议号 C(6) 可 预留
     *            'CCYNBR' => '10' ,// 交易货币 C(2) 附录A.3 否
     *            'NTFINF' => '大批量代发经办21.59' ,// 个性化短信内容 Z(22) 可 预留，录入则在收方入账短信里展示
     *            'BBKNBR' => '59' ,// 分行号 C(2) 附录A.1 否
     *            'ACCNBR' => '591902896710201' ,// 账号 C(35) 否
     *            'CCYMKT' => '2' ,// 货币市场 C(1) 取值 描述 0 不分钞汇 1 现钞 2 现汇 否
     *            'TRSTYP' => 'BYBC' ,// 交易类型 C(4) 否 即“交易代码编号”
     *            'NUSAGE' => '代发劳务收入' ,// 用途 Z(42) 否
     *            'EPTDAT' => '' ,// 期望日 D 可 默认为当前日期
     *            'EPTTIM' => '' ,// 期望时间 T 可 默认为“000000”
     *            'YURREF' => 'I145062000535281199' ,// 对方参考号 C(30) 否
     *            'DMANBR' => '' ,// 虚拟户编号 C(20) 可
     *            'GRTFLG' => '' ,// 网银审批标志 C(1) Y/N 可
     *            </pre>
     * @param array $NTAGCDTLY1
     *            * n
     *            <pre>
     *            'TRXSEQ' => '00000001' ,// 交易序号 C(8) 否 需要客户自行保证批次范围内的序号唯一性，代发代扣系统要求格式为全数字，如’00000001’、’00000002’
     *            'ACCNBR' => '6225880230001175' ,// 帐号 C(35) 否
     *            'ACCNAM' => '刘五' ,// 户名 Z(62) 否
     *            'TRSAMT' => '21.59' ,// 金额 M 否
     *            'TRSDSP' => '测试交易查询-经办' ,// 注释 Z(42) 可
     *            'BNKFLG' => '' ,// 系统内标志 C(1) Y/N 否 Y:开户行是招商银行;N：开户行是他行。
     *            'EACBNK' => '' ,// 他行户口开户行 Z(62) 可 他行必输
     *            'EACCTY' => '' ,// 他行户口开户地 Z(62) 可 他行必输
     *            'FSTFLG' => 'N' ,// 他行快速标志 C(1) 可 Y:快速N:普通
     *            'RCVBNK' => '' ,// 他行户口联行号 C(12) 可
     *            'CPRACT' => '' ,// 客户代码 C(20) 可 以前代扣将合作方帐号填到注释字段里，现在可以改为填到这个字段；代发可空
     *            'CPRREF' => '' ,// 合作方流水号 C(20) 可 暂无用，预留
     *            </pre>
     * @return \App\Services\Merchants\Ambigous
     */
    public function D47_NTAGCAPY($BUSMOD, array $NTAGCAGCX1, array $NTAGCDTLY1)
    {
        $sendArray = [
            'NTBUSMODY' => [
                'BUSMOD' => $BUSMOD
            ],
            'NTAGCAGCX1' => $NTAGCAGCX1,
            'NTAGCDTLY1' => $NTAGCDTLY1
        ];
        $result = $this->invokeApi('NTAGCAPY', $sendArray);
        // TODO : 无果
        return $result['NTAGCLMTZ1'];
    }

    /**
     * 4.6大批量代发工资经办
     *
     * @param unknown $BUSMOD            
     * @param array $NTAGCAGCX1
     *            <pre>
     *            'BEGTAG' => 'Y' ,// 批次开始标志 C(1) 否 必须为’Y’或’N’，’Y’表示批次开始，续传批次固定赋值为’N’
     *            'ENDTAG' => 'Y' ,// 批次结束标志 C(1) 否 必须为’Y’或’N’，’Y’表示批次结束，非结束批次固定赋值为’N’
     *            'REQNBR' => '' ,// 流程实例号 C(10) 可 第一次上传时必须为空；续传时不能为空，所有续传次数流程实例号必须为同一个；主机校验该字段值与批次开始、结束标志的匹配性
     *            'TTLAMT' => '' ,// 总金额 M 否 批次总金额，代发代扣系统要求第一次就要必输
     *            'TTLCNT' => '' ,// 总笔数 F(8,0) 否 批次总笔数，代发代扣系统要求第一次就要必输
     *            'TTLNUM' => '' ,// 总次数 F(3,0) 否 该批次数据计划分多少次上传完，代发代扣系统要求第一次就要必输
     *            'CURAMT' => '' ,// 本次金额 M 否
     *            'CURCNT' => '' ,// 本次笔数 F(8,0) 否
     *            'CNVNBR' => '' ,// 合作方协议号 C(6) 可 预留
     *            'CCYNBR' => '' ,// 交易货币 C(2) 附录A.3 否
     *            'NTFINF' => '' ,// 个性化短信内容 Z(22) 可 预留，录入则在收方入账短信里展示
     *            'BBKNBR' => '' ,// 分行号 C(2) 附录A.1 否
     *            'ACCNBR' => '' ,// 账号 C(35) 否
     *            'CCYMKT' => '' ,// 货币市场 C(1) 取值 描述 0 不分钞汇 1 现钞 2 现汇 否
     *            'TRSTYP' => '' ,// 交易类型 C(4) 代发工资固定为“BYSA” 否 即“交易代码编号”
     *            'NUSAGE' => '' ,// 用途 Z(42) 否
     *            'EPTDAT' => '' ,// 期望日 D 可 默认为当前日期
     *            'EPTTIM' => '' ,// 期望时间 T 可 默认为“000000”
     *            'YURREF' => '' ,// 对方参考号 C(30) 否
     *            'DMANBR' => '' ,// 虚拟户编号 C(20) 可
     *            'GRTFLG' => '' ,// 网银审批标志 C(1) Y/N 可
     *            </pre>
     * @param array $NTAGCDTLY1
     *            <pre>
     *            'TRXSEQ' => '' ,// 交易序号 C(8) 否 需要客户自行保证批次范围内的序号唯一性，代发代扣系统要求格式为全数字，如’00000001’、’00000002’
     *            'ACCNBR' => '' ,// 帐号 C(35) 否
     *            'ACCNAM' => '' ,// 户名 Z(62) 否
     *            'TRSAMT' => '' ,// 金额 M 否
     *            'TRSDSP' => '' ,// 注释 Z(42) 可
     *            'BNKFLG' => '' ,// 系统内标志 C(1) Y/N 否 Y:开户行是招商银行;N：开户行是他行。
     *            'EACBNK' => '' ,// 他行户口开户行 Z(62) 可 他行必输
     *            'EACCTY' => '' ,// 他行户口开户地 Z(62) 可 他行必输
     *            'FSTFLG' => '' ,// 他行快速标志 C(1) 可 Y:快速N:普通
     *            'RCVBNK' => '' ,// 他行户口联行号 C(12) 可
     *            'CPRACT' => '' ,// 客户代码 C(20) 可 以前代扣将合作方帐号填到注释字段里，现在可以改为填到这个字段；代发可空
     *            'CPRREF' => '' ,// 合作方流水号 C(20) 可 暂无用，预留
     *            </pre>
     * @return \App\Services\Merchants\Ambigous
     */
    public function D46_NTAGCSAL($BUSMOD, array $NTAGCAGCX1, array $NTAGCDTLY1)
    {
        $sendArray = [
            'NTBUSMODY' => [
                'BUSMOD' => $BUSMOD
            ],
            'NTAGCAGCX1' => $NTAGCAGCX1,
            'NTAGCDTLY1' => $NTAGCDTLY1
        ];
        $result = $this->invokeApi('NTAGCSAL', $sendArray);
        return $result['NTAGCLMTZ1'];
    }

    /**
     * 网银互联1.1查询业务经办业务控制信息
     *
     * @param unknown $BUSCOD
     *            业务类型 否
     *            <pre>
     *            N31010=网银贷记
     *            N31011=网银借记
     *            N31012=第三方贷记
     *            N31013=跨行账户信息查询
     *            </pre>
     * @param string $BUSMOD
     *            业务模式 C(5) 可
     * @return <pre> NTQBSCTLZ 0..1 输入接口字段BUSMOD不为空时才返回数据。
     *         [
     *         'CCYNBR' => '' ,// 币种 C(2)
     *         'LOWAMT' => '' ,// 金额下限 M
     *         'HGHAMT' => '' ,// 金额上限 M
     *         'RCVLMT' => '' ,// 是否有收方限制 C(1)
     *         'CHKSUM' => '' ,// 校验和 C(10)
     *         'RSV50Z' => '' ,// 保留字段 50 C(50)
     *         ]
     *         NTQEBACCZ n n为查询的实际结果数
     *         [
     *         'BBKNBR' => '' ,// 银行号 C(2) 附录A.1
     *         'ACCNBR' => '' ,// 帐号 C(35)
     *         'INNFLG' => '' ,// 我行帐号标志 C(1) Y:我行行内帐号 N:他行帐号
     *         'ACCNAM' => '' ,// 帐户名称 Z(200)
     *         'CCYNBR' => '' ,// 币种 C(2) 附录A.3
     *         'RELNBR' => '' ,// 客户关系编号 C(10)
     *         'BNKBRD' => '' ,// 行号 C(12)
     *         'ACCFLG' => '' ,// 状态 C(1)
     *         'RSV30Z' => '' ,// 保留字 30 C(30)
     *         ]
     *         </pre>
     */
    public function I11_NTQEBCTL($BUSCOD, $BUSMOD = '')
    {
        $sendArray = [
            'NTBUSMODY' => [
                'BUSCOD' => $BUSCOD,
                'BUSMOD' => $BUSMOD
            ]
        ];
        $result = $this->invokeApi('NTQEBCTL', $sendArray);
        
        return $result;
    }

    /**
     * 网银互联1.2交易查询
     *
     * @param array $NTWAUEBPY
     *            <pre>
     *            'BUSCOD' => '' ,// 业务类型 C(6) N31010 网银贷记 N31011 网银借记 N31012 第三方贷记 N31013 跨行账户信息查询 可
     *            'BGNDAT' => '' ,// 起始日期 D 否 日期间隔不能超过100天
     *            'ENDDAT' => '' ,// 结束日期 D 否
     *            'MINAMT' => '' ,// 最小金额 M
     *            'MAXAMT' => '' ,// 最大金额 M
     *            'YURREF' => '' ,// 业务参考号 C(30)
     *            'OPRLGN' => '' ,// 经办用户 Z(30)
     *            'AUTSTR' => '' ,// 请求状态 C(30) 附录A.5 可以组合取值，比如AUTSTR = 'AUTNTEWCF'
     *            'RTNSTR' => '' ,// 返回结果 C(30) 附录 A.6 可以组合取值，比如RTNSTR = 'SFBR'
     *            'CNVNBR' => '' ,// 内部协议号 C(10)
     *            </pre>
     * @return NTWAUEBPZ * n
     *         <pre>
     *         'REQNBR' => '' ,// 流程实例号 C(10)
     *         'BUSCOD' => '' ,// 业务类型 C(6) 附录A.4
     *         'BUSMOD' => '' ,// 业务模式 C(5)
     *         'BBKNBR' => '' ,// 银行号 C(2) 附录A.1
     *         'ACCNBR' => '' ,// 帐号 C(35)
     *         'CCYNBR' => '' ,// 币种 C(2) 附录A.3
     *         'TRSAMT' => '' ,// 金额 M
     *         'OPRDAT' => '' ,// 经办日 D
     *         'YURREF' => '' ,// 参考号 C(30)
     *         'REQSTS' => '' ,// 请求状态 C(3) 附录A.5
     *         'RTNFLG' => '' ,// 业务处理结果 C(1) 附录 A.6
     *         'ATHFLG' => '' ,// 附件标志 C(1)
     *         'RSV30Z' => '' ,// 保留字 30 C(30)
     *         </pre>
     */
    public function I12_NTQRYEBP(array $NTWAUEBPY)
    {
        $sendArray = [
            'NTWAUEBPY' => $NTWAUEBPY
        ];
        $result = $this->invokeApi('NTQRYEBP', $sendArray);
        
        return $result;
    }

    /**
     * 网银互联1.3业务交易明细查询
     *
     * @param string $REQNBR
     *            流程实例号 C(10) 是,非必输，但两个字段必输一个
     * @param string $TRXNBR
     *            NP交易流水号 C(11) 是
     * @return NTEBPINFZ <pre>
     *         'REQNBR' => '' ,// 流程实例号 C(10)
     *         'TRXNBR' => '' ,// NP交易流水号 C(11)
     *         'TRXDIR' => '' ,// 交易方向 C(1) I：提回 O：提出
     *         'MSGNBR' => '' ,// 报文类型 C(3) 101：网银贷记 103：网银借记 105：第三方贷记
     *         'TRXDAT' => '' ,// 交易日期 D
     *         'TRXSTS' => '' ,// 交易状态 C(2) 附录 A.48
     *         'IBPKEY' => '' ,// 报文键值 C(50)
     *         'TRSDAT' => '' ,// 委托日期 D
     *         'TRSTIM' => '' ,// 委托时间 T
     *         'CNVNBR' => '' ,// 内部协议号 C(10)
     *         'CCYNBR' => '' ,// 交易货币 C(2) 附录 A.3
     *         'TRXAMT' => '' ,// 交易金额 M
     *         'CLTEAC' => '' ,// 我行客户户口 C(35)
     *         'CLTNBR' => '' ,// 我行客户号 C(10)
     *         'ACRBRD' => '' ,// 付款人开户行行号 C(12)
     *         'ACRNAM' => '' ,// 付款人开户行名称 Z(100)
     *         'PYREAC' => '' ,// 付款人账号 C(35)
     *         'PYRNAM' => '' ,// 付款人名称 Z(100)
     *         'DBTCLR' => '' ,// 付款清算行行号 C(12)
     *         'DBTPAR' => '' ,// 付款人开户行所属网银系统行号 C(12)
     *         'OTHBRD' => '' ,// 发起参与者 C(12)
     *         'ACEBRD' => '' ,// 收款人开户行行号 C(12)
     *         'ACENAM' => '' ,// 收款人开户行名称 Z(100)
     *         'PYEEAC' => '' ,// 收款人账号 C(35)
     *         'PYENAM' => '' ,// 收款人名称 Z(100)
     *         'CDTCLR' => '' ,// 收款清算行行号 C(12)
     *         'CDTPAR' => '' ,// 收款人开户行所属网银系统行号 C(12)
     *         'FEEAMT' => '' ,// 手续费金额 M 第三方贷记手续费金额，其他业务为0
     *         'FEEBRD' => '' ,// 手续费收款行行号 C(12)
     *         'FEEACC' => '' ,// 手续费收款人账号 C(35)
     *         'FEENAM' => '' ,// 手续费收款人名称 Z(100)
     *         'NETDAT' => '' ,// 网上交易日期 D
     *         'NETSEQ' => '' ,// 网上交易单号 C(35)
     *         'NETTXT' => '' ,// 网上交易说明 Z(200)
     *         'AUTTYP' => '' ,// 认证方式 C(4)
     *         'AUTFLG' => '' ,// 认证信息附加标记 C(1) 1= 自动 2= 手工 3= 内部转账
     *         'AUTFMT' => '' ,// 认证信息 C(140) 客户单放在明细最末尾
     *         'REMARK' => '' ,// 交易附言 Z(235)
     *         'BUSCOD' => '' ,// 业务类型编码 C(4) 附录A.49
     *         'BUSTYP' => '' ,// 业务种类编码 C(5)
     *         'ISUCNL' => '' ,// 发起渠道 C(3)
     *         'CLTSEQ' => '' ,// 发起方键值 C(10)
     *         'YURREF' => '' ,// 业务参考号 C(30)
     *         'TRXSET' => '' ,// 交易套号 C(15)
     *         'ACTSET' => '' ,// 记账套号 C(15)
     *         'ACTDAT' => '' ,// 记账日期 D
     *         'RJCCOD' => '' ,// 拒绝码 C(4)
     *         'RJCRSN' => '' ,// 拒绝原因 Z(175)
     *         'RTNNAR' => '' ,// 结果摘要 Z(120)
     *         'RSV50Z' => '' ,// 保留字 50
     *         </pre>
     */
    public function I13_NTEBPINF($REQNBR, $TRXNBR = '')
    {
        $sendArray = [
            'NTEBPINFX' => [
                'REQNBR' => $REQNBR,
                'TRXNBR' => $TRXNBR
            ]
        ];
        $result = $this->invokeApi('NTEBPINF', $sendArray);
        
        return $result;
    }

    /**
     * 网银互联1.4业务总揽查询
     *
     * @param array $NTQNPEBPY
     *            <pre>
     *            'QRYACC' => '' ,// 账号 C(35) 否
     *            'TRXDIR' => '' ,// 交易方向 C(1) I：提回 O：提出 否
     *            'MSGNBR' => '' ,// 业务种类 C(3) 101：网银贷记 103：网银借记 105：第三方贷记 否
     *            'BGNDAT' => '' ,// 交易起始日期 D 否 日期间隔不能超过100天
     *            'ENDDAT' => '' ,// 交易结束日期 D 否
     *            'MINAMT' => '' ,// 最小金额 M
     *            'MAXAMT' => '' ,// 最大金额 M
     *            'YURREF' => '' ,// 业务参考号 C(30)
     *            'TRXSTS' => '' ,// 交易状态 C(2) 附录 A.48
     *            'PYREAC' => '' ,// 付款人账号 C(35)
     *            'PYEEAC' => '' ,// 收款人账号 C(35)
     *            'CNVNBR' => '' ,// 内部协议号 C(10)
     *            </pre>
     * @return NTQNPEBPZ * N <pre>
     *         'TRXNBR' => '' ,// NP交易流水号 C(11)
     *         'TRXDIR' => '' ,// 交易方向 C(1) I：提回 O：提出
     *         'MSGNBR' => '' ,// 业务种类 C(3) 101：网银贷记 103：网银借记 105：第三方贷记 否
     *         'TRXDAT' => '' ,// 交易日期 D
     *         'TRXSTS' => '' ,// 交易状态 C(2) 附录 A.48
     *         'CCYNBR' => '' ,// 交易货币 C(2) 附录 A.3
     *         'TRXAMT' => '' ,// 交易金额 M
     *         'PYREAC' => '' ,// 付款人账号 C(35)
     *         'PYRNAM' => '' ,// 付款人名称 Z(100)
     *         'PYEEAC' => '' ,// 收款人账号 C(3)
     *         'PYENAM' => '' ,// 收款人名称 Z(100)
     *         'YURREF' => '' ,// 业务参考号 C(30)
     *         'CNVNBR' => '' ,// 内部协议号 C(10)
     *         'RSV30Z' => '' ,// 保留字 30 C(30)
     *         </pre>
     */
    public function I14_NTQNPEBP(array $NTQNPEBPY)
    {
        $sendArray = [
            'NTQNPEBPY' => $NTQNPEBPY
        ];
        $result = $this->invokeApi('NTQNPEBP', $sendArray);
        
        return $result;
    }

    /**
     *
     * @return multitype:string <pre>
     *         'TRXDIR' => 'O' ,// 交易方向 C(1) I：提回 O：提出 否
     *         'MSGNBR' => '101' ,// 业务种类 C(3) 101：网银贷记 103：网银借记 105：第三方贷记 否
     *         'MINAMT' => '' ,// 最小金额 M
     *         'MAXAMT' => '' ,// 最大金额 M
     *         'TRXSTS' => '' ,// 交易状态 C(2) 附录 A.48
     *         'PYREAC' => '' ,// 付款人账号 C(35)
     *         'PYEEAC' => '' ,// 收款人账号 C(35)
     *         'CNVNBR' => '' ,// 内部协议号 C(10)
     *         </pre>
     */
    public function I14_NTQNPEBPY_const()
    {
        return [
            'TRXDIR' => 'O', // 交易方向 C(1) I：提回 O：提出 否
            'MSGNBR' => '101', // 业务种类 C(3) 101：网银贷记 103：网银借记 105：第三方贷记 否
            'MINAMT' => '', // 最小金额 M
            'MAXAMT' => '', // 最大金额 M
            'TRXSTS' => '', // 交易状态 C(2) 附录 A.48
            'PYREAC' => '', // 付款人账号 C(35)
            'PYEEAC' => '', // 收款人账号 C(35)
            'CNVNBR' => ''
        ]; // 内部协议号 C(10)
    }

    /**
     * 网银互联2网银贷记
     *
     * @param unknown $BUSCOD            
     * @param array $NTIBCOPRX
     *            <pre>
     *            'SQRNBR' => '0000000001',//流水号 C(10) 否 批次内唯一，批量经办时用作响应结果与请求的对应字段。
     *            'BBKNBR' => 'CB',//付款账号银行号 C(2) 否
     *            'ACCNBR' => '755903332110404',//付款账号 C(35) 否 我行账号
     *            'CNVNBR' => '0000001060',//协议号 C(10) 否 贷记内部协议号
     *            'YURREF' => '20140722100113',//业务参考号 C(30) 否 成功和在途的业务唯一
     *            'CCYNBR' => '10',//币种 C(2) 附录 A.3 否
     *            'TRSAMT' => '10',//金额 M 否
     *            'CRTSQN' => 'RCV0000002',//收方编号 C(20) 可
     *            'NTFCH1' => 'zhiling@msn.com',//通知方式一 C(40) 是
     *            'NTFCH2' => '18388889999',//通知方式二 C(40) 是
     *            'CDTNAM' => '林志玲',//收款人户名 Z(100) 否
     *            'CDTEAC' => '6226000011118888123',//收款人账号 C(35) 否
     *            'CDTBRD' => '102100000128',//收款行行号 C(12) 否
     *            'TRSTYP' => 'C208',//业务类型编码 C(4) 附录A.49 否
     *            'TRSCAT' => '02019',//业务种类编码 C(5) 否
     *            'RMKTXT' => '',//附言 Z(235) 是
     *            'RSV30Z' => '',//保留字 30
     *            </pre>
     * @return array NTOPRRTNZ
     *         <pre>
     *         'OPRSQN' => '' ,// 待处理操作序列 C(3)
     *         'OPRALS' => '' ,// 操作别名 Z(32)
     *         'RTNFLG' => 'F' ,// 业务处理结果 C(1) 附录 A.6
     *         'ERRTXT' => 'CSAC054 户口571908952410602透支(CSEACCK1RI)' ,// 错误文本 Z(92)
     *         'ERRCOD' => 'SUC0000',//错误代码 C(7) 系统返回的错误代码
     *         'REQNBR' => '0497688648',//流程实例号 C(10)
     *         'REQSTS' => 'BNK',//请求状态 C(3) 附录A.5
     *         'SQRNBR' => '0000000001',//流水号 C(10)
     *         </pre>
     *         NTOPRDRTZ 成功才有
     *         <pre>
     *         'RTNTIM' => '006' ,// 等待时间 N(3) 单位：秒
     *         'RSV50Z' => '' ,// 保留字段 50 C(50)
     *         </pre>
     */
    public function I20_NTIBCOPR($BUSMOD, array $NTIBCOPRX)
    {
        $sendArray = [
            'NTOPRMODX' => [
                'BUSMOD' => $BUSMOD
            ],
            'NTIBCOPRX' => $NTIBCOPRX
        ];
        $result = $this->invokeApi('NTIBCOPR', $sendArray);
        
        return $result;
    }

    public function I20_judgment($return)
    {
        if (isset($return['REQNBR']) && $return['REQSTS'] == 'BNK' && substr($return['ERRCOD'], 0, 3) == 'SUC' && ! isset($return['ERRTXT'])) {
            return true;
        }
        return false;
    }

    /**
     * NTIBCOPRX_const
     *
     * @return part of $NTIBCOPRX <pre>
     *         'CRTSQN' => 'RCV0000002',//收方编号 C(20) 可
     *         'NTFCH1' => '',//通知方式一邮箱 C(40) 是
     *         'NTFCH2' => '',//通知方式二 短信 C(40) 是
     *         'CCYNBR' => '10',//币种 C(2) 附录 A.3 否
     *         'BBKNBR' => 'CB',//付款账号银行号 C(2) 否
     *         'TRSTYP' => 'C208',//业务类型编码 C(4) 附录A.49 否
     *         'TRSCAT' => '02019',//业务种类编码 C(5) 否
     *         'RMKTXT' => '',//附言 Z(235) 是
     *         'RSV30Z' => '',//保留字 30
     *         </pre>
     */
    public function I20_NTIBCOPRX_const()
    {
        return [
            'CRTSQN' => 'RCV0000002', // 收方编号 C(20) 可
            'NTFCH1' => '', // 通知方式一邮箱 C(40) 是
            'NTFCH2' => '', // 通知方式二 短信 C(40) 是
            'CCYNBR' => '10', // 币种 C(2) 附录 A.3 否
            'BBKNBR' => 'CB', // 付款账号银行号 C(2) 否
            'TRSTYP' => 'C209', // 业务类型编码 C(4) 附录A.49 否
            'TRSCAT' => '01200', // 业务种类编码 C(5) 否
            'RMKTXT' => '', // 附言 Z(235) 是
            'RSV30Z' => ''
        ]; // 保留字 30
    }

    /**
     * 网银互联3.1网银贷记协议签订经办
     *
     * @param unknown $BUSMOD            
     * @param array $NTSGNCBCX
     *            <pre>
     *            'BBKNBR' => 'CB',//银行号 C(2) 否
     *            'ACCNBR' => '755903332110404',//我行账号 C(35) 否
     *            'PYETEL' => '',//联系电话 C(35) 是
     *            'YURREF' => '20140723113258',//业务参考号 C(30) 否
     *            'SGNLMT' => '',//单笔业务金额上限 M 是 不输表示无单笔金额限制
     *            'DAYCNT' => '',//日累计业务笔数上限 N(8) 是 不输表示无日累计业务笔数限制
     *            'DAYLMT' => '',//日累计金额上限 N(15) 是 不输表示无日累计金额上限限制
     *            'MTHCNT' => '',//月累计业务笔数上限 N(8) 是 不输无月累计业务笔数上限限制
     *            'MTHLMT' => '',//月累计金额上限 N(15) 是 不输表示无月累计金额上限
     *            'EFTDAT' => '20161231',//协议生效日期 D 否
     *            'IFTDAT' => '',//协议失效日期 D 是 如果不填写则表示协议永久有效
     *            'RMKINF' => '',//签约说明 Z(256) 是
     *            </pre>
     * @return \App\Services\Merchants\Ambigous
     */
    public function I31_NTSGNCBC($BUSMOD, array $NTSGNCBCX)
    {
        $sendArray = [
            'NTOPRMODX' => [
                'BUSMOD' => $BUSMOD
            ],
            'NTSGNCBCX' => $NTSGNCBCX
        ];
        $result = $this->invokeApi('NTSGNCBC', $sendArray);
        return $result;
    }

    /**
     * 网银互联3.2协议查询
     *
     * @param unknown $NTQRYCBQX
     *            <pre>
     *            'PTCTYP' => '' ,// 协议类型 C(2) 03=查询协议 02=授权支付协议 01=贷记协议 是 全部传空
     *            'QRYBBK' => '' ,// 银行号 C(2) 否
     *            'QRYACC' => '' ,// 查询账号 C(35) 否
     *            'CNVNBR' => '' ,// 内部协议编号 C(10) 是 输入为单笔查询
     *            'PTCNBR' => '' ,// 人行协议号 C(60) 是
     *            'PTCSTS' => '' ,// 协议状态 C(2) 是
     *            'BGNDAT' => '' ,// 协议生效起始日期 D 是
     *            'ENDDAT' => '' ,// 协议生效结束日期 D 是
     *            'EFTDAT' => '' ,// 协议失效起始日期 D 是
     *            'EFEDAT' => '' ,// 协议失效结束日期 D 是
     *            </pre>
     * @return NTQRYCBQZ * N <pre>
     *        
     *         'ACRTYP' => 'AT00',//协议方账户类型 C(4)
     *         'BEGDAT' => '20151223',//协议生效日期 D
     *         'BRDNAM' => '招商银行',//协议方开户行名称 Z(100)
     *         'CLTEAC' => '571908952410602',//我行客户户口 C(35)
     *         'CLTNBR' => '5719089524',//我行客户号 C(10)
     *         'CNVNBR' => '0000001136',//内部协议号 C(10) 贷记协议只有内部协议号
     *         'DAYBAL' => '9999999999999.93',//日限额余额 M
     *         'DAYLMT' => '9999999999999.99',//日支付限额 M
     *         'ENDDAT' => '99991231',//协议失效日期 D
     *         'EXOBAL' => '0.00',//限额一余额 M
     *         'EXOLMT' => '0.00',//扩展支付限额一 M
     *         'EXRBAL' => '0.00',//限额三余额 M
     *         'EXRLMT' => '0.00',//扩展支付限额三 M
     *         'EXTBAL' => '0.00',//限额二余额 M
     *         'EXTLMT' => '0.00',//扩展支付限额二 M
     *         'LMDCNT' => '99999999',//日累计业务笔数上限 N(8)
     *         'LMMCNT' => '99999999',//月累计业务笔数上限 N(8)
     *         'LMTCCY' => '10',//币种 C(2) 附录 A.3
     *         'MTHBAL' => '9999999999999.57',//月限额余额 M
     *         'MTHLMT' => '9999999999999.99',//月累计支付限额 M
     *         'ONELMT' => '9999999999999.99',//单笔支付限额 M
     *         'PTCTYP' => '01',//协议类型 C(2) 01:贷记协议 02:授权支付协议 03:查询协议
     *         'PYRBRD' => '308584000013',//协议方开户行所属网银系统行号 C(12)
     *         'PYREAC' => '571908952410602',//协议方账号 C(35)
     *         'PYRNAM' => '杭州仁创人力资源服务有限公司',//协议方名称 Z(100)
     *         'REQDAT' => '20151223',//签约日期 D
     *         'STSCOD' => 'A',//协议状态 C(2) 冻结、生效、已撤销 A=生效 H=冻结 C=已撤销
     *         'SYSBRN' => '571526',//处理机构 C(6) 行内系统机构
     *         </pre>
     */
    public function I32_NTQRYCBQ($NTQRYCBQX)
    {
        $sendArray = [
            'NTQRYCBQX' => $NTQRYCBQX
        ];
        $result = $this->invokeApi('NTQRYCBQ', $sendArray);
        return $result;
    }
}