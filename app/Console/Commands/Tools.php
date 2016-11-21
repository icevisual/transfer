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
        $this->comment(PHP_EOL . '> DONE !');
    }

    /**
     * OpenSDK 开发版 JS 在public文件夹内，会被外网访问到，要保证安全，又要保证更改性、可测试性
     *  只改public
     *  release时 移入 A
     *  public + gitignore
     *  A 移出public，
     *      A 同步public
     *  sha1
     */
    
    /**
     * 将 public/release 文件夹下JS 文件 覆盖到 app/OpenSDK/JavaScript 下
     */
    public function jsSDKStorageAction(){
        $source = public_path($this->getReleaseConfig('SDK-develop-dir')).DIRECTORY_SEPARATOR.'*';
        $dest = app_path($this->getReleaseConfig('SDK-storage-dir'));
        $this->cp($source, $dest);
    }
    
    /**
     * 将 app/OpenSDK/JavaScript 文件夹下文件 覆盖到 public/release 下
     */
    public function jsSDKDevelopAction(){
        // TODO SDK 版本号
        $source = app_path($this->getReleaseConfig('SDK-storage-dir')).DIRECTORY_SEPARATOR.'*';
        $dest =  public_path($this->getReleaseConfig('SDK-develop-dir'));
        if(!is_dir($dest)){
            mkdir($dest);
        }
        $this->cp($source, $dest);
    }
    
    public function cp($source,$dest){
        $command = "cp -fr {$source} $dest";
        $this->execLShellCMD($command);
    }
    
    public function rm($source){
        $command = "rm -fr {$source}";
        $this->execLShellCMD($command);
    }
    
    /**
     * Exec Liunx Shell Style CMD & replace D:/wnmp to /D/wnmp
     * @param unknown $command
     */
    public function execLShellCMD($command){
        $command = preg_replace(['/\\\\/','/(\w)\:/'], ['/','/\\1'], $command);
        $this->info("EXEC $command");
        system($command);
    }
    
    
    /**
     * release 相关配置   相对 public的位置
     * @var unknown
     */
    protected $_release_config = [
        'proto-struct' => 'release/lib/Proto/Simple.proto.js',// protocol buffer 协议文件
        'proto-struct-load' => 'release/lib/SDK/ProtocolStruct.js',// 载入 协议文件的JS位置
        'enum-list-md' => 'release/documentation/Enum-list.md',// gitbook 中 枚举列表 文件
        'r-js' => 'release/lib/r.js', // r.js
        'r-build-js' => 'release/build/build-compress-SDK.js',// r.js build 配置文件 、 输出文件、
        'r-build-js-res' => 'release/build/OpenSDK.min.js',
        'release-js' => 'release/dist/OpenSDK-v1.0.0.min.js',//release js 文件 build 出的文件包含 licence 信息 ，去除licence信息的原始文件
        'license' => 'release/License',// licence 文件
        'zip' => [
            'source' => 'release',
            'dest' => 'release/documentation/resource/OpenSDK-v1.0.0.zip',
            'only' => [
                'dist',
                'example'
            ],
        ],
        'SDK-doc' => 'release/documentation',// 生成 SDK 文档位置
        'SDK-doc-dest' => 'documentation', // 移动 SDK 文档 位置
        'SDK-storage-dir' => 'OpenSDK/JavaScript/release',// 存放 SDK release 文件的目录，app_path
        'SDK-develop-dir' => 'release', // 开发 SDK 目录 public_path
    ];
    
    public function setReleaseConfig($config){
        $this->_release_config = $config;
    }
    
    public function getReleaseConfig($key){
        return array_get($this->_release_config, $key);
        if(isset($this->_release_config[$key])){
            return $this->_release_config[$key];
        }
        throw new \Exception('Invalid key '.$key);
    }
    
    public function sha1Compare($file1,$file2){
        return sha1_file($file1) == sha1_file($file2);
    }
    
    public function releaseFileCompare($file){
        $DS = DIRECTORY_SEPARATOR;
        $compareBasePath = app_path("OpenSDK{$DS}JavaScript{$DS}");
        $source = $compareBasePath . $file;
        $dest = public_path($file);
        return  $this->sha1Compare($source,$dest);
    }
    
    public function releaseDirCompare($dir){
        $scanRet = reverseScandir(public_path($dir));
        foreach ($scanRet as $v){
            if(!$this->releaseFileCompare($dir.DIRECTORY_SEPARATOR.$v)){
                return false;
            }
        }
        return true;
    }
    
    public function releaseDirOnlyCompare($dir,$only){
        $scanRet = reverseScandir(public_path($dir),'',[
            'only' => $only
        ]);
        foreach ($scanRet as $v){
            if(!$this->releaseFileCompare($dir.DIRECTORY_SEPARATOR.$v)){
                return false;
            }
        }
        return true;
    }
    
    public function releaseAction(){
        // 检测 proto文件变动    
        // 单文件
        // 文件比对 app/OpenSDK/JavaScript/release 和 public/release
        
        $changed = false;
        
        
        if(!$this->releaseFileCompare($this->getReleaseConfig('proto-struct'))){
            $changed = true;
            
            // 压缩 protobuf 协议文件到 JS 文件中
            $this->info('压缩 protobuf 协议文件到 JS 文件中 ...');
            $this->generCompressAction();
            
            // 根据 protobuf 协议文件生成枚举列表
            $this->info('根据 protobuf 协议文件生成枚举列表 ...');
            $this->generEnumTableAction();
            
        }else {
            $this->info('protobuf 协议文件未更改');
        }

        // 检测 SDK 文件变动
        // 文件夹，递归检测，一个文件有不同，就需要重新生成
        
        if(!$this->releaseDirCompare('release/lib/SDK')){
            $changed = true;
            // 压缩合并 SDK JS 文件构建SDK.min.js
            $this->info('压缩合并 SDK JS 文件构建SDK.min.js...');
            $this->buildAction();
            
            // 去除引用的库的第三方 license 注释
            $this->info('去除引用的库的第三方 license 注释...');
            $this->stripJSLicenseAction();
            
            // SDK.min.js 加入 license 注释
            $this->info('SDK.min.js 加入 license 注释...');
            $this->mixLicenseAction();
        }else {
            $this->info('JS SDK 文件未更改');
        }
        
        // 检测 release 文件夹相关文件的变动
        // 多文件夹
        
        $zipConfig = $this->getReleaseConfig('zip');
        
        
        if(!$this->releaseDirOnlyCompare($zipConfig['source'], $zipConfig['only'])){
            $changed = true;
            // zip 压缩 release 文件夹
            $this->info('zip压缩release.zip...');
            $this->zipAction();
        }else {
            $this->info('release.zip 文件未更改');
        }
        
        // 检测 md 文件的变动
        // 多文件
        if(!$this->releaseDirCompare($this->getReleaseConfig('SDK-doc'))){
            $changed = true;
            // gitbook build 生成  _book 文件，并移动到 documentation 文件夹下
            $this->info('生成 gitbook 文件...');
            $this->gitbookAction();
        }else {
            $this->info('gitbook 文件未更改');
        }
        
        // 上述任意文件有变动都需要执行
        if($changed){
            // 存储 release 文件 ，为方便测试，原 release 文件夹在 public 下，发布时需移除
            $this->info('存储 release 文件 ...');
            $this->jsSDKStorageAction();
        }else {
            $this->info('No thing changed !');
        }
        
    }
    
    /**
     * 用r.js 打包SDK中的JS
     */
    public function buildAction(){
        // TODO 相对位置 和绝对位置
        system('node '.public_path($this->getReleaseConfig('r-js')).' -o '.public_path($this->getReleaseConfig('r-build-js')));
        // cp rm 等linux指令 不识别 D:/...等绝对位置 ,只识别 /d/wnmp...等绝对位置
        $this->cp(public_path($this->getReleaseConfig('r-build-js-res')), public_path($this->getReleaseConfig('release-js')));
//         system('cp public/OpenSDK.min.js '.public_path($this->getReleaseConfig('release-js')));
    }
    
    /**
     * 去除/ * * /注释 （License）
     */
    public function stripJSLicenseAction(){
        $this->stripBlockComments(public_path($this->getReleaseConfig('release-js')));
    }

    /**
     * 去除  / * ... * /注释
     **/
    public function stripBlockComments($source){
        $filepath = $source;
        $distpath = $source.'.__tmp';
        
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
        
        $this->execLShellCMD("rm $filepath");
        $this->execLShellCMD("cp $distpath  $filepath");
        // TODO ADD DATE
    }
    
    public function mixLicenseAction(){
        $licensePath = public_path($this->getReleaseConfig('license'));
        $sourcePath =  public_path($this->getReleaseConfig('release-js'));
        $tmpSuffix = '.__tmp';
        
        $command = "cat $licensePath $sourcePath > {$sourcePath}{$tmpSuffix}";
        $this->info("EXEC $command");
        system("cat $licensePath $sourcePath > {$sourcePath}{$tmpSuffix}");
        $this->execLShellCMD("rm $sourcePath");
        $this->execLShellCMD("mv {$sourcePath}{$tmpSuffix} {$sourcePath}");
    }
    
    /**
     * gitbook build & remove gitbook links 
     */
    public function gitbookAction()
    {
        $sdk_doc = $this->getReleaseConfig('SDK-doc');
        $sdk_doc_full = public_path($sdk_doc);
        system('gitbook build '.$sdk_doc_full);
        $this->info('整理  gitbook 文件...');
        $DS = DIRECTORY_SEPARATOR;
        // 删除
        $delArray = [
            '.gitignore',
            'c1s1.md',
            'Book.json',
            'Book.json.exp',
        ];
        $delArray = array_flip($delArray);
        
        $basePath = $sdk_doc_full.$DS.'_book'.$DS;
        $Scan =  reverseScandir($basePath);
        foreach ($Scan as $v){
            if(isset($delArray[$v])){
                unlink($basePath.$v);
            }else{
                if( strlen($v) > '5' && '.html' == substr($v, -5)){
                    $this->info("Processing  $v");
                    $this->clearHtml($basePath.$v);
                }
            }
        }
        $this->info('移动  gitbook 生成文件 ...');
        $s = public_path($this->getReleaseConfig('SDK-doc').DIRECTORY_SEPARATOR.'_book/*');
        $d = public_path($this->getReleaseConfig('SDK-doc-dest'));
        $this->cp($s, $d);
        $this->rm(substr($s, 0,-1));
    }
    
    /**
     * remove gitbook links 
     * @param unknown $filepath
     */
    public function clearHtml($filepath){
//         <a href="https://www.gitbook.com" target="blank" class="gitbook-link">
//         Published with GitBook
//         </a>
        $content = file_get_contents($filepath);
        $regex = "|<a href=\"https://www.gitbook.com\"[^<]+</a>|";
        $content = preg_replace($regex, '', $content);
//         <a href="./">Introduction</a>
//         <a href="../">Introduction</a>
//         <a href="." >Introduction</a>
        $regex = "|(<a href=\"[\.]{1,2}[\/]?\"[ ]?>)([\s]*Introduction[\s]*)(</a>)|";
        $content = preg_replace($regex, '\\1文档介绍\\3', $content);
        file_put_contents($filepath, $content);
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
	var Simple = simpleRoot.com.scentrealm;
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
        $source = public_path($this->getReleaseConfig('proto-struct'));
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
        file_put_contents(public_path($this->getReleaseConfig('enum-list-md')), $tppp);
    }
    
    /**
     * zip release folder
     */
    public function zipAction(){
        $config = $this->getReleaseConfig('zip');
        
        $zipFileName = public_path($config['dest']);
        $dir = public_path($this->getReleaseConfig('zip.source'));
        $scan = reverseScandir($dir,'',['only' => $config['only']]);
        if(file_exists($zipFileName)){
            unlink($zipFileName);
        }
        
        /**
         * \ZipArchive 压缩的文件在MAC OS 下无法正常打开，\Chumper\Zipper\Zipper 可以,linux 未测
         */
        $zipper = new \Chumper\Zipper\Zipper;
        $zipper->make($zipFileName);
        $arr = [];
        foreach ($scan as $v){
            $pathinfo = pathinfo($v);
            $file = $dir.DIRECTORY_SEPARATOR.$v;
            if(isset($pathinfo['dirname']) && '.' != $pathinfo['dirname']){
                $zipper->folder($pathinfo['dirname'])->add($file);
            }else{
                $zipper->add($file);
            }
        }
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



