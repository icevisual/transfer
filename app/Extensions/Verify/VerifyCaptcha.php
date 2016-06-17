<?php
namespace App\Extensions\Verify;


trait VerifyCaptcha
{


    protected $_VerifyCaptcheConfig = [
        self::CONFIG_NEED_CAPTCHE => 3,
        self::CONFIG_FOBIDDEN_LOGIN => 10,
        self::CONFIG_FOBIDDEN_TIME => 86400
    ];

    public function setVCConfig($key, $value = null)
    {
        if (is_array($key)) {
            $this->_VerifyCaptcheConfig += $key;
        }
        $this->_VerifyCaptcheConfig[$key] = $value;
    }

    public function getVCConfig($key = null)
    {
        if (! is_null($key)) {
            return isset($this->_VerifyCaptcheConfig[$key]) ? $this->_VerifyCaptcheConfig[$key] : null;
        }
        return $this->_VerifyCaptcheConfig;
    }

    
    public function getIdentifier()
    {
        $ip = \Request::ip().date('Y-m-d');
        return sha1($ip);
    }

    public function getLoginErrorTime()
    {
        $identity = $this->getIdentifier();
        return $count = session($identity);
    }

    public function setLoginErrorTime($times)
    {
        $identity = $this->getIdentifier();
        session([
            $identity => $times
        ]);
    }

    public function increaseLoginErrorTime()
    {
        $times = $this->getLoginErrorTime();
        $this->setLoginErrorTime($times + 1);
        if($times + 1 >= $this->getVCConfig(self::CONFIG_FOBIDDEN_LOGIN)){
            $this->setLoginFobidden();
        }
    }

    public function isLoginErrorTimeMoreThen($times = 3)
    {
        return $this->getLoginErrorTime() >= $times;
    }

    public function clearLoginErrorTime()
    {
        $identity = $this->getIdentifier();
        session([
            $identity => null
        ]);
    }

    public function needCaptcha()
    {
        return $this->isLoginErrorTimeMoreThen($this->getVCConfig(self::CONFIG_NEED_CAPTCHE));
    }

    public function setLoginFobidden(){
        session(['fobidden' => time() + $this->getVCConfig(self::CONFIG_FOBIDDEN_TIME)]);
    }
    
    public function fobiddenLogin()
    {
        $fobiddenTime = session('fobidden');
        if($fobiddenTime){
            if($fobiddenTime > time()) {
                return true;
            }else{
                session(['fobidden' => null]);
                $this->clearLoginErrorTime();
                return false;
            }
        }
        return false;
    }

    public function captchaCheck($captche)
    {
        $Verify = new Verify();
        return $Verify->check($captche);
    }

}
