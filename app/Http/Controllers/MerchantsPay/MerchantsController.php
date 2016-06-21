<?php
namespace App\Http\Controllers\MerchantsPay;

use App\Http\Controllers\BaseController;
use App\Services\Merchants\FBSdkService;
use function Symfony\Component\Debug\header;

class MerchantsController extends BaseController{
    
    
    protected $FBSdk = null;
    
    public function index(){
        $data = \Input::getContent();
        
        if($data){
            $this->FBSdk = FBSdkService::getInstance();
            
            $xmlArray = $this->FBSdk->__xmlToArray($data);
            
            $reqFunc = $xmlArray['INFO']['FUNNAM'];
            
            return call_user_func([$this,$reqFunc],$xmlArray);
        }else{
            return $this->emptyContext();
        }
    }
    
    protected function outputXml($str){
        $xml = simplexml_load_string($str);
        \header("Content-type: text/xml");
        exit($xml->saveXML());
    }
    
    protected function emptyContext(){
        $str = '<?xml version="1.0" encoding="GBK"?>
<CMBSDKPGK>
    <INFO>
        <ERRMSG>SDKM037 XML报文为空!</ERRMSG>
        <RETCOD>-1</RETCOD>
    </INFO>
</CMBSDKPGK>';
        return $this->outputXml($str);
    }
    
    protected function ListMode($xmlArray){
        
        $str = '<?xml version="1.0" encoding="GBK"?>
<CMBSDKPGK>
    <INFO>
        <DATTYP>2</DATTYP>
        <ERRMSG></ERRMSG>
        <FUNNAM>ListMode</FUNNAM>
        <LGNNAM>王勇W</LGNNAM>
        <RETCOD>0</RETCOD>
    </INFO>
    <NTQMDLSTZ>
        <BUSMOD>00002</BUSMOD>
        <MODALS>2</MODALS>
    </NTQMDLSTZ>
</CMBSDKPGK>';
        return $this->outputXml($str);
    }
    
    // 2  网银贷记-请求
    protected function NTIBCOPR($xmlArray){
        
        $data = $xmlArray['NTIBCOPRX'];
        
        if(!isset($data[0])){
            $data = [$data];
        }

        //         <SQRNBR>000000000</SQRNBR>
        //         <ACCNBR>571907650010808</ACCNBR>
        //         <YURREF>I16041910210011960000000</YURREF>
        //         <TRSAMT>29953.92</TRSAMT>
        //         <CDTNAM>武长芳</CDTNAM>
        //         <CDTEAC>6222810300480168</CDTEAC>
        
        
        foreach ($data as $v){
            
            
        }
        
        
        header("Content-type:text/xml;charset=GBK");
        $str = '<?xml version="1.0" encoding="GBK"?>
<CMBSDKPGK>
    <INFO>
        <DATTYP>2</DATTYP>
        <ERRMSG></ERRMSG>
        <FUNNAM>NTIBCOPR</FUNNAM>
        <LGNNAM>王勇W</LGNNAM>
        <RETCOD>0</RETCOD>
    </INFO>
    <NTOPRDRTZ>
        <RTNTIM>006</RTNTIM>
    </NTOPRDRTZ>
    <NTOPRDRTZ>
        <RTNTIM>006</RTNTIM>
    </NTOPRDRTZ>
    <NTOPRRTNZ>
        <ERRCOD>SUC0000</ERRCOD>
        <REQNBR>0545742875</REQNBR>
        <REQSTS>BNK</REQSTS>
        <SQRNBR>000000028</SQRNBR>
    </NTOPRRTNZ>
    <NTOPRRTNZ>
        <ERRCOD>SUC0000</ERRCOD>
        <REQNBR>0545742877</REQNBR>
        <REQSTS>BNK</REQSTS>
        <SQRNBR>000000029</SQRNBR>
    </NTOPRRTNZ>
</CMBSDKPGK>';
        echo $str;
    }
    
    // 1.3业务交易明细查询 
    protected function NTEBPINF(){
        
    }
    
    // 1.4业务总揽查询 
    protected function NTQNPEBP(){
    
    }
    
    
}



