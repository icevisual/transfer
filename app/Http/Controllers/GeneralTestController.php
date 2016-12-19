<?php
namespace App\Http\Controllers;

use App\Gather\AESTool;
use App\Models\Bill;

use Iot\Request\V20160530 as Iot;
use Illuminate\Cache\Repository;
use function GuzzleHttp\json_decode;

class GeneralTestController extends BaseController
{

    public function __xmlToArray($xmlStr)
    {
        $xml = simplexml_load_string($xmlStr);
        if ($xml === false) {
            throw new \App\Exceptions\ServiceException('Error Occured When Loading Xml From String');
        }
        $resultArray = [];
        $child = (array) $xml->children();
        $child = $child['content'];
        $i = 0;
        foreach ($xml->children() as $k => $v) {
            // contentuid
            $contentuid = '';
            foreach ($v->attributes() as $k1 => $v2) {
                $contentuid = $v2 . '';
            }
            $resultArray[$contentuid] = $child[$i ++];
        }
        return $resultArray;
    }

    public function mcurl(array $urls, array $data)
    {
        $mh = curl_multi_init();
        $curl_array = array();
        foreach ($urls as $i => $url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            // curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data[$i])); // 数据
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $curl_array[$i] = $ch;
            curl_multi_add_handle($mh, $curl_array[$i]);
        }

        function full_curl_multi_exec($mh, &$still_running)
        {
            do {
                $rv = curl_multi_exec($mh, $still_running);
            } while ($rv == CURLM_CALL_MULTI_PERFORM);
            return $rv;
        }
        $res = [];
        $still_running = null;
        full_curl_multi_exec($mh, $still_running); // start requests
        do { // "wait for completion"-loop
            curl_multi_select($mh); // non-busy (!) wait for state change
            full_curl_multi_exec($mh, $still_running); // get new state
            while ($info = curl_multi_info_read($mh)) {
                $curlInfo = curl_getinfo($info['handle']);
                dump($curlInfo);
                $res[$curlInfo['url']] = curl_multi_getcontent($info['handle']);
                curl_multi_remove_handle($mh, $info['handle']);
                // process completed request (e.g. curl_multi_getcontent($info['handle']))
            }
        } while ($still_running);
        curl_multi_close($mh);
        return $res;
    }

    public function loadXml1($xmlStr)
    {
        $xml = simplexml_load_string($xmlStr);
        echo '<style>body{background-color:#18171B;}</style>';
        
        $stack = [
            $xml->getName() => $xml
        ];

        function visitNode($node, $prefix, &$data)
        {
            if ($node->count()) {
                dump($prefix);
            } else {
                dump($prefix . '=>' . $node->getName() . ':' . $node->__toString());
                array_set($data, $prefix, $node->__toString());
            }
        }
        
        $data = [];
        $prefix = $xml->getName();
        // 先序
        while (! empty($stack)) {
            $prefix = array_keys($stack);
            $prefix = end($prefix);
            $node = array_pop($stack);
            visitNode($node, $prefix, $data);
            if ($childrens = $node->children()) {
                $unshift = [];
                foreach ($childrens as $k => $v) {
                    $unshift[$prefix . '.' . $k] = $v;
                }
                $unshift = array_reverse($unshift);
                foreach ($unshift as $k => $v) {
                    $stack[$k] = $v;
                }
            }
        }
        dump($data);
    }

    public function LUHN($card_no)
    {
        $len = strlen($card_no);
        $sum = 0;
        for ($i = $len - 1, $j = 0; $i >= 0; $i --, $j ++) {
            $n = $card_no{$i} + 0;
            if ($j % 2) {
                $n *= 2;
                $n > 9 ? ($n = $n % 10 + (int) ($n / 10)) : $n;
            }
            $sum += $n;
        }
        $r = (int) ($sum / 10);
        return $sum == $r * 10;
    }

    public function balanceQuery()
    {
        // //账户余额查询
        // String function = "ant.ebank.acount.balance.query";
        
        // XmlUtil xmlUtil = new XmlUtil();
        // Map<String, String> form = new HashMap<String, String>();
        // form.put("function", function);
        // form.put("reqTime", new Timestamp(System.currentTimeMillis()).toString());
        // //reqMsgId每次报文必须都不一样
        // form.put("reqMsgId", UUID.randomUUID().toString());
        
        // form.put("cardNo",HttpsMain.cardNo);
        // form.put("currencyCode",HttpsMain.currencyCode);
        // form.put("cashExCode","CSH");//CSH钞
        $function = 'ant.ebank.acount.balance.query';
        
        $form = [
            'function' => $function,
            'reqTime' => '2016-07-09 11:03:10.125'
        ];
    }

    public function __xmlToArray1($xmlString)
    {
        $simplexml = simplexml_load_string($xmlString);
        $dataArray = [];
        foreach ($simplexml->children() as $k => $v) {
            if ($v instanceof \SimpleXMLElement && $v->count()) {
                $dataArray[$k] = [];
                foreach ($v->children() as $k1 => $v1) {
                    $dataArray[$k][$k1] = $v1;
                }
            } else {
                $dataArray[$k] = trim($v->__toString());
            }
        }
        return $dataArray;
    }

    public function __arrayToXml1($dataArray)
    {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        
        $xml = $doc->createElement('xml');
        
        $doc->appendChild($xml);
        foreach ($dataArray as $k => $v) {
            if (is_array($v)) {
                $node = $doc->createElement($k);
                foreach ($v as $k1 => $v1) {
                    if (! is_array($v1)) {
                        if (! preg_match('/^\d+(\.\d+)?$/', $v1)) {
                            $node1 = $doc->createElement($k1);
                            $node1->appendChild($doc->createCDATASection($v1));
                            $node->appendChild($node1);
                        } else {
                            $node->appendChild($doc->createElement($k1, $v1));
                        }
                    }
                }
            } else {
                if (! preg_match('/^\d+(\.\d+)?$/', $v)) {
                    $node = $doc->createElement($k);
                    $node->appendChild($doc->createCDATASection($v));
                    $xml->appendChild($node);
                } else {
                    $xml->appendChild($doc->createElement($k, $v));
                }
            }
        }
        // $str = $doc->saveXML();
        
        // ( new \DOMDocument('1.0','UTF-8'))->saveXML();
        
        return $doc->saveXML($xml);
    }

    function httpPost($url, $data = [])
    {
        // TODO error
        $ch = curl_init();
        
        if (! empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $res = curl_exec($ch);
        
        if (false === $res) {
            curl_errno($ch);
            curl_error($ch);
        }
        curl_close($ch);
        
        return $res;
    }

    public function pjb()
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
        print "OS=" . $system->getProperty("os.name") . " " . $system->getProperty("os.version") . " on " . $system->getProperty("os.arch") . " /n";
    }
    
    public function __invoke(){
        dump(' --FUNC-- '.__FUNCTION__);
    }

    
    
