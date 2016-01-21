<?php
namespace App\Http\Controllers;

class GeneralTestController extends BaseController
{

    public static function setDate($tbname, $col, $whereCol, $whereVal, $days, $iv)
    {
        \DB::update("CALL set_table_date_iv('$tbname','$col','$whereCol',$whereVal,$days,$iv);");
    }

    public static function setDateForced($tbname, $col, $whereCol, $whereVal, $forcedDate)
    {
        \DB::update("CALL set_table_forced_date('$tbname','$col','$whereCol','$whereVal','$forcedDate');");
    }

    public static function addSalaryOrder($compay_id, $date = '')
    {
        $date || $date = date('Y-m-d H:i:s');
        $userList = \App\Models\User\UserCompany::select([
            'user.uid',
            'user_company.company_id'
        ])->join('user', 'user.uid', '=', 'user_company.uid')
            ->where('user_company.company_id', $compay_id)
            ->get()
            ->toArray();
        \DB::beginTransaction();
        // Create A new Salary Order
        $order_id = createSerialNum();
        $orderData = [
            'order_id' => $order_id,
            'company_id' => $compay_id,
            'date' => $date,
            'status' => 3,
            'payment_at' => $date,
            'confirm_at' => $date,
            'submit_at' => $date
        ];
        $order = \App\Models\Finance\SalaryOrder::create($orderData);
        $days = (strtotime(date('Y-m-d', strtotime($date))) - strtotime(date('Y-m-d'))) / 86400;
        \App\Http\Controllers\GeneralTestController::advanced([
            'tbname' => 'salary_order',
            'col' => 'created_at',
            'whereCol' => 'order_id',
            'whereVal' => $order_id
        ]);
        $salarys = [
            [
                4000,
                15
            ],
            [
                5000,
                100
            ],
            [
                6000,
                150
            ],
            [
                7000,
                200
            ],
            [
                8000,
                250
            ],
            [
                9000,
                300
            ],
            [
                10000,
                400
            ],
            [
                11000,
                500
            ],
            [
                12000,
                600
            ]
        ];
        
        foreach ($userList as $k => $v) {
            $salary_key = rand(0, count($salarys) - 1);
            $salaryPay = [
                'uid' => $v['uid'],
                'company_id' => $compay_id,
                'order_id' => $order_id,
                'salary' => $salarys[$salary_key][0],
                'tax' => $salarys[$salary_key][1]
            ];
            $salaryPay = \App\Models\Finance\SalaryPay::create($salaryPay);
            
            \App\Http\Controllers\GeneralTestController::advanced([
                'tbname' => 'salary_pay',
                'col' => 'created_at',
                'whereCol' => 'id',
                'whereVal' => $salaryPay['id']
            ]);
        }
        self::fireAdvance($days);
        \DB::commit();
    }

    /**
     *
     * @param array $data
     *            [
     *            'tbname',
     *            'col',
     *            'whereCol',
     *            'whereVal',
     *            ]
     * @return multitype:unknown
     */
    public static function advanced($data = [], $forcedDate = '')
    {
        static $_queue = [];
        if ($data) {
            
            if (is_array($data)) {
                $forcedDate && $data['forcedDate'] = $forcedDate;
                $_queue[] = $data;
            } else 
                if ('[clear]' == $data) {
                    $_queue = [];
                }
        }
        return $_queue;
    }

    public static function fireAdvance($day, $iv = 0)
    {
        $queue = self::advanced();
        $prefix = \DB::getTablePrefix();
        $s = strtotime(date('Y-m-d H:i:s')) - strtotime(date('Y-m-d 00:00:00')) - 60;
        foreach ($queue as $k => $v) {
            if (isset($v['forcedDate'])) {
                self::setDateForced($prefix . $v['tbname'], $v['col'], $v['whereCol'], $v['whereVal'], $v['forcedDate']);
            } else {
                self::setDate($prefix . $v['tbname'], $v['col'], $v['whereCol'], $v['whereVal'], $day, - $s + $k + $iv);
            }
        }
        self::advanced('[clear]');
    }

    public function queueTest()
    {
        $Collection = \Illuminate\Support\Collection::make([
            '0 . ',
            '1 . ',
            '2 . ',
            '3 . ',
            '4 . '
        ]);
        foreach (range(0, 2) as $v) {
            $message = \Illuminate\Foundation\Inspiring::quote();
            $job = (new \App\Jobs\PushMessage(1, $Collection->get($v)))->delay(20 * $v);
            \Bus::dispatch($job);
        }
        $job = (new \App\Jobs\PushMessage(1, $Collection->get(3), 1))->delay(20 * ($v + 1));
        \Bus::dispatch($job);
    }

    public static function daytimeDivision($timestamp)
    {
        $day_seconds = strtotime($timestamp) - strtotime(date('Y-m-d', strtotime($timestamp)));
        $daytime = [
            'morning' => [
                0,
                43200
            ],
            'afternoon' => [
                43201,
                54000
            ],
            'evening' => [
                54000,
                86400
            ]
        ];
        foreach ($daytime as $k => $v) {
            if ($day_seconds >= $v[0] && $day_seconds <= $v[1]) {
                return $k;
            }
        }
        return false;
    }

    /**
     * 通过提现记录生成提现事件，并将提现记录都设为待提现
     */
    public static function generateWithdrawEvents()
    {
        $handler = \App\Models\User\UserWithdraw::select([
            'withdraw_order.order_id',
            'withdraw_order.uid',
            'withdraw_order.amount',
            'withdraw_order.created_at',
            'withdraw_order.success_at',
            'withdraw_order.failed_at',
            'withdraw_order.reason',
            'withdraw_order.status'
        ])->orderBy('withdraw_order.created_at', 'ASC');
        $list = $handler->get()->toArray();
        $addWithdrawData = [];
        foreach ($list as $k => $v) {
            //
            $uid = $v['uid'];
            self::getsetEvent($v['created_at'], 'withdraw', [
                'uid' => $uid,
                'amount' => $v['amount'],
                'order_id' => $v['order_id'],
                'date' => $v['created_at']
            ]);
            // '提现状态0：已申请，1：提现中，2：提现成功，3：提现失败',
            if ($v['status'] == 2) {
                self::getsetEvent($v['success_at'], 'success', [
                    'uid' => $uid,
                    'order_id' => $v['order_id'],
                    'date' => $v['success_at']
                ]);
            } else 
                if ($v['status'] == 3) {
                    self::getsetEvent($v['failed_at'], 'failed', [
                        'uid' => $uid,
                        'order_id' => $v['order_id'],
                        'reason' => $v['reason'],
                        'date' => $v['failed_at']
                    ]);
                }
        }
        \App\Models\User\UserWithdraw::where('uid', '>', 0)->update([
            'status' => 0
        ]);
    }

    /**
     * 通过入职离职记录，生成入职离职事件，并删除入职离职记录
     */
    public static function generateJoinQuitEvents()
    {
        $list = \App\Models\User\UserCompany::all();
        foreach ($list as $k => $v) {
            //
            $uid = $v['uid'];
            self::getsetEvent($v['created_at'], 'join', [
                'uid' => $uid,
                'date' => $v['created_at'] . '',
                'join_at' => $v['join_at'],
                'company_id' => $v['company_id'],
                'user_no' => $v['user_no']
            ]);
            // '提现状态0：已申请，1：提现中，2：提现成功，3：提现失败',
            if ($v['status'] == 0) {
                $quit_at = $v['quit_at'] ? $v['quit_at'] : $v['updated_at'];
                self::getsetEvent($quit_at, 'quit', [
                    'uid' => $uid,
                    'company_id' => $v['company_id'],
                    'date' => $quit_at . ''
                ]);
            }
        }
        \DB::delete('delete from xb_user_company');
        // \DB::update('alter table xb_user_company AUTO_INCREMENT=1 ');
        return;
        \App\Models\User\UserBill::leaveOffice($uid, $company_id);
    }

    /**
     * 通过入职离职记录，生成入职离职事件，并删除入职离职记录
     */
    public static function generateSalaryPayEvents()
    {
        $list = \App\Models\Finance\SalaryOrder::all();
        foreach ($list as $k => $v) {
            //
            self::getsetEvent($v['payment_at'], 'salarypay', [
                'date' => $v['payment_at']
            ]);
        }
        return;
        \App\Services\Finance\FinanceService::salaryOrderPayOff();
    }

    /**
     * 通过合作记录
     */
    public static function generateTerminateCoorperationEvents()
    {
        // 设置企业加入时间为2015-09-11 12:12:12 以适应虚拟订单
        \DB::update('update xb_company set created_at = "2015-09-11 12:12:12" where company_id = 100;');
        // Add coorperation event
        $list = \App\Models\CompanyMo::all();
        foreach ($list as $k => $v) {
            //
            self::getsetEvent($v['created_at'], 'startCoorperation', [
                'company_id' => $v['company_id'],
                'date' => $v['created_at'] . ''
            ]);
            if ($v['status'] == 2) {
                $date = $v['quit_at'] ? $v['quit_at'] : $v['updated_at'];
                self::getsetEvent($date, 'terminateCoorperation', [
                    'company_id' => $v['company_id'],
                    'date' => $date . ''
                ]);
            }
        }
        \App\Models\CompanyMo::where('company_id', '>', 0)->update([
            'status' => 2
        ]);
        return;
    }

    /**
     * 更改企业收益率事件
     */
    public static function generateALterEnterpriseInterestEvents()
    {
        $list = \App\Models\CompanyBusinessConfigurationLogMo::all();
        foreach ($list as $k => $v) {
            //
            self::getsetEvent($v['created_at'], 'alterEnterpriseInterest', [
                'company_id' => $v['company_id'],
                'enterprise_interest' => $v['enterprise_interest'],
                'distribution_share' => $v['distribution_share'],
                'date' => $v['created_at'] . ''
            ]);
        }
        \App\Models\CompanyBusinessConfigurationMo::where('id', '>', 0)->delete();
        return;
    }

    /**
     * 创建用户事件
     */
    public static function generateUserCreateEvents()
    {
        // 三个虚拟订单自09-15始，设置用户加入时间小于09-15
        \DB::update('UPDATE xb_user SET created_at = \'2015-09-10 12:12:12\' WHERE uid < 11');
        $list = \App\Models\User\User::all();
        foreach ($list as $k => $v) {
            //
            self::getsetEvent($v['created_at'], 'userCreate', [
                'uid' => $v['uid'],
                'truename' => $v['truename'],
                'phone' => $v['phone'],
                'identity' => $v['identity'],
                'created_at' => $v['created_at'] . ''
            ]);
        }
        \App\Models\User\User::where('uid', '>', 0)->delete();
        return;
    }

    /**
     * 处理企业开始合作事件
     *
     * @param unknown $company_id            
     * @param unknown $date            
     */
    public static function handleStartCoorperation($company_id, $date)
    {
        \App\Models\Finance\JoinQuit::addRecord(3, [
            'company_id' => $company_id,
            'date' => $date
        ], $date);
        \App\Models\CompanyMo::where('company_id', $company_id)->update([
            'status' => 1
        ]);
    }

    public static function handleTerminateCoorperation($company_id, $date)
    {
        \App\Models\Finance\JoinQuit::addRecord(4, [
            'company_id' => $company_id,
            'date' => $date
        ], $date);
        \App\Models\Finance\CompanyBill::terminateCoorperation($company_id, $date);
    }

    /**
     * 处理用户创建事件
     *
     * @param unknown $uid            
     * @param unknown $truename            
     * @param unknown $phone            
     * @param unknown $identity            
     * @param unknown $created_at            
     */
    public static function handleUserCreate($uid, $truename, $phone, $identity, $created_at)
    {
        \App\Models\User\User::create([
            'uid' => $uid,
            'truename' => $truename,
            'phone' => $phone,
            'identity' => $identity,
            'created_at' => $created_at
        ]);
    }

    /**
     * 处理更改企业计息显示比和收益率事件
     *
     * @param unknown $company_id            
     * @param unknown $enterprise_interest            
     * @param unknown $distribution_share            
     * @param string $date            
     */
    public static function handleALterEnterpriseInterest($company_id, $enterprise_interest, $distribution_share, $date = '')
    {
        \App\Models\CompanyBusinessConfigurationMo::updateOrCreate([
            'company_id' => $company_id
        ], [
            'company_id' => $company_id,
            'enterprise_interest' => $enterprise_interest,
            'distribution_share' => $distribution_share
        ]);
        if ($company_id == 1) {
            funcCache([
                \App\Models\CompanyBusinessConfigurationMo::class,
                'getDefaultConfig'
            ], [], '[clear]');
        }
    }

    /**
     * 处理离职事件
     *
     * @param unknown $uid            
     * @param unknown $company_id            
     * @param unknown $date            
     */
    public static function handleLeaveOffice($uid, $company_id, $date)
    {
        \App\Models\Finance\JoinQuit::addRecord(2, [
            'uid' => $uid,
            'company_id' => $company_id,
            'date' => $date
        ], $date);
        \App\Models\User\UserBill::leaveOffice($uid, $company_id, $date);
    }

    /**
     * 处理用户入职事件
     *
     * @param unknown $uid            
     * @param unknown $date            
     * @param unknown $join_at            
     * @param unknown $company_id            
     * @param unknown $user_no            
     */
    public static function handleJoinCompany($uid, $date, $join_at, $company_id, $user_no)
    {
        \App\Models\Finance\JoinQuit::addRecord(1, get_defined_vars(), $date);
        \App\Models\User\UserCompany::create([
            'uid' => $uid,
            'join_at' => $join_at,
            'company_id' => $company_id,
            'user_no' => $user_no,
            'created_at' => $date
        ]);
    }

