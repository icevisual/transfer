<?php
namespace App\Http\Controllers;

use App\Gather\AESTool;
use App\Models\Bill;

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

    public function test()
    {
        $accessKeyId = "";
        $accessSecret = "";
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $accessKeyId, $accessSecret);
        $client = new \DefaultAcsClient($iClientProfile);
        dump('--end--');
        exit;
        $str = "100 邮储银行
102 工商银行
103 农业银行
104 中国银行
105 建设银行
201 国家开发银行
301 交通银行
302 中信银行
303 光大银行
304 华夏银行
305 民生银行
306 广发银行
307 平安银行
308 招商银行
309 兴业银行
310 浦发银行
311 恒丰银行
313 华融湘江银行
316 浙商银行
317 渤海银行
321 重庆三峡银行
401 上海银行
402 厦门银行
403 北京银行
404 烟台银行
405 福建海峡银行
408 宁波银行
409 齐鲁银行
410 福州银行
411 焦作市商业银行
412 温州银行
413 广州银行
414 汉口银行
415 甘肃银行
417 盛京银行
418 洛阳银行
419 辽阳银行
420 大连银行
421 苏州银行
422 河北银行
423 杭州银行
424 南京银行
425 东莞银行
426 金华银行
427 乌鲁木齐市商业银行
428 绍兴银行
429 成都银行
430 抚顺银行
431 临商银行
432 湖北银行
433 葫芦岛银行
434 天津银行
435 郑州银行
436 宁夏银行
437 珠海华润银行
438 齐商银行
439 锦州银行
440 徽商银行
441 重庆银行
442 哈尔滨银行
443 贵阳银行
444 西安银行
446 丹东银行
447 兰州银行
448 南昌银行
449 晋商银行
450 青岛银行
451 吉林银行
454 九江银行
455 日照银行
456 鞍山银行
457 秦皇岛市商业银行
458 青海银行
459 台州银行
461 长沙银行
462 潍坊银行
463 赣州银行
464 泉州银行
465 营口银行
466 富滇银行
467 阜新银行
470 嘉兴银行
472 廊坊银行
473 浙江泰隆商业银行
474 内蒙古银行
475 湖州银行
478 广西北部湾银行
479 包商银行
481 威海市商业银行
483 攀枝花市商业银行
485 绵阳市商业银行
486 泸州市商业银行
487 大同市商业银行
488 三门峡银行
489 广东南粤银行
490 张家口市商业银行
491 桂林银行
493 江苏长江商业银行
495 柳州银行
496 南充市商业银行
497 莱商银行
498 德阳银行
500 六盘水市商业银行
502 曲靖市商业银行
701 昆仑银行
1401 上海农村商业银行
1402 昆山农村商业银行
1403 江苏常熟农村商业银行
1404 深圳农村商业银行
1405 广州农村商业银行
1406 浙江萧山农村合作银行
1407 广东南海农村商业银行
1408 佛山顺德农村商业银行
1409 昆明市农村信用合作社联合社
1410 湖北省农村信用社联合社
1411 徐州市市郊农村信用合作社联合社
1412 江苏江阴农村商业银行
1413 重庆农村商业银行
1414 山东省农村信用社联合社
1415 东莞农村商业银行
1416 张家港农村商业银行
1417 福建省农村信用社联合社
1418 北京农村商业银行
1419 天津农村合作银行
1420 宁波鄞州农村合作银行
1421 佛山市三水区农村信用合作社联合社
1422 成都市农村信用合作社联合社
1423 沧州市农村信用合作社联合社
1424 江苏省农村信用合作社联合社
1425 江门市新会农村信用合作社联合社
1426 高要市农村信用合作社联合社
1427 佛山农村商业银行
1428 吴江农村商业银行
1429 浙江省农村信用社联合社
1430 江苏东吴农村商业银行
1431 珠海农商银行
1432 中山农村信用合作社联合社
1433 太仓农村商业银行
1434 临汾市尧都市农村信用合作社联合社
1436 贵州省农村信用社联合社
1437 无锡农村商业银行
1438 湖南省农村信用社联合社
1439 江西省农村信用社联合社
1440 甘肃省农村信用社联合社
1441 淄博市商业银行
1442 陕西省农村信用社联合社
1501 江苏银行
1502 邯郸市商业银行
1503 邢台银行
1504 承德银行
1505 沧州银行
1506 晋城市商业银行
1507 鄂尔多斯银行
1508 上饶银行
1509 东营市商业银行
1510 济宁银行
1511 泰安市商业银行
1512 德州银行
1513 开封市商业银行
1514 漯河市商业银行
1515 商丘市商业银行
1516 南阳市商业银行
1517 浙江民泰商业银行
1518 龙江银行
1519 浙江稠州商业银行
1520 安徽省农村信用联社
1521 广西壮族自治区农村信用社联合社
1522 海南省农村信用社联合社
1523 云南省农村信用社联合社
1524 宁夏黄河农村商业银行
1525 安顺市商业银行
1526 安阳市商业银行
1527 保定市商业银行
1528 成都农商银行
1529 广东农村信用社
1530 河北省农村信用社联合社
1531 鹤壁银行
1532 衡水市商业银行
1534 晋中市商业银行
1535 库尔勒市商业银行
1536 乐山市商业银行
1537 凉山州商业银行
1538 内蒙古农村信用社
1539 山西省农村信用社
1540 深圳福田银座村镇银行
1541 遂宁市商业银行
1542 唐山市商业银行
1543 天津滨海农村商业银行
1544 乌海银行
1545 象山县绿叶城市信用社
1546 许昌银行
1547 雅安市商业银行
1548 阳泉市商业银行
1549 宜宾市商业银行
1550 玉溪市商业银行
1551 周口市商业银行
1552 自贡市商业银行
1553 遵义市商业银行
1554 广东华兴银行
1555 长安银行
1556 北京顺义银座村镇银行
1557 浙江景宁银座村镇银行
1558 浙江三门银座村镇银行
1559 江西赣州银座村镇银行
1560 重庆渝北银座村镇银行
1561 重庆黔江银座村镇银行
1562 吉林省农村信用社联合社
1563 浙江省农村信用社联合社
1565 颍淮农村商业银行
1567 山西长子农村商业银行
1569 贵州银行
1570 长治商行
1571 朝阳银行
1572 江苏江南农村商业银行
1573 宁波东海银行
1574 平顶山银行
1575 青海省农村信用社联合社
1576 四川省农村信用合作社
1577 铁岭商业银行
1578 武汉农村商业银行
1579 信阳银行
3000 汇丰银行
3001 东亚银行
3002 南洋商业银行
3003 恒生银行(中国)有限公司
3004 中国银行（香港）有限公司
3005 集友银行有限公司
3006 创兴银行有限公司
3007 星展银行（中国）有限公司
3008 永亨银行（中国）有限公司
3009 永隆银行
3010 花旗银行（中国）有限公司
3011 美国银行有限公司
3012 摩根大通银行(中国)有限公司
3013 三菱东京日联银行(中国）有限公司
3014 日本三井住友银行股份有限公司
3015 瑞穗实业银行（中国）有限公司
3016 日本山口银行股份有限公司
3017 外换银行（中国）有限公司
3018 友利银行(中国)有限公司
3021 韩国产业银行
3022 新韩银行(中国)有限公司
3023 韩国中小企业银行
3024 韩亚银行（中国）有限公司
3025 华侨银行（中国）有限公司
3026 大华银行（中国）有限公司
3028 泰国盘谷银行(大众有限公司)
3029 奥地利中央合作银行股份有限公司
3030 比利时联合银行股份有限公司
3031 比利时富通银行有限公司
3032 荷兰银行
3033 荷兰安智银行股份有限公司
3034 渣打银行
3035 英国苏格兰皇家银行公众有限公司
3036 法国兴业银行（中国)有限公司
3037 法国东方汇理银行股份有限公司
3038 法国外贸银行股份有限公司
3039 德国德累斯登银行股份公司
3040 德意志银行（中国）有限公司
3041 德国商业银行股份有限公司
3042 德国西德银行股份有限公司
3043 德国巴伐利亚州银行
3044 德国北德意志州银行
3045 意大利联合圣保罗银行股份有限公司
3046 瑞士信贷银行股份有限公司
3047 瑞士银行
3048 加拿大丰业银行有限公司
3049 加拿大蒙特利尔银行有限公司
3050 澳大利亚和新西兰银行集团有限公司
3051 摩根士丹利国际银行（中国）有限公司
3052 联合银行(中国)有限公司
3053 荷兰合作银行有限公司
3054 厦门国际银行
3055 法国巴黎银行（中国）有限公司
3056 华商银行
3057 华一银行";
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
            'word' => '动漫少女头像',
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



