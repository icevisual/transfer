<?php
namespace App\Extensions\Verify;

class VerifyCaptcha
{

    /**
     * 配置项，错误几次出现验证码
     * @var unknown
     */
    const CONFIG_NEED_CAPTCHE = 'error-time-need-captche';
    
    /**
     * 配置项，错误几次禁止登录
     * @var unknown
     */
    const CONFIG_FOBIDDEN_LOGIN = 'error-time-fobidden-login';
    
    /**
     * 配置项，禁止登录时长（秒）
     * @var unknown
     */
    const CONFIG_FOBIDDEN_TIME = 'fobidden-second';
    
    /**
     * 配置项，存储驱动
     * @var unknown
     */
    const CONDIF_STORAGE_DRIVER = 'storage-driver';
    
    /**
     * 配置项，检测模式
     * @var unknown
     */
    const CONFIG_CHECKMODE = 'check-mode';
    
    /**
     * 检测模式，所有登录用户名错误次数累计
     * @var unknown
     */
    const CONFIG_CHECKMODE_ALL = '1';
    
    /**
     * 检测模式，错误次数细化到登录账户
     * @var unknown
     */
    const CONFIG_CHECKMODE_SINGLE = '2';
    
    
    /**
     * 
     * @var unknown
     */
    protected $name = null;
    
    /**
     * 存储信息Driver
     * 
     * @var VerifyStoreInterface
     */
    protected $storageDriver = null;
    
    /**
     * 用户身份标识
     * @var unknown
     */
    protected $identifier = null;
    
    /**
     * 所有失败次数，存储KEY
     * @var unknown
     */
    protected $totalTimesKey = null;
    
    /**
     * 禁止登陆存储KEY
     * @var unknown
     */
    protected $forbiddenKey = null;

    /**
     * 配置
     * 
     * @var array
     */
    protected $_VerifyCaptcheConfig = [
        self::CONFIG_NEED_CAPTCHE => 3, // 错误多少次显示验证码
        self::CONFIG_FOBIDDEN_LOGIN => 10, // 错误多少次禁止登录
        self::CONFIG_FOBIDDEN_TIME => 600, // 禁止登录时间，秒
        self::CONFIG_CHECKMODE => self::CONFIG_CHECKMODE_SINGLE, // 检测模式，1所有登录账号，2独立账号
        self::CONDIF_STORAGE_DRIVER => 'redis' // 存储驱动 redis|session
    ];

    /**
     * 初始化
     * @param unknown $config
     */
    public function __construct($name , $config = []){
        $this->name = $name;
        if(!empty($config)){
            $this->setConfig($config);
        }
        $this->createDriver();
        $this->identifier = $this->getUserIdentifier();
        $this->totalTimesKey =  $this->mix($this->identifier, 'ALL');
        $this->forbiddenKey =  $this->mix($this->identifier, 'forbidden');
    }
    
    
    /**
     * 创建存储Driver
     */
    protected function createDriver()
    {
        $ReflectionObject = new \ReflectionObject($this);
        $class = ucfirst($this->getConfig(self::CONDIF_STORAGE_DRIVER)) . 'StoreHandler';
        $class = $ReflectionObject->getNamespaceName().'\\'.$class;
        $this->storageDriver = new $class();
    }

    /**
     * 设置配置
     * @param unknown $key
     * @param string $value
     */
    public function setConfig($key, $value = null)
    {
        if (is_array($key)) {
            $this->_VerifyCaptcheConfig = $key + $this->_VerifyCaptcheConfig;
        }else{
            $this->_VerifyCaptcheConfig[$key] = $value;
        }
    }

    /**
     * 获取配置
     * @param string $key
     * @return Ambigous <NULL, multitype:>|multitype:
     */
    public function getConfig($key = null)
    {
        if (! is_null($key)) {
            return isset($this->_VerifyCaptcheConfig[$key]) ? $this->_VerifyCaptcheConfig[$key] : null;
        }
        return $this->_VerifyCaptcheConfig;
    }

    
    /**
     * 获取用户唯一标识
     *
     * @return string
     */
    public function getUserIdentifier()
    {
        $ip = \Request::ip() . date('Y-m-d');
        return 'XB-'.$this->name.'-Login-error-' . sha1($ip);
    }
    
    /**
     * 混合KEY
     * @param unknown $key
     * @param unknown $key1
     * @return string
     */
    public function mix($key, $key1)
    {
        return 'XB-'.$this->name.'-error-' . sha1($key . $key1);
    }
    
