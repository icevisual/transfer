<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Models\Common\Bill;
use GuzzleHttp;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

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
        $str = <<<EOL
{
    "0": {
        "topic": "/topic/v1.uuid.",
        "ip": "120.26.200.128"
    },
    "1": {
        "name": "1-12.mp3",
        "time": 12,
        "sn": "00000060"
    },
    "2": {
        "name": "2-10.mp3",
        "time": 10,
        "sn": "00000063"
    },
    "3": {
        "name": "4.mp3",
        "time": 14,
        "sn": "00000065"
    },
    "4": {
        "name": "3.mp3",
        "time": 11,
        "sn": "00000064"
    },
    "5": {
        "name": "16-12.mp3",
        "time": 12,
        "sn": "0000005C",
        "explain": "榴莲"
    },
    "6": {
        "name": "6-14.mp3",
        "time": 14,
        "sn": "00000061"
    },
    "7": {
        "name": "7-12.mp3",
        "time": 12,
        "sn": "00000059"
    },
    "8": {
        "name": "8.mp3",
        "time": 15,
        "sn": "0000005F"
    },
    "9": {
        "name": "9-12.mp3",
        "time": 12,
        "sn": "0000005B"
    },
    "10": {
        "name": "10-12.mp3",
        "time": 12,
        "sn": "0000005D"
    },
    "11": {
        "name": "11-10.mp3",
        "time": 10,
        "sn": "00000058"
    },
    "12": {
        "name": "12-10.mp3",
        "time": 10,
        "sn": "0000005E"
    },
    "13": {
        "name": "13-12.mp3",
        "time": 12,
        "sn": "0000005A"
    },
    "14": {
        "name": "14-13.mp3",
        "time": 13,
        "sn": "00000056"
    },
    "15": {
        "name": "15-12.mp3",
        "time": 12,
        "sn": "00000057"
    },
    "16": {
        "name": "5-12.mp3",
        "time": 12,
        "sn": "00000062"
    }
}        
        
EOL;
        
        $data = json_decode($str, 1);
        unset($data['0']);
        $array = [];
        foreach ($data as $k => $v) {
            $array[] = [
                'name' => '',
                'sid' => $v['sn'],
                'ptime' => $v['time'],
                'audio' => $v['name'],
                'listImg' => ($k) . '-1.png',
                'detailImg' => ($k) . '.png'
            ];
        }
        // dd(json_encode($array));
        // dd($array);
        
        // $content = curl_get('https://help.aliyun.com/document_detail/25656.html?spm=5176.doc25506.2.1.1ZxBYf',[],0);
        
        // dd($content);
        
        // $url = 'https://help.aliyun.com/document_detail/25656.html?spm=5176.doc25506.2.1.1ZxBYf';
        // $client = new \GuzzleHttp\Client();
        // $response = $client->request('GET', $url, [
        // 'stream' => true,
        // 'timeout' => 10
        // ]);
        // $data = $response->getBody();
        
        // file_put_contents(public_path('asd'), $data);
        
        // dd("0.00" > "0");
        // $data = [
        // 'b21CZTVzMzRwazE4dk42V2ZOaVFTeXdzdGNwQQ==',
        // 'b21CZTVzM3AxalZVb18xYzJoU1lTN0lrRjNhUQ==',
        // 'b21CZTVzODhpOElHVjhDaVlKclVOZ1ptUkhzUQ=='
        // ];
        
        // // object_name($name);
        // foreach ($data as $v){
        // echo $v . "\t" .base64_decode($v) .PHP_EOL;
        // }
        // exit;
        // dd(base64_decode('b21CZTVzODhpOElHVjhDaVlKclVOZ1ptUkhzUQ=='));
        
        // // createInsertSql($tbname, $data)
        
        // $pwd = '123456';
        // // $pwd = null;
        // dump(md5($pwd));
        // dd(md5(md5($pwd)));
        // if(1){
        // $a = 0;
        // }
        
        // dd($a);
        
        // $file = 'D:\desktop\header-logo.png';
        // $dest = 'D:\desktop\header-logo1.png';
        // // open an image file
        // $img = \Image::make($file);
        // // now you are able to resize the instance
        // // $img->resize($targetWidth, $newHeight);
        // // and insert a watermark for example
        // // $img->insert('public/watermark.png');
        
        // $img->crop(460, 410,0,0);
        
        // // finally we save the image as a new file
        // $img->save($dest);
        
        // exit;
        
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
                'desc' => '2017.02.15 缴纳水费',
                'amount' => 49.42
            ],
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.02.15 缴纳燃气费',
                'amount' => 146.30
            ],
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.02.15 缴纳电费',
                'amount' => 63.48
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.03.01 缴纳电费',
                'amount' => 52.72
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.03.18 缴纳水费',
                'amount' => 46.40
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.03.18 缴纳燃气费',
                'amount' => 49.60
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.04.02 缴纳电费',
                'amount' => 47.88
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_SHOULD_PAY_ALL,
                'desc' => '计算补正',
                'amount' => 0.02
            ]
        ];
       // - [2017-07-20 14:26:10]
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
                'desc' => '2017.05.07 缴纳电费',
                'amount' => 48.96
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.05.26 缴纳水费',
                'amount' => 60.9
            ],
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.06.09 缴纳网费（46.2*3）',
                'amount' => 138.6
            ],
            [
                'name' => '李蒙',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.06.16 缴纳电费',
                'amount' => 61.87
            ],
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.06.18 缴纳天然气',
                'amount' => 298.50
            ],
            [
                'name' => '顾云翔',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.07.05 缴纳电费',
                'amount' => 100.78
            ],
            [
                'name' => '金燕林',
                'type' => Bill::TYPE_PAYED,
                'desc' => '2017.07.20 缴纳水费',
                'amount' => 63.80
            ],
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
        // 2017-01-19
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
            // [
            // 'name' => '',
            // 'type' => Bill::TYPE_SHOULD_PAY_ALL,
            // 'desc' => '服务费',
            // 'amount' => 636
            // ],
            // [
            // 'name' => '李蒙',
            // 'type' => Bill::TYPE_PAYED,
            // 'desc' => '电费',
            // 'amount' => 89.31
            // ],
            // [
            // 'name' => '金燕林',
            // 'type' => Bill::TYPE_PAYED,
            // 'desc' => '2016年9月水费',
            // 'amount' => 64.03
            // ],
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