    public function dumpMatchs($_St,$data){
        
        echo '<table>';
        foreach ($data as $k => $v){
            echo '<tr>';
            echo '<td>';
            echo $k;
            echo '</td>';
            echo '<td>';
            echo array_get($_St, 'time.'.$k,'[null]');
            echo '</td>';
            echo '<td>';
            echo array_get($_St, 'date.'.$k,'[null]');
            echo '</td>';
            echo '<td style="width:70%">';
            echo $data[$k];
            echo '</td>';
            
            echo '</tr>';
        }
        
        echo '</table>';
    }
    
    
    public function matchSmsMsg($subject,$date = ''){
        static $_St = [
            'time' => [],
            'date' => [],
        ];
        
        $data = [
            '您注册的验证码为：([\d]+)，如不是本人操作请忽略。',
            '您找回密码的验证码为：([\d]+)，如不是本人操作请忽略',
            '您的还款日已到，请登录网站或打开仁仁分期APP还款，逾期将产生滞纳金。客服小妹：([\d\-]+)',
            '同学您好，您的自动还款设置账户([\d]+)，扣款未成功，扣款金额([\d\.]+)，请及时还款，逾期将产生滞纳金!',
            '您的还款日将到，请登录网站或打开仁仁分期APP还款，逾期将产生滞纳金。客服小妹：([\d\-]+)',
            '请在本月([\d]+)日前登录网站或打开仁仁分期APP还款，逾期将产生滞纳金。客服小妹：([\d\-]+)',
            '尊敬的用户！您购买的商品还款日已到，逾期会有滞纳金产生，请尽快还款哦！',
            '尊敬的客户，您仁仁分期的订单已逾期，为避免留下不良信用记录与相关服务受限，请及时登陆仁仁分期APP进行还款。若有疑问，请致电([\d\-]+)。如已还款，请忽略。',
            '尊敬的客户，您仁仁分期的订单已逾期，为避免留下不良信用记录与相关服务受限，请及时登陆仁仁分官网或APP进行还款。若有疑问，请致电客服小妹：([\d\-]+)。如已还款，请忽略',
            '尊敬的客户，您仁仁分期的订单已逾期多日，虽经我司多次提醒仍未入账。请重视您的个人信用记录，尽快安排缴款。否则我司将根据合同相关规定终止您的赊销分期权利，届时需提前全额缴清，若有疑问，请致电([\d\-]+)。',
        
            '尊敬的客户，您仁仁分期的订单已逾期多日，虽经我司多次提醒仍未入账。请重视您的个人信用记录，尽快安排缴款。否则我司将根据合同相关规定终止您的赊销分期权利，交由律师处理，若有疑问，请致电([\d\-]+)。',
            '尊敬的客户，您仁仁分期的订单已逾期多日，虽经我司多次提醒仍未入账。请重视您的个人信用记录，尽快安排缴款。否则我司将根据合同相关规定终止您的赊销分期权利，届时需提前全额缴清，情节严重者将通过法律手段处理此事。若有疑问，请致电([\d\-]+)。',
            '尊敬的客户，您仁仁分期的订单已逾期多日，虽经我司多次提醒仍未入账。请重视您的个人信用记录，尽快安排缴款。否则我司将根据合同相关规定终止您的赊销分期权利，届时需提前全额缴清，情节严重者将采取法律手段。若有疑问，请致电([\d\-]+)。',
            '尊敬的客户，您仁仁分期的订单已逾期多日，虽经我司多次提醒仍未入账。请重视您的个人信用记录，尽快安排缴款。否则我司将根据合同相关规定终止您的赊销分期权利，届时需提前全额缴清，情节严重者将采取法律手段处理此事。若有疑问，请致电([\d\-]+)。',
            '尊敬的客户，您仁仁分期的订单已逾期多日，虽经我司多次提醒仍未入账。请重视您的个人信用记录，尽快安排缴款。否则我司将根据合同相关规定终止您的赊销分期权利，届时需提前全额缴清，情节严重者将采取法律手段处理欠款。若有疑问，请致电([\d\-]+)或([\d]+)。',
        
            '([^\x{ff0c}]+)，身份证：([\dX]+)，手机号([\d]+)，此人于([\d]+)年([\d]+)月([\d]+)日在仁仁分期分期购买产品，至今未还款，我公司将上报全国个人诚信档案系统，烦请转告，如果打扰敬请原谅！若有疑问请致电([\d\-]+)或([\d]+)',
            '([^\x{ff0c}]+)，身份证：([\dX]+)，手机号([\d]+)，此人于([\d]+)年([\d]+)月([\d]+)日在仁仁分期借款，至今未还，我公司将上报全国个人诚信档案系统，烦请转告，如果打扰敬请原谅！若有疑问请致电([\d\-]+)或([\d]+)',
            '([^\x{ff0c}]+)，身份证：([\dX]+)，手机号([\d]+)，你于([\d]+)年([\d]+)月([\d]+)日在仁仁分期分期购买产品，至今未还款，我公司将上报全国个人诚信档案系统，请尽快安排还款，若恶意拖欠我公司不排除将采取法律手段处理此事！若有疑问请致电([\d\-]+)或([\d]+)',
            '([^\x{ff0c}]+)，身份证：([\dX]+)，手机号([\d]+)，此人于([\d]+)年([\d]+)月([\d]+)日在仁仁分期分期购买产品，至今未还款，我公司将上报全国个人诚信档案系统，情节严重者将采取法律手段处理此事，烦请转告，如果打扰敬请原谅！若有疑问请致电([\d\-]+)或([\d]+)',
            '([^\x{ff0c}]+)，身份证：([\dX]+)，手机号([\d]+)，此人于([\d]+)年([\d]+)月([\d]+)日在仁仁分期分期借款，至今未还款，我公司将上报全国个人诚信档案系统，情节严重者将采取法律手段处理此事，烦请转告，如果打扰敬请原谅！若有疑问请致电([\d\-]+)或([\d]+)',
        
        ];
        
        if('[dump]' == $subject){
            $this->dumpMatchs($_St, $data);
            return $_St;
        }
        
        foreach ($data as $key =>  $msgReg){
            if(preg_match('/^'.$msgReg.'$/iu', $subject,$matchs)){
                if(isset($_St['time'][$key])){
                    $_St['time'][$key] ++;
                    if($date > $_St['date'][$key])
                    $_St['date'][$key] = $date;
                }else{
                    $_St['time'][$key] = 1;
                    $_St['date'][$key] = $date;
                }
                return $matchs;
            }
        }
        return false;
    }
    
    public function anLog (){
        $dirPath = public_path('logs').DIRECTORY_SEPARATOR;
        $scandir = scandir($dirPath);
        $ignorl = '银行代扣款失败，请跟进处理';
        foreach ($scandir as $path){
            if(is_file($dirPath.$path)){
                $fileLineArray = file($dirPath.$path);
                //                 dump('Process '.$dirPath.$path);
                foreach ($fileLineArray as $line){
                    $line = trim($line);
        
                    if(preg_match('/'.$ignorl.'/', $line)){
                        continue;
                    }
        
                    $i = 0 ;
                    $j = strlen($line) - 1;
                    while ($i <= $j && '{' != $line{$i++});
                    if($i < $j){
                        while ('}' != $line{$j--} && $j >= $i );
                        if($i < $j){
                            $json = substr($line,$i - 1, $j - $i + 3);
                            $data = json_decode($json,1);
                            if(isset($data['message']) && $data['message']){
                                if(!$this->matchSmsMsg(trim($data['message']) ,substr($line, 0,21))){
//                                     dump($line);
                                }
                            }
                        }
                    }
        
                }
            }
        }
        
        $res = $this->matchSmsMsg('[dump]');
        ksort($res['time']);
        ksort($res['date']);
        dump($res);
        
        exit;
        
    }
    