    /**
     * 获取用户唯一标识
     * 
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    /**
     * 获取所有失败次数KEY
     * @return \App\Extensions\Verify\unknown
     */
    public function getTotalTimesKey()
    {
        return $this->totalTimesKey;
    }
    
    /**
     * 获取禁止登陆存储kEY
     * @param unknown $loginAccount
     * @return \App\Extensions\Verify\unknown|string
     */
    public function getForbiddenKey($loginAccount)
    {
        $mode = $this->getConfig(self::CONFIG_CHECKMODE);
        if (self::CONFIG_CHECKMODE_ALL == $mode) {
            return $this->forbiddenKey;
        } else {
            return $this->mix($this->forbiddenKey, $loginAccount);
        }
    }


    /**
     * 获取存储数据
     * @param unknown $key
     * @param string $callback
     * @return mixed|unknown
     */
    public function getData($key,$callback = null)
    {
        $value = $this->storageDriver->get($key);
        if(!is_null($callback) && function_exists($callback)){
            return call_user_func($callback,$value);
        }
        return $value;
    }

    /**
     * 设置存储数据
     * @param unknown $key
     * @param unknown $value
     */
    public function setData($key, $value)
    {
        return $this->storageDriver->set($key, $value);
    }

    /**
     * 获取错误次数存储的KEY
     * 
     * @return string
     */
    public function getStoreKey($loginAccount)
    {
        $identity = $this->getIdentifier();
        $mode = $this->getConfig(self::CONFIG_CHECKMODE);
        if (self::CONFIG_CHECKMODE_ALL == $mode) {
            //针对所有登录账户
            return $identity;
        } else {
            //针对单个账户
            return $this->mix($identity, $loginAccount);
        }
    }

    /**
     * 增加失败次数
     * @param unknown $loginAccount
     */
    public function increase($loginAccount)
    {
        $timeStoreKey = $this->getStoreKey($loginAccount);
        $totalTimesStoreKey = $this->getTotalTimesKey();
        
        $times = $this->getData($timeStoreKey,'intval');
        $totalErrorTimes = $this->getData($totalTimesStoreKey,'intval');
        
        $this->setData($timeStoreKey,$times + 1);
        $this->setData($totalTimesStoreKey,$totalErrorTimes + 1);
        // 失败次数达到禁止登录的次数
        if ($times + 1 >= $this->getConfig(self::CONFIG_FOBIDDEN_LOGIN)) {
            $this->setFobidden($loginAccount);
        }
        return $lastTimes = $this->getConfig(self::CONFIG_FOBIDDEN_LOGIN) - $times - 1 ;
    }

    /**
     * 清除错误次数
     * @param unknown $loginAccount
     */
    public function clear($loginAccount)
    {
        $key = $this->getStoreKey($loginAccount);
        return $this->storageDriver->del($key);
    }

    /**
     * 显示验证码，以总的错误次数为准
     * 
     * @return boolean
     */
    public function needCaptcha()
    {
        return $this->getData($this->getTotalTimesKey(),'intval') >= $this->getConfig(self::CONFIG_NEED_CAPTCHE);
    }

    /**
     * 设置禁止的登录
     * @param unknown $loginAccount
     */
    public function setFobidden($loginAccount)
    {
        return $this->storageDriver->set($this->getForbiddenKey($loginAccount), time() + $this->getConfig(self::CONFIG_FOBIDDEN_TIME));
    }
    
    /**
     * 获取
     * @return number
     */
    public function getForbiddenTime($unit = 'minute'){
        $div = 1;
        switch ($unit){
            case 'second':break;
            case 'minute':$div = 60;break;
            case 'hour':$div = 3600;break;
            case 'day':$div = 86400;break;
            default:break;
        }
        return ceil($this->getConfig(self::CONFIG_FOBIDDEN_TIME) / $div);
    }

    /**
     * 是否禁止登录
     * @param unknown $loginAccount
     * @return boolean
     */
    public function isFobidden($loginAccount)
    {
        $fobiddenTime = $this->storageDriver->get($this->getForbiddenKey($loginAccount));
        if ($fobiddenTime) {
            if ($fobiddenTime > time()) {
                return true;
            } else {
                $this->storageDriver->del($this->getForbiddenKey($loginAccount));
                $this->clear($loginAccount);
                return false;
            }
        }
        return false;
    }

    /**
     * 检测验证码
     * TODO 是否考虑已禁止
     * @param unknown $captche
     */
    public function checkCaptcha($captche)
    {
        $Verify = new Verify();
        return true;
        return $Verify->check($captche);
    }
}
