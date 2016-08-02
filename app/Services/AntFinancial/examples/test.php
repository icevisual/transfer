<?php
namespace AntFinancial\Test;

use AntFinancial\Sdk\Sign;
use DOMDocument;
use RobRichards\XMLSecLibs\XMLSecEnc;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use AntFinancial\Sdk\Util;
use AntFinancial\Sdk\XmlSignUtil;

class test
{

    /**
     * 身份证
     */
    protected $partner = "202210000000000001025";

    /**
     * 身份证
     */
    protected $certNo = "430830198907201280";

    /**
     * 银行卡号
     */
    protected $cardNo = "8888888453427229";

    /**
     * 渠道
     */
    protected $channel = "MYBANK";

    /**
     * 短信验证码
     */
    protected $smsCode = "888888";

    /**
     * 币种
     */
    protected $currencyCode = "156";

    protected $si = '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';

    /**
     * 公钥 RSA
     */
    public static $RSA_publicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvZprNztidrvAObGaomWTe8Ra+VYSIGGVsHZlPV9YKYH2A6pbcFnfk1gf+mI2TPDK0ID/0ET1KxIgsUiHlbqTpCzuoZdWnOhPmDNoCD39LAOrZ6w/DQaVPUCohwGCG6qX7MJ5shSVjr9Vxh79bLNAoK10BdXMUdSoE3we9TSEnf4zCPoMT1Wm6LCaca0m77k12K16IWfsdjE8V0p7IoiCv2AQHPPRlBq0ANIQoKNiwYUVcSgO73NOAXukuNBL42jAYsop8S3HgoNsH2IWgEyseLSqi2VwVRjqPLPpu0/zGBxljT4TVmKd7J8IuaWMtXKc5XBQBqKWotVVsojolK7NuQIDAQAB";

    /**
     * 私钥 RSA
     */
    public static $RSA_privateKey = "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC9mms3O2J2u8A5sZqiZZN7xFr5VhIgYZWwdmU9X1gpgfYDqltwWd+TWB/6YjZM8MrQgP/QRPUrEiCxSIeVupOkLO6hl1ac6E+YM2gIPf0sA6tnrD8NBpU9QKiHAYIbqpfswnmyFJWOv1XGHv1ss0CgrXQF1cxR1KgTfB71NISd/jMI+gxPVabosJpxrSbvuTXYrXohZ+x2MTxXSnsiiIK/YBAc89GUGrQA0hCgo2LBhRVxKA7vc04Be6S40EvjaMBiyinxLceCg2wfYhaATKx4tKqLZXBVGOo8s+m7T/MYHGWNPhNWYp3snwi5pYy1cpzlcFAGopai1VWyiOiUrs25AgMBAAECggEAG0LrrYpVUvsV3XXC5RyzwvTtm7Ibcxp02mV7kwJ1e6pbBBXnhdT2R4pBNtAOPKvWjXouzfRMSAAYQUVLcWTdO5rWSNeotXDVmO2zRJQdJcn1SDfE7QuIQ8FbOeYmnfG+XGVt+APrqRWrsIveJlXzseaeqwQdl5p6/Co5jUoa01hsp1ZilQJBzNHwnAtqiNG4rRNgcsxDp7Ei4CmoYEsNPV0gq5uHeYOJV9kGjggJmWg3Qrk9bI1qKJtXl/hAPzbgZh5mVIBzQ66k8jyQzL3a6RPU2oGxlOu9YlA4eLqIiAXVXyHj1YnljHsfSri+jYDo0Li21BjpVNv6CvO7Wah0sQKBgQD1Usic42Nv1nc5+IqOoC4bgQMqBR3YSoYOG+JuiIeim8os0AcRc6+1PBhwxzGpYaw7ec6P3TIa9kNzecYnHaqEDGSOai4KLdSNsM4bl3YO0IPME2TA8EwIkW7lvJ6qJD+rhkCrPNTlByeUgKfyMCZL8xeEpDVCV9picyiH5AwXnQKBgQDF2tdSlg9s6kt8ZuC5nJxeNmkmnvIt7TdSme9iJ1FG7gc65VtLCIKbkbi3ibfvZgvanrAvH29yShJXrgzvnrok1nFwvbhuWd/jpr8l/StHiaJndzMv7xoHdSmb/7KU+sC7vWLFDjtm8t6BuqkT7QLmmH1D/IdRJc6Bt1rF4OrpzQKBgQDTT9pzoT4uwFp0eczHq9vrXwZdtIiPnSm5j3VMZpgGjhDo5suf0blg8AHRaxMw5mwX0wUFUK/vH37cQeFYIiqVkaMwNO+xXua+obP3elB71Eoih/X6Z0HnA3a1tvIodg7N7VdY6I4tNSt3tBZ1+9mRBDSW0Wb63XfD45Pe8aUk1QKBgGJdSEuQux6E5P52Dyd4Su0z09cVkoVut+BjE3YS4f+HeyS2vkpxcq1xJwpod3+XljEcT689y6RgWvooV9oRaa3CxycryzNhj0OYtNNoKEoqjQkvY81i6+flQciCuQAEIim0IBSj3Lhz6ldIu6JiZNzL1wsj0wpu51nqFEDobyHJAoGBANdbyUnOBSk2r09ZAaL2T4SdH9EHGL2h9XEcRKdc16H0y15dQMRchLWrNpjvZS769pcqgJMsaCa7oNcawaoHsXoIivNBZaG0Y2gS/gBh2yM00ngKvuFayQPSvQjH0ospqLBd7lTPA2fttui1LHXBdhdGkHuUxEf8MEml6lD9CkKf";

