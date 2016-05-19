<?php


namespace App\Services\Email;

class AliyunEmail {
    
    protected $_url = 'http://dm.aliyuncs.com';
    
    protected $_method = 'GET';
    
    protected $AccessKeySecret = '';
    
    protected $AccessKeyId = '';
    
    protected $AccountName = '';
    
    protected $errno = '';
    
    protected $error = '';
    
    protected  $_config = [
        'Format' => 'JSON' ,// String	否	返回值的类型，支持JSON与XML。默认为XML
        'Version' => '2015-11-23' ,// String	是	API版本号，为日期形式：YYYY-MM-DD，本版本对应为2015-11-23
        'SignatureMethod' => 'HMAC-SHA1' ,// String	是	签名方式，目前支持HMAC-SHA1
        'SignatureVersion' => '1.0' ,// String	是	签名算法版本，目前版本是1.0
    ];
    
    public function __construct($AccountName,$AccessKeyId,$AccessKeySecret){
        $this->setAccessKeySecret($AccessKeySecret);
        $this->setAccessKeyId($AccessKeyId);
        $this->setAccountName($AccountName);
    }
    
    public function setAccessKeySecret($AccessKeySecret){
        $this->AccessKeySecret = $AccessKeySecret;
    }
    
    public function getAccessKeySecret(){
        return $this->AccessKeySecret;
    }
    
    public function setAccessKeyId($AccessKeyId){
        $this->AccessKeyId = $AccessKeyId;
    }
    
    public function getAccessKeyId(){
        return $this->AccessKeyId;
    }
    
    public function setAccountName($AccountName){
        $this->AccountName = $AccountName;
    }
    
    public function getAccountName(){
        return $this->AccountName;
    }
    
    public function getMethod(){
        return $this->_method;
    }
    
    public function getDefaultParams(){
        
        $this->_config['AccessKeyId'] = $this->getAccessKeyId();
        
        return $this->_config;
    }
    
    public function createSignatureNonce($len = 18,$format = 'CHAR'){
        
        switch ($format) {
            case 'ALL':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
                break;
            case 'CHAR':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
                break;
            case 'NUMBER':
                $chars = '0123456789';
                break;
            default:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }
        // mt_srand ( ( double ) microtime () * 1000000 * getmypid () );
        $password = "";
        while (strlen($password) < $len)
            $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
        return $password;
    }
    
    public function curl($url, array $data)
    {
        $ch = curl_init();
        $requestUrl = $url . "/?";
        foreach ($data as $apiParamKey => $apiParamValue)
        {
            $requestUrl .= "$apiParamKey=" . urlencode($apiParamValue). "&";
        }
        $url = substr($requestUrl, 0, -1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 设置超时
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 跟踪301
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 返回结果
        $result = curl_exec($ch);
        $this->errno = curl_errno($ch);
        $this->error = curl_error($ch);
        curl_close($ch);
        return  json_decode($result, 1) ;
    }
    
    public function getUTCDate(){
        date_default_timezone_set("GMT");
        $data = date('Y-m-d\TH:i:s\Z');
        date_default_timezone_set("PRC");
        return $data;
    }
    
    protected function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
    
    public function data_process($reqData){
        $sendData = $this->getDefaultParams();
        $sendData['Timestamp'] = $this->getUTCDate();
        $sendData['SignatureNonce'] = $this->createSignatureNonce();
        $sendData += $reqData;
        ksort($sendData);
        $canonicalizedQueryString = '';
        foreach($sendData as $key => $value)
        {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key). '=' . $this->percentEncode($value);
        }
        $StringToSign = $this->getMethod().'&'.$this->percentEncode('/').'&'.$this->percentEncode(substr($canonicalizedQueryString, 1));
        $Sha1Key = $this->getAccessKeySecret().'&';
        $HMAC = hash_hmac('sha1',$StringToSign,$Sha1Key,true);
        $sendData['Signature'] = base64_encode($HMAC);
        return $sendData;
    }
    
    public function __result_handle($result){
        if(isset($result['Message'])  && isset($result['Code'])){
            return false;
        }else{
            return true;
        }
    }
    
    public function __send($reqData){
        $sendData = $this->data_process($reqData);
        $result = $this->curl($this->_url,$sendData);
        return $result;
    }
    
    /**
     * 发送单个邮件
     * @param unknown $ToAddress
     *  目标地址
     * @param unknown $Subject
     *  标题
     * @param unknown $Content
     *  内容
     * @return mixed
     */
    public function SingleSendMail($ToAddress,$Subject,$Content){
        $param = [
        	'Action' => 'SingleSendMail' ,// String	是	操作接口名，系统规定参数，取值：SingleSendMail
            'AccountName' => $this->getAccountName() ,// String	必须	管理控制台中配置的发信地址
            'ReplyToAddress' => 'false' ,// Boolean	必须	是否使用管理控制台中配置的回信地址（状态必须是验证通过）
            'AddressType' => '1' ,// Number	必须	取值范围0~1: 0为随机账号(推荐,可以更好的统计退信情况);1为发信地址
            'ToAddress' => $ToAddress ,// String	必须	目标地址，多个Email地址可以逗号分隔
            'FromAlias' => '' ,// String	可选	发信人昵称,长度小于15个字符 例如:发信人昵称设置为"小红"，发信地址为"test@example.com"，收信人看到的发信地址为"小红"
            'Subject' => $Subject ,// String	必须	邮件主题
            'HtmlBody' => $Content ,// String	可选	邮件html正文
            'TextBody' => '' ,// String	可选	邮件text正文
        ];
        return $this->__send($param);
    }
    
}
