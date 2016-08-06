<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Finance\SalaryPay;
use App\Models\Finance\SalaryOrder;
use App\Models\Company\PayrollProperty;
use App\Models\Company\CompanyPayrollProperty;
use App\Models\Company\CompanyMo;

class DataUpgrade extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-upgrade {n=1000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DataUpgrade from 1.3.1 A to 1.3.1 B';

    /**
     * 对已有的salary_payroll_value ，加入剩余字段
     */
    public function updateExsistsPropertyValue()
    {
        $sql = 'UPDATE xb_salary_payroll_value
JOIN xb_salary_pay ON xb_salary_payroll_value.pay_id = xb_salary_pay.id
JOIN xb_payroll_property ON xb_salary_payroll_value.payroll_id = xb_payroll_property.id
JOIN xb_company_payroll_property ON xb_salary_payroll_value.payroll_id = xb_company_payroll_property.payroll_id
AND xb_company_payroll_property.company_id = xb_salary_pay.company_id
SET xb_salary_payroll_value.`name` = xb_payroll_property.`name`,
 xb_salary_payroll_value.`code` = xb_payroll_property.`code`,
 xb_salary_payroll_value.`width` = xb_payroll_property.`width`,
 xb_salary_payroll_value.`type` = xb_payroll_property.`type`,
 xb_salary_payroll_value.`weixin_sort` = xb_company_payroll_property.`weixin_sort`,
 xb_salary_payroll_value.`excel_sort` = xb_company_payroll_property.`excel_sort`,
 xb_salary_payroll_value.`is_weixin_show` = xb_company_payroll_property.`is_weixin_show`
WHERE
	xb_salary_payroll_value.type = 0';
        
        \DB::update($sql);
    }

    /**
     * 获取没有税前或者个税的订单信息
     *
     * @return Ambigous <multitype:multitype: , number>
     */
    public function getNoSalaryOrTaxMap()
    {
        $prefix = \DB::getTablePrefix();
        $noSalaryOrTaxhandle = SalaryPay::select([
            'salary_order.order_id',
            \DB::raw("SUM({$prefix}salary_pay.salary) AS sum_salary"),
            \DB::raw("SUM({$prefix}salary_pay.tax) AS sum_tax")
        ])->join('salary_order', 'salary_order.order_id', '=', 'salary_pay.order_id')
            ->groupBy('salary_pay.order_id')
            ->havingRaw("SUM({$prefix}salary_pay.salary) = 0 or SUM({$prefix}salary_pay.tax) = 0 ")
            ->get();
        
        $noSalaryOrTaxhandle && $noSalaryOrTaxhandle = $noSalaryOrTaxhandle->toArray();
        // 没有tax 或者 salary 的订单
        $noSalaryOrTaxMap = [
            'salary' => [],
            'tax' => []
        ];
        foreach ($noSalaryOrTaxhandle as $v) {
            if ($v['sum_salary'] == 0) {
                $noSalaryOrTaxMap['salary'][$v['order_id']] = 1;
            }
            if ($v['sum_tax'] == 0) {
                $noSalaryOrTaxMap['tax'][$v['order_id']] = 1;
            }
        }
        return $noSalaryOrTaxMap;
    }

    /**
     * 获取默认的一些属性
     *
     * @return multitype:unknown
     */
    public function getLimitedModificationPropertyMap()
    {
        $requiredPropertyHandle = PayrollProperty::select([
            'payroll_property.id',
            'payroll_property.name',
            'payroll_property.type',
            'payroll_property.width',
            'payroll_property.code'
        ])->where('payroll_property.limited_modification', PayrollProperty::LIMITED_MODIFICATION_YES)->get();
        
        $requiredPropertyHandle && $requiredPropertyHandle = $requiredPropertyHandle->toArray();
        $requiredPropertyMap = [];
        foreach ($requiredPropertyHandle as $v) {
            $requiredPropertyMap[$v['id']] = $v;
        }
        return $requiredPropertyMap;
    }