    /**
     * 处理用户提现事件
     *
     * @param unknown $uid            
     * @param unknown $amount            
     * @param unknown $order_id            
     * @param string $date            
     * @return boolean
     */
    public static function handleUserWithdraw($uid, $amount, $order_id, $date = '')
    {
        \DB::beginTransaction();
        $bankroll = \App\Models\User\UserBankRoll::getUserBankroll($uid);
        $payway = \App\Models\User\UserPayway::getUserCurrentPayway($uid);
        // $order_id = createSerialNum();
        
        $from_company_amt = $bankroll['company_amount'] > $amount ? $amount : $bankroll['company_amount'];
        $from_personal_amt = $bankroll['company_amount'] > $amount ? 0 : $amount - $bankroll['company_amount'];
        
        $userCompany = \App\Models\User\UserCompany::getUserCompany($uid);
        $from_company_id = $userCompany['status'] == 1 ? $userCompany['company_id'] : '';
        
        $withdrawData = [
            'amount' => $amount,
            'rest_amount' => $bankroll['rest_amount'] - $amount,
            'from_company_amt' => $from_company_amt,
            'from_personal_amt' => $from_personal_amt,
            'from_company_id' => $from_company_id
        ];
        $withdrawRecord = \App\Models\User\UserWithdraw::where([
            'uid' => $uid,
            'order_id' => $order_id
        ])->update($withdrawData);
        $inputData = [
            'uid' => $uid,
            'amount' => $amount,
            'after_rest_amt' => $withdrawData['rest_amount'],
            'after_personal_amt' => $bankroll['personal_amount'] - $from_personal_amt,
            'after_company_amt' => $bankroll['company_amount'] - $from_company_amt,
            'relation_id' => $order_id,
            '_event_name_params' => [
                substr($payway['card_no'], - 4)
            ]
        ];
        \App\Models\User\UserBill::withdrawBill($uid, $inputData, $date);
        \App\Models\User\UserBankRoll::financeChange($uid, [
            'rest_amount' => - $amount, // 余额
            'personal_amount' => - $from_personal_amt, // 个人资金池
            'company_amount' => - $from_company_amt, // 企业资金池// 提现金额
            'withdraw_amount' => $amount
        ]);
        // /////////////
        if ($from_company_amt) {
            if ($userCompany && $userCompany['status'] == 1) {
                $company_id = $userCompany['company_id'];
                $companyBankroll = \App\Models\Finance\CompanyBankRoll::getCompanyBankroll($company_id);
                \App\Models\Finance\CompanyBill::addEventRecord($company_id, [
                    'amount' => $from_company_amt, // 变动金额
                    'after_rest_amt' => $companyBankroll['company_amount'] - $from_company_amt, // 变动后余额
                    'event_type' => 2, // 变动类别// 事件类别，0用户收益，1薪资发放，2提现，3提现失败返回，4离职企业资金池转入个人资
                    'relation_id' => $order_id,
                    'relation_uid' => $uid
                ], $date); // 企业资金池
                \App\Models\Finance\CompanyBankRoll::financeChange($company_id, [
                    'company_amount' => - $from_company_amt
                ]); // 税前薪资
            }
        }
        // //////////////////
        \DB::commit();
        return true;
    }

    /**
     * 添加/获取事件
     *
     * @param string $datetime
     *            发生事件
     * @param string $event
     *            事件类型
     * @param array $params
     *            事件参数
     * @return Ambigous <multitype:, multitype:string unknown >
     */
    public static function getsetEvent($datetime = '', $event = '', $params = [])
    {
        static $_data = [];
        if ($datetime) {
            $datetime .= '';
            $date = date('Y-m-d', strtotime($datetime));
            $daytime = self::daytimeDivision($datetime);
            $_data[$date][$daytime][] = [
                'datetime' => $datetime,
                'event' => $event,
                'params' => $params
            ];
        }
        return $_data;
    }

    /**
     * 触发事件
     *
     * @param unknown $date
     *            年月日
     * @param unknown $daytime
     *            早中晚
     * @return void|boolean
     */
    public static function fireEvents($date, $daytime)
    {
        static $data = [];
        empty($data) && $data = self::getsetEvent();
        
        // $data = self::getsetEvent();
        // foreach ($data as $key => $value) {
        // foreach ($value as $k => $v) {
        // usort($data[$key][$k], function ($a, $b) {
        // return $a['datetime'] > $b['datetime'];
        // });
        // }
        // }
        $eventHandler = [
            'withdraw' => '\App\Http\Controllers\GeneralTestController::handleUserWithdraw',
            'success' => '\App\Models\User\UserWithdraw::userWithdrawSuccess',
            'failed' => '\App\Models\User\UserWithdraw::userWithdrawFailed',
            'join' => '\App\Http\Controllers\GeneralTestController::handleJoinCompany',
            'quit' => '\App\Http\Controllers\GeneralTestController::handleLeaveOffice',
            'salarypay' => '\App\Services\Finance\FinanceService::salaryOrderPayOff',
            'terminateCoorperation' => '\App\Http\Controllers\GeneralTestController::handleTerminateCoorperation',
            'alterEnterpriseInterest' => '\App\Http\Controllers\GeneralTestController::handleALterEnterpriseInterest',
            'userCreate' => '\App\Http\Controllers\GeneralTestController::handleUserCreate',
            'startCoorperation' => '\App\Http\Controllers\GeneralTestController::handleStartCoorperation'
        ];
        if (isset($data[$date][$daytime])) {
            usort($data[$date][$daytime], function ($a, $b) {
                return $a['datetime'] > $b['datetime'];
            });
            foreach ($data[$date][$daytime] as $k => $v) {
                call_user_func_array(explode('::', $eventHandler[$v['event']]), $v['params']);
            }
            unset($data[$date][$daytime]);
        }
    }

    public function developGeneration()
    {
        \App\Services\Finance\FinanceService::makeCompanyBankroll();
        \App\Services\Finance\FinanceService::makeUserBankroll();
        \DB::beginTransaction();
        $day = 120;
        $startDate = strtotime("-$day days", time());
        /**
         * 公司取消合作，员工以离职处理，会覆盖员工离职情况
         */
        $result = \App\Models\CompanyMo::select([
            'company.company_id',
            'company.quit_at',
            'company.updated_at'
        ])->join('user_company', function ($query) {
            $query->on('user_company.company_id', '=', 'company.company_id')
                ->on('company.quit_at', '=', 'user_company.updated_at');
        })
            ->where('company.status', 2)
            ->groupBy('company.company_id')
            ->get()
            ->toArray();
        
        foreach ($result as $k => $v) {
            $v = (array) $v;
            self::getsetEvent($v['quit_at'], 'terminateCoorperation', [
                'company_id' => $v['company_id'],
                'date' => $v['quit_at']
            ]);
        }
        \DB::update('UPDATE xb_company c
JOIN xb_user_company uc ON uc.company_id = c.company_id
SET uc.`status` = 1,
 c.`status` = 1,
 uc.quit_at = NULL
WHERE
	c.`status` = 2
AND c.updated_at = uc.updated_at;');
        // 设置初始计息显示比、收益分配比
        self::getsetEvent('2015-09-01 15:45:24', 'alterEnterpriseInterest', [
            'company_id' => 1,
            'enterprise_interest' => 100,
            'distribution_share' => 1,
            'date' => '2015-09-01 15:45:24'
        ]);
        
        self::generateWithdrawEvents();
        self::generateJoinQuitEvents();
        self::generateSalaryPayEvents();
        self::generateTerminateCoorperationEvents();
        self::generateALterEnterpriseInterestEvents();
        self::generateUserCreateEvents();
        
        foreach (range(0, $day) as $k => $v) {
            $today = date('Y-m-d', strtotime('+' . $k . ' days', $startDate));
            $yesterday = date('Y-m-d', strtotime('+' . ($k - 1) . ' days', $startDate));
            // 00:01
            \App\Services\Finance\FinanceService::computeUserProfit($today);
            \App\Services\Finance\FinanceService::computeCompanyProfitAnyTime($today);
            self::fireAdvance(- $day + $v);
            self::fireEvents($today, 'morning');
            // 12:00
            // \App\Models\Finance\IncomeRate::setRate(1, rand(200, 600) / 100, $today);
            self::fireEvents($today, 'afternoon');
            // 16:01
            // \App\Services\Finance\FinanceService::salaryOrderPayOff($today . ' 15:01:00'); // date('H:i:s')
            self::fireEvents($today, 'evening');
            self::fireAdvance(- $day + $v, 57600);
        }
        // \Artisan::queue('queue:listen');
        \DB::commit();
    }

    public function developTest()
    {
        set_time_limit(400);
        ini_set('memory_limit', '1014M');
        mt_mark('start-all');
        $this->developGeneration();
        dump(mt_mark('start-all', 'end-all', 'MB'));
        exit();
    }

    public function str()
    {
        
        
        
        $str = 'BUSCOD	业务代码	C(6)	附录A.4	否	
BUSMOD	业务模式	C(5)		否	
DBTBBK	付方开户地区代码	C(2)	附录A.1	否	
DBTACC	付方帐号	C(35)		否	企业用于付款的转出帐号，该帐号的币种类型与币种字段相符。
DBTNAM	付方帐户名	C(58)		否	企业用于付款的转出帐号的户名
DBTBNK	付方开户行	Z(62)		否	企业用于付款的转出帐号的开户行名称，如：招商银行北京分行。
DBTADR	付方行地址	Z(62)		可	企业用于付款的转出帐号的开户行地址
CRTBBK	收方开户地区代码	C(2)	附录A.1	可	
CRTACC	收方帐号	C(35)		否	收款企业的转入帐号，该帐号的币种类型与币种字段相符。
CRTNAM	收方帐户名	Z(62)		否	收款方企业的转入帐号的帐户名称。
RCVBRD	收方大额行号	C(12)			二代支付新增
CRTBNK	收方开户行	Z(62)		可	收方帐号的开户行名称，如：招商银行北京分行。
CRTADR	收方行地址	Z(62)		可	收方帐号的开户行地址。
GRPBBK	母公司开户地区代码	C(2)	附录A.1	可	
GRPACC	母公司帐号	C(35)		可	企业所属母公司的帐号。只对集团支付有效。
GRPNAM	母公司帐户名	Z(62)		可	企业所属母公司帐号的帐户名称。只对集团支付有效。
CCYNBR	币种代码	N(2)	附录A.3	否	
TRSAMT	交易金额	M		否	该笔业务的付款金额。
EPTDAT	期望日	D		可	企业银行客户端经办时指定的期望日期。
EPTTIM	期望时间	T		可	企业银行客户端经办时指定的期望时间。只有小时数有效。
BNKFLG	系统内外标志	C(1)	“Y”表示系统内， “N”表示系统外	可	表示该笔业务是否为招行系统内的支付结算业务。
REGFLG	同城异地标志	C(1)	“Y”表示同城业务； “N”表示异地业务	可	表示该笔业务是否为同城业务。
STLCHN	结算方式代码	C(1)	N-普通；F-快速	可	
NUSAGE	用途	Z(28)		可	
NTFCH1	收方电子邮件	C(36)		可	收款方的电子邮件地址，用于邮件通知。
NTFCH2	收方移动电话	C(16)		可	收款方的移动电话，用于短信通知。
OPRDAT	经办日期	D		可	经办该笔业务的日期。
YURREF	业务参考号	C(30)		否	用于标识该笔业务编号，企业银行编号+业务类型+业务参考号必须唯一。
REQNBR	流程实例号	C(10)		可	
BUSNAR	业务摘要	Z(196)		可	用于企业付款时填写说明或者备注。
REQSTS	业务请求状态代码	C(3)	附录A.5	否	
RTNFLG	业务处理结果代码	C(1)	附录A.6	可	
OPRALS	操作别名	Z(28)		可	待处理的操作名称。
RTNNAR	结果摘要	Z(88)		可	支付结算业务处理的结果描述，如失败原因、退票原因等
RTNDAT	退票日期	D		可	
ATHFLG	是否有附件信息	C(1)	“Y”表示有附件，“N”表示无附件	可	
LGNNAM	经办用户登录名	Z(30)		可	
USRNAM	经办用户姓名	Z(30)		可	
TRSTYP	业务种类	C(6)		可	二代支付新增
FEETYP	收费方式	C(1)	N = 不收费     Y = 收费  	可	
RCVTYP	收方公私标志	C(1)	A=对公 P=个人 X=信用卡	可	
BUSSTS	汇款业务状态	C(1)	A =待提出 C=已撤销 D =已删除 P =已提出 R=已退票 W=待处理（待确认）	可	
TRSBRN	受理机构	C(6)		可	
TRNBRN	转汇机构	C(6)		可	
RSV30Z	保留字段	C(30)		可	虚拟户支付时前十位为虚拟户编号
    ';
//         $arr = $this->tableViewArray($str, false);
//         preArrayKV($arr);
        $this->tableViewToArrayAnn($str);
        exit();
        $xml = '<?xml version="1.0" encoding="GBK"?><CMBSDKPGK><INFO><DATTYP>2</DATTYP><ERRMSG></ERRMSG><FUNNAM>NTIBCOPR</FUNNAM><LGNNAM>王勇W</LGNNAM><RETCOD>0</RETCOD></INFO><NTOPRRTNZ><ERRCOD>CSAC054</ERRCOD><ERRTXT>CSAC054 户口571908952410602透支(CSEACCK1RI)</ERRTXT><REQNBR>0497530340</REQNBR><REQSTS>FIN</REQSTS><RTNFLG>F</RTNFLG><SQRNBR>0000000001</SQRNBR></NTOPRRTNZ></CMBSDKPGK>';
        
        $this->tableViewToArrayAnn($str);
        $xml = iconv('UTF-8', 'GBK', $xml);
        $FBSdk = \App\Services\Merchants\FBSdkService::getInstance();
        $resultExample = $FBSdk->__xmlToArray($xml);
        $this->resultExampleAnn($resultExample['NTOPRRTNZ'], $this->tableViewArray($str));
        
        exit();
    }

    public function wangYinHuLian()
    {
        $FBSdk = \App\Services\Merchants\FBSdkService::getInstance();
        // /REQNBR 0497530340
        $REQNBR = '0497819644';
        edump($FBSdk->NTEBPINF($REQNBR));
        // $REQNBR = '0497764343';
        // edump($FBSdk->NTEBPINF($REQNBR));
        // edump($FBSdk->NTEBPINF($REQNBR));
        // dump($FBSdk->ListMode('N31010'));
        // edump($FBSdk->ListAccount('00001','N31010'));
        // edump($FBSdk->GetAccInfo([
        // 'BBKNBR' => '57', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
        // 'C_BBKNBR' => '',// 分行名称 Z(1,62) 附录A.1 是
        // 'ACCNBR' => '571908952410602',// 账号 C(1,35) 否
        // ]));
        // 0497688648
        // 网银互联2 网银贷记
        edump($FBSdk->NTIBCOPR('00001', [
            [
                'SQRNBR' => '0000000001', // 流水号 C(10) 否 批次内唯一，批量经办时用作响应结果与请求的对应字段。
                'BBKNBR' => 'CB', // 付款账号银行号 C(2) 否
                'ACCNBR' => '571908952410602', // 付款账号 C(35) 否 我行账号
                'CNVNBR' => '0000001136', // 协议号 C(10) 否 贷记内部协议号
                'YURREF' => 'I201512231422', // 业务参考号 C(30) 否 成功和在途的业务唯一
                'CCYNBR' => '10', // 币种 C(2) 附录 A.3 否
                'TRSAMT' => '0.01', // 金额 M 否
                'CRTSQN' => 'RCV0000001', // 收方编号 C(20) 可
                'NTFCH1' => 'jinyanlin@renrenfenqi.com', // 通知方式一 C(40) 是
                'NTFCH2' => '18767135775', // 通知方式二 C(40) 是
                'CDTNAM' => '金燕林1', // 收款人户名 Z(100) 否
                'CDTEAC' => '6228580699005714769', // 收款人账号 C(35) 否
                'CDTBRD' => '402331000007', // 收款行行号 C(12) 否
                'TRSTYP' => 'C201', // 业务类型编码 C(4) 附录A.49 否
                'TRSCAT' => '02008', // 业务种类编码 C(5) 否
                'RMKTXT' => '测试网银互联名字错误', // 附言 Z(235) 是
                'RSV30Z' => ''
            ]
        ])) // 保留字 30

        ;
        // 402331000007
        // 网银互联1.1查询业务经办业务控制信息
        edump($FBSdk->NTQEBCTL('N31010'));
        // 网银互联1.2交易查询
        edump($FBSdk->NTQRYEBP([
            'BUSCOD' => 'N31010', // 业务类型 C(6) N31010 网银贷记 N31011 网银借记 N31012 第三方贷记 N31013 跨行账户信息查询 可
            'BGNDAT' => '', // 起始日期 D 否 日期间隔不能超过100天
            'ENDDAT' => '', // 结束日期 D 否
            'MINAMT' => '', // 最小金额 M
            'MAXAMT' => '', // 最大金额 M
            'YURREF' => '', // 业务参考号 C(30)
            'OPRLGN' => '', // 经办用户 Z(30)
            'AUTSTR' => '', // 请求状态 C(30) 附录A.5 可以组合取值，比如AUTSTR = 'AUTNTEWCF'
            'RTNSTR' => '', // 返回结果 C(30) 附录 A.6 可以组合取值，比如RTNSTR = 'SFBR'
            'CNVNBR' => ''
        ])); // 内部协议号 C(10)

        // 网银互联1.3业务交易明细查询
        edump($FBSdk->NTEBPINF($REQNBR));
        // 网银互联1.4业务总揽查询
        edump($FBSdk->NTQNPEBP([
            'QRYACC' => '', // 账号 C(35) 否
            'TRXDIR' => '', // 交易方向 C(1) I：提回 O：提出 否
            'MSGNBR' => '', // 业务种类 C(3) 101：网银贷记 103：网银借记 105：第三方贷记 否
            'BGNDAT' => '', // 交易起始日期 D 否 日期间隔不能超过100天
            'ENDDAT' => '', // 交易结束日期 D 否
            'MINAMT' => '', // 最小金额 M
            'MAXAMT' => '', // 最大金额 M
            'YURREF' => '', // 业务参考号 C(30)
            'TRXSTS' => '', // 交易状态 C(2) 附录 A.48
            'PYREAC' => '', // 付款人账号 C(35)
            'PYEEAC' => '', // 收款人账号 C(35)
            'CNVNBR' => ''
        ])); // 内部协议号 C(10)

        
        // 网银互联3.1网银贷记协议签订经办
        edump($FBSdk->NTSGNCBC('00001', [
            'BBKNBR' => 'CB', // 银行号 C(2) 否
            'ACCNBR' => '755903332110404', // 我行账号 C(35) 否
            'PYETEL' => '', // 联系电话 C(35) 是
            'YURREF' => '20140723113258', // 业务参考号 C(30) 否
            'SGNLMT' => '', // 单笔业务金额上限 M 是 不输表示无单笔金额限制
            'DAYCNT' => '', // 日累计业务笔数上限 N(8) 是 不输表示无日累计业务笔数限制
            'DAYLMT' => '', // 日累计金额上限 N(15) 是 不输表示无日累计金额上限限制
            'MTHCNT' => '', // 月累计业务笔数上限 N(8) 是 不输无月累计业务笔数上限限制
            'MTHLMT' => '', // 月累计金额上限 N(15) 是 不输表示无月累计金额上限
            'EFTDAT' => '20161231', // 协议生效日期 D 否
            'IFTDAT' => '', // 协议失效日期 D 是 如果不填写则表示协议永久有效
            'RMKINF' => ''
        ])); // 签约说明 Z(256) 是

        
        // 网银互联3.2协议查询
        edump($FBSdk->NTQRYCBQ([
            'PTCTYP' => '03', // 协议类型 C(2) 03=查询协议 02=授权支付协议 01=贷记协议 是 全部传空
            'QRYBBK' => 'CB', // 银行号 C(2) 否
            'QRYACC' => '755903332110404', // 查询账号 C(35) 否
            'CNVNBR' => '', // 内部协议编号 C(10) 是 输入为单笔查询
            'PTCNBR' => '', // 人行协议号 C(60) 是
            'PTCSTS' => '', // 协议状态 C(2) 是
            'BGNDAT' => '', // 协议生效起始日期 D 是
            'ENDDAT' => '', // 协议生效结束日期 D 是
            'EFTDAT' => '', // 协议失效起始日期 D 是
            'EFEDAT' => ''
        ])); // 协议失效结束日期 D 是

    }

    function _sock($url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);
        $port = $port ? $port : 80;
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);
        if ($query)
            $path .= '?' . $query;
        if ($scheme == 'https') {
            $host = 'ssl://' . $host;
        }
        
