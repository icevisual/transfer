<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class AliyunSms extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms {phone=18767135775}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'aliyun sms';

    public function handle()
    {
        $client = new \TopClient();
        $client->appkey = '23336039';
        $client->secretKey = 'b7c3d3ec7ad79a0252ba59df635e83ed';
        $phone = $this->argument('phone');
        
        $p0 = [
            'phone' => '18767135775',
            'code' => '123456'
        ];
        $p1 = [
            'product' => '大风',
            'tpl' => 'SMS_2520565'
        ];
        $p = $p0 + $p1;
        $req = new \AlibabaAliqinFcSmsNumSendRequest();
        $req->setSmsType('normal');
        $req->setSmsTemplateCode($p['tpl']);
        $req->setSmsFreeSignName('注册验证'); // 【注册验证】
        $req->setSmsParam("{\"code\":\"{$p['code']}\",\"product\":\"{$p['product']}\"}");
        $req->setRecNum($p['phone']);
        $response = $client->execute($req);
        dump($response);
        // Error
        if (isset($response->code)) {
            $msg = 'unknown error';
            $code = 6003;
            
            if (isset($response->sub_code)) {
                if ($response->sub_code == 'isv.MOBILE_NUMBER_ILLEGAL') {
                    $msg = '手机号码格式错误';
                    $code = 6000;
                } else 
                    if ($response->sub_code == 'isv.BUSINESS_LIMIT_CONTROL') {
                        /**
                         * 短信验证码，使用同一个签名，对同一个手机号码发送短信验证码，
                         * 允许每分钟1条，累计每小时7条。
                         *
                         * 短信通知，使用同一签名、同一模板，对同一手机号发送短信通知，
                         * 允许每天50条（自然日）。
                         */
                        $msg = '短信发送过于频繁';
                        $code = 6002;
                    }
            }
            
            throw new \Exception($msg, $code);
        }
    }
}