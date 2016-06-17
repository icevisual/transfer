<?php

namespace App\Extensions\Verify;

interface VerifyCaptchaContract {
    
    const CONFIG_NEED_CAPTCHE = 'error-time-need-captche';
    
    const CONFIG_FOBIDDEN_LOGIN = 'error-time-fobidden-login';
    
    const CONFIG_FOBIDDEN_TIME = 'fobidden-second';
    
}
