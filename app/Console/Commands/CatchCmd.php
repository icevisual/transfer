<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Models\Common\Bill;
use PHPHtmlParser\Dom;
use App\Extensions\Mqtt\MqttUtil;

class CatchCmd extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cc {start=1} {end=501}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    
    public function error($string,$v=  null){
        $this->line( MqttUtil::colorString($string,MqttUtil::COLOR_RED) );
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//         $this->initProxyList();
//         while ($this->selectProxy());
//         $this->reloadProxy();
        $this->runLoop();
        
        exit;
        $html = $this->curl('https://www.google.com', '', [
//             CURLOPT_COOKIEFILE => $cookie_file,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ]);
        
        dump($html);
        exit;
        
        // $this->curl_login();
        $content = file_get_contents(tmp_path('html'));
//         $content = file_get_contents(tmp_path('cookie.txt'));
        $ret = $this->getPageInformation($content,222);
        dump($ret);
        
        $sql = createInsertSql('chinese_col', $ret);
        echo($sql);
        $this->comment(PHP_EOL . Inspiring::quote() . PHP_EOL);
    }
    
    public function getIgnoreUids(){
        $dt = file_get_contents(tmp_path('catchde.list'));
        $arr = explode("\n", $dt);
        $catchedUids = [];
        foreach ($arr as $v){
            $d = explode(" ", $v);
            $iiid = substr($d[3],0, strpos($d[3], "[") - 1);
            $catchedUids[$iiid] = 1;
        }
        return $catchedUids;
    }
    
    
    public function runLoop(){
        
        // 代理 分组 异常 超时 超时重连
        
        $start = $this->argument('start');
        $end = $this->argument('end');
        
        $cookie_file = tmp_path('cookie.txt');
        
        $filename  = "{$start}-{$end}.sql";
        $inputFile = tmp_path($filename);
        
        $fp = fopen($inputFile, "w");
        // 初始代理
        $proxy = $this->selectProxy();
        $num = $end - $start;
        
        $uidArray = range($start, $end - 1);
        usort($uidArray, function ($a,$b){
            return mt_rand(1,10) > 5 ;
        });
        
        $ignoreUids = $this->getIgnoreUids();
        
        // 先通过代理 ,出问题（超时|过短），更换代理（设置最大更换次数），失败后取消代理，获取信息后再启用代理
        for($i = 0 ; $i < $num ; $i ++){
            
            $uid = $uidArray[$i] ;
            
            if(isset($ignoreUids[$uid])){
                $this->info("[ Ignore Uid {$uid} ]");
                continue;
            }
            
            $url = 'http://bbs.ubnt.com.cn/home.php?mod=space&uid='.$uid.'&do=profile';
            
            if(mt_rand(1,100) > 98){
                // 随机更换代理
                $this->info("[ Hit Random ]");
                $proxy = $this->selectProxy();
            }
            
            try {
                
                $optArray = [
//                     CURLOPT_PROXY => $proxy,
                    CURLOPT_COOKIEFILE => $cookie_file,
                    CURLOPT_CUSTOMREQUEST => 'GET'
                ];
                // 代理源循环一遍，会清空队列，这时返回false，则此次请求不使用代理
                if(false === $proxy){// 代理用尽
                    unset($optArray[CURLOPT_PROXY]);
                }
                $html = $this->curl($url, '',$optArray);
//                 file_put_contents(tmp_path('tmp'), $html);

                // 字符数小于1000的（实际480 - 600左右），是对方服务器返回的404 或 50x页面
                $length = strlen($html);
                if($length < 1000){
                    // 认为失败
                    $proxy = $this->selectProxy();
                    // 直接减小迭代数
                    $i -- ;
                }else{
                    // 分析页面数据
                    $info = $this->getPageInformation($html, $uid);
                    if(false === $info){
                        // 页面格式错误
                        // Uid Not Exists
                        $this->warn("[{$i}][ Uid = {$uid} Not Exists][ Proxy = {$proxy} ]");
                        // 记录不存在的UID
                        \LRedis::HSET('X-uid-not-exists',$uid,'000');
                    }else{
                        // 获取需要数据
                        
                        if(!isset($info['Email'])){
                            // 此为关键信息，如无，则需要重新登录
                            $this->reloadLogin();
                            $i --;
                            continue;
                        }
                        
                        $this->info("[{$i}][ Uid = {$uid}][ Proxy = {$proxy} ]");
                        // 数据格式化为SQL语句
                        $sql = createInsertSql('chinese_col', $info);
                        fwrite($fp, $sql.';'.PHP_EOL);
                        
                        \LRedis::HSET('X-uid-catched',$uid,'000');
                    }
                }
                // 不用代理，重新载入代理数据
                if(false === $proxy){// 代理用尽
                    $this->reloadProxy();
                    $proxy = $this->selectProxy();
                }
            }catch (\Exception $e){
                // Timeout
                $this->error("[{$i}][ Uid = {$uid}][ Proxy = {$proxy} ][".$e->getCode().']['.$e->getMessage().']');
                $proxy = $this->selectProxy();
                $i -- ;
            }
            usleep(mt_rand(100000,400000));
        }
        fclose($fp);
        copy($inputFile, tmp_path('test/'.$filename));
    }
    
    public function initProxyList(){
        $this->info('[ '.__FUNCTION__.' ]');
        $proxyArray = [
            '119.6.136.122:80',
            '171.35.36.93:8118',
            '202.171.253.72:80',
            '106.75.128.89:80',
            '122.96.59.104:80',
            '59.34.2.92:3128',
            '14.29.124.53:80',
            '61.132.241.103:808',
            '1.82.216.135:80',
            '112.112.70.115:80',
        ];
        \LRedis::DEL('X-proxy-list');
        \LRedis::DEL('X-proxy-list-fail');
        foreach ($proxyArray as $v){
            \LRedis::RPUSH('X-proxy-list',$v);
        }
    }
    
    public function reloadProxy(){
        $this->info('[ '.__FUNCTION__.' ]');
        $a = 'X-proxy-list-fail';
        $b = 'X-proxy-list';
        while(\LRedis::LLEN($a) > 0 ){
            \LRedis::RPUSH($b,\LRedis::LPOP($a));
        }
    }
    
    public function selectProxy(){
        $this->info('[ '.__FUNCTION__.' ]');
        $a = 'X-proxy-list-fail';
        $b = 'X-proxy-list';
        if(\LRedis::LLEN($b) > 0 ){
            $value = \LRedis::LPOP($b);
            \LRedis::RPUSH($a,$value);
            return $value;
        }
        return false;
        exit;
        
        $proxyArray = [
            '119.6.136.122:80',
            '171.35.36.93:8118',
            '202.171.253.72:80',
            '106.75.128.89:80',
            '122.96.59.104:80',
            '59.34.2.92:3128',
            '14.29.124.53:80',
            '61.132.241.103:808',
            '1.82.216.135:80',
            '112.112.70.115:80',
        ];
        
        $list = file_get_contents(tmp_path('proxy.list'));
        $list = explode("\n", $list);
        
        $uid = 4 ;
        $url = 'http://bbs.ubnt.com.cn/home.php?mod=space&uid='.$uid.'&do=profile';
        $proxy = '113.86.20.45:9999';
        $cookie_file = tmp_path('cookie.txt');
        $this->info("[ Proxy Selecting ]");
        
        $list = $proxyArray;
        $okArray = [];
        
        foreach ($list as $v){
            $proxy = $v;
//             $proxy = '113.86.20.45:9999';
            try {
                mt_mark('start');
                $html = $this->curl($url, '', [
                    CURLOPT_PROXY => $proxy,
                    CURLOPT_COOKIEFILE => $cookie_file,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_TIMEOUT => 3
                ]);
                $length = strlen($html);
                $mt = mt_mark('start','end');
                mt_mark('[clear]');
                if($length < 1000){
                    // 认为失败
                    $this->warn("[ Proxy = {$proxy} ][ t = {$mt['t']} ][ L = {$length} ]");
                }else{
                    $okArray [] = $proxy;
                    $this->info("[ Proxy = {$proxy} ][ t = {$mt['t']} ][ L = {$length} ]");
                }

            }catch (\Exception $e){
                // Timeout
                $this->error("[ Proxy = {$proxy} ][".$e->getCode().']['.$e->getMessage().']');
            }
            usleep(1000000);
        }
        dump($okArray);
    }

    public function getPageInformation($content,$uid)
    {
        $this->info('[ '.__FUNCTION__.' ]');
        $dom = new Dom();
        $rr = $dom->loadStr($content, []);
        $infoMap = [];
        
        $ret = $dom->find("#ct");
        if(count($ret) == 0){
            return false;
        }
        
        $lis = $ret->find('li');
        
        if(count($lis) == 0){
            return false;
        }
        
        $nickname = $dom->find("#uhd")->find(".h")[0]->find(".mt")[0]->text;
        $nickname = str_replace( "'", "\'", $nickname);
        $infoMap['昵称'] = trim($nickname);
        $infoMap['uid'] = $uid;
        
        foreach ($lis as $v){
            $key = $v->find('em')->text ;
            $key = str_replace( '&nbsp;', '', $key);
            $value = substr($v->innerhtml, strpos($v->innerhtml, '</em>') + 5 );
            $value = strip_tags(trim($value));
            $value = str_replace( "'", "\'", $value);
            if($key == '统计信息'){
                $sss = explode("|", $value);
                foreach ($sss as $vvv){
                    $ssss = explode(" ", trim($vvv));
                    $infoMap[$ssss[0]] = $ssss[1];
                }
            }else{
                $infoMap[$key] = $value;
            }
        }
        return $infoMap;
    }

    public function reloadLogin()
    {
        $this->info('[ '.__FUNCTION__.' ]');
        $cookie_file = tmp_path('cookie.txt');
        $data = '{"user":"samliao","password":"A69v/940"}';
        $url = 'https://api.ubnt.com.cn/login';
        $info = $this->curl($url, '', [
            CURLOPT_COOKIEFILE => $cookie_file,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ]);
        $this->warn("[ reloadLogin ]");
        $this->warn($info);
    }
    
    public function curl_login()
    {
        $cookie_file = tmp_path('cookie.txt');
        $data = '{"user":"icevisual@hotmail.com","password":"10109267"}';
        $data = '{"user":"samliao","password":"A69v/940"}';
        $url = 'https://api.ubnt.com.cn/login';
        
        // curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); // 存储cookies
        // curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); // 存储cookies
        
        // $info = $this->curl($url,$data,[
        // CURLOPT_COOKIEJAR => $cookie_file,
        // ]);
        // dump($info);
        
        $url = 'http://bbs.ubnt.com.cn/home.php?mod=space&uid=222&do=profile';
        $info = $this->curl($url, '', [
            CURLOPT_COOKIEFILE => $cookie_file,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ]);
        
        file_put_contents(tmp_path('html'), $info);
        
        dump($info);
    }

    public function curl($url, $data = '', $opts = [])
    {
        
        $this->info('[ '.__FUNCTION__.' ]');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // url
                                             // curl_setopt($ch, CURLOPT_PROXY, "122.0.74.166:3389");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
        curl_setopt($ch, CURLOPT_USERAGENT, $User_Agen);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        
        curl_setopt_array($ch, $opts);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // 数据
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $info = curl_exec($ch);
        
        if(false === $info){
            throw new \Exception(curl_error($ch),curl_errno($ch));
        }
        curl_close($ch);
        return $info;
    }
}




