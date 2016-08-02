<?php
namespace AntFinancial\Sdk;

class Sign
{
    
    public static function signFile($plainData, $encapsulate)
    {
        ItrusConfig::getInstance()->init();
        $CertStore = new \JavaClass("cn.topca.api.cert.CertStore");
        $certSet = $CertStore->listAllCerts();
        $certificate = $certSet->get(0);
        $signedData = $certificate->signP7(Util::getBytes($plainData), $encapsulate);
        $base64 = new \JavaClass("org.apache.commons.codec.binary.Base64");
        $result = $base64->encodeBase64($signedData);
        return $result.'';
    }
    
    public static function signToTxt($source, $destination)
    {
        $content = file_get_contents($source);
    
        $data = self::signFile($content, true);
    
        $ret = [
            'file' => $destination.'.txt',
            'full' => storage_path('exports/'.$destination.'.txt'),
            'path' => storage_path('exports'),
        ];
        
        file_put_contents($ret['full'], $data);
        
        return $ret;
    }
    
    public static function signToExcel($source, $destination)
    {
        $content = file_get_contents($source);
        
        $data[0] = self::signFile($content, true);
        
        
        $ret = [
            'file' => $destination.'.xls',
            'full' => storage_path('exports/'.$destination.'.xls'),
            'path' => storage_path('exports'),
        ];
        file_put_contents($ret['full'],  $data[0]);
        return $ret;
        return Util::exportExcelByTemplate($destination.'.xls', $data);
              
        return \Excel::create($destination, function($excel) use($data) {
            $excel->sheet('Sheet1', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->store('xls',false,true);
    }
    
    public static function sign($plainData, $encapsulate)
    {
        ItrusConfig::getInstance()->init();
        
        $CertStore = new \JavaClass("cn.topca.api.cert.CertStore");
        $certSet = $CertStore->listAllCerts();
        $certificate = $certSet->get(0);
        // $string = new \Java("java.lang.String",$plainData);
        // $string->getBytes() string为中文时，签名错误
        $signedData = $certificate->signP7(Util::getBytes($plainData), $encapsulate);
        $base64 = new \JavaClass("org.apache.commons.codec.binary.Base64");
        $result = $base64->encodeBase64String($signedData);
        return $result . '';
    }
}