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
    public static function sendSms($phones, $tempID = 1, $data = null)
    {
        static $instance = null;
        if (! $instance) {
            $instance = new static();
        }
        return $instance->send($phones, $tempID, $data);
    }
}