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
    protected $signature = 'iot {action=test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'internet of thing';
    
    /**
     * 
     * @var \DefaultAcsClient
     */
    protected $client = null;
    
    
    protected $productID = '1000067525';
    
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $action = $this->argument('action');
        $funcName = strtolower($action).'Action';
        if(method_exists($this, $funcName)){
            call_user_func([$this,$funcName]);
        }else{
            $this->error(PHP_EOL. 'No Action Found');
        }
    }
    
    public function initClient(){
        if(null == $this->client){
            $accessKeyId = "Pv76EaJOq60VzLJc";
            $accessSecret = "758pf5jAOt6Mb1uLlXI0mmrwyxfzmB";
            $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $accessKeyId, $accessSecret);
            $this->client = new \DefaultAcsClient($iClientProfile);
        }
    }
    
    
    public function subAction(){
        $this->initClient();
        
        $request = new Iot\SubRequest();
        $request->setProductKey($this->productID);
        $request->setSubCallback("http://test.open.qiweiwangguo.com/iot/consumer");//当topic有消息时候，接受消息的地址，参考服务器回调
        $request->setTopicList("/{$this->productID}/#");//订阅的topic列表
        $response = $this->client->getAcsResponse($request);
        print_r("\r\n");
        print_r($response);
    }
    

    public function registAction(){
        $this->initClient();
    
        $request = new Iot\RegistDeviceRequest();
    
        $request->setProductKey($this->productID);
        $request->setDeviceName('device0002');// Hello world base64 String.
        $response = $this->client->getAcsResponse($request);
    
        print_r("\r\n");
        print_r($response);
    }
    
    public function pubAction(){
        $this->initClient();
        
        $msg = 'When there is no desire - Laozi';
        $request = new Iot\PubRequest();
        
        $request->setProductKey($this->productID);
        $request->setMessageContent(base64_encode($msg));// Hello world base64 String.
        $request->setTopicFullName("/{$this->productID}/a");//消息发送给哪个topic中.
        $response = $this->client->getAcsResponse($request);
        
        print_r("\r\n");
        print_r($response);
    }
    
    
    public function testAction(){

        // sign = 061397c0ec57664173a81c0345dbbba9
        $str = '{\"message\":\"aGVsbG93b3JsZA==\",\"topic\":\"/23344127/#\",\"sign\":\"061397c0ec57664173a81c0345dbbba9\",\"messageId\":4,\"appKey\":\"23344127\"}"}';
        dump(stripslashes($str));
        
        $this->comment(md5('23344127e3RlbToyMH0=1be73f483da7bcd875e0496d57603a99'));
    }
    
    public function fireAction(){
    
        $this->comment(__FUNCTION__);
    }
    
}
