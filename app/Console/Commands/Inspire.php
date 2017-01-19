<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Models\Common\Bill;
use GuzzleHttp;

class Inspire extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $url = 'http://192.168.5.61:18083/api/clients';
        // $uname = 'admin';
        // $upass = 'public';
        // $param = [
        // ];
        
        // $Authorization = 'Basic '.base64_encode($uname.':'.$upass);
        
        // $client = new \GuzzleHttp\Client();
        // $res = $client->request('GET', $url,[
        // 'headers' => [
        // 'Authorization' => $Authorization
        // ]
        // ]);
        
        // echo $res->getStatusCode();
        // // 200
        // echo $res->getHeaderLine('content-type');
        // // 'application/json; charset=utf8'
        // echo $res->getBody();
        // // '{"id": 1420053, "name": "guzzle", ...}'
        // exit;
        // Send an asynchronous request.
        // $request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org');
        // $promise = $client->sendAsync($request)->then(function ($response) {
        // echo 'I completed! ' . $response->getBody();
        // });
        // $promise->wait();
        
        // 1430 1630 1540
        
        // 97.44
        // 5673.33
        // 307.74
        // 1073.33
        
        // array:3 [
        // 1430 => "1763.67"
        // 1540 => "1899.33"
        // 1630 => "2010.33"
        
        // ]
        
        // 押二付三
        // dump(1630 / 4600 * 5300); // 1878
        // dump(1540 / 4600 * 5300); // 1774
        // dump(1430 / 4600 * 5300); // 1648
        
        // dump(1630 / 4600 * 1400); // 496
        // dump(1540 / 4600 * 1400); // 469
        // dump(1430 / 4600 * 1400); // 435
        
        // 四舍五入
        // 顾云翔 1630 / 4600 * 1400 = 496
        // 李蒙 1540 / 4600 * 1400 = 469
        // 金燕林 1430 / 4600 * 1400 = 435
        
        // 496.08695652174
        // 468.69565217391
        // 435.21739130435
        
        // exit();
        $data = [
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_SHOULD_PAY_SINGLE,
                'desc' => '房租 [每月 1630 / 4600 * 5300 = 1878]',
                'amount' => 1878 * 3
            ],
            [
                'name' => '李蒙',
                'type' => Bill::TYPE_SHOULD_PAY_SINGLE,
                'desc' => '房租 [每月 1540 / 4600 * 5300 = 1774]',
                'amount' => 1774 * 3
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_SHOULD_PAY_SINGLE,
                'desc' => '房租 [每月 1430 / 4600 * 5300 = 1648]',
                'amount' => 1648 * 3
            ],
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2016.11.13 电费',
                'amount' => 66.71
            ],
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.01.04 电费',
                'amount' => 71.02
            ],
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_PAYED,
                'desc' => '长城宽带',
                'amount' => 360
            ],
            // [
            // 'name' => '李蒙',
            // 'type' => Bill::TYPE_SHOULD_PAY_SINGLE,
            // 'desc' => '押金差额 [1540 / 4600 * 1400 = 469]',
            // 'amount' => 469
            // ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2016.11.16 水费',
                'amount' => 55.10
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2016.11.19 燃气费',
                'amount' => 111.60
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2016.12.10 电费',
                'amount' => 90.92
            ],
//             [
//                 'name' => '',
//                 'type' => Bill::TYPE_SHOULD_PAY_ALL,
//                 'desc' => '服务费',
//                 'amount' => 636
//             ],
//             [
//                 'name' => '李蒙',
//                 'type' => Bill::TYPE_PAYED,
//                 'desc' => '电费',
//                 'amount' => 89.31
//             ],
//             [
//                 'name' => '金燕林',
//                 'type' => Bill::TYPE_PAYED,
//                 'desc' => '2016年9月水费',
//                 'amount' => 64.03
//             ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_SHOULD_PAY_ALL,
                'desc' => '计算补正',
                'amount' => 0.02
            ]
        ];
        
        $actual = 0;
        foreach ($data as $v) {
            if ($v['type'] != Bill::TYPE_PAYED)
                $actual += $v['amount'];
        }
        
        $payAll = Bill::run($data, PHP_EOL);
        dump($payAll, $actual);
        exit();
        
        $payed = 97.44 + 5673.33 + 307.74;
        
        $a = [
            1430,
            1540,
            1630
        ];
        $sum = 4600;
        $pay = 5673.33;
        $aa = [];
        foreach ($a as $k => $v) {
            $aa[$v] = toFix($pay * $v / $sum);
        }
        dump($aa);
        $avgPay = 97.44;
        foreach ($a as $k => $v) {
            $aa[$v] += toFix($avgPay / 3);
        }
        $s = 0;
        foreach ($a as $k => $v) {
            $s += $aa[$v];
        }
        
        dump($aa);
        dump($s);
        dump("Should Be " . $payed);
        // 97.44
        // 5673.33
        
        $this->comment(PHP_EOL . Inspiring::quote() . PHP_EOL);
    }
}




