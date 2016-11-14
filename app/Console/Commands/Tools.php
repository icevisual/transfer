<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Extensions\Mqtt\MqttUtil;

class Tools extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tl {action=stripAnn} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Common Tools';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $action = $this->argument('action');
        $funcName = strtolower($action) . 'Action';
        if (method_exists($this, $funcName)) {
            call_user_func([
                $this,
                $funcName
            ]);
        } else {
            $this->error(PHP_EOL . 'No Action Found');
        }
        $this->comment(PHP_EOL . '--DONE--');
    }

    protected $_release_config = [];
    
    public function setReleaseConfig($config){
        $this->_release_config = $config;
    }
    
    public function getReleaseConfig($key){
        if(isset($this->_release_config[$key])){
            return $this->_release_config[$key];
        }
        throw new \Exception('Invalid key '.$key);
    }

    public function releaseAction(){
        // 相对 public的位置
        $config = [
            'proto-struct' => 'OpenSDK/Proto/Simple.proto.js',// protocol buffer 协议文件
            'proto-struct-load' => 'OpenSDK/SDK/ProtocolStruct.js',// 载入 协议文件的JS位置
            'enum-list-md' => 'SDKDoc/Enum-list.md',// gitbook 中 枚举列表 文件
            'r-js' => 'js/r.js', // r.js
            'r-build-js' => 'build-compress-SDK.js',// r.js build 配置文件 、 输出文件、
            'release-js' => 'release/dist/OpenSDK-v1.0.0.min.js',//release js 文件 build 出的文件包含 licence 信息 ，去除licence信息的原始文件
            'license' => 'OpenSDK/License',// licence 文件
            'zip' => 'SDKDoc/resource/OpenSDK-v1.0.0.zip',// zip 文件 信息，考虑新的zip方案
            'release-zip-dir' => 'release',
        ];
        $this->setReleaseConfig($config);
        // TODO Path Config 
        $this->info('压缩 protobuf 协议文件 ...');
        // 压缩 protobuf 协议文件
        $this->generCompressAction();
        $this->info('生成枚举列表 ...');
        // 生成枚举列表
        $this->generEnumTableAction();
        $this->info('构建  SDK.min.js...');
        // 构建 min.js
        $this->buildAction();
        $this->info('去除第三方license...');
        // 去除第三方 license
        $this->stripJSLicenseAction();
        $this->info('zip压缩release.zip...');
        // zip压缩
        $this->zipAction();
//         $this->info('开启 gitbook服务...');
//         // 开启 gitbook服务
//         $this->gitbookAction();
    }
    
    public function buildAction(){
        system('node '.public_path($this->getReleaseConfig('r-js')).' -o '.public_path($this->getReleaseConfig('r-build-js')));
        system('cp public/OpenSDK.min.js '.public_path($this->getReleaseConfig('release-js')));
    }
    
    public function stripJSLicenseAction(){
        $this->compressComment($this->getReleaseConfig('release-js'));
    }
    
    /**
     * 去除  / * ... * /注释
     **/
    public function compressComment($publicfilepath){
        $filepath = public_path($publicfilepath);
        $tmppath = $publicfilepath.'.tmp';
        $distpath = public_path($tmppath);
        $distFp = fopen($distpath,'w+');
        $contentArray = file($filepath);
        $isAnn = false;
        foreach($contentArray as $k => $line){
            $writeStr = '';
            if($isAnn){
                // Find Ann End Tag
                if(false !== ($annEdPos = strpos($line, '*/') )){
                    // Get Ann End Tag
                    $writeStr = substr($line, $annEdPos + 2);
                    $isAnn = false;
                }
            }else{
                // Find Ann Start Tag
                if(false !== ($annStPos = strpos($line, '/*') ) ){
                    // Get Ann Start Tag
                    $isAnn = true;
                    if(false !== ($annEdPos = strpos($line, '*/',$annStPos + 2) )){
                        $writeStr = substr($line, 0,$annStPos) . substr($line, $annEdPos + 2);
                        $isAnn = false;
                    }else{
                        $writeStr = substr($line, 0,$annStPos);
                    }
                }else{
                    $writeStr = $line;
                }
            }
            if(trim($writeStr) != ''){
                // TODO \r\n  \n
                fwrite($distFp, $writeStr);
            }
        }
        fclose($distFp);
        system('rm '.$filepath);
//         cat  ./License ./OpenSDK-v1.0.0.min.js > OpenSDK-v1.0.0.min.js
        $this->info('加入License...');
        $licensePath = public_path($this->getReleaseConfig('license'));
        system("cat $licensePath $distpath > $filepath");
//         system("cp $distpath $filepath");
        system('rm '.$distpath);
        // TODO  ADD License 
        // ADD DATE
    }
    
    
    public function gitbookAction()
    {
        // nohup
        system('gitbook serve '.public_path('SDKDoc'));
    }

    /**
     * Generate Compressed Protobuf struct file
     *
     * CMD : artisan tl generCompress
     */
    public function generCompressAction()
    {
        $source = public_path($this->getReleaseConfig('proto-struct'));
        $dist = public_path($this->getReleaseConfig('proto-struct-load'));
        $compressedContent = $this->stripAnn($source);
        $template = <<<EOL
define(['Utils'],function(Utils) {
	var simpleRoot = Utils.loadProtoString('{$compressedContent}');
	var Simple = simpleRoot.Proto2.Scentrealm.Simple;
	return Simple;
});
EOL;
        file_put_contents($dist, $template);
    }

    /**
     * Generate EnumTable
     * CMD : artisan tl generEnumTable
     */
    public function generEnumTableAction()
    {
        $source = public_path($this->getReleaseConfig('enum-list-md'));
        $contentArray = file($source);
        $count = count($contentArray);
        $retArray = [];
        // Read Data
        for ($i = 0; $i < $count; $i ++) {
            $line = trim($contentArray[$i]);
            // enum
            if (strlen($line) > 4 && $line{0} == 'e' && $line{1} == 'n' && $line{2} == 'u' && $line{3} == 'm') {
                // Get Items
                $j = $i ;
                $eArray = [];
                while($j < $count && $contentArray[$j]{0} != '}'){
                    $ll = trim($contentArray[$j]);
                    if(strlen($ll) > 1 && strpos($ll, '=') !== false){
                        $tp = explode('=', $ll);
                        $desc = '';
                        if( ($descPos = strpos($tp[1], '//')) !== false ){
                            $desc = substr($tp[1], $descPos + 2);
                        }
                        $eArray[] = [
                            'code' => trim($tp[0]),
                            'desc' => trim($desc)
                        ];
                    }
                    $j ++;
                }
                // Get enum desc
                $j = $i ;
                $enumDesp = '';
                while($j >= 0 && $contentArray[$j]{0} != '}'){
                    $ll = trim($contentArray[$j]);
                    if(strlen($ll) > 2 && $ll{0} == '/' && $ll{1} == '/'){
                        $enumDesp = trim($ll,'/ ');
                        break;
                    }
                    $j --;
                }
                
                
                $retArray[trim(explode(' ', $line)[1])] = [
                    'desc' => $enumDesp,
                    'items' => $eArray
                ]; 
                
            }
        }
        // Get Data
        
        // generate template 
//         edump($retArray);
        $retString = '';
        
        foreach ($retArray as $enumKey => $enumContent ) {
            if('SrCmdId' == $enumKey) continue;
            $items = $enumContent['items'];
            $tmp =<<<EOG
    <tr>
        <td><b>{$enumKey}</b></td>
        <td><b>{$enumContent['desc']}</b></td>
    </tr>
     
EOG;
            foreach ($items as $k => $v){
                $tmp .= <<<EOG
    <tr>
        <td>{$v['code']}</td>
        <td>{$v['desc']}</td>
    </tr>

EOG;
            }
            $retString .= $tmp;
        }
        
        $tppp =<<<EOF
## 枚举列表

枚举结构都在SDK.protoRoot 下

<table>
    <tr>
        <th align="left">项目</th>
        <th align="left">解释</th>
    </tr>
{$retString}
</table>         
EOF;
        
        file_put_contents(public_path('SDKDoc/Enum-list.md'), $tppp);
//         edump($tmp);
    }
    
    public function zipAction(){
        $zipFileName = public_path($this->getReleaseConfig('zip')); 
        $dir = public_path($this->getReleaseConfig('release-zip-dir'));
        $scan = reverseScandir($dir);
        if(file_exists($zipFileName)){
            unlink($zipFileName);
        }
        $zip = new \ZipArchive();
        $zip->open($zipFileName,\ZipArchive::CREATE);
        foreach ($scan as $v){
            $zip->addFile($dir.DIRECTORY_SEPARATOR.$v,$v);
        }
        $zip->close();
    }
    
    

    /**
     * 去除文件注释，当前去除 // 注释
     */
    public function stripAnn($filepath)
    {
        // $file = public_path('OpenSDK/Proto/Simple.proto.js');
        // $dist = public_path('OpenSDK/Proto/Simple.proto.min.js');
        // $dist1 = public_path('OpenSDK/Proto/Simple.proto.min.dt');
        $contentArray = file($filepath);
        // dump($contentArray);
        $content = '';
        foreach ($contentArray as $line) {
            $str = trim($line);
            if (false !== ($pos = strpos($line, '//'))) {
                $str = substr($line, 0, $pos);
            }
            $content .= trim($str);
        }
        return $content;
        
        $encode = base64_encode($content);
        
        $gzStr = gzdeflate($content, 9);
        $result = base64_encode($gzStr);
        
        dump($encode);
        dump($result);
        dump(strlen($content));
        dump(strlen($encode));
        dump(strlen($result));
        dump(strlen($gzStr));
        file_put_contents($dist, $content);
        file_put_contents($dist1, $result);
    }
}



