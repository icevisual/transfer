<?php

namespace App\Services\MyBank\Sdk;

class Sign {
    
    public static function sign($plainData,$encapsulate){
        
        ConfigTool::getInstance()->init();
        
        $CertStore = new \JavaClass("cn.topca.api.cert.CertStore");
        $certSet = $CertStore->listAllCerts();
        $certificate = $certSet->get(0);
        
        $string = new \Java("java.lang.String",$plainData);
        
        $signedData = $certificate->signP7($string->getBytes(), $encapsulate);
        $base64 = new \JavaClass("org.apache.commons.codec.binary.Base64");
        $result = $base64->encodeBase64String($signedData);
        return $result;
    }
    
}