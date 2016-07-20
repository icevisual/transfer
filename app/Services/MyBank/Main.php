<?php
namespace App\Services\MyBank;

class Main
{
    public function __construct(){
        
    }
    
    public static function main()
    {
        mt_mark('start');
        // 设置javabridge监听端口，如果开启javabridge.jar设置的端口不是8080，可通过此语句更改
        define("JAVA_HOSTS", "127.0.0.1:8080"); 
        
        $mybankDir = __DIR__;
        
        defined('DS') or define('DS',DIRECTORY_SEPARATOR);
        
        require_once ($mybankDir.DS.'PHPJavaBridge'.DS.'Java.inc'); // 
        // 前面是配置环境，下面开始真正的调用：
        $system = new \JavaClass("java.lang.System"); 
        // 初始化JAVA下的类，主要操作就是创建Java类的实例，
        // Java类的第一个参数是JAVA开发的类的名字包含包路径，
        // 路径表示按JAVA里导入包的格式。如果JAVA下的类需要使用构造函数，
        // 可以在使用第二个参数。
        $uuid = new \JavaClass("java.util.UUID");
        $timestamp = new \Java('java.sql.Timestamp',$system->currentTimeMillis());
        
        dump(''.$uuid->randomUUID());
        dump(''.$timestamp->toString());
        
        $configFile = $mybankDir.'/conf/TopESA.SignAndVerify.onlyCACert.conf.json';
        
        $json = file_get_contents($configFile);
        dump($json);
        $TCA = new \JavaClass("cn.topca.api.cert.TCA"); 
        $TCA->config($json);
        $CertStore = new \JavaClass("cn.topca.api.cert.CertStore"); 
        $certSet = $CertStore->listAllCerts();
        $certificate = $certSet->get(0);
        
        $string = new \Java("java.lang.String","cardNo=8888888453427229||currencyCode=156||cashExCode=CSH"); 
        
        $signedData = $certificate->signP7($string->getBytes(), false);
        $base64 = new \JavaClass("org.apache.commons.codec.binary.Base64");
        $result = $base64->encodeBase64String($signedData);
        dump(''.$result);
        dmt_mark('start','end');
        
        
        
    }
    
    
}