    public function generatePunctuationRegex()
    {
        $specialChars = '，。！？';
        $tsr = iconv('UTF-8', 'UCS-2', $specialChars);
        $arrstr = str_split($tsr, 2);
        $res = [];
        foreach ($arrstr as $value) {
            $res[] = bin2hex($value{0}) . bin2hex($value{1});
        }
        return $regex = '/[\d\x{' . implode('}\x{', $res) . '}]/u';
    }
    
    public function matchSmsMsg1($subject,$date = ''){
        $data = [
            111912 => '您找回密码的验证码为：([\d]+)，如不是本人操作请忽略',
            111917 => '您的还款日已到，请登录网站或打开仁仁分期APP还款，逾期将产生滞纳金。客服小妹：([\d\-]+)',
            112170 => '同学您好，您的自动还款设置账户([\d]+)，扣款未成功，扣款金额([\d\.]+)，请及时还款，逾期将产生滞纳金!',
            
            111923 => '您的还款日将到，请登录网站或打开仁仁分期APP还款，逾期将产生滞纳金。客服小妹：([\d\-]+)',
            111924 => '请在本月([\d]+)日前登录网站或打开仁仁分期APP还款，逾期将产生滞纳金。客服小妹：([\d\-]+)',
            111925 => '尊敬的用户！您购买的商品还款日已到，逾期会有滞纳金产生，请尽快还款哦！',
            
            112007 => '尊敬的客户，您仁仁分期的订单已逾期，为避免留下不良信用记录与相关服务受限，请及时登陆仁仁分期APP进行还款。若有疑问，请致电([\d\-]+)。如已还款，请忽略。',
            111927 => '尊敬的客户，您仁仁分期的订单已逾期，为避免留下不良信用记录与相关服务受限，请及时登陆仁仁分官网或APP进行还款。若有疑问，请致电客服小妹：([\d\-]+)。如已还款，请忽略',
            112008 => '尊敬的客户，您仁仁分期的订单已逾期多日，虽经我司多次提醒仍未入账。请重视您的个人信用记录，尽快安排缴款。否则我司将根据合同相关规定终止您的赊销分期权利，届时需提前全额缴清，若有疑问，请致电([\d\-]+)。',
        ];
        $ret = [
            'msgId' => '',
            'params' => []
        ];
        foreach ($data as $key =>  $msgReg){
            if(preg_match('/^'.$msgReg.'$/iu', $subject,$matchs)){
                $ret['msgId'] = $key ;
                array_shift($matchs);
                $ret['params'] = $matchs;
                return $ret;
            }
        }
        return false;
    }
    
    public function ang(){
        $ignorl = '银行代扣款失败，请跟进处理';
         
        $dirPath = public_path('logs').DIRECTORY_SEPARATOR;
        $scandir = scandir($dirPath);
        $retDmo = [];
        foreach ($scandir as $path){
            if(is_file($dirPath.$path)){
                $fileLineArray = file($dirPath.$path);
                //                 dump('Process '.$dirPath.$path);
                foreach ($fileLineArray as $line){
                    $line = trim($line);
        
                    if(preg_match('/'.$ignorl.'/', $line)){
                        continue;
                    }
        
                    $i = 0 ;
                    $j = strlen($line) - 1;
                    while ($i <= $j && '{' != $line{$i++});
                    if($i < $j){
                        while ('}' != $line{$j--} && $j >= $i );
                        if($i < $j){
                            $json = substr($line,$i - 1, $j - $i + 3);
                            $data = json_decode($json,1);
                            if(isset($data['message']) && $data['message']){
                                $ret = $this->matchSmsMsg1(trim($data['message']) ,substr($line, 0,21));
                                if($ret){
                                    if(!isset($retDmo[$ret['msgId']])){
                                        $retDmo[$ret['msgId']] = $ret;
                                    }
                                }
                            }
                        }
                    }
        
                }
            }
        }
        ksort($retDmo);
        dump($retDmo);;
        return $retDmo;
    }
    

    
    public function test()
    {
        
        chineseWord();
        
        
        exit;
        
        $msg = '尊敬的客户，您仁仁分期的订单已逾期，为避免留下不良信用记录与相关服务受限，请及时登陆仁仁分期APP进行还款。若有疑问，请致电4007800087。如已还款，请忽略。';
        $msg = '尊敬的客户，您仁仁分期的订单已逾期多日，虽经我司多次提醒仍未入账。请重视您的个人信用记录，尽快安排缴款。否则我司将根据合同相关规定终止您的赊销分期权利，届时需提前全额缴清，若有疑问，请致电4007800087。';
        
        $ret = $this->matchSmsMsg1($msg);
        
        edump($ret);
        
//         $res = \App\Services\Sms\Yuntongxun\YuntongxunSms::sendYun('18767135775', 111924, [12, 15]);
//         edump($res);
//         $this->ang();
        exit;
        $this->anLog();
        $message = '您的还款日已到，请登录网站或打开仁仁分期APP还款，逾期将产生滞纳金。客服小妹：400-780-0087';
        $res = \App\Services\Sms\Yuntongxun\YuntongxunSms::sendSms('18767135775', $message);
        
        edump($res);
        
//         $this->anLog();
        $retDmo = $this->ang();
        $ret = [];
        foreach ($retDmo as $v){
            $ret [] = \App\Services\Sms\Yuntongxun\YuntongxunSms::sendSms('18767135775', $v['msgId'], $v['params']);
        }
        edump($ret);
//         \App\Services\Sms\Yuntongxun\YuntongxunSms::sendSms('18767135775', 98838, ['22222', 15]);
        exit;
        $client = new \TopClient;
        $client->appkey = '23336039';
        $client->secretKey = 'b7c3d3ec7ad79a0252ba59df635e83ed';
        $p0=['phone'=>'18767135775', 'code'=>'123456'];
        $p1=['product'=>'大风', 'tpl'=>'SMS_2520565'];
        $p = $p0 + $p1;
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType('normal');
        $req->setSmsTemplateCode($p['tpl']);
        $req->setSmsFreeSignName('注册验证');   // 【注册验证】
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
                } else if ($response->sub_code == 'isv.BUSINESS_LIMIT_CONTROL') {
                    /**
                     *
                     * 短信验证码，使用同一个签名，对同一个手机号码发送短信验证码，
                     * 允许每分钟1条，累计每小时7条。
                     *
                     * 短信通知，使用同一签名、同一模板，对同一手机号发送短信通知，
                     * 允许每天50条（自然日）。
                     *
                     */
                    $msg = '短信发送过于频繁';
                    $code = 6002;
                }
            }
        
            throw new \Exception($msg, $code);
        }
        
