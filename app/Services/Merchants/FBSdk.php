<?php
namespace App\Services\Merchants;

use  \App\Models\Mcpay\FbsdkLog;

class FBSdk 
{
    use  FBSdkConstTrait;
    
    /**
     * XML编码
     *
     * @var unknown
     */
    protected $_charset = 'GBK';

    /**
     * 前置机URL
     *
     * @var unknown
     */
    protected $_url = '';

    /**
     * 登陆用户名
     *
     * @var unknown
     */
    protected $_loginName = '';


    /**
     * 
     * @var unknown
     */
    protected $_curlOptions = [];
    
    
    public function __construct($url, $loginName)
    {
        $this->_url = $url;
        $this->_loginName = $loginName;
        if (! $this->_url || ! $this->_loginName) {
            throw new \App\Exceptions\ServiceException('参数缺失[url|loginName]', 60000);
        }
    }
    
    public function setCurlOptions(array $options){
        $this->_curlOptions = $options + $this->_curlOptions;
    }
    
    public function getCurlOptions(){
        return  $this->_curlOptions;
    }
    

    /**
     * 以银行名字获取收款行行号
     *
     * @param unknown $name            
     * @return Ambigous <>|boolean
     */
    public function getBankNoByName($name)
    {
        $bankArray = $this->_CDTBRD;
        if (isset($bankArray[$name])) {
            return $bankArray[$name];
        }
        return false;
    }

    /**
     * 获取登录账户名
     *
     * @return \App\Services\unknown
     */
    public function getLoginName()
    {
        return $this->_loginName;
    }

    /**
     * 设置登录账户名
     *
     * @param unknown $loginName            
     */
    public function setLoginName($loginName)
    {
        $this->_loginName = $loginName;
    }

    /**
     * 设置XML输出
     */
    public function setXmlOutputHeader()
    {
        \header('Content-type:text/xml;charset=' . $this->_charset);
    }

    /**
     * XML字符串转数组，同节点名成数组
     *
     * @param string $xmlStr            
     * @throws \App\Exceptions\ServiceException
     * @return multitype:array multitype:Ambigous <multitype:>
     */
    public function __xmlToArray($xmlStr)
    {
        $xml = simplexml_load_string($xmlStr);
        if ($xml === false) {
            throw new \App\Exceptions\ServiceException('Error Occured When Loading Xml From String');
        }
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
        return $resultArray;
    }

    /**
     * Array 转XML，Root节点为CMBSDKPGK
     *
     * @param array $data            
     * @throws \App\Exceptions\ServiceException
     * @return mixed
     */
    public function __toXml(array $data)
    {
        $xmlStr = '<?xml version="1.0" encoding = "' . $this->_charset . '"?><CMBSDKPGK></CMBSDKPGK>';
        $root = simplexml_load_string($xmlStr);
        foreach ($data as $k => $v) {
            $newChildNode = $root->addChild($k);
            if (is_array($v)) {
                foreach ($v as $k1 => $v1) {
                    if (is_array($v1)) {
                        $parNode = $k1 > 0 ? $root->addChild($k) : $newChildNode;
                        foreach ($v1 as $k2 => $v2) {
                            is_string($v2) && $v2 = htmlentities($v2);
                            $parNode->addChild($k2, $v2);
                        }
                    } else {
                        is_string($v1) && $v1 = htmlentities($v1);
                        $newChildNode->addChild($k1, $v1);
                    }
                }
            } else {
                // TODO : handle exception
                throw new FBSdkException('Array Is Required Here ');
            }
        }
        return $root->asXML();
    }

    
    /**
     * 发出和收到的XML长度超过700，则压缩
     * @param unknown $str
     * @return Ambigous <string, unknown>
     */
    public function __xmlGzipAuto($str){
    
        if(function_exists('mb_detect_encoding')){
            $encoding = mb_detect_encoding($str, array(
                "ASCII",
                'UTF-8',
                "GB2312",
                "GBK",
                'BIG5'
            ));
            if($encoding != 'UTF-8'){
                $iconvStr = iconv($encoding, 'UTF-8', $str);
            }
        }else{
            $iconvStr = iconv('GBK', 'UTF-8', $str);
        }
        if(isset($iconvStr{700})){
            $gzStr = gzdeflate($iconvStr,9);
            $result = base64_encode($gzStr);
        }else{
            $result = $iconvStr;
        }
        return $result ;
    }
    
    /**
     * 发出和收到的XML长度超过700，则压缩
     * @param unknown $str
     * @return Ambigous <string, unknown>
     */
    public function __xmlGzip($str){
        $iconvStr = iconv('GBK', 'UTF-8', $str);
        if(isset($iconvStr{700})){
            $gzStr = gzdeflate($iconvStr,9);
            $result = base64_encode($gzStr);
        }else{
            $result = $iconvStr;
        }
        return $result ;
    }
    
    /**
     * 发送XML报文
     *
     * @param unknown $xmldata            
     * @throws \App\Exceptions\ServiceException
     * @return mixed
     */
    protected function __send($xmlArray,$time = 3)
    {
        $xmldata = $this->__toXml($xmlArray);
        $url = $this->_url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmldata);
        $options = [
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
        ];
        $configOption = $this->getCurlOptions();
        $options = $configOption + $options;
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $FbsdkLog = FbsdkLog::create([
            'func_name' => $xmlArray['INFO']['FUNNAM'],
            'login_name' => $xmlArray['INFO']['LGNNAM'],
            'send_xml' => $this->__xmlGzip($xmldata),
            'send_url' => $url,
            'send_status' => $result === false ? 0 : 1,
            'send_error' => $result === false ? '[' . curl_errno($ch) . ']' . curl_error($ch) : '',
            'received_xml' => $this->__xmlGzip($result)//iconv('GBK', 'UTF-8', $result)
        ]);
        if ($result === false) {
            throw new FBSdkException("REQ ERR[$url]" . curl_error($ch), curl_errno($ch) + 60000);
        }
        curl_close($ch);
        $resultArray = $this->__xmlToArray($result);
        $resultArray['log_id'] = $FbsdkLog['id'];
        return $resultArray;
    }

    /**
     * 调起接口
     *
     * @param unknown $FUNNAM            
     * @param array $params            
     * @return Ambigous <multitype:multitype: , multitype:array multitype:Ambigous <multitype:> >
     */
    protected function invokeApi($FUNNAM, array $params = [])
    {
        $sendArray = [
            'INFO' => [
                'FUNNAM' => $FUNNAM,
                'DATTYP' => '2',
                'LGNNAM' => $this->_loginName
            ]
        ] + $params;
            
        $resultArray = $this->__send($sendArray);
        
        if (($processResult = $this->processResult($resultArray)) === true) {
            return $resultArray;
        } else {
            $this->handleResultError($processResult['ERRMSG'], $processResult['RETCOD']);
        }
        return $resultArray;
    }

    /**
     * 处理接口调用失败
     *
     * @param unknown $message            
     * @param unknown $code            
     * @throws FBSdkException
     */
    protected function handleResultError($message, $code)
    {
        // $message = iconv('GBK', 'UTF-8', $message);
        throw new FBSdkException($message, $code);
    }

    /**
     * 判断接口调用成功与否
     *
     * @param array $result            
     * @return boolean|unknown
     */
    protected function processResult(array $result)
    {
        if ($result['INFO']['RETCOD'] == 0) {
            return true;
        } else {
            return $result['INFO'];
        }
    }
}

class FBSdkException extends \Exception
{

    public function __construct($message, $code = 600)
    {
        parent::__construct($message, $code);
    }
}
