<?php
namespace AntFinancial;

use AntFinancial\Sdk\Sign;
use DOMDocument;
use AntFinancial\Sdk\HttpsMain;
use RobRichards\XMLSecLibs\XMLSecEnc;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use AntFinancial\Sdk\Util;
use AntFinancial\Sdk\XmlSignUtil;
use AntFinancial\Exceptions\AntXmlFormatException;
use AntFinancial\Exceptions\AntSendException;
use AntFinancial\Exceptions\AntVerifyException;
use AntFinancial\Exceptions\AntException;

abstract class BaseApi implements IApi
{
    /**
     * 请求的方法名
     * 
     * @var unknown
     */
    protected $function = 'ant.ebank.acount.balance.query';
    
    /**
     * 参数验证规则
     * 
     * @var unknown
     */
    protected $parameterValidate = [
        'cardNo' => '',
        'currencyCode' => '',
        'cashExCode' => ''
    ];
    
    protected $reqMsgId = null;

    /**
     * {@inheritdoc}
     */
    public function validateParameters(array $data)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run($params)
    {
        // TODO : mt 冲突
        mt_mark('start');
        $data['function'] = $this->function;
        $data['appid'] = $this->getAppid();
        $data['partner'] = $this->getPartner();
        $data['reqTime'] = $this->getReqTime();
        $data['reqMsgId'] = $this->getReqMsgId();
        
        // 参数验证
        if (true === $this->validateParameters($params)) {
            $data = array_merge($data, array_only($params, array_keys($this->parameterValidate)));
        }else{
            
        }
        // 生成业务加密字符串
        $signOriginString = $this->assemblySignOriginString($data);
        // 业务内容加密
        $data['sign'] = $this->signParameters($signOriginString);
        // 封装报文
        $format = $this->formatToXml($data);
        // 报文加密
        $xml = $this->signXml($format);
        
//         $this->console($xml);
        
//         $this->bankPublicKeyFile = 'XmlSignUtil_public_key.pem';
// //         $this->bankPublicKeyFile = 'exp_cert.pem';
//         $res = $this->verifyXml($xml);
//         $this->console($res)->stop();

        $recordData = [
            'function' => $data['function'],
            'req_time' => $data['reqTime'],
            'req_msg_id' => $data['reqMsgId'],
            'sign_string' => $signOriginString,
            'send_xml' => $xml,
            'received_xml' => '',
            'send_status' => false,
            'send_error' => '',
            'verify_status' => false,
            'verify_error' => '',
            'time_usage' => 0,
            'memory_usage' => 0,
            'result_status' => '',
            'result_code' => '',
            'result_msg' => '',
        ];
//         verify_status
        $return = false;
        try {
            // 发送报文
            $response = $this->send($xml);
            $recordData['send_status'] = true;
        } catch (AntSendException $e) {
            $recordData['send_error'] = $e->getMessage();
            $return = 'send_error';
        }
        
        if($recordData['send_status']){
            $recordData ['received_xml'] = $response;
            if($this->isXml($response)){
                try {
                    $recordData['verify_status'] = $this->verifyXml($response);
                }catch(AntVerifyException $e){
                    $recordData['verify_error'] = $e->getMessage();
                    $return = 'verify_error';
                }
                // 验签报文
            }else{
                $recordData['verify_error'] = $response;
            }
        }
        $resultStatus = false;
        if($recordData['verify_status']){
            
            $responseArray = $this->xmlToArray($response);
            
            $resultInfo = $this->getResultInfo($responseArray);
            
            $return = $resultInfo;
            $recordData['result_status'] = $resultInfo['resultStatus'];
            $recordData['result_code'] = $resultInfo['resultCode'];
            $recordData['result_msg'] = $resultInfo['resultMsg'];
            
            if($this->judgeResult($resultInfo)){
                $return = $this->getResultContent($responseArray);
                $resultStatus = true;
            }
        }
        $mt = mt_mark('start','end','MB',8);
        mt_mark('[clear]');
        $recordData['time_usage'] = $mt['t'];
        $recordData['memory_usage'] = $mt['m'];
        $this->recordRequest($recordData);
        return $resultStatus ? $this->resultFormat($return) : $return;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getReqUrl(){
        return HttpsMain::getReqUrl();
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function base64JsonDecode($data){
        $dataJson = base64_decode($data);
        $dataJsonArray = json_decode($dataJson,true);
        return $dataJsonArray;
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function resultFormat($return){
        return $return;
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function getAppid(){
        return HttpsMain::getAppid();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPartner(){
        return HttpsMain::getPartner();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getResultContent($responseArray){
        $return = array_get($responseArray,'document.response.body');
        unset($return['resultInfo']);
        return $return;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getResultInfo($responseArray){
        return array_get($responseArray, 'document.response.body.resultInfo');
    }
    
    /**
     * {@inheritdoc}
     */
    public function judgeResult($resultInfo){
        return 'S' == array_get($resultInfo,'resultStatus');
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function getReqTime(){
        return date('Y-m-dHis');
    }
    
    /**
     * {@inheritdoc}
     */
    public function setReqMsgId($msgId){
        $this->reqMsgId = $msgId;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getReqMsgId(){
        if(!$this->reqMsgId){
            $this->reqMsgId = Util::uuid();
        }
        return $this->reqMsgId;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isXml($string){
        return $string{0} == '<';
    }
    
    /**
     * {@inheritdoc}
     */
    public function xmlToArray($xmlStr)
    {
        $xml = simplexml_load_string($xmlStr);
      
        $stack = [
            $xml->getName() => $xml
        ];
        
        $data = [];
        $prefix = $xml->getName();
        // 先序
        while (! empty($stack)) {
            $prefix = array_keys($stack);
            $prefix = end($prefix);
            $node = array_pop($stack);
            // Visit Node
            if (!$node->count()) {
                array_set($data, $prefix, $node->__toString());
            }
            
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
        return $data;
    }
    

    /**
     * {@inheritdoc}
     */
    public function assemblySignOriginString(array $data)
    {
        $data = array_except($data, [
            'reqTime',
            'reqMsgId',
            'function',
            'partner',
            'appid'
        ]);
        foreach ($data as $k => $v) {
            $data[$k] = $k . '=' . trim($v);
        }
        return implode('||', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function signParameters($originString)
    {
        return Sign::sign($originString, false);
    }

    /**
     * {@inheritdoc}
     */
    public function formatToXml(array $data)
    {
        $function = $data['function'];
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
    
    
    /**
     * 获取我方私钥文件名
     */
    public function getPrivateKeyFile(){
        return HttpsMain::getPrivateKeyFile();
    }
    
    /**
     * 获取网商公钥
     */
    public function getBankPublicKeyFile(){
        return HttpsMain::getBankPublicKeyFile();
    }
    

    /**
     * {@inheritdoc}
     */
    public function signXml($xmlStr)
    {
        $doc = new DOMDocument();
        $doc->loadXML($xmlStr);
        // Create a new Security object
        $objDSig = new XMLSecurityDSig();
        // Use the c14n exclusive canonicalization
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        // Sign using SHA-256
        // $doc->getElementsByTagName('request')->item(0)
        $ReferenceDoc = $doc->getElementsByTagName('request')->item(0);
        // 加入  "force_uri" => true 后网商验签成功，<ds:Reference> TO  <ds:Reference URI="">
        $objDSig->addReference($doc, XMLSecurityDSig::SHA1, array(
            'http://www.w3.org/2000/09/xmldsig#enveloped-signature',
        ), array(
            "force_uri" => true
        ));
        
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array(
            'type' => 'private'
        ));
        // id_rsa_pkcs8.pem
        $objKey->loadKey(ant_config_path($this->getPrivateKeyFile()), true);
        
        $objDSig->sign($objKey);
        
        $objDSig->appendSignature($doc->documentElement);
        
        return $doc->saveXML($doc,LIBXML_NOEMPTYTAG  );
    }

    /**
     * {@inheritdoc}
     */
    public function verifyXml($xmlStr)
    {
        $doc2 = new DOMDocument();
        $doc2->loadXML($xmlStr);
        
        $objXMLSecDSig = new XMLSecurityDSig();
        $objDSig = $objXMLSecDSig->locateSignature($doc2);
        
        if (! $objDSig) {
            throw new AntVerifyException('Cannot locate Signature Node');
        }
        $objXMLSecDSig->canonicalizeSignedInfo();
        
        $retVal = $objXMLSecDSig->validateReference();
        if (! $retVal) {
            throw new AntVerifyException('Reference Validation Failed');
        }
        $objKey = $objXMLSecDSig->locateKey();
        if (! $objKey) {
            throw new AntVerifyException('We have no idea about the key');
        }
        
        $objKeyInfo = XMLSecEnc::staticLocateKeyInfo($objKey, $objDSig);
        $objKey->loadKey(ant_config_path($this->getBankPublicKeyFile()), TRUE);
        
        if (!$objXMLSecDSig->verify($objKey)) {
            throw new AntVerifyException('Verify Failed');
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function send($xmlStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getReqUrl());
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/xml; charset=utf-8",
            "Expect: 100-continue"
        ));
        $result = curl_exec($ch);
        if ($result === false) {
            throw new AntSendException(curl_error($ch), curl_errno($ch));
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function console($string)
    {
        static $init = false;
        
        if(!$init){
//             echo '<style>body{background-color:#18171B;}</style>';
            $init = true;
        }
        $message = $string;
        $context = [];
        if(is_array($string)){
            $message = '$string';
            $context = $string;
            dump($context);
        }else{
            dump($string);
        }
        \Log::info($message, $context);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        exit();
    }

    /**
     * {@inheritdoc}
     */
    public function recordRequest($recordData)
    {
        \App\Models\Ants\Ants::log($recordData);
    }
}