//         'sms' => [
//             'app_key' => '23336039',
//             'app_secret' => 'b7c3d3ec7ad79a0252ba59df635e83ed',
//             'expire' => 3000,
//         ],
        
        exit;
        $res = \App\Services\Sms\Yuntongxun\YuntongxunSms::sendSms('18767135775', 98838, ['22222', 15]);
        edump($res);
        exit;
        $prefix = '601382610100';
        $start = 1008932;
        $fp = fopen(public_path('card.txt'), 'w');
        foreach (range(1, 600) as $v){
            $line = $prefix.($v + $start) .PHP_EOL;
            fwrite($fp, $line);
        }
        fclose($fp);
        exit;
        $str = '1×× Informational
100 Continue
101 Switching Protocols
102 Processing
2×× Success
200 OK
201 Created
202 Accepted
203 Non-authoritative Information
204 No Content
205 Reset Content
206 Partial Content
207 Multi-Status
208 Already Reported
226 IM Used
3×× Redirection
300 Multiple Choices
301 Moved Permanently
302 Found
303 See Other
304 Not Modified
305 Use Proxy
307 Temporary Redirect
308 Permanent Redirect
4×× Client Error
400 Bad Request
401 Unauthorized
402 Payment Required
403 Forbidden
404 Not Found
405 Method Not Allowed
406 Not Acceptable
407 Proxy Authentication Required
408 Request Timeout
409 Conflict
410 Gone
411 Length Required
412 Precondition Failed
413 Payload Too Large
414 Request-URI Too Long
415 Unsupported Media Type
416 Requested Range Not Satisfiable
417 Expectation Failed
418 I\'m a teapot
421 Misdirected Request
422 Unprocessable Entity
423 Locked
424 Failed Dependency
426 Upgrade Required
428 Precondition Required
429 Too Many Requests
431 Request Header Fields Too Large
444 Connection Closed Without Response
451 Unavailable For Legal Reasons
499 Client Closed Request
5×× Server Error
500 Internal Server Error
501 Not Implemented
502 Bad Gateway
503 Service Unavailable
504 Gateway Timeout
505 HTTP Version Not Supported
506 Variant Also Negotiates
507 Insufficient Storage
508 Loop Detected
510 Not Extended
511 Network Authentication Required
599 Network Connect Timeout Error';
        $lines = preg_split('/[\r\n]+/', $str);
        foreach ($lines as $v){
            $res = explode(' ', $v,2);
            $vv = intval($res[0]);
           
            $k = 'HTTP_'.strtoupper($res[1]);
            $k = 'HTTP_'.$vv;
            $k = preg_replace('/[- \']+/', '_', $k);
            echo '<pre>';
            echo "/**
 * {$v}
 * @var int
 */";
            echo '</pre>';
           
            echo 'const '.$k.' = '.$vv.';<br/>';
        }
//         edump(preg_split('/[\r\n]+/', $str));
        
        
        exit;
        
        
        $message = \Input::get('data');
        $messageJsonArray = json_decode($message,1);
        $data = [
            'message' => array_get($messageJsonArray, 'message'),//message
            'topic' => array_get($messageJsonArray, 'topic'),//topic
            'sign' => array_get($messageJsonArray, 'sign'),//sign= md5_32(productKey+(message)+productSecret)
            'messageId' => array_get($messageJsonArray, 'messageId'),//messageId
            'appKey' => array_get($messageJsonArray, 'appKey'),//appKey
            'deviceId' => array_get($messageJsonArray, 'deviceId'),//deviceId
        ];
        $sql = createInsertSql('xb_topic_msg', $data);
        \DB::insert($sql);
        
        
        dump(is_callable($this));
        $this();
        
        exit;
        //4060
        $aa = \App\Models\Common\RequestLog::find('4060');
        
        edump($aa->created_at);
        edump( \Carbon\Carbon::now()->toDateTimeString());
        
        function getMoneyFunc() {
            $rmb = 1;
            $func = function() use ( &$rmb ) {
                echo $rmb.'<br/>';
                //把$rmb的值加1
                $rmb++;
            };
            return $func;
        }
        
        $getMoney = getMoneyFunc();
        $getMoney();
        $getMoney();
        $getMoney();
        
        exit;
        
        
        $accessKeyId = "nL5Y7fL9P7RXUZ5J";
        $accessSecret = "saBQK7zYCkWXBi7vV7YCI8Fl7kc5i2";
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);
        
        $request = new Iot\PubRequest();
        $request->setProductKey(23344127);
        $request->setMessageContent("aGVsbG93b3JsZA==");// Hello world base64 String.
        $request->setTopicFullName("/23344127/home/admin/adfadsfa/dsafsfa");//消息发送给哪个topic中.
        $response = $client->getAcsResponse($request);
        
        
        dump($response);
        
        
        
        dump('--end--');
        exit;
        $data = explode("\r\n", $str);
        $arr = [];
        foreach ($data as $v){
            $exp = explode(" ", $v);
            $arr[$exp[0]] = $exp[1];
        }
        preArrayKV($arr);
        
//         edump($data);
        exit;
        
        \App\Services\MyBank\Main::main();
        
        exit;
        
        $this->pjb();
        exit();
        \App\Models\Common\Bill::run();
        exit();
        
        $date = new \DateTime('NOW');
        
        dump($date->format('Y-m-d H:i:s.u'));
        dump($date->format(\DateTime::W3C));
        
        exit();
        
        dump(\Request::getRequestUri());
        
        dump(\Route::getCurrentRoute()->getPath());
        
        exit();
        
        $a = [
            'a' => [
                'a' => 0
            ]
        ];
        dump(array_get($a, 'a.a'));
        edump(array_get($a, 'a.a') === 0);
        // $pattern = '/[\u4E00-\u9FA5]{2,5}(?:·[\u4E00-\u9FA5]{2,5})*';
        $pattern = '/^[\x{4E00}-\x{9FA5}]{2,5}(?:·[\x{4E00}-\x{9FA5}]{2,5})*$/u';
        
        // / ，例如：阿沛·阿旺晋美、卡尔·马克思
        
        edump(preg_match($pattern, '阿沛·阿旺晋美'));
        
        phpinfo();
        exit();
        $asd = cookie('asd');
        cookie('asd', '123', 12);
        
        dump($asd);
        exit();
        $data = session('ads');
        
        // \Session::put('ads','ssssssss');
        
        session([
            'ads' => 'ssssssss'
        ]);
        dump_object_name(app('session'));
        // dump_object_name(app('session.store'));
        dump($data);
        return '';
        
        $url = 'http://api.xb.com/v1/employee/payrollAuthority';
        $res = $this->httpPost($url, [
            'uid' => 27
        ]);
        edump($res);
        
        edump(\Config::get('session.driver'));
        // session(['ads' => time()]);
        // $data = \Session::get('ads');
        $data = session('ads');
        dump(time());
        edump($data);
        \Session::put('ads', time());
        
        exit();
        
        $a = null;
        edump(isset($a));
        
        $str = '333838393967388619435193';
        
        // preg_split('//', $str)
        
        edump(array_sum(preg_split('//', $str)));
        
        exit();
        
        $xml = '<xml>
    <ToUserName>
        <![CDATA[gh_6dc799215ddc]]>
    </ToUserName>\n
    <FromUserName>
        <![CDATA[opE6QwUk4aYmfKdL3dOzjeZ0-4BE]]>
    </FromUserName>\n
    <CreateTime>1468212370</CreateTime>\n
    <MsgType>
        <![CDATA[text]]>
    </MsgType>\n
    <Content>
        <![CDATA[公关部]]>
    </Content>\n
    <MsgId>6305924113223005219</MsgId>\n
