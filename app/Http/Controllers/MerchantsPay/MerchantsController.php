<?php
namespace App\Http\Controllers\MerchantsPay;

use App\Http\Controllers\BaseController;
use App\Services\Merchants\FBSdkService;

class MerchantsController extends BaseController{
    
    
    protected $FBSdk = null;
    
    public function index(){
        $data = \Input::getContent();
        
        $this->FBSdk = FBSdkService::getInstance();
        
        $xmlArray = $this->FBSdk->__xmlToArray($data);
        
        $reqFunc = $xmlArray['INFO']['FUNNAM'];
//         return $this->NTIBCOPR();
        file_put_contents('output.txt',  var_export($xmlArray,true));
    }
    
    // 2  网银贷记-请求
    protected function NTIBCOPR($xmlArray){
        
        $data = $xmlArray['NTIBCOPRX'];
        
        if(!isset($data[0])){
            $data = [$data];
        }
        
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



