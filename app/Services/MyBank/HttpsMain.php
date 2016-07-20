<?php
namespace App\Services\MyBank;

class HttpsMain
{

    /**
     * 是否开启验签
     */
    public static  $isSign = true;

    /**
     * 身份证
     */
    public static  $certNo = "430830198907201280";

    /**
     * 银行卡号
     */
    public static  $cardNo = "8888888453427229";

    /**
     * 渠道
     */
    public static  $channel = "MYBANK";

    /**
     * 短信验证码
     */
    public static  $smsCode = "888888";

    /**
     * 币种
     */
    public static  $currencyCode = "156";

    /**
     * 联调环境url
     */
    // public static  String reqUrl = "https://fcsupergw.dev.dl.alipaydev.com/open/api/common/request.htm";
    // sit环境url
    public static  $reqUrl = "https://fcsupergw.dl.alipaydev.com/open/api/common/request.htm";

    /**
     */
    public static function main()
    {
        

        // define("JAVA_DEBUG", true); //调试设置
        define("JAVA_HOSTS", "127.0.0.1:8080"); // 设置javabridge监听端口，如果开启javabridge.jar设置的端口不是8080，可通过此语句更改
        
        require_once (public_path('Java.inc')); // p
        $here = realpath(dirname($_SERVER["SCRIPT_FILENAME"]));
        
        
        //         \java_set_library_path($here . PATH_SEPARATOR . '.'); // 设置java开发包（class或jar文件）路径，多个路径就用PATH_SEPARATOR分隔，保证跨平的支持。
        //         \java_set_file_encoding("UTF-8"); // 设置JAVA编码。没试过其它的编码，也没深入研究如何能用其它的编码。
         
        // 前面是配置环境，下面开始真正的调用：
        //         System.currentTimeMillis();
        $system = new \Java("java.lang.System"); // 初始化JAVA下的类，主要操作就是创建Java类的实例，Java类的第一个参数是JAVA开发的类的名字包含包路径，路径表示按JAVA里导入包的格式。如果JAVA下的类需要使用构造函数，可以在使用第二个参数。
        //UUID.randomUUID()
        $uuid = new \Java("java.util.UUID");
        $timestamp = new \Java('java.sql.Timestamp',$system->currentTimeMillis());
        //         new TimestampC
        //props.getProperty("java.home"));
        dump(''.$uuid->randomUUID());
        dump(''.$timestamp->toString());
        print "Java version=" . $system->getProperty("java.home") . " /n";
        print "Java vendor=" . $system->getProperty("java.vendor") . " /n/n";
        print "OS=" . $system->getProperty("os.name") . " " . $system->getProperty("os.version") ;
        
    }

    public static function httpsReq($reqUrl, $param)
    {}
}