</xml>';
        
        $array = $this->__xmlToArray1($xml);
        
        dump($this->__arrayToXml1($array));
        
        edump($array);
        
        // edump($res);
        
        exit();
        edump(openssl_get_md_methods());
        \App\Services\MyBank\BalanceQuery::main();
        exit();
        // 1468034664090
        // 1468034687
        edump(uuid());
        edump(microtime());
        
        exit();
        
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->loadXML('<document><request id="request"><head></head><body></body></request></document>');
        // $nod = new \DOMNode();
        
        exit();
        
        exit();
        // debug_backtrace()
        
        $str = '6222801464011032243
6212261202033044
6212261202033044
6228480018653809576
6212253803002654982
6228480259003808278
6228480259003808278
6215590200011038262
6217220200004433631
6212260200103306940
6222020401011569807
6222020710002688624
6222020710002688624
6222021202030140314';
        edump($str);
        // 6222620170931032
        // 6222620170003931032
        
        edump($this->LUHN('6214855710279726'));
        
        $str = "
          /**
 * 订单打款状态，未打款
 *
 * @var unknown
 */
define('ORDER_PAY_STATUS_UNPAY', 1);
/**
 * 订单打款状态，打款中
 *
 * @var unknown
 */
define('ORDER_PAY_STATUS_PAYING', 2);
/**
 * 订单打款状态，完成打款，结果查询中
 *
 * @var unknown
 */
define('ORDER_PAY_STATUS_QUERY', 3);
/**
 * 订单打款状态，完成结果查询
 *
 * @var unknown
 */
define('ORDER_PAY_STATUS_RESULT', 4);";
        
        echo (preg_replace("!define\('([^\']*)', (\d)\)!", "\tconst \\1 = \\2", $str));
        exit();
        dump(4007.00 - 2003.01 + 2004.00 - 2004.00);
        dump(2003.99);
        
        $a = 4007.00 - 2003.01 + 2004.00 - 2004.00;
        $b = 2003.99;
        $a = $a . '';
        $b = $b . '';
        dump($a - $b);
        dump($a == $b);
        exit();
        echo '<style>body{background-color:#18171B;}</style>';
        
        $xmlStr = '<?xml version="1.0" encoding="GBK"?>
<CMBSDKPGK>
    <INFO>
        <DATTYP>2</DATTYP>
        <ERRMSG></ERRMSG>
        <FUNNAM>GetTransInfo</FUNNAM>
        <LGNNAM>银企直连专用普通1</LGNNAM>
        <RETCOD>0</RETCOD>
    </INFO>
    <NTQTSINFZ>
        <AMTCDR>C</AMTCDR>
        <APDFLG>Y</APDFLG>
        <ATHFLG>N</ATHFLG>
        <BBKNBR>59</BBKNBR>
        <BUSNAM>企业银行代发</BUSNAM>
        <C_ATHFLG>无</C_ATHFLG>
        <C_BBKNBR>福州</C_BBKNBR>
        <C_ETYDAT>2014年11月03日</C_ETYDAT>
        <C_GSBBBK></C_GSBBBK>
        <C_RPYBBK></C_RPYBBK>
        <C_TRSAMT>1,329.10</C_TRSAMT>
        <C_TRSAMTC>1,329.10</C_TRSAMTC>
        <C_TRSBLV>2,973,575.11</C_TRSBLV>
        <C_VLTDAT>2014年11月03日</C_VLTDAT>
        <ETYDAT>20141103</ETYDAT>
        <ETYTIM>163751</ETYTIM>
        <GSBBBK></GSBBBK>
        <NAREXT>N000000786</NAREXT>
        <NARYUR>代发余额退款</NARYUR>
        <REFNBR>K2212200000007C</REFNBR>
        <REFSUB></REFSUB>
        <REQNBR>0028589877</REQNBR>
        <RPYBBK></RPYBBK>
        <RSV30Z>**</RSV30Z>
        <RSV31Z>10</RSV31Z>
        <RSV50Z></RSV50Z>
        <TRSAMT>1329.10</TRSAMT>
        <TRSAMTC>1329.10</TRSAMTC>
        <TRSANL>AIGATR</TRSANL>
        <TRSBLV>2973575.11</TRSBLV>
        <TRSCOD>AGRD</TRSCOD>
        <VLTDAT>20141103</VLTDAT>
        <YURREF>qfq10114334071365262315</YURREF>
    </NTQTSINFZ>
    <NTQTSINFZ>
        <AMTCDR>C</AMTCDR>
        <APDFLG>Y</APDFLG>
        <ATHFLG>N</ATHFLG>
        <BBKNBR>59</BBKNBR>
        <BUSNAM>企业银行代发</BUSNAM>
        <C_ATHFLG>无</C_ATHFLG>
        <C_BBKNBR>福州</C_BBKNBR>
        <C_ETYDAT>2014年11月03日</C_ETYDAT>
        <C_GSBBBK></C_GSBBBK>
        <C_RPYBBK></C_RPYBBK>
        <C_TRSAMT>145.20</C_TRSAMT>
        <C_TRSAMTC>145.20</C_TRSAMTC>
        <C_TRSBLV>2,973,720.31</C_TRSBLV>
        <C_VLTDAT>2014年11月03日</C_VLTDAT>
        <ETYDAT>20141103</ETYDAT>
        <ETYTIM>163751</ETYTIM>
        <GSBBBK></GSBBBK>
        <NAREXT>N000000785</NAREXT>
        <NARYUR>代发余额退款</NARYUR>
        <REFNBR>K2211900000005C</REFNBR>
        <REFSUB></REFSUB>
        <REQNBR>0028589876</REQNBR>
        <RPYBBK></RPYBBK>
        <RSV30Z>**</RSV30Z>
        <RSV31Z>10</RSV31Z>
        <RSV50Z></RSV50Z>
        <TRSAMT>145.20</TRSAMT>
        <TRSAMTC>145.20</TRSAMTC>
        <TRSANL>AIGATR</TRSANL>
        <TRSBLV>2973720.31</TRSBLV>
        <TRSCOD>AGRD</TRSCOD>
        <VLTDAT>20141103</VLTDAT>
        <YURREF>qfq10114334071352266040</YURREF>
    </NTQTSINFZ>