    /**
     * 企业的工资单属性配置信息
     *
     * @param unknown $companyIDArray            
     * @return Array <pre>
     *         array:13 [
     *         154 => array:16 [
     *         1 => array:9 [
     *         "payroll_id" => 1
     *         "name" => "姓名"
     *         "code" => "truename"
     *         "width" => 20
     *         "type" => 1
     *         "company_id" => 154
     *         "weixin_sort" => 1
     *         "excel_sort" => 1
     *         "is_weixin_show" => 1
     *         ]
     *        
     *         </pre>
     */
    public function getCompanyPayrollPropertyMap($companyIDArray)
    {
        $companyPayrollPropertyHandle = CompanyPayrollProperty::select([
            'payroll_property.id AS payroll_id',
            'payroll_property.name',
            'payroll_property.code',
            'payroll_property.width',
            'payroll_property.type',
            'company_payroll_property.company_id',
            'company_payroll_property.weixin_sort',
            'company_payroll_property.excel_sort',
            'company_payroll_property.is_weixin_show'
        ])->join('payroll_property', 'company_payroll_property.payroll_id', '=', 'payroll_property.id')
            ->whereIn('company_id', $companyIDArray)
            ->get();
        $companyPayrollPropertyHandle && $companyPayrollPropertyHandle = $companyPayrollPropertyHandle->toArray();
        $companyPayrollPropertyMap = [];
        foreach ($companyPayrollPropertyHandle as $v) {
            $companyPayrollPropertyMap[$v['company_id']][$v['payroll_id']] = $v;
        }
        return $companyPayrollPropertyMap;
    }

    /**
     *
     * @param unknown $orderIDArray            
     * @return array <pre>
     *         "146789335412811359" => array:1 [
     *         0 => array:9 [
     *         "order_id" => "146789335412811359"
     *         "pay_id" => 18081
     *         "company_id" => 155
     *         "truename" => "閲戠嚂鏋?
     *         "bank_name" => "中国银行"
     *         "card_no" => "6222620170931032"
     *         "salary" => "10.00"
     *         "tax" => "9.00"
     *         "amount" => "0.01"
     *         ]
     *         ]
     *         </pre>
     */
    public function getSalaryPayDataMap($orderIDArray)
    {
        $salaryPayDataHandle = SalaryPay::select([
            'salary_pay.order_id',
            'salary_pay.id AS pay_id',
            'salary_pay.company_id',
            'salary_pay.truename',
            'salary_pay.bank_name',
            'salary_pay.card_no',
            'salary_pay.salary',
            'salary_pay.tax',
            'salary_pay.amount'
        ])->whereIn('salary_pay.order_id', $orderIDArray)->get();
        $salaryPayDataHandle && $salaryPayDataHandle = $salaryPayDataHandle->toArray();
        $this->info('$salaryPayDataHandle count = ' . count($salaryPayDataHandle));
        $salaryPayDataMap = [];
        foreach ($salaryPayDataHandle as $k => $v) {
            $salaryPayDataMap[$v['order_id']][] = $v;
        }
        return $salaryPayDataMap;
    }

    /**
     * 新建企业的默认工资单配置
     *
     * @return boolean
     */
    public function createDefaultCompanyPayrollProperties()
    {
        $prefix = \DB::getTablePrefix();
        
        $now = date('Y-m-d H:i:s');
        
        $noPayrollPropertiesHandle = CompanyMo::select([
            'company.company_id'
        ])->leftJoin('company_payroll_property', 'company_payroll_property.company_id', '=', 'company.company_id')
            ->groupBy('company.company_id')
            ->havingRaw("COUNT({$prefix}company_payroll_property.id) = 0")
            ->get();
        $noPayrollPropertiesHandle && $noPayrollPropertiesHandle = $noPayrollPropertiesHandle->toArray();

        $defaultPropertiesHandle = PayrollProperty::select([
            'id'
        ])->where('limited_modification', PayrollProperty::LIMITED_MODIFICATION_YES)
            ->orderBy('id', 'asc')
            ->get();
        $defaultPropertiesHandle && $defaultPropertiesHandle = $defaultPropertiesHandle->toArray();
        
        $defaultPropertiesMap = [];
        foreach ($defaultPropertiesHandle as $v) {
            $defaultPropertiesMap[] = $v['id'];
        }
        $groupInsertData = [];
        foreach ($noPayrollPropertiesHandle as $v) {
            foreach ($defaultPropertiesMap as $k1 => $v1) {
                $data = [
                    'company_id' => $v['company_id'],
                    'payroll_id' => $v1,
                    'excel_sort' => $k1 + 1,
                    'weixin_sort' => $k1 + 1,
                    'is_weixin_show' => CompanyPayrollProperty::WEIXIN_SHOW_YES,
                    'created_at' => $now
                ];
                $groupInsertData[] = $data;
            }
        }
        \DB::beginTransaction();
        insertGroupSql('xb_company_payroll_property', $groupInsertData);
        \DB::commit();
        return true;
    }

