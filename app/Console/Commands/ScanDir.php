<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Models\Common\Bill;
use PHPHtmlParser\Dom;
use App\Extensions\Mqtt\MqttUtil;

class ScanDir extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scandir';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'scandir';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $url = 'https://opensource.apple.com/source/CommonCrypto/CommonCrypto-60092.1.2/lib/';
        $url = 'https://opensource.apple.com/source/CommonCrypto/CommonCrypto-60092.1.2/include/';

        $urls = [
            'https://opensource.apple.com/source/CommonCrypto/CommonCrypto-60092.1.2/lib/',
            'https://opensource.apple.com/source/CommonCrypto/CommonCrypto-60092.1.2/include/'
        ];
        
        $array = [];
        
        foreach ($urls as $url){
            $files = $this->getfiles($url);
            
            foreach ($files as $file){
                $array[] = $url.$file;
            }
        }
        
        
        file_put_contents('dld', implode(PHP_EOL, $array));
        
        dump($array);
        
        
//         dump($table->innerhtml);
        $this->comment('--END--');
    }
    
    
    public function getfiles($url){
        $storefile = public_path(sha1($url));
        if(!file_exists($storefile)){
            $str = $this->curl_get($url,[],false);
            file_put_contents($storefile, $str);
        }
        $Dom = new Dom();
        $Dom->loadFromFile($storefile);
        $table = $Dom->find("#content")->find('table')[0];
        preg_match_all('/[\w]*\.[ch]/', $table, $matches);
        return array_keys(array_flip($matches[0]));
    }
    
    
    
    /**
     *
     * @param unknown $url
     * @param array $data
     * @param string $json
     * @param array $config
     */
    function curl_get($url, array $data = [], $json = true, array $config = [])
    {
        // $api = 'http://v.showji.com/Locating/showji.com20150416273007.aspx?output=json&m='.$phone;
        $ch = curl_init();
        if (! empty($data)) {
            $url = $url . '?' . http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 设置超时
        // curl_setopt($ch, CURLOPT_USERAGENT, $User_Agen); //用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 跟踪301
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 返回结果
        $config && curl_setopt_array($ch, $config);
        $result = curl_exec($ch);
        if(false === $result){
            throw new \Exception(curl_error($ch),curl_errno($ch));
        }
        curl_close($ch);
        if($json && false !== ($ret = is_json($result))){
            return $ret;
        }
        return $result;
    }
    
    

    public function curl($url, $data = '', $opts = [])
    {
        
//         $this->info('[ '.__FUNCTION__.' ]');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // url
                                             // curl_setopt($ch, CURLOPT_PROXY, "122.0.74.166:3389");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
//         curl_setopt($ch, CURLOPT_USERAGENT, $User_Agen);
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