</CMBSDKPGK>';
        // edump(loadXml($xmlStr));
        
        $xmlStr = '<?xml version="1.0" encoding="UTF-8"?>
<document>
    <request>
            <version>1.0.0</version>
        <head>
            <version>1.0.0</version>
            <appId>CCB001</appId>
            <function>mybk.loan.payment.apply</function>
            <reqTime>20150729164630</reqTime>
            <reqTimeZone>Asia/Shanghai</reqTimeZone>
            <reqMsgId>52be7386-8f30-41de-bbc0-f27feb45d25f</reqMsgId>
        </head>
        <body>
            <!-- OrgResultInfo，可选字段，用于通知类报文。与Response的ResultInfo是同一套数据，
    	   但因为是Response对应的Notify，所以全部添加Org前缀，如果非通知类报文，那么是不含该域的 -->
            <orgResultInfo>
                <orgResultStatus>S</orgResultStatus>
                <orgResultCode>0000</orgResultCode>
                <orgResultMsg>Accepted</orgResultMsg>
            </orgResultInfo>
            <bizNo>20150729164629000011931600</bizNo>
            <txAmt>100</txAmt>
            <txCcy>392</txCcy>
            <txTime>20150715120100</txTime>
            <transferTime>20150715120102</transferTime>
        </body>
            <ds-SignedInfo>123</ds-SignedInfo>
        <ds:Signature
            xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
            <ds:SignedInfo>
                <ds:CanonicalizationMethod
	Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"></ds:CanonicalizationMethod>
                <ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"></ds:SignatureMethod>
                <ds:Reference URI="">
                    <ds:Transforms>
                        <ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></ds:Transform>
                    </ds:Transforms>
                    <ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"></ds:DigestMethod>
                    <ds:DigestValue>2pj3xjZlfy04SC33A3fw2hYozUk=</ds:DigestValue>
                </ds:Reference>
            </ds:SignedInfo>
            <ds:SignatureValue>
		vl6+56s2TM5V+0VYiwvLIORfspSIL3Rzx45jWKCmx/ieYoXckPua8uJVOvA016UFd6X7qjKiNv9V
		84JkVW6X//1XvDiek8ILOn8m10zJTalBazsfkpovBcljMwZn1PXnvNF3lORqQ0YRKnsMw1O1G7W4
		Ju/Ow9mKRbHckr1zFZg=
	</ds:SignatureValue>
        </ds:Signature>
    </request>