        $fp = fsockopen($host, $port, $error_code, $error_msg, 1);
        if (! $fp) {
            return array(
                'error_code' => $error_code,
                'error_msg' => $error_msg
            );
        } else {
            stream_set_blocking($fp, true); // 开启了手册上说的非阻塞模式
            stream_set_timeout($fp, 1); // 设置超时
            $header = "GET $path HTTP/1.1\r\n";
            $header .= "Host: $host\r\n";
            $header .= "Connection: close\r\n\r\n"; // 长连接关闭
            fwrite($fp, $header);
            usleep(1000); // 这一句也是关键，如果没有这延时，可能在nginx服务器上就无法执行成功
            fclose($fp);
            return array(
                'error_code' => 0
            );
        }
    }

    public function fs()
    {
        echo 'start' . time(), PHP_EOL;
        $host = $_SERVER['HTTP_HOST'];
        $fsp = fsockopen($host, '80', $errno, $errstr, 30);
        // 这里指定要请求的地址/CliTest/Server.php
        $header = "GET /v1/tttt HTTP/1.1\r\n";
        $header .= "Host: $host\r\n";
        $header .= "Connection: Close\r\n\r\n";
        stream_set_blocking($fsp, 0); // 开启了手册上说的非阻塞模式
                                     // stream_set_timeout($fsp,1);//设置超时
        fwrite($fsp, $header);
        // $row = fread($fsp, 4096);
        usleep(1000);
        fclose($fsp);
        echo 'end' . time(), PHP_EOL;
        exit();
    }

    public function tttt()
    {
        ignore_user_abort(true); // 如果客户端断开连接，不会引起脚本abort
        set_time_limit(0); // 取消脚本执行延时上限
        \LRedis::SETEX('IN_FUNC', 10, time());
        sleep(5);
        \LRedis::SETEX('TTTT', 60, 'sad');
        
        $uid = \Input::get('uid');
        \LRedis::SETEX('UID-' . $uid, 60, 'sad');
        echo 'ok';
    }

    
    
    public function __xmlToArray($xmlStr){
        
        $xml = simplexml_load_string($xmlStr);
        if ($xml === false) {
            throw new \App\Exceptions\ServiceException('Error Occured When Loading Xml From String');
        }
        $resultArray = [];
        $child = (array) $xml->children();
        $child = $child['content'];
        $i = 0;
        foreach ($xml->children() as $k => $v) {
//             contentuid
            $contentuid = '';
            foreach ($v->attributes() as $k1=>$v2){
                $contentuid = $v2.'';
            }
            $resultArray[$contentuid] = $child[$i++];
        }
        return $resultArray;
    }
    
    
    public function test()
    {
        
        
        $word = \Input::get('w');

//         if(!$word){
//             echo 'Word Is Required!';
//             exit();
//         }
        
        $sourceData =  \App\Models\Transfer::all()->toArray();
        $transferData = [];
        
        
        $file = 'yun.xml';
        $content = file_get_contents($file);
        $resEN =  $this->__xmlToArray($content);
        
        $n = 0;
        
        foreach ($sourceData as $v){
            if($v['status'] ==  0 && $v['eng'] != $resEN[$v['uid']]){
                echo $n.'[En]'.$v['eng'].'<br/>';
                echo $n.'[Ch]'.$resEN[$v['uid']].'<br/>';
                $n ++;
            }
        }
        
        exit;
        
        foreach ($sourceData as $v){

            $transferData[$v['contentuid']] = $v;

            $reg = '/'.$word.'/i';
            if($word && preg_match($reg, $v['eng'])){
                $str = preg_replace($reg, '<font color="red">\\0</font>', $v['eng']);
                echo '[EN]:'.$str .'<br/>';
                echo '[CH]:'. ($v['status'] == 1 ? $v['chi'] : '<textarea></textarea>' ) .'<br/>';
            }
        }

//         dump($transferData);

        exit;
        
        exit;
        $fbsdk = \App\Services\Merchants\FBSdkService::getInstance();
        
        $file = 'english.xml';
        $content = file_get_contents($file);
        $resEN =  $this->__xmlToArray($content);
        $file = 'hanhua.xml';
        $content = file_get_contents($file);
        $resCH =  $this->__xmlToArray($content);
        
        
//         $fp = fopen('result.xml', 'w');
//         fputs($fp, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?\>
// <contentList>'.PHP_EOL);
        
//         $fp1 = fopen('eng.xml', 'w');
//         fputs($fp1, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?\>
// <contentList>'.PHP_EOL);
        $word = \Input::get('w');
        
        if(!$word){
            echo 'Word Is Required!';
            exit();
        }
        
        set_time_limit(0);
        $fromEn = 0;
        $fromCh = 0;
        $length = 0;
        
        
        $insertData = [];
        
        foreach ($resEN as $k => $v){
            if(strlen($v) > $length ) $length = strlen($v);
            
//             $reg = '/'.$word.'/i';
//             if(preg_match($reg, $resEN[$k])){
//                 $resEN[$k] = preg_replace($reg, '<font color="red">\\0</font>', $resEN[$k]);
//                 echo ''.$resEN[$k] .'--'. ( isset($resCH[$k]) ? $resCH[$k] : '<textarea></textarea>' ) .'<br/>';
//             }
            
            
            if(isset($resCH[$k])){
//                 $resEN[$k] = $resCH[$k];
                $fromCh ++ ;
                $record = [
                    'uid' => $k,
                    'eng' => $resEN[$k],
                    'chi' => $resCH[$k],
                    'status' => 1,
                ];
            }else{
                $resEN[$k] = htmlspecialchars($resEN[$k]);
                $fromEn ++ ;
                $str = "\t<content contentuid=\"{$k}\">{$resEN[$k]}</content>".PHP_EOL;
//                 if(strlen($resEN[$k]) < 20)
//                 echo htmlspecialchars($resEN[$k]).'<br/>';;
                
//                 fputs($fp1, $str);
                $record = [
                    'uid' => $k,
                    'eng' => $resEN[$k],
                    'chi' => '',
                    'status' => 0,
                ];
            }
            $resEN[$k] = htmlspecialchars($resEN[$k]);
            $str = "\t<content contentuid=\"{$k}\">{$resEN[$k]}</content>".PHP_EOL;
//             fputs($fp, $str);
            $insertData [] = $record;
            
            if(count($insertData) > 100 ){
                \App\Models\Transfer::insert($insertData);
                $insertData = [];
            }
            
        }
        
        $insertData && \App\Models\Transfer::insert($insertData);
        
//         fputs($fp, '</contentList>');
//         fputs($fp1, '</contentList>');
//         fclose($fp);
//         fclose($fp1);
        dump('From English:'.$fromEn);
        dump('From Chinese:'.$fromCh);
        dump($length);
        exit;
        edump($resEN);
        
        
//         http://fanyi.baidu.com/v2transapi
        
//         from:en
//         to:zh
//         query:Invariant
//         transtype:realtime
//         simple_means_flag:3
        
        
        $res = curl_post('http://fanyi.baidu.com/v2transapi', [
            'from' => 'en',
            'to' => 'zh',
            'query' => 'realtime',
            'transtype' => 'realtime',
            'simple_means_flag' => '3',
        ]);
        edump(json_decode($res,1));
        
//         $this->test1();
        
//         edump(env('APNS_PRODUCTION',false));
//         $this->str();
        
//         edump(\DB::connection('admin')-> table('area')->where('cid','<',10)->get()) ;
        
        
//         exit;
        
        $FBSdk = \App\Services\Merchants\FBSdkService::getInstance();
        $MerchantsPay = new \App\Services\Merchants\MerchantsPay();
        
//         $MerchantsPay->serverConnectionAndAccountBalence();
//         exit;
        
        
        edump($FBSdk->GetAccInfo([
            'BBKNBR' => '', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
            'C_BBKNBR' => '杭州',// 分行名称 Z(1,62) 附录A.1 是
            'ACCNBR' => '571908952410602',// 账号 C(1,35) 否
        ]));
        edump($FBSdk->GetPaymentInfo([
            'BUSCOD' => 'N02031',//业务类别 C(6) 附录A.4 可
            'BGNDAT' => '20151201',//起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230',//结束日期 C(8) 否
            'DATFLG' => 'A',//日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            'MINAMT' => '0.00',//MINAMT 最小金额 M 可 空时表示0.00
            'MAXAMT' => '99999999.99',//MAXAMT 最大金额 M 可 空时表示9999999999999.99
            'YURREF' => '145112736380309257',//业务参考号 C(1,30) 可
        ]));
        
        edump($FBSdk->NTSTLINF('0499940046'));
        
        
//         'TRSAMT' => 'required|numeric', // 金额 M 否
//         'YURREF' => 'required|max:30', // 业务参考号 C(30) 否 成功和在途的业务唯一
//         'NTFCH1' => 'sometimes|email', // 通知方式一 C(40) 是
//         'NTFCH2' => 'sometimes|mobile', // 通知方式二 C(40) 是
//         'CDTNAM' => 'required', // 收款人户名 Z(100) 否
//         'CDTEAC' => 'required|numeric', // 收款人账号 C(35) 否
//         'CDTBRD' => 'required|numeric', // 收款行行号 C(12) 否
//         'CRTSQN' => 'required'
            
        $res =  $FBSdk->DCPayment([
            'YURREF' => '1212312312313222', //否 业务参考号
            'TRSAMT' => '0.01',//否 交易金额
            'NUSAGE' => '测试',//否 用途 对应对账单中的摘要NARTXT
            'CRTACC' => '6214855710279726',//否 收方帐号 收款企业的转入帐号，该帐号的币种类型必须与币种字段相符。
            'CRTNAM' => '杨扬', //否 收方帐户名 收款方企业的转入帐号的帐户名称。
            'NTFCH2' => '15158133652',
        ] + \Config::get('merchants.DCPAYMENT.DCOPDPAYX') );
        
        dump($res);
        edump($FBSdk->GetPaymentInfo([
            'BUSCOD' => 'N02031',//业务类别 C(6) 附录A.4 可
            'BGNDAT' => '20151201',//起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230',//结束日期 C(8) 否
            'DATFLG' => 'A',//日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            'MINAMT' => '0.00',//MINAMT 最小金额 M 可 空时表示0.00
            'MAXAMT' => '99999999.99',//MAXAMT 最大金额 M 可 空时表示9999999999999.99
            'YURREF' => '1212312312313222',//业务参考号 C(1,30) 可
            'RTNFLG' => '',//业务处理结果 C(1) 附录A.6 可 空表示查询所有结果的数据
            'OPRLGN' => '',//经办用户 可
        ]));
        
        $MerchantsPay->netBankInterconnectionPayOne([
            'SQRNBR' => '0000000001', // 流水号 C(10) 否 批次内唯一，批量经办时用作响应结果与请求的对应字段。
            'TRSAMT' => '0.01', // 金额 M 否
            'YURREF' => 'I201512261226', // 业务参考号 C(30) 否 成功和在途的业务唯一
            'CRTSQN' => 'RCV0000001', // 收方编号 C(20) 可 UID0000001
            'NTFCH2' => '15158133652', // 通知方式一 C(40) 是
            'CDTNAM' => '杨扬', // 收款人户名 Z(100) 否
            'CDTEAC' => '6214855710279726', // 收款人账号 C(35) 否
            'CDTBRD' => '308584000013', // 收款行行号 C(12) 否
            'RMKTXT' => '测试同行转',
            'RSV30Z' => ''
        ]);
        exit;
//         edump($FBSdk->ListMode());
        // __fsocket_get('/v1/withdrawResultSelect',[
        // 'wait' => 6,
        // 'REQNBR' => 2,
        // 'uid' => 3,
        // 'order_id' => 4,
        // ]);
        
        // exit;
        $REQNBR = $MerchantsPay->payment([
            [
                'ACCNBR' => '6214855710279726', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
                'CLTNAM' => '杨扬', // 户名 Z(1,62) 否
                'TRSAMT' => '0.01', // 金额 M 否
                'BNKFLG' => 'Y', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
                'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
                'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
                'TRSDSP' => '同安同行转账'
            ]
        ]);
        dump($REQNBR);
        edump($FBSdk->NTAGDINF($REQNBR));
        
        
        $FBSdk->getBankNoByName('asd');
        $res = \App\Models\FbsdkLog::where('func_name', 'NTIBCOPR')->select('received_xml')
            ->get()
            ->toArray();
//         edump($FBSdk->GetHisNotice([
//             'BGNDAT' => '20151218', //否 开始日期 开始日期和结束日期的间隔不能超过100天
//             'ENDDAT' => '20151230', //否 结束日期
//             'MSGTYP' => 'NCDBTTRS', //可 消息类型
//             'MSGNBR' => '', //可 消息号
//         ]));
//         edump($FBSdk->GetAccInfo([
//             'BBKNBR' => '', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
//             'C_BBKNBR' => '杭州',// 分行名称 Z(1,62) 附录A.1 是
//             'ACCNBR' => '571908952410602',// 账号 C(1,35) 否
//         ]));
        // edump($FBSdk->NTEBPINF('0499030253'));
//         edump($FBSdk->NTQNPEBP([
//             'QRYACC' => '571908952410602', // 账号 C(35) 否
//             'TRXDIR' => 'O', // 交易方向 C(1) I：提回 O：提出 否
//             'MSGNBR' => '101', // 业务种类 C(3) 101：网银贷记 103：网银借记 105：第三方贷记 否
//             'BGNDAT' => '20151218', // 交易起始日期 D 否 日期间隔不能超过100天
//             'ENDDAT' => '20151230', // 交易结束日期 D 否
//             'MINAMT' => '', // 最小金额 M
//             'MAXAMT' => '', // 最大金额 M
//             'YURREF' => '', // 业务参考号 C(30)
//             'TRXSTS' => '', // 交易状态 C(2) 附录 A.48
//             'PYREAC' => '', // 付款人账号 C(35)
//             'PYEEAC' => '', // 收款人账号 C(35)
//             'CNVNBR' => ''
//         ] // 内部协议号 C(10)

//         ));
//         edump($FBSdk->NTQRYEBP([
//             // 'BUSCOD' => 'N31010' ,// 业务类型 C(6) N31010 网银贷记 N31011 网银借记 N31012 第三方贷记 N31013 跨行账户信息查询 可
//             'BGNDAT' => '20151210', // 起始日期 D 否 日期间隔不能超过100天
//             'ENDDAT' => '20151230'
//         ] // 结束日期 D 否
//                                  // 'YURREF' => 'I20151224100111232' ,// 业务参考号 C(30)
//         ));
        // foreach ($res as $v){
        // $v['received_xml'] = iconv('UTF-8', 'GBK', $v['received_xml']);
        // $array = $FBSdk->__xmlToArray($v['received_xml']);
        // preArrayKV($array);
        // }
        // exit;
        
        
       
        
        
        $res = $MerchantsPay->netBankInterconnectionPayOne([
//             'TRSAMT' => '0.01', // 金额 M 否
//             'YURREF' => 'I20151224509552', // 业务参考号 C(30) 否 成功和在途的业务唯一
//             'CRTSQN' => '11', // 收方编号 C(20) 可
//             'NTFCH1' => '18767135775', // 通知方式一 C(40) 是
//             'CDTNAM' => '金燕林', // 收款人户名 Z(100) 否
//             'CDTEAC' => '6222600170008404351', // 收款人账号 C(35) 否
//             'CDTBRD' => '301290000007', // 收款行行号 C(12) 否
//             'RMKTXT' => '成功'
            
              "TRSAMT" => "0.01",
              "YURREF" => "145101137894487859",
              "CRTSQN" => "1",
              "NTFCH1" => "18767135775",
              "CDTNAM" => "金燕林",
              "CDTEAC" => "6222600170008404351",
              "CDTBRD" => "301290000007",
              "RMKTXT" => "提现 0.01 元",
        ]);
        dump($res);
        exit();
        
        // CDTBRD错误
        $res = $MerchantsPay->netBankInterconnectionPay([
            [
                'TRSAMT' => '0.01', // 金额 M 否
                'YURREF' => 'I20151224100111232', // 业务参考号 C(30) 否 成功和在途的业务唯一
                'CRTSQN' => 'UID0000011', // 收方编号 C(20) 可
                'NTFCH1' => '18767135775', // 通知方式一 C(40) 是
                'CDTNAM' => '金燕林', // 收款人户名 Z(100) 否
                'CDTEAC' => '6222600170008404351', // 收款人账号 C(35) 否
                'CDTBRD' => '301290000007', // 收款行行号 C(12) 否
                'RMKTXT' => '成功'
            ]
        ]);
        // 'TRSTYP' => 'D200', // 业务类型编码 C(4) 附录A.49 否
        // 'TRSCAT' => '01200'
        
        
        dump($res);
        exit();
        
        // 先失败，后成功
        $res = $MerchantsPay->netBankInterconnectionPay([
            [
                'TRSAMT' => '0.01', // 金额 M 否
                'YURREF' => 'I2015122409541', // 业务参考号 C(30) 否 成功和在途的业务唯一
                'CRTSQN' => 'RCV0000001', // 收方编号 C(20) 可
                'NTFCH1' => '18767135775', // 通知方式一 C(40) 是
                'CDTNAM' => '金燕林', // 收款人户名 Z(100) 否
                'CDTEAC' => '6222600170008404351', // 收款人账号 C(35) 否
                'CDTBRD' => '301290000007', // 收款行行号 C(12) 否
                'RMKTXT' => '先失败，后成功',
                'TRSTYP' => 'D200', // 业务类型编码 C(4) 附录A.49 否
                'TRSCAT' => '09001'
            ],
            [
                'TRSAMT' => '0.01', // 金额 M 否
                'YURREF' => 'I2015122409542', // 业务参考号 C(30) 否 成功和在途的业务唯一
                'CRTSQN' => 'RCV0000001', // 收方编号 C(20) 可
                'NTFCH1' => '18767135775', // 通知方式一 C(40) 是
                'CDTNAM' => '金燕林', // 收款人户名 Z(100) 否
                'CDTEAC' => '6228580699005714769', // 收款人账号 C(35) 否
                'CDTBRD' => '402331000007', // 收款行行号 C(12) 否
                'RMKTXT' => '先失败，后成功'
            ]
        ]);
        dump($res);
        exit();
        // 两个失败
        $res = $MerchantsPay->netBankInterconnectionPay([
            [
                'TRSAMT' => '0.01', // 金额 M 否
                'YURREF' => 'I20151223173512', // 业务参考号 C(30) 否 成功和在途的业务唯一
                'CRTSQN' => 'RCV0000001', // 收方编号 C(20) 可
                'NTFCH1' => '18767135775', // 通知方式一 C(40) 是
                'CDTNAM' => '金燕林', // 收款人户名 Z(100) 否
                'CDTEAC' => '6222600170008404351', // 收款人账号 C(35) 否
                'CDTBRD' => '301290000007', // 收款行行号 C(12) 否
                'RMKTXT' => '测试网银互联多笔都直接失败',
                'TRSTYP' => 'D200', // 业务类型编码 C(4) 附录A.49 否
                'TRSCAT' => '09001'
            ],
            [
                'TRSAMT' => '0.01', // 金额 M 否
                'YURREF' => 'I201512231735112', // 业务参考号 C(30) 否 成功和在途的业务唯一
                'CRTSQN' => 'RCV0000001', // 收方编号 C(20) 可
                'NTFCH1' => '18767135775', // 通知方式一 C(40) 是
                'CDTNAM' => '金燕林', // 收款人户名 Z(100) 否
                'CDTEAC' => '6228580699005714769', // 收款人账号 C(35) 否
                'CDTBRD' => '402331000007', // 收款行行号 C(12) 否
                'RMKTXT' => '测试网银互联多笔都直接失败',
                'TRSTYP' => 'D200', // 业务类型编码 C(4) 附录A.49 否
                'TRSCAT' => '09001'
            ]
        ]);
        dump($res);
        exit();
        $this->wangyinhulian();
        // /0028772686
        // edump($FBSdk->ListMode('N02031'));
        // N02031
        // edump($FBSdk->GetNewNotice());
        // edump($FBSdk->GetSysInfo());
        
        edump($FBSdk->GetAgentDetail('0028774597'));
        dump($FBSdk->GetNewNotice());
        edump($FBSdk->NTAGDINF('0028774597'));
        // $REQNBR = 0028774597
        // $YURREF = I145083971767998818
        $REQNBR = $MerchantsPay->payment([
            [
                'ACCNBR' => '6225880230001175', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
                'CLTNAM' => '刘五', // 户名 Z(1,62) 否
                'TRSAMT' => '11.01', // 金额 M 否
                'BNKFLG' => 'Y', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
                'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
                'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
                'TRSDSP' => '批量代发模式0004'
            ],
            [
                'ACCNBR' => '6225885910000108', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
                'CLTNAM' => 'Judy', // 户名 Z(1,62) 否
                'TRSAMT' => '11.01', // 金额 M 否
                'BNKFLG' => 'Y', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
                'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
                'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
                'TRSDSP' => '批量代发失败一笔'
            ]
        ]);
        dump($REQNBR);
        edump($FBSdk->NTAGDINF($REQNBR));
        
        // ////////////////////////////
        
        dump($FBSdk->GetNewNotice());
        edump($FBSdk->NTAGDINF('0028774595'));
        // 0028774595
        $REQNBR = $MerchantsPay->payment([
            [
                'ACCNBR' => '6225880230001175', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
                'CLTNAM' => '刘五', // 户名 Z(1,62) 否
                'TRSAMT' => '10.58', // 金额 M 否
                'BNKFLG' => 'Y', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
                'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
                'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
                'TRSDSP' => '批量代发模式0004'
            ],
            [
                'ACCNBR' => '6225885910000108', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
                'CLTNAM' => 'Judy Zeng', // 户名 Z(1,62) 否
                'TRSAMT' => '10.59', // 金额 M 否
                'BNKFLG' => 'Y', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
                'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
                'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
                'TRSDSP' => '批量代发模式0004'
            ]
        ]);
        dump($REQNBR);
        edump($FBSdk->NTAGDINF($REQNBR));
        // dump($FBSdk->ListMode('N01010'));
        // edump($FBSdk->ListAccount('00001','N01010'));
        edump($FBSdk->NTAGDINF('0028774570'));
        edump($FBSdk->GetAgentInfo([
            'BUSCOD' => 'N03020', // 业务代码 C(6) N03010：代发工资；N03020：代发；N03030：代扣 可
            'BGNDAT' => '20151219', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230', // 结束日期 C(8) 否
            'DATFLG' => 'A', // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            'YURREF' => 'I145083711997357369', // 业务参考号 C(1,30) 可
            'OPRLGN' => ''
        ])); // 经办用户 Z(30) 可

        
        edump($FBSdk->GetNewNotice());
        // 0028774570
        // I145083711997357369
        $REQNBR = $MerchantsPay->payment([
            'ACCNBR' => '6225880230001175', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
            'CLTNAM' => '刘五', // 户名 Z(1,62) 否
            'TRSAMT' => '10.16', // 金额 M 否
            'BNKFLG' => 'Y', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
            'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
            'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
            'TRSDSP' => '代发模式0004'
        ]);
        edump($FBSdk->NTAGDINF($REQNBR));
        
        edump($FBSdk->ListMode('N03020'));
        edump($FBSdk->NTAGDINF('0028772707'));
        
        edump($FBSdk->GetSysInfo());
        
        edump($FBSdk->ListAccount('00001'));
        edump($FBSdk->GetNewNotice());
        exit();
        // 从头测试
        //
        // 代发劳务收入
        $REQNBR = $MerchantsPay->payment([
            'ACCNBR' => '6225885910000108', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
            'CLTNAM' => 'Judy Zeng', // 户名 Z(1,62) 否
            'TRSAMT' => '0.01', // 金额 M 否
            'BNKFLG' => '', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
            'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
            'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
            'TRSDSP' => '测试1219'
        ]); // 注释 Z(1,20) 可 代扣：如果签订有代扣协议，则必须填写与代扣协议一致的合作方账号（该号为扣款方的客户标识ID）
            
        // 4.9查询大批量代发代扣明细信息
        
        $大批量 = '';
        $res = $FBSdk->NTAGDINF($REQNBR);
        dump($REQNBR);
        edump($res);
        
        edump($FBSdk->GetNewNotice());
        // dump($FBSdk->GetAgentDetail('0028772707'));
        // edump($FBSdk->NTAGDINF('0028772707'));
        
        edump($FBSdk->NTAGCAPY('00004', [
            'BEGTAG' => 'Y', // 批次开始标志 C(1) 否 必须为’Y’或’N’，’Y’表示批次开始，续传批次固定赋值为’N’
            'ENDTAG' => 'Y', // 批次结束标志 C(1) 否 必须为’Y’或’N’，’Y’表示批次结束，非结束批次固定赋值为’N’
            'REQNBR' => '', // 流程实例号 C(10) 可 第一次上传时必须为空；续传时不能为空，所有续传次数流程实例号必须为同一个；主机校验该字段值与批次开始、结束标志的匹配性
            'TTLAMT' => '21.59', // 总金额 M 否 批次总金额，代发代扣系统要求第一次就要必输
            'TTLCNT' => '1', // 总笔数 F(8,0) 否 批次总笔数，代发代扣系统要求第一次就要必输
            'TTLNUM' => '1', // 总次数 F(3,0) 否 该批次数据计划分多少次上传完，代发代扣系统要求第一次就要必输
            'CURAMT' => '21.59', // 本次金额 M 否
            'CURCNT' => '1', // 本次笔数 F(8,0) 否
            'CNVNBR' => '00001', // 合作方协议号 C(6) 可 预留
            'CCYNBR' => '10', // 交易货币 C(2) 附录A.3 否
            'NTFINF' => '大批量代发经办21.59', // 个性化短信内容 Z(22) 可 预留，录入则在收方入账短信里展示
            'BBKNBR' => '59', // 分行号 C(2) 附录A.1 否
            'ACCNBR' => '591902896710201', // 账号 C(35) 否
            'CCYMKT' => '2', // 货币市场 C(1) 取值 描述 0 不分钞汇 1 现钞 2 现汇 否
            'TRSTYP' => 'BYBC', // 交易类型 C(4) 否 即“交易代码编号”
            'NUSAGE' => '代发劳务收入', // 用途 Z(42) 否
            'EPTDAT' => '', // 期望日 D 可 默认为当前日期
            'EPTTIM' => '', // 期望时间 T 可 默认为“000000”
            'YURREF' => 'I145062000535281199', // 对方参考号 C(30) 否
            'DMANBR' => '', // 虚拟户编号 C(20) 可
            'GRTFLG' => ''
        ], // 网银审批标志 C(1) Y/N 可
[
            'TRXSEQ' => '00000001', // 交易序号 C(8) 否 需要客户自行保证批次范围内的序号唯一性，代发代扣系统要求格式为全数字，如’00000001’、’00000002’
            'ACCNBR' => '6225880230001175', // 帐号 C(35) 否
            'ACCNAM' => '刘五', // 户名 Z(62) 否
            'TRSAMT' => '21.59', // 金额 M 否
            'TRSDSP' => '测试交易查询-经办', // 注释 Z(42) 可
            'BNKFLG' => 'Y', // 系统内标志 C(1) Y/N 否 Y:开户行是招商银行;N：开户行是他行。
            'EACBNK' => '', // 他行户口开户行 Z(62) 可 他行必输
            'EACCTY' => '', // 他行户口开户地 Z(62) 可 他行必输
            'FSTFLG' => 'N', // 他行快速标志 C(1) 可 Y:快速N:普通
            'RCVBNK' => '', // 他行户口联行号 C(12) 可
            'CPRACT' => '', // 客户代码 C(20) 可 以前代扣将合作方帐号填到注释字段里，现在可以改为填到这个字段；代发可空
            'CPRREF' => ''
        ])); // 合作方流水号 C(20) 可 暂无用，预留

        edump($FBSdk->NTAGCLMT([
            'ACCNBR' => '591902896710201', // 账号 C(35) 否
            'BBKNBR' => '59'
        ])); // 分行号 C(2) 否

        edump($FBSdk->GetNewNotice());
        edump($FBSdk->GetHisNotice([
            'BGNDAT' => '20151218', // 否 开始日期 开始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230'
        ])); // 否 结束日期
            // 'MSGTYP' => 'NCBCHOPR', //可 消息类型
        
        
        // NTSGNCBC
        
        edump($FBSdk->NTQEBCTL('N31013'));
        
        edump($FBSdk->GetHisNotice([
            'BGNDAT' => '20151218', // 否 开始日期 开始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230'
        ])); // 否 结束日期
            // 'MSGTYP' => 'NCBCHOPR', //可 消息类型
        
        
        // dump($FBSdk->GetNewNotice());
        dump($FBSdk->NTAGDINF('0028772706'));
        edump($FBSdk->GetAgentDetail('0028772706'));
        edump($FBSdk->GetAgentInfo([
            // 'BUSCOD' => 'N03020', // 业务代码 C(6) N03010：代发工资；N03020：代发；N03030：代扣 可
            'BGNDAT' => '20151218', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230', // 结束日期 C(8) 否
            'DATFLG' => 'A'
        ])); // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            // 'YURREF' => 'I145051489665969178', // 业务参考号 C(1,30) 可
            // 'OPRLGN' => '',// 经办用户 Z(30) 可
        
        edump($FBSdk->GetHisNotice([
            'BGNDAT' => '20151218', // 否 开始日期 开始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230', // 否 结束日期
                                    // 'MSGTYP' => 'NCDBTTRS', //可 消息类型
            'MSGNBR' => ''
        ])); // 可 消息号

        edump($FBSdk->NTAGDINF('0028772704'));
        // edump($FBSdk->GetPaymentInfo($SDKPAYQYX));
        // edump($FBSdk->NTSTLINF('0028772524'));
        // NTAGDINF
        // edump($FBSdk->GetNewNotice());
        
        // 他行打款测试，未提供账户，用本人
        // 0028772706
        $MerchantsPay->payment([
            'ACCNBR' => '6222600170008404351', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
            'CLTNAM' => '金燕林', // 户名 Z(1,62) 否
            'TRSAMT' => '0.01', // 金额 M 否
            'BNKFLG' => 'N', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
            'EACBNK' => '交通银行', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
            'EACCTY' => '杭州', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
            'TRSDSP' => '测试他行打款'
        ]);
        exit();
        $MerchantsPay->payment([
            'ACCNBR' => '6225880230001175', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
            'CLTNAM' => '刘五', // 户名 Z(1,62) 否
            'TRSAMT' => '20.17', // 金额 M 否
            'BNKFLG' => '', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
            'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
            'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
            'TRSDSP' => '测试122001'
        ]);
        exit();
        // 0028772682
        // 0028772686
        // 0028772687
        // $str = 'BUSCOD 业务代码 C(6) N03010：代发工资；N03020：代发；N03030：代扣 可
        // BGNDAT 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
        // ENDDAT 结束日期 C(8) 否
        // DATFLG 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
        // YURREF 业务参考号 C(1,30) 可
        // OPRLGN 经办用户 Z(30) 可 ';
        // $this->tableViewToArrayAnn($str);
        // exit;
        // MerchantsPay
        // $res= $this->tableViewToArrayAnn($str);
        $FBSdk = \App\Services\Merchants\FBSdkService::getInstance();
        // dump($FBSdk->NTSTLINF([
        // '0028772686'
        // ]));
        // NTAGDINF
        edump($FBSdk->GetAccInfo([
            'BBKNBR' => '59', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
                              // 'C_BBKNBR' => '',// 分行名称 Z(1,62) 附录A.1 是
            'ACCNBR' => '591902896710201'
        ])); // 账号 C(1,35) 否
        
        edump($FBSdk->NTAGDINF('0028772686'));
        edump($FBSdk->GetAgentInfo([
            // 'BUSCOD' => 'N03020', // 业务代码 C(6) N03010：代发工资；N03020：代发；N03030：代扣 可
            'BGNDAT' => '20151218', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151220', // 结束日期 C(8) 否
            'DATFLG' => 'A'
        ])); // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
             // 'YURREF' => 'I145051489665969178', // 业务参考号 C(1,30) 可
             // 'OPRLGN' => '',// 经办用户 Z(30) 可
             
        // 201512190022034174
        edump($FBSdk->GetHisNotice([
            'BGNDAT' => '20151219', // 否 开始日期 开始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151219'
        ])); // 否 结束日期
             // 'MSGTYP' => 'NCBCHOPR', //可 消息类型
             // 'MSGNBR' => '', //可 消息号
        
        dump($FBSdk->GetNewNotice());
        edump($FBSdk->GetPaymentInfo([
            // 'BUSCOD' => 'N03020',//业务类别 C(6) 附录A.4 可
            'BGNDAT' => '20151218', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230'
        ])); // 结束日期 C(8) 否
             // 'DATFLG' => 'A',//日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
             // 'MINAMT' => '0.00',//MINAMT 最小金额 M 可 空时表示0.00
             // 'MAXAMT' => '99999999.99',//MAXAMT 最大金额 M 可 空时表示9999999999999.99
             // 'YURREF' => 'I145051489665969178',//业务参考号 C(1,30) 可
             // 'RTNFLG' => '',//业务处理结果 C(1) 附录A.6 可 空表示查询所有结果的数据
             // 'OPRLGN' => '',//经办用户 可
             
        // I145051489665969178
        edump($FBSdk->AgentRequest([
            'BUSCOD' => 'N03020', // 业务类别 C(6) N03010:代发工资N03020:代发N03030:代扣 否 默认为代发工资: N03010
            'BUSMOD' => '00001', // 业务模式编号 C(5) 否 编号和名称填写其一，填写编号则名称无效。可通过前置机或者查询可经办的业务模式信息（ListMode）获得，必须使用无审批的业务模式
            'MODALS' => '', // 业务模式名称 Z(62)
            'C_TRSTYP' => '代发劳务收入', // 交易代码名称 Z 附录A.45 否 为空时默认BYSA：代发工资，代发和代扣时必填，可通过4.1获得可以使用的交易代码，也可以通过前置机获取。
            'TRSTYP' => 'BYBC', // 交易代码 C(4)
            'EPTDAT' => '', // 期望日期 D 可 不用填写，不支持期望日直接代发
            'DBTACC' => '591902896710201', // 转出账号/转入账号 C(35) 否 代发为转出账号；代扣为转入账号
            'BBKNBR' => '59', // 分行代码 C(2) 附录A.1 否 代码和名称不能同时为空。同时有值时BBKNBR有效。
            'BANKAREA' => '', // 分行名称 附录A.1
            'SUM' => '16.52', // 总金额 M 否
            'TOTAL' => '1', // 总笔数 N(4) 否
            'CCYNBR' => '10', // 币种代码 N(2) 附录A.3 可 默认10：人民币 同时有值时CCYNBR有效。
            'CURRENCY' => '人民币', // 币种名称 Z(1,10) 附录A.3
            'YURREF' => 'I' . createSerialNum(), // 业务参考号 C(1,30) 否
            'MEMO' => '测试代发--', // 用途 Z(1,42) 否
            'DMANBR' => '', // 虚拟户编号 C(1,20) 可 记账宝使用
            'GRTFLG' => ''
        ], // 直连经办网银审批标志 C(1) Y：直连经办、网银审批；空或者其他值：直连经办、无需审批。 可 为Y时必须使用有审批岗的模式；不为Y时，必须使用无审批岗的模式。
[
            'ACCNBR' => '6225880230001175', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
            'CLTNAM' => '刘五', // 户名 Z(1,62) 否
            'TRSAMT' => '16.52', // 金额 M 否
            'BNKFLG' => '', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
            'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
            'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
            'TRSDSP' => '测试1219'
        ])); // 注释 Z(1,20) 可 代扣：如果签订有代扣协议，则必须填写与代扣协议一致的合作方账号（该号为扣款方的客户标识ID）
             
        //
        edump($FBSdk->GetPaymentInfo([
            // 'BUSCOD' => 'N03020',//业务类别 C(6) 附录A.4 可
            'BGNDAT' => '20151218', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230', // 结束日期 C(8) 否
                                    // 'DATFLG' => 'A',//日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
                                    // 'MINAMT' => '0.00',//MINAMT 最小金额 M 可 空时表示0.00
                                    // 'MAXAMT' => '99999999.99',//MAXAMT 最大金额 M 可 空时表示9999999999999.99
            'YURREF' => 'I145051335549254273'
        ])); // 业务参考号 C(1,30) 可
             // 'RTNFLG' => '',//业务处理结果 C(1) 附录A.6 可 空表示查询所有结果的数据
             // 'OPRLGN' => '',//经办用户 可
             
        // edump($FBSdk->ListMode());
             // 0028772682
        edump($FBSdk->AgentRequest([
            'BUSCOD' => 'N03020', // 业务类别 C(6) N03010:代发工资N03020:代发N03030:代扣 否 默认为代发工资: N03010
            'BUSMOD' => '00001', // 业务模式编号 C(5) 否 编号和名称填写其一，填写编号则名称无效。可通过前置机或者查询可经办的业务模式信息（ListMode）获得，必须使用无审批的业务模式
            'MODALS' => '', // 业务模式名称 Z(62)
            'C_TRSTYP' => '代发劳务收入', // 交易代码名称 Z 附录A.45 否 为空时默认BYSA：代发工资，代发和代扣时必填，可通过4.1获得可以使用的交易代码，也可以通过前置机获取。
            'TRSTYP' => 'BYBC', // 交易代码 C(4)
            'EPTDAT' => '', // 期望日期 D 可 不用填写，不支持期望日直接代发
            'DBTACC' => '591902896710201', // 转出账号/转入账号 C(35) 否 代发为转出账号；代扣为转入账号
            'BBKNBR' => '59', // 分行代码 C(2) 附录A.1 否 代码和名称不能同时为空。同时有值时BBKNBR有效。
            'BANKAREA' => '', // 分行名称 附录A.1
            'SUM' => '12.19', // 总金额 M 否
            'TOTAL' => '1', // 总笔数 N(4) 否
            'CCYNBR' => '10', // 币种代码 N(2) 附录A.3 可 默认10：人民币 同时有值时CCYNBR有效。
            'CURRENCY' => '人民币', // 币种名称 Z(1,10) 附录A.3
            'YURREF' => 'I' . createSerialNum(), // 业务参考号 C(1,30) 否
            'MEMO' => '测试代发', // 用途 Z(1,42) 否
            'DMANBR' => '', // 虚拟户编号 C(1,20) 可 记账宝使用
            'GRTFLG' => ''
        ], // 直连经办网银审批标志 C(1) Y：直连经办、网银审批；空或者其他值：直连经办、无需审批。 可 为Y时必须使用有审批岗的模式；不为Y时，必须使用无审批岗的模式。
[
            'ACCNBR' => '6225885910000108', // 收款账号/被扣款账号 C(1,35) 否 非空，可以是旧版一卡通、新版一卡通或存折，旧版一卡通应包含4位分行地区码和8位卡号（共12位），新版一卡通为16位，存折必须加4位分行地区码（共14位）。如：075512888888（旧版一卡通）或07551288888811（存折））
            'CLTNAM' => 'Judy Zeng', // 户名 Z(1,62) 否
            'TRSAMT' => '12.19', // 金额 M 否
            'BNKFLG' => '', // 系统内标志 C(1) 可 Y:开户行是招商银行。 N：开户行是他行。为空默认为招行。
            'EACBNK' => '', // 他行户口开户行 Z(1,62) 可 当BNKFLG=N时必填
            'EACCTY' => '', // 他行户口开户地 Z(1,62) 可 当BNKFLG=N时必填
            'TRSDSP' => '测试1219'
        ])); // 注释 Z(1,20) 可 代扣：如果签订有代扣协议，则必须填写与代扣协议一致的合作方账号（该号为扣款方的客户标识ID）
        
        edump($FBSdk->QueryAgentList('N03020'));
        edump($FBSdk->GetNewNotice());
        // edump($FBSdk->GetHisNotice([
        // 'BGNDAT' => '20151218', //否 开始日期 开始日期和结束日期的间隔不能超过100天
        // 'ENDDAT' => '20151219', //否 结束日期
        // 'MSGTYP' => 'NCDRTPAY', //可 消息类型
        // //201512170022033263
        // ]));
        edump($FBSdk->GetPaymentInfo([
            'BUSCOD' => 'N02031', // 业务类别 C(6) 附录A.4 可
            'BGNDAT' => '20151201', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151219', // 结束日期 C(8) 否
            'DATFLG' => 'A', // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            'MINAMT' => '0.00', // MINAMT 最小金额 M 可 空时表示0.00
            'MAXAMT' => '99999999.99'
        ])); // MAXAMT 最大金额 M 可 空时表示9999999999999.99
             // 'YURREF' => 'ICE145042353952394359',//业务参考号 C(1,30) 可
             // 'RTNFLG' => '',//业务处理结果 C(1) 附录A.6 可 空表示查询所有结果的数据
             // 'OPRLGN' => '',//经办用户 可
        
        edump($FBSdk->DCPayment([
            // 145042353952394359
            'YURREF' => 'ICE145042353952394359', // 否 业务参考号
                                                 // 用于标识该笔业务的编号，企业银行编号 + 业务类型+业务参考号必须唯一。
                                                 // 企业可以自定义业务参考号，也可使用银行缺省值（单笔支付），批量支付须由企业提供。
                                                 // 直联必须用企业提供
            'DBTACC' => '591902896710201', // 否 付方帐号 企业用于付款的转出帐号，该帐号的币种类型必须与币种字段相符。
            'DBTBBK' => '59', // 否 付方开户地区代
            'TRSAMT' => '116.78', // 否 交易金额
            'CCYNBR' => '10', // 否 币种代码
            'STLCHN' => 'N', // 否 结算方式代码 只对跨行交易有效 N：普通 F：快速
            'NUSAGE' => '测试', // 否 用途 对应对账单中的摘要NARTXT
            'BNKFLG' => 'Y', // 否 系统内外标志 Y：招行；N：非招行；
            'CRTACC' => '6225885910000108', // 否 收方帐号 收款企业的转入帐号，该帐号的币种类型必须与币种字段相符。
            'CRTNAM' => 'Judy Zeng', // 否 收方帐户名 收款方企业的转入帐号的帐户名称。
            'CRTBNK' => '招商银行'
        ])); // 可 收方开户行 跨行支付（BNKFLG=N）必填
        
        edump($FBSdk->GetHisNotice([
            'BGNDAT' => '20151217', // 否 开始日期 开始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151219', // 否 结束日期
            'MSGTYP' => 'NCDRTPAY'
        ])); // 可 消息类型
             // 201512170022033263
        
        edump($FBSdk->GetPaymentInfo([
            'BUSCOD' => 'N02031', // 业务类别 C(6) 附录A.4 可
            'BGNDAT' => '20151201', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230', // 结束日期 C(8) 否
            'DATFLG' => 'A', // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            'MINAMT' => '-100.00', // MINAMT 最小金额 M 可 空时表示0.00
            'MAXAMT' => '99999999.99', // MAXAMT 最大金额 M 可 空时表示9999999999999.99
                                       // 'YURREF' => 'FBS145033704654',//业务参考号 C(1,30) 可
            'RTNFLG' => '', // 业务处理结果 C(1) 附录A.6 可 空表示查询所有结果的数据
            'OPRLGN' => ''
        ])); // 经办用户 可
        
        edump($FBSdk->GetPaymentInfo([
            'BUSCOD' => 'N02031', // 业务类别 C(6) 附录A.4 可
            'BGNDAT' => '20151217', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151218', // 结束日期 C(8) 否
            'DATFLG' => 'A', // 日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
            'MINAMT' => '0.00', // MINAMT 最小金额 M 可 空时表示0.00
            'MAXAMT' => '99999999.99', // MAXAMT 最大金额 M 可 空时表示9999999999999.99
            'YURREF' => '145042353952394359', // 业务参考号 C(1,30) 可
            'RTNFLG' => '', // 业务处理结果 C(1) 附录A.6 可 空表示查询所有结果的数据
            'OPRLGN' => ''
        ])); // 经办用户 可
        
        $NCCRTTRS_Ann = 'MSGTYP	通知类型	C（8）	NCCRTTRS-到帐通知；NCDBTTRS-付款通知；	否	
MSGNBR	通知序号	C（18）		否	唯一标示一笔通知信息
BBKNBR	分行号	N（2,2）	附录A.1	否	如"75"代表深圳
ACCNBR	帐号	C（35）		否	
CCYNBR	币种代码	C（2,2）		否	如"10"代表人民币
TRSAMT	交易金额	M		可	
BLVAMT	余额	M		否	
ACCNAM	帐户名称	Z（62）		否	
TRSDAT	交易日期	D		否	
TRSTIM	交易时间	T		否	
AMTCDR	借贷码	C（1）	\'C\'代表贷方；\'D\'代表借方	否	
RVSTAG	冲补帐标志	C（1）	*代表冲账；X代表补帐	可	
VALDAT	起息日	D		可	
BILTYP	凭证类型	C（4）		可	
BILTXT	凭证描述	Z（12）		可	凭证描述信息
BILNBR	凭证号码	C（10）		可	如凭证类型为支票，该字段为支票号码
NEWBIL	票据号	C（20）			
TRSSET	交易套号	C（15）		可	
SEQNBR	交易流水号	C（15）		可	银行会计系统交易流水号
NARTXT	摘要	Z（62）		可	有效长度为16字节。若为企业银行客户端经办的交易，则该字段为用途信息（4.0版代发代扣业务除外），若为其它渠道经办的交易，则该字段为交易的简单说明和注解。
RPYBBK	收付方帐号分行号	N（2）		可	
RPYACC	收付方帐号	C（35）		可	
RPYNAM	收付方名称	Z（62）		可	
RPYBBN	收付方开户行行号	C（20）		可	联行号
RPYBNK	收付方开户行行名	Z（62）		可	
RPYADR	收付方开户行地址	Z（62）		可	
GSBBBK	母子帐号分行号	N（2）		可	
GSBACC	母子公司帐号	C（35）		可	
GSBNAM	母子公司名称	Z（62）		可	
GSBBBN	母子公司开户行行号	C（20）		可	联行号
GSBBNK	母子公司开户行行名	Z（62）		可	
GSBADR	母子公司开户行地址	Z（62）		可	
INFFLG	信息标志	C（1）		可	为空表示付方帐号和子公司；为“1”表示收方帐号和子公司；为“2”表示收方帐号和母公司，一般用于判断收付方；对于集团公司，如果字段GSBACC不为空，也用于判断母子公司
TRSANL	交易分析码	C（6）		可	1-2位取值含义件附录A.8，3-6位取值含义件附录A.9。建议：该字段取值后台没有统一标准，所以附录额A.8和A.9不易公开发表。如有客户需要区分不同交易，再根据具体情况提供取值范围。
YURREF	对方参考号	C（30）		可	取值为企业银行客户端经办时录入的参考号，其他情况为空
BUSNAR	业务摘要	Z（200）		可	取值为企业银行客户端经办时录入的摘要字段,其他情况为空';
        
        // NCCRTTRS-到帐通知；NCDBTTRS-付款通知；
        $Ann['NCCRTTRS'] = $this->tableViewArray($NCCRTTRS_Ann);
        // $Ann['NCDBTTRS'] = $Ann['NCCRTTRS'];
        
        $NCDRTPAY_Ann = '
            MSGTYP	通知类型	C（8）	NCDRTPAY-直接支付结果通知	否	
MSGNBR	通知序号	C（18）		否	唯一标示一笔通知信息
REQNBR	流程实例号	C(10)		可	
FLWTYP	业务类型	C（6）		否	N02031-直接支付；N02041-直接集团支付
REQDTA	业务数据	C（30）		可	暂时不用
BBKNBR	分行地区码	C（2,2）		否	
ACCNBR  帐号	C（35）		否	
KEYVAL	帐号	C（35）		否	
CCYNBR	币种代码	C（2）		否	如"10"代表人民币
YURREF	业务参考号	C（30）		可	取值为企业银行客户端经办时录入的参考号，其他情况为空
ACCNAM	帐户名称	Z（62）		否	
TRSAMT  金额	M		否	
ENDAMT	金额	M		否	
EPTDAT	期望日期	D		否	
EPTTIM	期望时间	T		可	
OPRDAT	经办日期	D		否	
RTNFLG	业务请求结果	C（1）	A.6 业务处理结果	否	如"成功"、"失败"、"退票"、"经办失败"等
RTNDSP	业务结果描述	Z（92）		可	
            ';
        // NCDRTPAY-直接支付结果通知
        $Ann['NCDRTPAY'] = $this->tableViewArray($NCDRTPAY_Ann);
        
        $NCBCHOPR_Ann = 'MSGTYP	通知类型	C（8）	NCBCHOPR-批量支付经办结果通知或批量代理清算经办结果通知	否	当FLWTYP=N09010时表示批量代理清算经办结果通知，否表示批量支付经办结果通知
MSGNBR	通知序号	C（18）		否	唯一标示一笔通知信息
FLWCOD	业务模式	C（5）		否	
FLWTYP	业务类型	C（6）		否	
RSTSET	处理结果批号	C（10）		否	
RSV30Z	保留字	C（30）		可	';
        // NCBCHOPR-批量支付经办结果通知或批量代理清算经办结果通知
        $Ann['NCBCHOPR'] = $this->tableViewArray($NCBCHOPR_Ann);
        
        $NCBUSFIN_Ann = 'MSGTYP	通知类型	C（8）	NCBUSFIN – 业务完成通知	否	
MSGNBR	通知序号	C（18）		否	唯一标示一笔通知信息
REQNBR	流程实例号	C（10）		否	
FLWTYP	业务类型	C（6）		否	
REQDTA	业务数据	C（30）		可	
KEYVAL	业务键值	C(40)		可	
BBKNBR	分行号	C(2)		否	
ACCNAM	帐户名称	Z(62)		否	
ENDAMT	金额	M		否	
CCYNBR	币种	C(2)		否	
OPRDAT	经办日期	D		否	
EPTDAT	期望日期	D		否	
EPTTIM	期望时间	T		可	
YURREF	对方参考号	C(30)		否	
RTNFLG	业务请求结果	C(1)	A.6 业务处理结果	可	
RTNDSP	业务结果描述	Z(92)		可	
RSV50Z	保留字	C（50）		可	';
        $Ann['NCBUSFIN'] = $this->tableViewArray($NCBUSFIN_Ann);
        edump($Ann);
        $xml = '<?xml version="1.0" encoding="GBK"?><CMBSDKPGK><INFO><DATTYP>2</DATTYP><ERRMSG></ERRMSG><FUNNAM>GetNewNotice</FUNNAM><LGNNAM>银企直连专用集团1</LGNNAM><RETCOD>0</RETCOD></INFO><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><EPTDAT>20151208</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N03020</FLWTYP><MSGNBR>201512080022030430</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20151208</OPRDAT><REQNBR>0028771063</REQNBR><RTNDSP>无一笔成功入账</RTNDSP><RTNFLG>F</RTNFLG><TRSAMT>10.00</TRSAMT><YURREF>2015120810000521</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BLVAMT>147695.33</BLVAMT><CCYNBR>10</CCYNBR><MSGNBR>201512080022030432</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>代发余额退款</NARTXT><SEQNBR>K7876600000007C</SEQNBR><TRSAMT>10.00</TRSAMT><TRSANL>AIGATR</TRSANL><TRSDAT>20150416</TRSDAT><TRSSET>K78766P416AAAAJ</TRSSET><TRSTIM>192016</TRSTIM><VALDAT>20150416</VALDAT><YURREF>2015120810000521</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><EPTDAT>20151209</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N03020</FLWTYP><MSGNBR>201512090022030455</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20151209</OPRDAT><REQNBR>0028771098</REQNBR><RTNDSP>无一笔成功入账</RTNDSP><RTNFLG>F</RTNFLG><TRSAMT>58.00</TRSAMT><YURREF>2015120910000526</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BLVAMT>147637.33</BLVAMT><CCYNBR>10</CCYNBR><INFFLG>1</INFFLG><MSGNBR>201512090022030458</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>2015120910000526</NARTXT><SEQNBR>K7879900000003C</SEQNBR><TRSAMT>58.00</TRSAMT><TRSANL>AIGATR</TRSANL><TRSDAT>20150416</TRSDAT><TRSSET>K78799P416AAAAJ</TRSSET><TRSTIM>110153</TRSTIM><VALDAT>20150416</VALDAT><YURREF>2015120910000526</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BLVAMT>147695.33</BLVAMT><CCYNBR>10</CCYNBR><MSGNBR>201512090022030459</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>代发余额退款</NARTXT><SEQNBR>K7874600000011C</SEQNBR><TRSAMT>58.00</TRSAMT><TRSANL>AIGATR</TRSANL><TRSDAT>20150416</TRSDAT><TRSSET>K78746P416AAACJ</TRSSET><TRSTIM>110202</TRSTIM><VALDAT>20150416</VALDAT><YURREF>2015120910000526</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710704</ACCNBR><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><EPTDAT>20150416</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><MSGNBR>201512090022030463</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150416</OPRDAT><RTNDSP>NCB1804 -当地无招行分支机构</RTNDSP><RTNFLG>F</RTNFLG><TRSAMT>15.00</TRSAMT><YURREF>160115111908036</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><EPTDAT>20151209</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N03020</FLWTYP><MSGNBR>201512090022030464</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20151209</OPRDAT><REQNBR>0028771103</REQNBR><RTNFLG>S</RTNFLG><TRSAMT>0.10</TRSAMT><YURREF>2015120900000033</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BLVAMT>147695.23</BLVAMT><CCYNBR>10</CCYNBR><INFFLG>1</INFFLG><MSGNBR>201512090022030466</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>2015120900000033</NARTXT><SEQNBR>K7877200000005C</SEQNBR><TRSAMT>0.10</TRSAMT><TRSANL>AIGATR</TRSANL><TRSDAT>20150416</TRSDAT><TRSSET>K78772P416AAABJ</TRSSET><TRSTIM>111657</TRSTIM><VALDAT>20150416</VALDAT><YURREF>2015120900000033</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><EPTDAT>20151209</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N03020</FLWTYP><MSGNBR>201512090022030467</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20151209</OPRDAT><REQNBR>0028771104</REQNBR><RTNFLG>S</RTNFLG><TRSAMT>0.10</TRSAMT><YURREF>2015120900000034</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BLVAMT>147695.13</BLVAMT><CCYNBR>10</CCYNBR><INFFLG>1</INFFLG><MSGNBR>201512090022030469</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>2015120900000034</NARTXT><SEQNBR>K7876900000022C</SEQNBR><TRSAMT>0.10</TRSAMT><TRSANL>AIGATR</TRSANL><TRSDAT>20150416</TRSDAT><TRSSET>K78769P416AAACJ</TRSSET><TRSTIM>111859</TRSTIM><VALDAT>20150416</VALDAT><YURREF>2015120900000034</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><EPTDAT>20151209</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N03020</FLWTYP><MSGNBR>201512090022030473</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20151209</OPRDAT><REQNBR>0028771106</REQNBR><RTNFLG>S</RTNFLG><TRSAMT>0.10</TRSAMT><YURREF>2015120900000035</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BLVAMT>147695.03</BLVAMT><CCYNBR>10</CCYNBR><INFFLG>1</INFFLG><MSGNBR>201512090022030475</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>2015120900000035</NARTXT><SEQNBR>K5844000000017C</SEQNBR><TRSAMT>0.10</TRSAMT><TRSANL>AIGATR</TRSANL><TRSDAT>20150416</TRSDAT><TRSSET>K58440P416AAAAJ</TRSSET><TRSTIM>113302</TRSTIM><VALDAT>20150416</VALDAT><YURREF>2015120900000035</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BLVAMT>147694.73</BLVAMT><CCYNBR>10</CCYNBR><INFFLG>1</INFFLG><MSGNBR>201512090022030605</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>2015120900000036</NARTXT><SEQNBR>K5840900000013C</SEQNBR><TRSAMT>0.30</TRSAMT><TRSANL>AIGATR</TRSANL><TRSDAT>20150417</TRSDAT><TRSSET>K58409P417AAAAJ</TRSSET><TRSTIM>122003</TRSTIM><VALDAT>20150417</VALDAT><YURREF>2015120900000036</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><EPTDAT>20151209</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N03020</FLWTYP><MSGNBR>201512090022030606</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20151209</OPRDAT><REQNBR>0028771108</REQNBR><RTNFLG>S</RTNFLG><TRSAMT>0.30</TRSAMT><YURREF>2015120900000036</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户10</ACCNAM><ACCNBR>591902896810209</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BLVAMT>145.00</BLVAMT><CCYNBR>10</CCYNBR><GSBACC>591902896710812</GSBACC><GSBNAM>银企直连专用账户9</GSBNAM><INFFLG>2</INFFLG><MSGNBR>201512090022030615</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>由591902896710812协议转入</NARTXT><SEQNBR>F3900100072280C</SEQNBR><TRSAMT>145.00</TRSAMT><TRSANL>ZHGATR</TRSANL><TRSDAT>20150416</TRSDAT><TRSSET>F39001P416AMBIJ</TRSSET><TRSTIM>122107</TRSTIM><VALDAT>20150416</VALDAT></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><EPTDAT>20151209</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N03020</FLWTYP><MSGNBR>201512090022030692</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20151209</OPRDAT><REQNBR>0028771110</REQNBR><RTNFLG>S</RTNFLG><TRSAMT>0.10</TRSAMT><YURREF>2015120900000037</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BLVAMT>147694.63</BLVAMT><CCYNBR>10</CCYNBR><INFFLG>1</INFFLG><MSGNBR>201512090022030694</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>2015120900000037</NARTXT><SEQNBR>K5842000000009C</SEQNBR><TRSAMT>0.10</TRSAMT><TRSANL>AIGATR</TRSANL><TRSDAT>20150417</TRSDAT><TRSSET>K58420P417AAAAJ</TRSSET><TRSTIM>123803</TRSTIM><VALDAT>20150417</VALDAT><YURREF>2015120900000037</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><EPTDAT>20151209</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N03020</FLWTYP><MSGNBR>201512090022030695</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20151209</OPRDAT><REQNBR>0028771111</REQNBR><RTNFLG>S</RTNFLG><TRSAMT>0.10</TRSAMT><YURREF>2015120900000038</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BLVAMT>147694.53</BLVAMT><CCYNBR>10</CCYNBR><INFFLG>1</INFFLG><MSGNBR>201512090022030697</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>2015120900000038</NARTXT><SEQNBR>K5842100000011C</SEQNBR><TRSAMT>0.10</TRSAMT><TRSANL>AIGATR</TRSANL><TRSDAT>20150417</TRSDAT><TRSSET>K58421P417AAAAJ</TRSSET><TRSTIM>131601</TRSTIM><VALDAT>20150417</VALDAT><YURREF>2015120900000038</YURREF></NTQNTCGTZ><NTQNTCGTZ><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><EPTDAT>20151209</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N03020</FLWTYP><MSGNBR>201512090022030698</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20151209</OPRDAT><REQNBR>0028771112</REQNBR><RTNFLG>S</RTNFLG><TRSAMT>0.10</TRSAMT><YURREF>2015120900000039</YURREF></NTQNTCGTZ></CMBSDKPGK>';
        $xml = '<?xml version="1.0" encoding="GBK"?><CMBSDKPGK><INFO><DATTYP>2</DATTYP><ERRMSG></ERRMSG><FUNNAM>GetHisNotice</FUNNAM><LGNNAM>银企直连专用集团1</LGNNAM><RETCOD>0</RETCOD></INFO><FBDLRHMGZ><CRTTIM>20151218120512</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033588</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218120556</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218085736</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033517</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><RCVTIM>20151218085819</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218085743</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033518</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218085826</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218105349</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033543</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218105429</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218105235</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033542</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218105317</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218110218</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033546</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218110257</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218110825</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033553</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218110909</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218110841</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033566</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><RCVTIM>20151218110922</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120511</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033576</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218120550</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120511</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033575</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218120550</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120511</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033578</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218120550</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120512</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033583</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218120556</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120512</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033585</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218120556</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033617</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120512</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033591</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218120556</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120512</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033593</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218120556</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120512</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033597</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><RCVTIM>20151218120556</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033605</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033607</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033609</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033610</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033611</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033613</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033614</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033615</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033616</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033618</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120544</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033619</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120545</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033620</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120545</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033621</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120545</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033622</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120545</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033623</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120545</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033624</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><FBDLRHMGZ><CRTTIM>20151218120545</CRTTIM><EFTDAT>20151218</EFTDAT><EXPDAT>20151228</EXPDAT><MSGNBR>201512180022033625</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><RCVTIM>20151218120627</RCVTIM></FBDLRHMGZ><NCCRTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710704</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>343962.71</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG></INFFLG><MSGNBR>201512180022033617</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710201</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行福州分行白马支行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002307C</SEQNBR><TRSAMT>1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA6AJ</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846067467</YURREF></NCCRTTRSY><NCCRTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710704</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>343958.71</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG></INFFLG><MSGNBR>201512180022033607</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710201</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行福州分行白马支行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002299C</SEQNBR><TRSAMT>1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA56J</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429844729498</YURREF></NCCRTTRSY><NCCRTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710704</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>343959.71</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG></INFFLG><MSGNBR>201512180022033610</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710201</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行福州分行白马支行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002301C</SEQNBR><TRSAMT>1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA57J</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429845004977</YURREF></NCCRTTRSY><NCCRTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710704</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>343960.71</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG></INFFLG><MSGNBR>201512180022033613</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710201</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行福州分行白马支行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002303C</SEQNBR><TRSAMT>1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA58J</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429845047553</YURREF></NCCRTTRSY><NCCRTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710704</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>343961.71</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG></INFFLG><MSGNBR>201512180022033615</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710201</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行福州分行白马支行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002305C</SEQNBR><TRSAMT>1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA59J</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846022035</YURREF></NCCRTTRSY><NCCRTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710704</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>343963.71</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG></INFFLG><MSGNBR>201512180022033619</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710201</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行福州分行白马支行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002309C</SEQNBR><TRSAMT>1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA6BJ</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846188344</YURREF></NCCRTTRSY><NCCRTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710704</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>343964.71</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG></INFFLG><MSGNBR>201512180022033621</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710201</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行福州分行白马支行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002311C</SEQNBR><TRSAMT>1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA6CJ</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846279148</YURREF></NCCRTTRSY><NCCRTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710704</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>343965.71</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG></INFFLG><MSGNBR>201512180022033623</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710201</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行福州分行白马支行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002313C</SEQNBR><TRSAMT>1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA6DJ</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846335719</YURREF></NCCRTTRSY><NCCRTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710704</ACCNBR><AMTCDR>C</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>343966.71</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG></INFFLG><MSGNBR>201512180022033625</MSGNBR><MSGTYP>NCCRTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710201</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行福州分行白马支行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002315C</SEQNBR><TRSAMT>1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA6EJ</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846485490</YURREF></NCCRTTRSY><NCDBTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>19895.68</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG>1</INFFLG><MSGNBR>201512180022033517</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>测试</NARTXT><NEWBIL></NEWBIL><RPYACC>6225880230001175</RPYACC><RPYADR>重庆市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行</RPYBNK><RPYNAM>刘五</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400000890C</SEQNBR><TRSAMT>-123.45</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150423</TRSDAT><TRSSET>G12924P423AAMMJ</TRSSET><TRSTIM>085719</TRSTIM><VALDAT>20150423</VALDAT><YURREF>qsq00001</YURREF></NCDBTTRSY><NCDBTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>19817.90</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG>1</INFFLG><MSGNBR>201512180022033566</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>测试7778</NARTXT><NEWBIL></NEWBIL><RPYACC>6225885910000108</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行</RPYBNK><RPYNAM>Judy Zeng</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400001886C</SEQNBR><TRSAMT>-77.78</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150423</TRSDAT><TRSSET>G12924P423AA0GJ</TRSSET><TRSTIM>110759</TRSTIM><VALDAT>20150423</VALDAT><YURREF>145040466774985098</YURREF></NCDBTTRSY><NCDBTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>19816.90</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG>1</INFFLG><MSGNBR>201512180022033605</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710704</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002298C</SEQNBR><TRSAMT>-1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA56J</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429844729498</YURREF></NCDBTTRSY><NCDBTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>19815.90</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG>1</INFFLG><MSGNBR>201512180022033609</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710704</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002300C</SEQNBR><TRSAMT>-1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA57J</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429845004977</YURREF></NCDBTTRSY><NCDBTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>19814.90</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG>1</INFFLG><MSGNBR>201512180022033611</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710704</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002302C</SEQNBR><TRSAMT>-1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA58J</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429845047553</YURREF></NCDBTTRSY><NCDBTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>19813.90</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG>1</INFFLG><MSGNBR>201512180022033614</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710704</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002304C</SEQNBR><TRSAMT>-1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA59J</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846022035</YURREF></NCDBTTRSY><NCDBTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>19812.90</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG>1</INFFLG><MSGNBR>201512180022033616</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710704</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002306C</SEQNBR><TRSAMT>-1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA6AJ</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846067467</YURREF></NCDBTTRSY><NCDBTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>19811.90</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG>1</INFFLG><MSGNBR>201512180022033618</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710704</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002308C</SEQNBR><TRSAMT>-1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA6BJ</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846188344</YURREF></NCDBTTRSY><NCDBTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>19810.90</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG>1</INFFLG><MSGNBR>201512180022033620</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710704</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002310C</SEQNBR><TRSAMT>-1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA6CJ</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846279148</YURREF></NCDBTTRSY><NCDBTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>19809.90</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG>1</INFFLG><MSGNBR>201512180022033622</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710704</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002312C</SEQNBR><TRSAMT>-1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA6DJ</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846335719</YURREF></NCDBTTRSY><NCDBTTRSY><ACCNAM>银企直连专用账户9</ACCNAM><ACCNBR>591902896710201</ACCNBR><AMTCDR>D</AMTCDR><BBKNBR>59</BBKNBR><BILNBR></BILNBR><BILTXT></BILTXT><BILTYP></BILTYP><BLVAMT>19808.90</BLVAMT><BUSNAR></BUSNAR><CCYNBR>10</CCYNBR><GSBACC></GSBACC><GSBADR></GSBADR><GSBBBK></GSBBBK><GSBBBN></GSBBBN><GSBBNK></GSBBNK><GSBNAM></GSBNAM><INFFLG>1</INFFLG><MSGNBR>201512180022033624</MSGNBR><MSGTYP>NCDBTTRS</MSGTYP><NARTXT>企业付款</NARTXT><NEWBIL></NEWBIL><RPYACC>591902896710704</RPYACC><RPYADR>福建省福州市</RPYADR><RPYBBK></RPYBBK><RPYBBN></RPYBBN><RPYBNK>招商银行</RPYBNK><RPYNAM>银企直连专用账户9</RPYNAM><RSV50Z></RSV50Z><RVSTAG></RVSTAG><SEQNBR>G1292400002314C</SEQNBR><TRSAMT>-1.00</TRSAMT><TRSANL>CPGATR</TRSANL><TRSDAT>20150424</TRSDAT><TRSSET>G12924P424AA6EJ</TRSSET><TRSTIM>120449</TRSTIM><VALDAT>20150424</VALDAT><YURREF>1429846485490</YURREF></NCDBTTRSY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>1.00</ENDAMT><EPTDAT>20150424</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033588</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150424</OPRDAT><REQDTA>N000000000000000            YN</REQDTA><REQNBR>0028566016</REQNBR><RSV50Z></RSV50Z><RTNDSP></RTNDSP><RTNFLG>S</RTNFLG><YURREF>1429846335719</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>123.45</ENDAMT><EPTDAT>20150423</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033518</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20151217</OPRDAT><REQDTA>N000000000000000            YN</REQDTA><REQNBR>0028772409</REQNBR><RSV50Z></RSV50Z><RTNDSP></RTNDSP><RTNFLG>S</RTNFLG><YURREF>qsq00001</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>123.45</ENDAMT><EPTDAT>20150423</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033543</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150423</OPRDAT><REQDTA></REQDTA><REQNBR></REQNBR><RSV50Z></RSV50Z><RTNDSP>NCB4241 -业务参考号重复</RTNDSP><RTNFLG>F</RTNFLG><YURREF>qsq00001</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>123.45</ENDAMT><EPTDAT>20150423</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033542</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150423</OPRDAT><REQDTA></REQDTA><REQNBR></REQNBR><RSV50Z></RSV50Z><RTNDSP>NCB4241 -业务参考号重复</RTNDSP><RTNFLG>F</RTNFLG><YURREF>qsq00001</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>123.45</ENDAMT><EPTDAT>20150423</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033546</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150423</OPRDAT><REQDTA></REQDTA><REQNBR></REQNBR><RSV50Z></RSV50Z><RTNDSP>NCB4241 -业务参考号重复</RTNDSP><RTNFLG>F</RTNFLG><YURREF>qsq00001</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>77.78</ENDAMT><EPTDAT>20150423</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033553</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20151218</OPRDAT><REQDTA>N000000000000000            YN</REQDTA><REQNBR>0028772438</REQNBR><RSV50Z></RSV50Z><RTNDSP></RTNDSP><RTNFLG>S</RTNFLG><YURREF>145040466774985098</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>1.00</ENDAMT><EPTDAT>20150424</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033576</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150424</OPRDAT><REQDTA>N000000000000000            YN</REQDTA><REQNBR>0028566017</REQNBR><RSV50Z></RSV50Z><RTNDSP></RTNDSP><RTNFLG>S</RTNFLG><YURREF>1429846485490</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>1.00</ENDAMT><EPTDAT>20150424</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033575</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150424</OPRDAT><REQDTA>N000000000000000            YN</REQDTA><REQNBR>0028566010</REQNBR><RSV50Z></RSV50Z><RTNDSP></RTNDSP><RTNFLG>S</RTNFLG><YURREF>1429845047553</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>1.00</ENDAMT><EPTDAT>20150424</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033578</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150424</OPRDAT><REQDTA>N000000000000000            YN</REQDTA><REQNBR>0028566014</REQNBR><RSV50Z></RSV50Z><RTNDSP></RTNDSP><RTNFLG>S</RTNFLG><YURREF>1429846188344</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>1.00</ENDAMT><EPTDAT>20150424</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033583</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150424</OPRDAT><REQDTA>N000000000000000            YN</REQDTA><REQNBR>0028566012</REQNBR><RSV50Z></RSV50Z><RTNDSP></RTNDSP><RTNFLG>S</RTNFLG><YURREF>1429846022035</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>1.00</ENDAMT><EPTDAT>20150424</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033585</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150424</OPRDAT><REQDTA>N000000000000000            YN</REQDTA><REQNBR>0028566015</REQNBR><RSV50Z></RSV50Z><RTNDSP></RTNDSP><RTNFLG>S</RTNFLG><YURREF>1429846279148</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>1.00</ENDAMT><EPTDAT>20150424</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033591</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150424</OPRDAT><REQDTA>N000000000000000            YN</REQDTA><REQNBR>0028566007</REQNBR><RSV50Z></RSV50Z><RTNDSP></RTNDSP><RTNFLG>S</RTNFLG><YURREF>1429844729498</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>1.00</ENDAMT><EPTDAT>20150424</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033593</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150424</OPRDAT><REQDTA>N000000000000000            YN</REQDTA><REQNBR>0028566009</REQNBR><RSV50Z></RSV50Z><RTNDSP></RTNDSP><RTNFLG>S</RTNFLG><YURREF>1429845004977</YURREF></NCDRTPAYY><NCDRTPAYY><ACCNAM>银企直连专用账户9</ACCNAM><BBKNBR>59</BBKNBR><CCYNBR>10</CCYNBR><ENDAMT>1.00</ENDAMT><EPTDAT>20150424</EPTDAT><EPTTIM>000000</EPTTIM><FLWTYP>N02031</FLWTYP><KEYVAL>591902896710201</KEYVAL><MSGNBR>201512180022033597</MSGNBR><MSGTYP>NCDRTPAY</MSGTYP><OPRDAT>20150424</OPRDAT><REQDTA>N000000000000000            YN</REQDTA><REQNBR>0028566013</REQNBR><RSV50Z></RSV50Z><RTNDSP></RTNDSP><RTNFLG>S</RTNFLG><YURREF>1429846067467</YURREF></NCDRTPAYY></CMBSDKPGK>';
        $FBSdk = \App\Services\ServiceFBSdk::getInstance();
        $xml = iconv('UTF-8', 'GBK', $xml);
        $xmlArray = $FBSdk->__xmlToArray($xml);
        $sta = [];
        foreach ($xmlArray['NTQNTCGTZ'] as $k => $v) {
            // "MSGTYP" => "NCDRTPAY"
            $sta[$v['MSGTYP']] = 1;
            if (isset($Ann[$v['MSGTYP']])) {
                echo 'MSGTYP = ' . $v['MSGTYP'] . '<br/>';
                $this->resultExampleAnn($v, $Ann[$v['MSGTYP']]);
                unset($Ann[$v['MSGTYP']]);
            }
        }
        dump($sta);
        exit();
        
        edump($Ann);
        
        $this->printTableView($str);
        
        $FBSdk = \App\Services\ServiceFBSdk::getInstance();
        edump($FBSdk->GetNewNotice());
        edump($FBSdk->GetPaymentInfo([
            'BUSCOD' => 'N02031', // 业务类别 C(6) 附录A.4 可
            'BGNDAT' => '20151216', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230', // 结束日期 C(8) 否
                                    // 'DATFLG' => 'A',//日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
                                    // 'MINAMT' => '0.00',//MINAMT 最小金额 M 可 空时表示0.00
                                    // 'MAXAMT' => '99999999.99',//MAXAMT 最大金额 M 可 空时表示9999999999999.99
            'YURREF' => '145040466774985098'
        ])); // 业务参考号 C(1,30) 可
        
        edump($FBSdk->DCPayment([
            'YURREF' => createSerialNum(), // 否 业务参考号
                                           // 用于标识该笔业务的编号，企业银行编号 + 业务类型+业务参考号必须唯一。
                                           // 企业可以自定义业务参考号，也可使用银行缺省值（单笔支付），批量支付须由企业提供。
                                           // 直联必须用企业提供
            'DBTACC' => '591902896710201', // 否 付方帐号 企业用于付款的转出帐号，该帐号的币种类型必须与币种字段相符。
            'DBTBBK' => '59', // 否 付方开户地区代
            'TRSAMT' => '77.78', // 否 交易金额
            'CCYNBR' => '10', // 否 币种代码
            'STLCHN' => 'N', // 否 结算方式代码 只对跨行交易有效 N：普通 F：快速
            'NUSAGE' => '测试7778', // 否 用途 对应对账单中的摘要NARTXT
            'BNKFLG' => 'Y', // 否 系统内外标志 Y：招行；N：非招行；
            'CRTACC' => '6225885910000108', // 否 收方帐号 收款企业的转入帐号，该帐号的币种类型必须与币种字段相符。
            'CRTNAM' => 'Judy Zeng', // 否 收方帐户名 收款方企业的转入帐号的帐户名称。
            'CRTBNK' => '招商银行'
        ])); // 可 收方开户行 跨行支付（BNKFLG=N）必填
        
        edump($FBSdk->GetPaymentInfo([
            'BUSCOD' => 'N02031', // 业务类别 C(6) 附录A.4 可
            'BGNDAT' => '20151201', // 起始日期 C(8) 否 起始日期和结束日期的间隔不能超过100天
            'ENDDAT' => '20151230'
        ])); // 结束日期 C(8) 否
             // 'DATFLG' => 'A',//日期类型 C(1) A：经办日期；B：期望日期 可 空表示A
             // 'MINAMT' => '0.00',//MINAMT 最小金额 M 可 空时表示0.00
             // 'MAXAMT' => '99999999.99',//MAXAMT 最大金额 M 可 空时表示9999999999999.99
             // 'YURREF' => '352003144990033386100000000000',//业务参考号 C(1,30) 可
        
        edump($FBSdk->GetAccInfo([
            'BBKNBR' => '59', // 分行号 N(2) 附录A.1 否 分行号和分行名称不能同时为空
            'C_BBKNBR' => '福州', // 分行名称 Z(1,62) 附录A.1 是
            'ACCNBR' => '591902896710704'
        ])); // 账号 C(1,35) 否
             
        // edump($FBSdk->GetHisNotice([
             // 'BGNDAT' => '20151018', //否 开始日期 开始日期和结束日期的间隔不能超过100天
             // 'ENDDAT' => '20151218', //否 结束日期
             // 'MSGTYP' => 'NCBCHOPR', //可 消息类型
             // ]));
        dump($FBSdk->GetTransInfo([
            'BBKNBR' => '59', // 分行号 N(2) 附录A.1 可 分行号和分行名称不能同时为空
            'C_BBKNBR' => '福州', // 分行名称 Z(1,62) 附录A.1 可
            'ACCNBR' => '591902896710704', // 账号 C(1,35) 否
            'BGNDAT' => '20120928', // 起始日期 D 否
            'ENDDAT' => '20121230'
        ])); // 结束日期 D 否 与结束日期的间隔不能超过100天
             // 'LOWAMT' => '0.00',//最小金额 M 可 默认0.00
             // 'HGHAMT' => '9999999.99',//最大金额 M 可 默认9999999999999.99
             // 'AMTCDR' => 'C',//借贷码 C(1) C：收入 D：支出 可
        
        exit();
        $str = '
人民币	10
港币	21
澳元	29
美元	32
欧元	35
加拿大元	39
英镑	43
日元	65
新加坡元	69
挪威克朗	83
丹麦克朗	85
瑞士法郎	87
瑞典克朗	88';
        $this->printTableView($str, false);
        
        // $res = $FBSdk->__toXml($data);
        // // $FBSdk->setXmlOutputHeader();
        // $r = $FBSdk->__xmlToArray($res);
        // dump($r);
        // echo($res);
        exit();
    }

    public function getCode()
    {
        $phone = \Input::get('phone');
        
        $key = \App\Services\User\UserService::getRedisKeys('smsKey', [
            date('Ymd'),
            $phone
        ]);
        
        $data = \LRedis::GET($key);
        
        $data && $data = json_decode($data, 1);
        $last = end($data);
        
        return $this->__json(200, 'ok', $last);
    }

    public function printTableView($str, $kv = true)
    {
        $result = $this->tableViewArray($str, $kv);
        preArrayKV($result);
        exit();
    }

    public function tableViewToArrayAnn($str)
    {
        $array = $this->tableViewArray($str, 1, 0);
        
        echo '<pre>';
        array_walk($array, function ($v, $k) {
            echo sprintf("'%s' => '' ,// %s\n", $k, $v);
        });
        echo '</pre>';
    }

    /**
     *
     * @param unknown $str            
     * @param string $kv
     *            $k => $v
     * @param string $ks
     *            ksort
     * @return boolean|multitype:string
     */
    public function tableViewArray($str, $kv = true, $ks = true)
    {
        $res = explode("\r", $str);
        // edump($res);
        $res = array_filter($res, function ($v) {
            return trim($v) != '';
        });
        $result = [];
        foreach ($res as $k => $v) {
            $v = trim($v);
            $vv = preg_split("/\s/", $v, 2);
            // edump($vv);
            $kv && $result[trim($vv[0])] = trim($vv[1]);
            $kv === false && $result[trim($vv[1])] = trim($vv[0]);
        }
        $ks && ksort($result);
        return $result;
    }

    public function resultExampleAnn($resultExample, $ann)
    {
        echo '<pre>';
        foreach ($resultExample as $k => $v) {
            echo " '$k' => '$v',//" . (isset($ann[$k]) ? $ann[$k] : 'UNKNOWN') . " " . PHP_EOL;
        }
        echo '</pre>';
    }

    public function printTableViewAnn($str, $kv = true)
    {
        $result = $this->tableViewArray($str, $kv);
        preArrayKV($result);
        exit();
    }
    
    
    public function test1(){

        
        \App\Models\McpayDetail::importFromLog();
        exit;
        
        \App\Models\FbsdkLog::create([]);
        dump(\App\Models\FbsdkLog::create([],true));
        exit;
        $fbsdk = \App\Services\Merchants\FBSdkService::getInstance();
        
        //DCPAYMNT
        //NTIBCOPR
        
        $logs =  \App\Models\FbsdkLog::where('func_name','DCPAYMNT')
        ->orWhere('func_name','NTIBCOPR')->get()->toArray();
        
        
        $dataKey = [
            'DCPAYMNT' => ['DCOPDPAYX','NTQPAYRQZ'],
            'NTIBCOPR' => ['NTIBCOPRX','NTOPRRTNZ'],
        ];
        
        
        foreach ($logs as $k => $v){
            if($v['send_status']){
                $log_id = $v['id'];
                $funcname = $v['func_name'];
                $sendData = $fbsdk->__xmlToArray(iconv( 'UTF-8', 'GBK',$v['send_xml']));
                $receiveData = $fbsdk->__xmlToArray(iconv( 'UTF-8', 'GBK',$v['received_xml']));
                //DCOPDPAYX NTQPAYRQZ
                //NTIBCOPRX NTOPRRTNZ 
                
                $send = isset($sendData[$dataKey[$funcname][0]]) ? $sendData[$dataKey[$funcname][0]] :[];
                $receive = isset($receiveData[$dataKey[$funcname][1]]) ? $receiveData[$dataKey[$funcname][1]] :[];
                
                \App\Models\McpayDetail::record(
                    $log_id, $funcname, 
                    $send, 
                    $receive
                );
            }
        }
        
        
        esqlLastSql();
        
        exit;
        
        
        
        
        edump($_SERVER);
        
        $v = iconv('utf8', 'iso-8859-1', "sdsd代发");
        header("Content-Type: text/xml;encoding=utf-8");
        echo utf8_decode(wddx_serialize_value($v));
        exit;
  
        edump(get_class());
        echo false;
        
        $a = 'asd';
        
        exit;
        get_class();
        
        $str =<<<qw
asdasd
qw;
        
        $str = <<<'EOT'
My name is "$name". I am printing some $foo->foo.
Now, I am printing some {$foo->bar[1]}.
This should not print a capital 'A': \x41
EOT;
        
        edump($str);
        dump(decbin(12));
        dump(floor((0.1+0.7)*10) );
         dump( ((0.1+0.7) *10) );
        var_dump(010120);
        
        exit;
    }
}



