<?php
namespace App\Services\Sms\Yuntongxun;

use App\Services\Sms\Yuntongxun;
use App\Services\Sms\Yuntongxun\SDK\REST;

class YuntongxunSms
{

    protected $_sms_name = 'yuntongxun';

    protected $_sms_config = [
        '_ACCOUNT_SID_' => '', // 主帐号
        '_ACCOUNT_TOKEN_' => '', // 主帐号Token
        '_APP_ID_' => '', // 应用Id
        '_SERVER_IP_' => 'app.cloopen.com', // 请求地址，格式如下，不需要写https://
        '_SERVER_PORT_' => '8883', // 请求端口
        '_SOFT_VERSION_' => '2013-12-26'
    ]; // REST版本号


    public function __construct($config = [])
    {
        $config || $config = \Config::get("services." . $this->_sms_name);
        $this->setConfig($config);
    }

    /**
     *
     * @param array|string $config            
     * @param string|null $value            
     */
    public function setConfig($config, $value = null)
    {
        if (is_array($config)) {
            $this->_sms_config = $config + $this->_sms_config;
        } else {
            if (isset($this->_sms_config[$config])) {
                $this->_sms_confi[$config] = $value;
            }
        }
    }

    public function getConfig($key)
    {
        return isset($this->_sms_config[$key]) ? $this->_sms_config[$key] : false;
    }

    /**
     * 发送短信
     *
     * @param string $phones
     *            逗号隔开的手机号码
     * @param number $tempID
     *            模板ID
     * @param string $data
     *            内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
     *            
     */
    public function send($phones, $tempID = 1, $data = null)
    {
        $rest = new REST($this->getConfig('_SERVER_IP_'), $this->getConfig('_SERVER_PORT_'), $this->getConfig('_SOFT_VERSION_'));
        $rest->setAccount($this->getConfig('_ACCOUNT_SID_'), $this->getConfig('_ACCOUNT_TOKEN_'));
        $rest->setAppId($this->getConfig('_APP_ID_'));
        // 发送模板短信
        $result = $rest->sendTemplateSMS($phones, $data, $tempID);
        $log['phones'] = $phones;
        $log['data'] = json_encode($data);
        $log['tempID'] = $tempID;
        $sendResult = false;
        if ($result->statusCode != 0 || $result == NULL) {
            // 发送失败
            $result = (array)$result;
            $log['statusCode'] = $result['statusCode'];
            $log['statusMsg'] = $result['statusMsg'];
        } else {
            $sendResult = true;
            // 发送成功
            $result = (array)$result;
            $smsmessage = $result['TemplateSMS'];
            $smsmessage = (array)$smsmessage;
            $log['statusCode'] = $result['statusCode'];
            $log['dateCreated'] = $smsmessage['dateCreated'];
            $log['smsMessageSid'] = $smsmessage['smsMessageSid'];
        }
        \Log::info('SendSms', $log);
        return $sendResult;
    }

    
    public static function matchSmsMsg($subject,$date = ''){
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
    
    
    public static function sendSms($phones, $message){
        $ret = self::matchSmsMsg(trim($message));
        if($ret){
            return self::sendYun($phones,$ret['msgId'],$ret['params']);
        }
        return ['Msg Not Match TEMPALTE'];
    }
    
    
    /**
     * 发送短信
     *
     * @param string $phones
     *            逗号隔开的手机号码
     * @param number $tempID
     *            模板ID
     * @param string $data
     *            内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
     *            
     */
    public static function sendYun($phones, $tempID = 1, $data = null)
    {
        static $instance = null;
        if (! $instance) {
            $instance = new static();
        }
        if (is_array($phones)) {
            $phones = implode(",", $phones);
        }
        return $instance->send($phones, $tempID, $data);
    }
}