    /**
     * 网商银行测试环境公钥 RSA
     */
    public static $bank_RSA_publicKey = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDOb4B1dnwONcW0RoJMa0IOq3O6jiqnTGLUpxEw2xJg+c7wsb6DBy5CAoR0w2ZjZ/BjKxGIQ+DoDg3NsHJeyuEjNF0/Ro/R5xVpFC5z4cBVSC2/gddz4a1EoGDJewML/Iv0yIw7ylB86++h23nRd079c5S9RZXurBfnLW2Srhqk2QIDAQAB";

    private $uid = 17;

    public function test()
    {
        require ('xmlseclibs.php');
        if (file_exists('./firmas/sign-basic-test_mio.xml')) {
            unlink('./firmas/sign-basic-test_mio.xml');
        }
        $doc = new DOMDocument();
        $doc->load('./firmas/results.xml');
        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        $objDSig->addReference($doc, XMLSecurityDSig::SHA1, array(
            'http://www.w3.org/2000/09/xmldsig#enveloped-signature',
            array(
                'http://www.w3.org/TR/1999/REC-xpath-19991116' => array(
                    "query" => "ancestor-or-self::*[local-name()='SolicitudRegistro']"
                )
            )
        ), array(
            "force_uri" => true
        ));
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array(
            'type' => 'private'
        ));
        /* load private key */
        $objKey->loadKey('i.pem', TRUE);
        $objDSig->sign($objKey);
        /* Add associated public key */
        $objDSig->add509Cert(file_get_contents('instancia_imta_ope.crt'));
        $objDSig->appendSignature($doc->documentElement);
        $doc->save('./firmas/sign-basic-test_mio.xml');
        $sign_output = file_get_contents('./firmas/sign-basic-test_mio.xml');
        $sign_output_def = file_get_contents('./firmas/sign-basic-test_mio.res');
        if ($sign_output != $sign_output_def) {
            echo "NOT THE SAME";
        }
        echo "DONE";
    }

    /**
     * sit环境url
     */
    protected $reqUrl = "https://fcsupergw.dl.alipaydev.com/open/api/common/request.htm";

    public static function main1()
    {
        $signString = 'cardNo=8888888453427229||currencyCode=156||cashExCode=CSH';
        $signed = Sign::sign($signString, false);
        edump($signed);
        $function = 'ant.ebank.acount.balance.query';
        
        $form = [
            'function' => $function,
            'reqTime' => '2016-07-09 11:03:10.125',
            'reqMsgId' => CommonUtil::uuid(),
            'cardNo' => HttpsMain::$cardNo,
            'currencyCode' => HttpsMain::$currencyCode,
            'cashExCode' => 'CSH'
        ];
        // signStr => cardNo=8888888453427229||currencyCode=156||cashExCode=CSH
        
        $sign = Sign::sign("cardNo=" . $form['cardNo'] . "||currencyCode=" . $form['cardNo'] . "||cashExCode=" . $form['cardNo'], false);
        $form['sign'] = $sign;
        edump($form);
    }

    public function openssl_csr()
    {
        $config = array(
            "digest_alg" => "sha256",
            "private_key_bits" => 2048
        );
        $dn = array(
            "countryName" => "UK",
            "stateOrProvinceName" => "Somerset",
            "localityName" => "Glastonbury",
            "organizationName" => "The Brain Room Limited",
            "organizationalUnitName" => "PHP Documentation Team",
            "commonName" => "Wez Furlong",
            "emailAddress" => "wez@example.com"
        );
        $privkey = openssl_pkey_new($config);
        $csr = openssl_csr_new($dn, $privkey);
        $sscert = openssl_csr_sign($csr, null, $privkey, 365);
        openssl_x509_export($sscert, $publickey);
        openssl_pkey_export($privkey, $privatekey);
        openssl_csr_export($csr, $csrStr);
        
        $private = base64_encode($privatekey);
        $public = base64_encode($publickey);
        dump(strlen($private));
        dump(strlen($public));
        return [
            'private' => $private,
            'public' => $private
        ];
    }

    public function console($string)
    {
        dump($string);
        \Log::info($string, []);
        
        return $this;
    }

    public function stop()
    {
        exit();
    }

    function signfrompfx($strData, $filePath, $keyPass)
    {
        if (! file_exists($filePath)) {
            return false;
        }
        
        $pkcs12 = file_get_contents($filePath);
        
        if (openssl_pkcs12_read($pkcs12, $certs, $keyPass)) {
            $privateKey = $certs['pkey'];
            
            $publicKey = $certs['cert'];
            $signedMsg = "";
            if (openssl_sign($strData, $signedMsg, $privateKey)) {
                $signedMsg = bin2hex($signedMsg); // 这个看情况。有些不需要转换成16进制，有些需要base64编码。看各个接口
                return $signedMsg;
            } else {
                return '';
            }
        } else {
            return '0';
        }
    }

    /**
     * 公钥cer转pem（即x.509证书dem格式转换为pem）
     *
     * @param unknown $data            
     * @param unknown $signature            
     * @param unknown $filePath            
     * @return number
     */
    function verifyReturn($data, $signature, $filePath)
    {
        /*
         * <br>filePath为crt,cert文件路径。x.509证书
         * cer to dem， Convert .cer to .pem, cURL uses .pem
         */
        $certificateCAcerContent = file_get_contents($filePath);
        $certificateCApemContent = '-----BEGIN CERTIFICATE-----' .
         PHP_EOL . chunk_split(base64_encode($certificateCAcerContent), 64, PHP_EOL) 
        . '-----END CERTIFICATE-----' . PHP_EOL;
    
        $pubkeyid = openssl_get_publickey($certificateCApemContent);
        
        $len = strlen($signature);
        $signature = pack("H" . $len, $signature); // Php-16进制转换为2进制,看情况。有些接口不需要，有些需要base64解码
        
        $data = str_replace('<?xml version=\"1.0\" encoding=\"GBK\"?>', '<?xml version="1.0" encoding="GBK"?>', $data); // 这个看情况。
                                                                                                                        // state whether signature is okay or not
        $ok = openssl_verify($data, $signature, $pubkeyid);
        openssl_free_key($pubkeyid);
        return $ok;
    }

    public function index()
    {
        // $this->verifyXml();
        $function = "ant.ebank.acount.balance.query";
        $data['partner'] = $this->partner;
        $data['reqTime'] = Util::TimestampCurrentTimeMillis();
        $data['reqMsgId'] = Util::randomUUID();
        // reqTime = "2016-07-21 13:23:08.866";
        // reqMsgId = "5c01a394-8c87-45d4-bbec-d52541298fac";
        // $data['reqTime'] = "2016-07-21 13:23:08.866";
        // $data['reqMsgId'] = "5c01a394-8c87-45d4-bbec-d52541298fac";
        
        $data['function'] = $function;
        
        $data['cardNo'] = $this->cardNo;
        $data['currencyCode'] = $this->currencyCode;
        $data['cashExCode'] = 'CSH';
        
        // 业务层签名规则：对除partner、sign本身之外的业务接口字段进行签名，
        // 如已无其他业务接口字段则对空字符串签名；如有则按以下文档中业务字段顺序签名。
        // 如有字段a，值A，字段b，值B，则签名原串为”a=A||b=B”,||为分隔符。
        // 若a字段值为空则签名原串为”a=||b=B”。
        $sign = $this->assemblySignOriginStr(array_except($data, [
            'reqTime',
            'reqMsgId',
            'function',
            'partner'
        ]));
        
        // 业务内容加密
        $data['sign'] = Sign::sign($sign, false);
        
        // $this->console($data['sign']);
        // 封装报文
        $format = $this->format($data, $function);
        // $this->console($format);
        // 报文加密
        
        // XmlSignUtil.sign(param);
        // $xml = $this->signXml($format);
        
        $xml = $this->signXml($format);
        // $xml = XmlSignUtil::sign($format);
        
        $this->console($xml);
        // $xml = str_replace('<?xml version="1.0" encoding="UTF-8"? >', '', $xml);
        // $xml = trim($xml);
        // $this->console($xml);
        // edump($xml);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->reqUrl);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/xml; charset=utf-8",
            "Expect: 100-continue"
        ));
        $res = curl_exec($ch);
        $info['content'] = $format;
        $info['reqmsgid'] = $this->guid();
        $info['created_at'] = time();
        $info['user_id'] = $this->uid;
        $verifyResult = null;
        if ($res{0} == '<') {
            $verifyResult = $this->verifyXml($res);
        }
        \App\Models\Ants\Ants::log($function, $data, $sign, $xml, $res, $verifyResult);
        $this->console($verifyResult);
        
        $this->console($res)->stop();
        
        if ($res['status'] == 200) {
            return true;
        }
        return false;
    }

    /**
     * 业务层签名规则：对除partner、sign本身之外的业务接口字段进行签名，
     * 如已无其他业务接口字段则对空字符串签名；如有则按以下文档中业务字段顺序签名。
     * 如有字段a，值A，字段b，值B，则签名原串为”a=A||b=B”,||为分隔符。
     * 若a字段值为空则签名原串为”a=||b=B”。
     *
     * @param array $data            
     * @return string
     */
    public function assemblySignOriginStr($data)
    {
        foreach ($data as $k => $v) {
            $data[$k] = $k . '=' . trim($v);
        }
        return implode('||', $data);
    }

    public function verify(Request $request)
    {
        return $request;
    }

    private function dateBlankTime()
    {
        return date('Y-m-d H:i:s.ss', time());
    }

    private function format($data, $function)
    {
        $mkXml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><document><request></request></document>');
        $mkXml->request->addAttribute('id', 'request');
        // 报头
        $file = ant_path('xml/head.xml');
        $xml = simplexml_load_file($file);
        $head = $mkXml->request->addChild('head');
        $count = count($xml->xmlTag);
        for ($i = 0; $i < $count; $i ++) {
            $key = $xml->xmlTag[$i]->attributes()->tagName;
            if (in_array($key, array_keys($data))) {
                $val = $data["$key"];
                if ($val == '') {
                    $defaultValue = $xml->xmlTag[$i]->attributes()->defaultValue;
                    if ($defaultValue != '') {
                        $val = $defaultValue;
                    }
                }
                if ($val != null) {
                    $head->addChild($key, $val);
                }
            } else {
                // 如加签业务参数没有在报文中出现，或其值为全空白（即截断两端空白字符后长度为0），则拼接签名要素串时忽略该加签业务要素。
                if (isset($xml->xmlTag[$i]->attributes()->{'defaultValue'})) {
                    $head->addChild($key, $xml->xmlTag[$i]->attributes()->defaultValue);
                }
            }
        }
        
        // 报体
        $file = ant_path('xml/' . $function . '.xml');
        $xml = simplexml_load_file($file);
        $body = $mkXml->request->addChild('body');
        $count = count($xml->xmlTag);
        for ($i = 0; $i < $count; $i ++) {
            $key = $xml->xmlTag[$i]->attributes()->tagName;
            if (in_array($key, array_keys($data))) {
                $val = $data["$key"];
                if ($val == '') {
                    $defaultValue = $xml->xmlTag[$i]->attributes()->defaultValue;
                    if ($defaultValue != '') {
                        $val = $defaultValue;
                    }
                }
                if ($val != null) {
                    $body->addChild($key, $val);
                }
            } else {
                $body->addChild($key, $xml->xmlTag[$i]->attributes()->defaultValue);
            }
        }
        
        return ($mkXml->asXML());
    }

    private function guid()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000); // optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
            
            return $uuid;
        }
    }

    private function sign($sign)
    {
        $pkcs12 = file_get_contents(ant_config_path('yinqizhiliantest.pfx'));
        if (openssl_pkcs12_read($pkcs12, $certs, 'mayibank')) {
            edump($certs);
            if (openssl_sign($sign, $signedMsg, $certs['pkey'], 'sha256WithRSAEncryption')) {
                return $signedMsg = base64_encode($signedMsg);
            }
        }
    }

    private function verifyXml($xml_pay2)
    {
        $doc2 = new DOMDocument();
        $doc2->loadXML($xml_pay2);
        
        $objXMLSecDSig = new XMLSecurityDSig();
        $objDSig = $objXMLSecDSig->locateSignature($doc2);
        
        if (! $objDSig) {
            echo "Cannot locate Signature Node";
            die();
        }
        $objXMLSecDSig->canonicalizeSignedInfo();
        
        // $objXMLSecDSig->idKeys = array(
        // 'wsu:Id'
        // );
        
        // $objXMLSecDSig->idNS = array(
        // 'wsu' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'
        // );
        
        $retVal = $objXMLSecDSig->validateReference();
        if (! $retVal) {
            die("Reference Validation Failed");
        }
        $objKey = $objXMLSecDSig->locateKey();
        if (! $objKey) {
            echo "We have no idea about the key";
            die();
        }
        
        $key = NULL;
        
        $objKeyInfo = XMLSecEnc::staticLocateKeyInfo($objKey, $objDSig);
        $objKey->loadKey(ant_config_path('id_rsa2'), TRUE);
        
        if ($objXMLSecDSig->verify($objKey)) {
            return true;
        } else {
            return false;
        }
    }

    private function verifyXml11()
    {
        $doc = new DOMDocument();
        
        $testFile = ant_config_path('test.xml');
        
        // $content = file_get_contents($testFile);
        
        // file_put_contents($testFile,iconv(detect_encoding($content),'UTF-8',$content));
        $doc->load($testFile);
        $objXMLSecDSig = new XMLSecurityDSig();
        $objDSig = $objXMLSecDSig->locateSignature($doc);
        if (! $objDSig) {
            throw new \Exception("Cannot locate Signature Node");
        }
        $objXMLSecDSig->canonicalizeSignedInfo();
        // $objXMLSecDSig->idKeys = array(
        // 'wsu:Id'
        // );
        // $objXMLSecDSig->idNS = array(
        // 'wsu' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'
        // );
        $retVal = $objXMLSecDSig->validateReference();
        if (! $retVal) {
            throw new \Exception("Reference Validation Failed");
        }
        $objKey = $objXMLSecDSig->locateKey();
        if (! $objKey) {
            throw new Exception("We have no idea about the key");
        }
        $key = NULL;
        $objKeyInfo = XMLSecEnc::staticLocateKeyInfo($objKey, $objDSig);
        if (! $objKeyInfo->key && empty($key)) {
            $objKey->loadKey(ant_config_path('id_rsa2'), TRUE);
        }
        if ($objXMLSecDSig->verify($objKey)) {
            print "Signature validateddd!";
        } else {
            print "Failure!!!!!!!!";
        }
        print "\n";
    }

    private function signXml($format)
    {
        $doc = new DOMDocument();
        $doc->loadXML($format);
        
        // Create a new Security object
        $objDSig = new XMLSecurityDSig();
        // Use the c14n exclusive canonicalization
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        // Sign using SHA-256
        // $doc->getElementsByTagName('request')->item(0)
        $ReferenceDoc = $doc->getElementsByTagName('request')->item(0);
        $objDSig->addReference($doc, XMLSecurityDSig::SHA1, array(
            'http://www.w3.org/2000/09/xmldsig#enveloped-signature'
        ));
        
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array(
            'type' => 'private'
        ));
        // id_rsa_pkcs8.pem exp_pkey.pem
        $objKey->loadKey(ant_config_path('exp_pkey.pem'), true);
        
        $objDSig->sign($objKey);
        
        $objDSig->appendSignature($doc->documentElement->firstChild);
        
        return $doc->saveXML();
    }

    private function rsaVerify($data)
    {
        $pkcs12 = file_get_contents(ant_config_path('yinqizhiliantest.pfx'));
        if (openssl_pkcs12_read($pkcs12, $certs, 'mayibank')) {
            $pubkey = $certs['cert'];
        }
        $result = (bool) openssl_verify($data, $pubkey, base64_decode($this->si), 'sha256WithRSAEncryption');
        return $result;
    }
}