</document>';
        edump(loadXml2($xmlStr));
        edump(loadXml($xmlStr));
        
        $doc = new \DOMDocument();
        $doc->loadXML($xmlStr);
        echo '<style>body{background-color:#18171B;}</style>';
        
        $stack = [
            $doc->nodeName => $doc
        ];

        function visitNode($node, $prefix, &$data)
        {
            if ($node->nodeName != '#comment') {
                
                if (ends_with($prefix, 'DigestMethod')) {
                    // edump($node);
                }
                
                if ($node->childNodes->length == 0) {
                    dump($prefix . ' ===> ');
                    array_set($data, $prefix, '');
                } else 
                    if ($node->childNodes->length == 1 && $node->childNodes[0]->nodeType == XML_TEXT_NODE) {
                        dump($prefix . ' ===> ' . $node->nodeName . ':' . trim($node->childNodes[0]->wholeText));
                        array_set($data, $prefix, trim($node->childNodes[0]->wholeText));
                    } else {
                        dump($prefix);
                    }
            }
        }
        
        $data = [];
        // 先序
        while (! empty($stack)) {
            $prefix = array_keys($stack);
            $prefix = end($prefix);
            $node = array_pop($stack);
            visitNode($node, $prefix, $data);
            if ($childrens = $node->childNodes) {
                if ($childrens->length > 1 || ($childrens->length == 1 && $childrens[0]->nodeType != XML_TEXT_NODE)) {
                    $unshift = [];
                    foreach ($childrens as $k => $v) {
                        $v->nodeType != XML_TEXT_NODE && $unshift[$prefix . '.' . $v->nodeName] = $v;
                    }
                    $unshift = array_reverse($unshift);
                    foreach ($unshift as $k => $v) {
                        $stack[$k] = $v;
                    }
                }
            }
        }
        dump($data);
        exit();
        
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->loadXML($xmlStr);
        dump($doc->hasChildNodes());
        foreach ($doc->childNodes as $k => $v) {
            // dump($v);
            foreach ($v->childNodes as $k1 => $v1) {
                // dump($v1);
                // XML_ELEMENT_NODE
                if ($v1->nodeType != XML_TEXT_NODE) {
                    dump($v1);
                    foreach ($v1->childNodes as $k2 => $v2) {
                        dump($v2);
                    }
                }
            }
        }
        edump($doc);
        
        exit();
        
        $xml = simplexml_load_string($xmlStr, '\SimpleXMLElement', 0, '', true);
        echo '<style>body{background-color:#18171B;}</style>';
        
        $stack = [
            $xml->getName() => $xml
        ];

        function visitNode($node, $prefix, &$data)
        {
            if ($node->count()) {
                dump($prefix);
            } else {
                dump($prefix . '=>' . $node->getName() . ':' . $node->__toString());
                array_set($data, $prefix, $node->__toString());
            }
        }
        
        $data = [];
        $prefix = $xml->getName();
        // 先序
        while (! empty($stack)) {
            $prefix = array_keys($stack);
            $prefix = end($prefix);
            $node = array_pop($stack);
            visitNode($node, $prefix, $data);
            if ($childrens = $node->children()) {
                $unshift = [];
                foreach ($childrens as $k => $v) {
                    $unshift[$prefix . '.' . $k] = $v;
                }
                $unshift = array_reverse($unshift);
                foreach ($unshift as $k => $v) {
                    $stack[$k] = $v;
                }
            }
        }
        dump($data);
        exit();
        
        $stack = [
            $xml->getName() => $xml
        ];

        function visitNode($node)
        {
            if ($node->count()) {
                dump($node->getName());
            } else {
                dump($node->getName() . ':' . $node->__toString());
            }
        }
        
        // 先序
        while (! empty($stack)) {
            $prefix = array_keys($stack)[0];
            $node = array_shift($stack);
            visitNode($node);
            if ($childrens = $node->children()) {
                $unshift = [];
                foreach ($childrens as $k => $v) {
                    $unshift[$k] = $v;
                }
                $unshift = array_reverse($unshift);
                array_unshift($unshift, 0);
                $unshift[0] = &$stack;
                call_user_func_array('array_unshift', $unshift);
            }
        }
        exit();
        $resultArray = [];
        foreach ($xml->children() as $k => $v) {
            if ($v instanceof \SimpleXMLElement) {
                if (isset($resultArray[$k])) {
                    if (array_key_exists(0, $resultArray[$k])) {
                        $resultArray[$k][] = (array) $v;
                    } else {
                        $resultArray[$k] = [
                            $resultArray[$k]
                        ];
                        $resultArray[$k][] = (array) $v;
                    }
                } else {
                    $resultArray[$k] = (array) $v;
                }
            }
        }
        
        dump($resultArray);
        edump($xml);
        
        exit();
        
        exit();
        Bill::run();
        // 房租、多人支出、由谁付
        
        // 个人应付、平均付
        // 需付，已付
        
        exit();
        
        edump(exec('dir'));
        exit();
        $HttpUtils = new \App\Gather\Utils\HttpUtils();
        // $url = 'http://nyato.com/data/upload/avatar/ac/d8/d5/original_200_200.jpg';
        // $res = $HttpUtils->httpDownloadSha1($url);
        // dump($res);
        // exit;
        set_time_limit(100);
        $url = 'http://image.baidu.com/search/avatarjson';
        $data = [
            'tn' => 'resultjsonavatarnew',
            'ie' => 'utf-8',
            'word' => '苹果',
            'cg' => 'wallpaper',
            'pn' => '0',
            'rn' => '60',
            'itg' => '0',
            'z' => '0',
            'fr' => '',
            'width' => '',
            'height' => '',
            'lm' => '-1',
            'ic' => '0',
            's' => '0',
            'st' => '-1',
            'gsm' => '1e'
        ];
        
        $res = curl_get($url, $data);
        $fp = fopen('ig-result.txt', 'w');
        $error_fp = fopen('ig-error.txt', 'w');
        if (isset($res['imgs'])) {
            foreach ($res['imgs'] as $key => $v) {
                fwrite($fp, $v['objURL'] . "\r\n");
                $url = $v['objURL'];
                $HttpUtils->httpDownloadSha1($url, 'Sha1');
            }
        }
        fclose($error_fp);
        fclose($fp);
        exit();
        
        $urls = [
            'http://api.xb.com/test?tag=1',
            'http://api.xb.com/test?tag=2'
        ];
        
        $res = $this->mcurl($urls, [
            [
                'a' => 1
            ],
            [
                'a' => 2
            ]
        ]);
        dump($res);
        
        // 'Maatwebsite\Excel\ExcelServiceProvider',
        // Maatwebsite\Excel\Facades\Excel
        
        exit();
        
        \Excel::load('test.xls', function ($reader) {
            
            // Getting all results
            dd($results = $reader->get());
            // ->all() is a wrapper for ->get() and will work the same
            $results = $reader->all();
        });
        
        exit();
        edump(md5('jinyanlin123456'));
        // Input::get('pass') == 'jUT4GlZtq3tHoPUE')
        exit();
        $phones = file_get_contents('phones.txt');
        $arr = preg_split('/\s+/', $phones);
        $array = array_chunk($arr, intval(count($arr) / 3) + 1);
        $rse = [];
        foreach ($array as $k => $v) {
            $key = '7WQRJOnYAQUYGVSzke';
            $phone = $v;
            $message = '仁仁提醒：近期同学反馈有不法分子在百度留下私人电话冒充仁仁客服，对方会谎称同学还款失败需要重新还款，并要求将款项打入私人账户！请同学们提高警惕，如有任何问题先拨打官方唯一客服电话4007800087或风控部电话13454129972咨询！';
            $params = array(
                'mobile' => implode(',', $phone),
                'message' => $message
            );
            $params['pass'] = 'jUT4GlZtq3tHoPUE';
            $url = "https://secure.renrenfenqi.com/interface/crm/sms";
            $rse[] = curl_post($url, $params);
            sleep(2);
        }
        
        $url = "https://gw.alicdn.com/tps/i3/TB1QeiDGFXXXXb8XVXXszjdGpXX-140-140.png?imgtag=avatar";
        
        $content = file_get_contents($url);
        file_put_contents("TB1QeiDGFXXXXb8XVXXszjdGpXX-140-140.png", $content);
        exit();
        $content = file_get_contents('20160218');
        
        edump(detect_encoding($content));
        
        dump(__FUNCTION__);
        
        exit();
        $unit = new \App\Gather\RPG\UnitBase('Unit001');
        
        $unit->showDiedMessage();
        
        exit();
        $str = 'showInjuredMessage';
        
        dump(preg_replace('/[A-Z]/', '#\\0', $str));
        
        edump(preg_split('/[A-Z]/', $str));
        
        exit();
        
        if (version_compare(PHP_VERSION, '5.3.6', '>=')) {
            list ($current, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        } else {
            list ($current, $caller) = debug_backtrace(false, 2);
        }
        // edump($backtrace);
        // list($current, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        dump($current);
        edump($caller);
        
        \App\Gather\AESTool::main();
        
        exit();
        
        $url = 'http://www.xunshu.la/22_22665/7813282.html';
        
        $word = \Input::get('w');
        // if(!$word){
        // echo 'Word Is Required!';
        // exit();
        // }
        $sourceData = \App\Models\Transfer::all()->toArray();
        $transferData = [];
        $file = 'yun.xml';
        $content = file_get_contents($file);
        $resEN = $this->__xmlToArray($content);
        $n = 0;
        \DB::beginTransaction();
        foreach ($sourceData as $v) {
            
            if ($v['status'] == 0 && $v['eng'] != $resEN[$v['uid']]) {
                $str = html_entity_decode($resEN[$v['uid']]);
                $r = preg_replace('/<[^>]*>/', '', $str);
                preg_match_all('/[a-zA-Z]/i', $r, $metchs);
                if (count($metchs[0]) >= 1) {
                    continue;
                }
                echo $n . '[En]' . $v['eng'] . '<br/>';
                echo $n . '[Ch]' . $resEN[$v['uid']] . '<br/>';
                $n ++;
                \App\Models\Transfer::where('id', $v['id'])->update([
                    'status' => 2,
                    'chi' => htmlspecialchars($resEN[$v['uid']])
                ]);
            }
        }
        \DB::commit();
        exit();
        foreach ($sourceData as $v) {
            $transferData[$v['contentuid']] = $v;
            $reg = '/' . $word . '/i';
            if ($word && preg_match($reg, $v['eng'])) {
                $str = preg_replace($reg, '<font color="red">\\0</font>', $v['eng']);
                echo '[EN]:' . $str . '<br/>';
                echo '[CH]:' . ($v['status'] == 1 ? $v['chi'] : '<textarea></textarea>') . '<br/>';
            }
            $resEN[$k] = htmlspecialchars($resEN[$k]);
            $str = "\t<content contentuid=\"{$k}\">{$resEN[$k]}</content>" . PHP_EOL;
            fputs($fp, $str);
        }
        fputs($fp, '</contentList>');
        fputs($fp1, '</contentList>');
        fclose($fp);
        fclose($fp1);
        dump('From English:' . $fromEn);
        dump('From Chinese:' . $fromCh);
        exit();
        edump($resEN);
    }

    public function generateData()
    {
        $file = 'english.xml';
        $content = file_get_contents($file);
        $resEN = $this->__xmlToArray($content);
        $file = 'hanhua.xml';
        $content = file_get_contents($file);
        $resCH = $this->__xmlToArray($content);
        
        set_time_limit(0);
        $fromEn = 0;
        $fromCh = 0;
        $length = 0;
        
        $insertData = [];
        
        foreach ($resEN as $k => $v) {
            if (strlen($v) > $length)
                $length = strlen($v);
            
            if (isset($resCH[$k])) {
                // $resEN[$k] = $resCH[$k];
                $fromCh ++;
                $record = [
                    'uid' => $k,
                    'eng' => $resEN[$k],
                    'chi' => $resCH[$k],
                    'status' => 1
                ];
            } else {
                $resEN[$k] = htmlspecialchars($resEN[$k]);
                $fromEn ++;
                $str = "\t<content contentuid=\"{$k}\">{$resEN[$k]}</content>" . PHP_EOL;
                $record = [
                    'uid' => $k,
                    'eng' => $resEN[$k],
                    'chi' => '',
                    'status' => 0
                ];
            }
            $resEN[$k] = htmlspecialchars($resEN[$k]);
            $str = "\t<content contentuid=\"{$k}\">{$resEN[$k]}</content>" . PHP_EOL;
            $insertData[] = $record;
            if (count($insertData) > 100) {
                \App\Models\Transfer::insert($insertData);
                $insertData = [];
            }
        }
        $insertData && \App\Models\Transfer::insert($insertData);
        dump('From English:' . $fromEn);
        dump('From Chinese:' . $fromCh);
        dump($length);
        exit();
    }

    public function test22()
    {
        $word = \Input::get('w');
        // if(!$word){
        // echo 'Word Is Required!';
        // exit();
        // }
        $sourceData = \App\Models\Transfer::all()->toArray();
        $transferData = [];
        
        $file = 'yun.xml';
        $content = file_get_contents($file);
        $resEN = $this->__xmlToArray($content);
        
        $n = 0;
        
        foreach ($sourceData as $v) {
            if ($v['status'] == 0 && $v['eng'] != $resEN[$v['uid']]) {
                echo $n . '[En]' . $v['eng'] . '<br/>';
                echo $n . '[Ch]' . $resEN[$v['uid']] . '<br/>';
                $n ++;
            }
        }
        
        exit();
        
        foreach ($sourceData as $v) {
            
            $transferData[$v['contentuid']] = $v;
            
            $reg = '/' . $word . '/i';
            if ($word && preg_match($reg, $v['eng'])) {
                $str = preg_replace($reg, '<font color="red">\\0</font>', $v['eng']);
                echo '[EN]:' . $str . '<br/>';
                echo '[CH]:' . ($v['status'] == 1 ? $v['chi'] : '<textarea></textarea>') . '<br/>';
            }
        }
        
        // dump($transferData);
        
        exit();
        
        $res = curl_post('http://fanyi.baidu.com/v2transapi', [
            'from' => 'en',
            'to' => 'zh',
            'query' => 'realtime',
            'transtype' => 'realtime',
            'simple_means_flag' => '3'
        ]);
        edump(json_decode($res, 1));
    }

    public function printTableView($str, $kv = true)
    {
        $result = $this->tableViewArray($str, $kv);
        preArrayKV($result);
        exit();
    }

    public function tableViewToArrayAnn($str)
    {
        $array = $this->tableViewArray($str, 1, 0);
        
        echo '<pre>';
        array_walk($array, function ($v, $k) {
            echo sprintf("'%s' => '' ,// %s\n", $k, $v);
        });
        echo '</pre>';
    }

    /**
     *
     * @param unknown $str            
     * @param string $kv
     *            $k => $v
     * @param string $ks
     *            ksort
     * @return boolean|multitype:string
     */
    public function tableViewArray($str, $kv = true, $ks = true)
    {
        $res = explode("\r", $str);
        // edump($res);
        $res = array_filter($res, function ($v) {
            return trim($v) != '';
        });
        $result = [];
        foreach ($res as $k => $v) {
            $v = trim($v);
            $vv = preg_split("/\s/", $v, 2);
            // edump($vv);
            $kv && $result[trim($vv[0])] = trim($vv[1]);
            $kv === false && $result[trim($vv[1])] = trim($vv[0]);
        }
        $ks && ksort($result);
        return $result;
    }

    public function resultExampleAnn($resultExample, $ann)
    {
        echo '<pre>';
        foreach ($resultExample as $k => $v) {
            echo " '$k' => '$v',//" . (isset($ann[$k]) ? $ann[$k] : 'UNKNOWN') . " " . PHP_EOL;
        }
        echo '</pre>';
    }

    public function printTableViewAnn($str, $kv = true)
    {
        $result = $this->tableViewArray($str, $kv);
        preArrayKV($result);
        exit();
    }

    public function test1()
    {
        \App\Models\McpayDetail::importFromLog();
        exit();
        
        \App\Models\FbsdkLog::create([]);
        dump(\App\Models\FbsdkLog::create([], true));
        exit();
        $fbsdk = \App\Services\Merchants\FBSdkService::getInstance();
        
        // DCPAYMNT
        // NTIBCOPR
        
        $logs = \App\Models\FbsdkLog::where('func_name', 'DCPAYMNT')->orWhere('func_name', 'NTIBCOPR')
            ->get()
            ->toArray();
        
        $dataKey = [
            'DCPAYMNT' => [
                'DCOPDPAYX',
                'NTQPAYRQZ'
            ],
            'NTIBCOPR' => [
                'NTIBCOPRX',
                'NTOPRRTNZ'
            ]
        ];
        
        foreach ($logs as $k => $v) {
            if ($v['send_status']) {
                $log_id = $v['id'];
                $funcname = $v['func_name'];
                $sendData = $fbsdk->__xmlToArray(iconv('UTF-8', 'GBK', $v['send_xml']));
                $receiveData = $fbsdk->__xmlToArray(iconv('UTF-8', 'GBK', $v['received_xml']));
                // DCOPDPAYX NTQPAYRQZ
                // NTIBCOPRX NTOPRRTNZ
                
                $send = isset($sendData[$dataKey[$funcname][0]]) ? $sendData[$dataKey[$funcname][0]] : [];
                $receive = isset($receiveData[$dataKey[$funcname][1]]) ? $receiveData[$dataKey[$funcname][1]] : [];
                
                \App\Models\McpayDetail::record($log_id, $funcname, $send, $receive);
            }
        }
        
        esqlLastSql();
        
        exit();
        
        edump($_SERVER);
        
        $v = iconv('utf8', 'iso-8859-1', "sdsd代发");
        header("Content-Type: text/xml;encoding=utf-8");
        echo utf8_decode(wddx_serialize_value($v));
        exit();
        
        edump(get_class());
        echo false;
        
        $a = 'asd';
        
        exit();
        get_class();
        
        $str = <<<qw
asdasd
qw;
        
        $str = <<<'EOT'
My name is "$name". I am printing some $foo->foo.
Now, I am printing some {$foo->bar[1]}.
This should not print a capital 'A': \x41
EOT;
        
        edump($str);
        dump(decbin(12));
        dump(floor((0.1 + 0.7) * 10));
        dump(((0.1 + 0.7) * 10));
        var_dump(010120);
        
        exit();
    }
}