    /**
     *
     * @param unknown $limitedModificationPropertyMap            
     * @param unknown $noSalaryOrTaxMap            
     * @return array <pre>
     *         array:2 [
     *         155 => 1
     *         154 => 1
     *         ]
     *         array:13 [
     *         "146789335412811359" => array:2 [
     *         "company_id" => 155
     *         "data" => array:6 [
     *         "truename" => 1
     *         "bank_name" => 2
     *         "card_no" => 3
     *         "amount" => 4
     *         "salary" => 5
     *         "tax" => 6
     *         ]
     *         ]
     *        
     *         </pre>
     */
    public function getUpgradeData($limitedModificationPropertyMap, $noSalaryOrTaxMap)
    {
        $prefix = \DB::getTablePrefix();
        
        $separator = '#E#'; // 值的分隔符
        
        $n = $this->argument('n');
        
        $this->info('$n = ' . $n);
        
        $handle = SalaryPay::select([
            'salary_pay.company_id',
            'salary_pay.order_id',
            \DB::raw("GROUP_CONCAT('{$separator}',{$prefix}salary_payroll_value.payroll_id,'{$separator}') AS property_id")
        ])->leftJoin('salary_payroll_value', 'salary_pay.id', '=', 'salary_payroll_value.pay_id')
            ->where('salary_pay.created_at', '>=', '20160412')
            ->groupBy('salary_pay.id')
            ->orderBy('salary_pay.id', 'desc')
            ->limit($n);
        
        $handle = $handle->get();
        $handle && $handle = $handle->toArray();
        
        $needFix = [];
        
        // 需要修复数据的企业，用于获取企业的工资单属性配置信息
        $needFixCompanyMap = [];
        
        foreach ($handle as $k => $v) {
            
            if (isset($needFix[$v['order_id']])) {
                continue;
            }
            $payrollIdArray = groupConcatToArray($v['property_id'], $separator);
            $payrollIdArray = array_flip($payrollIdArray);
            
            $payrollIdArray = [];
            foreach ($limitedModificationPropertyMap as $k1 => $v1) {
                if (! isset($payrollIdArray[$v1['id']])) {
                    $needFix[$v['order_id']][$v1['code']] = $v1['id'];
                }
            }
            if (isset($needFix[$v['order_id']])) {
                if (isset($noSalaryOrTaxMap['salary'][$v['order_id']])) {
                    unset($needFix[$v['order_id']]['salary']);
                }
                if (isset($noSalaryOrTaxMap['tax'][$v['order_id']])) {
                    unset($needFix[$v['order_id']]['tax']);
                }
            } else {
                // edump($v['order_id']);
            }
            if (isset($needFix[$v['order_id']]) && $needFix[$v['order_id']]) {
                $needFix[$v['order_id']] = [
                    'company_id' => $v['company_id'],
                    'data' => $needFix[$v['order_id']]
                ];
                $needFixCompanyMap[$v['company_id']] = 1;
            }
        }
        
        return [
            'needFix' => $needFix,
            'needFixCompanyMap' => $needFixCompanyMap
        ];
    }

