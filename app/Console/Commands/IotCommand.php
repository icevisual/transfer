<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Iot\Request\V20160530 as Iot;

class IotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    
    /**
     * 
     * @var \DefaultAcsClien
     */
    protected $client = null;
    
    
    public function initClient(){
        $accessKeyId = "nL5Y7fL9P7RXUZ5J";
        $accessSecret = "saBQK7zYCkWXBi7vV7YCI8Fl7kc5i2";
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $accessKeyId, $accessSecret);
        $this->client = new \DefaultAcsClient($iClientProfile);
    }
    
    
    public function testSub(){

        $request = new Iot\SubRequest();
        $request->setProductKey(23344127);
        $request->setSubCallback("http://api.xb.guozhongbao.com/mock/consumer");//当topic有消息时候，接受消息的地址，参考服务器回调
        $request->setTopicList("/23344127/#");//订阅的topic列表
        $response = $this->client->getAcsResponse($request);
        print_r("\r\n");
        print_r($response);
        
    }
    
    
    public function testPub(){
    
        $request = new Iot\PubRequest();
        $request->setProductKey(23344127);
        $request->setMessageContent("aGVsbG93b3JsZA==");// Hello world base64 String.
        $request->setTopicFullName("/23344127/home/admin/adfadsfa/dsafsfa");//消息发送给哪个topic中.
        $response = $this->client->getAcsResponse($request);
        
        print_r("\r\n");
        print_r($response);
        
        
    }
    
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // sign = 061397c0ec57664173a81c0345dbbba9
        $str = '{\"message\":\"aGVsbG93b3JsZA==\",\"topic\":\"/23344127/#\",\"sign\":\"061397c0ec57664173a81c0345dbbba9\",\"messageId\":4,\"appKey\":\"23344127\"}"}';
        dump(stripslashes($str));
        
        $this->comment(md5('23344127e3RlbToyMH0=1be73f483da7bcd875e0496d57603a99'));
        
        
        
        exit;
        $this->initClient();
        
        $this->testPub();
        
        
        
        // 23344127 aGVsbG93b3JsZA== 1be73f483da7bcd875e0496d57603a99
        //sign= md5_32(productKey+(message)+productSecret)
//         $this->comment(PHP_EOL.Inspiring::quote().PHP_EOL);
    }
}
