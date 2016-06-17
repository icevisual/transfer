<?php

namespace App\Extensions\Verify;

class Demo implements VerifyCaptchaContract{
    
    use VerifyCaptcha;
    
    public function captcha()
    {
        $Verify = new  Verify([
            'length' => 4,
            'imageW' => 200,
            'imageH' => 50,
        ]);
        $Verify->entry();
    }
    
    public function getLogin()
    {
        $username = Input::get('username');
        $password = Input::get('password');
    
        $data = [
            'need_captcha' => 0,
        ];
        if($this->needCaptcha()){
            $data['need_captcha'] = 1;
        }
    
        if (isset($username) && isset($password)) {
    
            if($this->fobiddenLogin()){
                $data['login_errors'] = '登录过于频繁';
                return Redirect::to('/login')->with($data);
            }
    
            if($this->needCaptcha()){
                $captcha = Input::get('captcha');
                if(!$this->captchaCheck($captcha)){
                    $data['login_errors'] = '验证码不正确！';
                    return Redirect::to('/login')->with($data);
                }
            }
    
            if (\Auth::attempt(array(
                'username' => $username,
                'password' => $password
            ), false)) {
                $this->clearLoginErrorTime();
                $userInfo = \Auth::getUser();
                Fun::add_member_option("login", "", '管理员登录');
                return Redirect::to('/');
            } else {
    
                $this->increaseLoginErrorTime();
                // 输错三次密码，出现验证码
                // 输错五次今日不可登录 对于session ip
                $data['login_errors'] = '用户名或密码不正确！';
    
                return Redirect::to('/login')->with($data);
            }
        } else
            return view('login')->with($data);
    }
    
}