    /**
     * 1是没有payroll_value的新增所有需要的
     *
     * @param unknown $limitedModificationPropertyMap            
     * @param unknown $noSalaryOrTaxMap            
     * @return array <pre>
     *         array:2 [
     *         155 => 1
     *         154 => 1
     *         ]
     *         array:13 [
     *         "146789335412811359" => array:2 [
     *         "company_id" => 155
     *         "data" => array:6 [
     *         "truename" => 1
     *         "bank_name" => 2
     *         "card_no" => 3
     *         "amount" => 4
     *         "salary" => 5
     *         "tax" => 6
     *         ]
     *         ]
     *        
     *         </pre>
     */
    public function getUpgradeDataOfEmpty($limitedModificationPropertyMap)
    {
        $prefix = \DB::getTablePrefix();
        
        $separator = '#E#'; // 值的分隔符
        
        $n = $this->argument('n');
        
//         $this->info('$n = ' . $n);
        
        $handle = SalaryOrder::select([
            'salary_order.company_id',
            'salary_order.order_id'
        ])
            ->where('salary_order.created_at', '>=', '20160412')
            ->groupBy('salary_order.order_id');
//             ->limit($n);
        
        $handle = $handle->get();
        $handle && $handle = $handle->toArray();
        $needFix = [];
        
        // 需要修复数据的企业，用于获取企业的工资单属性配置信息a
        $needFixCompanyMap = [];
        
        foreach ($handle as $k => $v) {
            foreach ($limitedModificationPropertyMap as $k1 => $v1) {
                $needFix[$v['order_id']][$v1['code']] = $v1['id'];
            }
            if (isset($needFix[$v['order_id']]) && $needFix[$v['order_id']]) {
                $needFix[$v['order_id']] = [
                    'company_id' => $v['company_id'],
                    'data' => $needFix[$v['order_id']]
                ];
                $needFixCompanyMap[$v['company_id']] = 1;
            }
        }
        return [
            'needFix' => $needFix,
            'needFixCompanyMap' => $needFixCompanyMap
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '512M');
        
        $this->createDefaultCompanyPayrollProperties();
        // 对已有的salary_payroll_value ，加入剩余字段
        $this->updateExsistsPropertyValue();
        
        $noSalaryOrTaxMap = $this->getNoSalaryOrTaxMap();
        
        $this->info('$noSalaryOrTaxMap count = ' . count($noSalaryOrTaxMap));
        
        $limitedModificationPropertyMap = $this->getLimitedModificationPropertyMap();
        
        $this->info('$limitedModificationPropertyMap count = ' . count($limitedModificationPropertyMap));
        
        $prefix = \DB::getTablePrefix();
        
        // 补全分为两部分，
        // 1是没有payroll_value的新增所有需要的
        // 2是已有的，添加缺失的
        
        // 补全 salary(optional) tax(optional) amount bank_name card_no truename
        
        $upgradeData = $this->getUpgradeDataOfEmpty($limitedModificationPropertyMap);
        
        $needFix = $upgradeData['needFix'];
        
        // 需要修复数据的企业，用于获取企业的工资单属性配置信息
        $needFixCompanyMap = $upgradeData['needFixCompanyMap'];
        
        $this->info('$needFixCompanyMap count = ' . count($needFixCompanyMap));
//         edump($needFixCompanyMap);
        $this->info('$needFix count = ' . count($needFix));
        
        unset($upgradeData);
        
        if ($needFixCompanyMap) {
            // 企业的工资单属性配置信息
            $companyPayrollPropertyMap = $this->getCompanyPayrollPropertyMap(array_keys($needFixCompanyMap));

            $this->info('$companyPayrollPropertyMap count = ' . count($companyPayrollPropertyMap));
            // edump($companyPayrollPropertyMap);
            $fixOrderIDArray = array_keys($needFix);
            
            $salaryPayDataMap = $this->getSalaryPayDataMap($fixOrderIDArray);
            
            $this->info('$salaryPayDataMap count = ' . count($salaryPayDataMap));
            
            $createData = [];
            
            \DB::beginTransaction();
            
            foreach ($needFix as $order_id => $v) {
                $company_id = $v['company_id'];
                $appendColumns = $v['data'];
                $propertyData = [];
                foreach ($appendColumns as $colName => $payrollID) {
                    if($payrollID == 25){
                        dump($order_id);
                        edump($appendColumns);
                    }
                    $propertyData[$colName] = $companyPayrollPropertyMap[$company_id][$payrollID];
                }
                foreach ($salaryPayDataMap[$order_id] as $k => $v) {
                    
                    foreach ($appendColumns as $colName => $payrollID) {
                        $v1 = $companyPayrollPropertyMap[$company_id][$payrollID];
                        $data = [
                            'order_id' => $order_id,
                            'pay_id' => $v['pay_id'],
                            'payroll_id' => $v1['payroll_id'],
                            'value' => $v[$colName],
                            'name' => $v1['name'],
                            'code' => $v1['code'],
                            'width' => $v1['width'],
                            'type' => $v1['type'],
                            'weixin_sort' => $v1['weixin_sort'],
                            'excel_sort' => $v1['excel_sort'],
                            'is_weixin_show' => $v1['is_weixin_show']
                        ];
                        $createData[] = $data;
                        if (count($createData) >= 100) {
                            $sql = creategroupInsertSql('xb_salary_payroll_value', $createData);
                            \DB::insert($sql);
                            $createData = [];
                        }
                    }
                }
                if (count($createData) >= 100) {
                    // edump($createData);
                    $sql = creategroupInsertSql('xb_salary_payroll_value', $createData);
                    \DB::insert($sql);
                    $createData = [];
                    // edump($createData);
                }
            }
            if ($createData) {
                $sql = creategroupInsertSql('xb_salary_payroll_value', $createData);
                \DB::insert($sql);
            }
            \DB::commit();
        }
        
        $this->comment(PHP_EOL . '--END--' . PHP_EOL);
    }
}



