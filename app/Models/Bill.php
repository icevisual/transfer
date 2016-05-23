<?php
namespace App\Models;

class Bill extends \Eloquent
{

    /**
     * 单人需付（一般指房租）
     * @var unknown
     */
    const TYPE_SHOULD_PAY_SINGLE = 1;
    /**
     * 需平摊的费用
     * @var unknown
     */
    const TYPE_SHOULD_PAY_ALL = 2;
    /**
     * 单人已付金额
     * @var unknown
     */
    const TYPE_PAYED = 3;
    
    protected $table = 'bill';

    protected $primaryKey = 'id';

    protected $guarded = [];
    
    public static function getTableCreateStatment(){
        return $str = "
CREATE TABLE `x_bill` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT '姓名',
  `type` tinyint(4) NOT NULL COMMENT '金额类别，1需付，1已付',
  `desc` varchar(160) NOT NULL COMMENT '描述',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=603 DEFAULT CHARSET=utf8 COMMENT='用户账单表'
            ";
    }

    public static function run(){
        $data = [
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_SHOULD_PAY_SINGLE,
                'desc' => '房租',
                'amount' => 1430 * 2
            ],
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_SHOULD_PAY_SINGLE,
                'desc' => '房租',
                'amount' => 1630 * 2
            ],
            [
                'name' => '李蒙',
                'type' => Bill::TYPE_SHOULD_PAY_SINGLE,
                'desc' => '房租',
                'amount' => 1540 * 2
            ],
            [
                'name' => '',
                'type' => Bill::TYPE_SHOULD_PAY_ALL,
                'desc' => '物业费',
                'amount' => 79 * 2
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2016年3月水费',
                'amount' => 40.60
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2016年3月电费',
                'amount' => 65.10
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '厕所喷头、节能灯费用',
                'amount' => 32 + 24 
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '修锁费用',
                'amount' => 90 + 70
            ],
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2016年5月水费-缴费号744265',
                'amount' => 52.20
            ],
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_PAYED,
                'desc' => '电费=缴费号6010066207',
                'amount' => 101.61
            ]
        ];
        Bill::clearData();
        foreach ($data as $v) {
            Bill::addRecord($v['name'], $v['type'], $v['amount'], $v['desc']);
        }
        Bill::settlement();
    }
    
    
    
    /**
     * 结算
     */
    public static function settlement(){
        
        $prefix = \DB::getTablePrefix();
        $handle = self::select([
            'name',
            'type',
            'amount',
            'desc',
//             \DB::raw('SUM(amount) AS amount'),
        ]);
        
        $result = $handle->get()->toArray();
        
        $bill = [
        ];
        $allShouldPay = 0;
        $shouldPayDetail = [];
        $payedDetail = [];
        foreach ($result as $v){
            if($v['name'] && !isset($bill[$v['name']])){
                $bill[$v['name']] = [
                    'should' => 0,
                    'payed' => 0,
                ];
            }
            switch ($v['type']){
                case self::TYPE_SHOULD_PAY_SINGLE:
                    $bill[$v['name']]['should'] += $v['amount'];
                    break;
                case self::TYPE_SHOULD_PAY_ALL:
                    $allShouldPay += $v['amount'];
                    $shouldPayDetail [] = $v;
                    break;
                case self::TYPE_PAYED:
                    $bill[$v['name']]['payed'] += $v['amount'];
                    $payedDetail [$v['name']] [] = $v;
                    break;
                default:;
            }
        }
        $num = count($bill);
        $nrl = '<br/>';
        $shouldPayCount = count($shouldPayDetail);
        foreach ($shouldPayDetail as $k => $shp){
            if($k == 0){
                echo '平摊费用 = ('.$nrl;
            }
            echo " + {$shp['amount']} ({$shp['desc']}) $nrl";
            if($k == $shouldPayCount - 1){
                echo ') / '.$num;
            }
        }
        $shouldPayDiv = $allShouldPay / $num;
        echo ' = '.$shouldPayDiv.' '.$nrl.$nrl;
        foreach ($bill as $name =>  $v){
            echo "$name $nrl";
            echo " + {$v['should']} (房租) $nrl + $shouldPayDiv ";
            echo $nrl;
            if(isset($payedDetail[$name] )){
                
                foreach ($payedDetail[$name] as $k => $shp){
                    echo "- {$shp['amount']} ({$shp['desc']}) $nrl";
                }
                echo $nrl;
            }
            $shouldPay = $v['should'] + $shouldPayDiv - $v['payed'];
            echo "$name 应付  $shouldPay $nrl $nrl";
        }
    }
    
    
    public static function clearData(){
        self::where('id','>',0)->delete();
    }
    
    public static function addRecord($name,$type,$amount,$desc){
        $data = [
            'name' => $name,
            'type' => $type,
            'desc' => $desc,
            'amount' => $amount,
        ];
        
        self::create($data);
        // 代缴平摊费用
        if(self::TYPE_PAYED  == $type ){
            $data = [
                'type' => self::TYPE_SHOULD_PAY_ALL,
                'desc' => $desc,
                'amount' => $amount,
            ];
            self::create($data);
        }
    }
    
    
}