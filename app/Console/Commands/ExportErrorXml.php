<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class ExportErrorXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export-error-xml {name=其他} {--clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'export error xml';
    
    protected $log = null;
    
    protected $outputEncoding = 'GBK';
    protected $usingEncoding = 'UTF-8';
    
    protected $storagePath = 'exportXml';
    
    public function argument($key = null)
    {
        $result = parent::argument($key);
        if ($result) {
            if(is_array($result)){
                array_walk($result, function($v,$k){
                    return iconv(detect_encoding($v), $this->usingEncoding, $v);
                });
            }else {
                $result = iconv(detect_encoding($result), $this->usingEncoding, $result);
            }
        }
        return $result;
    }
    
    public function info($string)
    {
        $string = iconv(detect_encoding($string), $this->outputEncoding, $string);
        parent::info($string);
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $this->init();
        
        $name = $this->argument('name');
        
        $isClear = $this->option('clear');
        
        if($isClear){
            $this->clearStorage();
        }
        
        $this->exportSpecifiedErrorXml($name);
    }
    
    public function clearStorage(){
        $command = 'rm -fr '.$this->storagePath.DIRECTORY_SEPARATOR.'*';
        $command = str_replace(DIRECTORY_SEPARATOR, '/', $command);
        $this->comment(PHP_EOL.'Exec System Cmd : '.$command.PHP_EOL);
        system($command);
    }
    
    public function getAbsolutePath($filename){
        $DS = DIRECTORY_SEPARATOR;
        return $this->storagePath.$DS.$filename;
    }
    
    protected function init(){

        $this->log = new \App\Services\CLogs('export','export');
        
        $log = $this->log;
        
        $log->showLog();
        
        $this->storagePath = storage_path($this->storagePath);
        if(!file_exists($this->storagePath)){
            mkdir($this->storagePath);
        }
    }

    protected function exportSpecifiedErrorXml($error)
    {
        
        $log = $this->log;
        
        $result = \App\Models\Finance\SalaryPay::select([
            'truename',
            'YURREF',
            'pay_log_id',
            'query_log_id',
            'group_query_log_id',
        ])->where('reason', $error)
        ->get();
        $result && $result = $result->toArray();
        
        $log->info('This error count '.count($result));
        
        
        foreach ($result as $v){
            if($v['group_query_log_id']){
                $this->FBSDK_XML_Export($v['truename'], [$v['pay_log_id'],$v['group_query_log_id'],$v['query_log_id']]);
            }else{
                if($v['pay_log_id'] && $v['query_log_id']){
                    $xmlRequest = \App\Models\Mcpay\FbsdkLog::whereBetween('id',[$v['pay_log_id'],$v['query_log_id']])
                    ->where('func_name','NTQNPEBP')
                    ->get();
                    $xmlRequest && $xmlRequest = $xmlRequest->toArray();
                    $thirdId = '';
                    if(count($xmlRequest) ){
                        if(count($xmlRequest) > 1){
                            foreach ($xmlRequest as $v1){
                                $send_xml = $v1['send_xml'];
                                if($send_xml{0} != '<'){
                                    $send_xml = gzinflate(base64_decode($send_xml));
                                }
                                if(preg_match('<YURREF\>(\w\d+)\<\/YURREF>', $send_xml,$matches)){
                                    $YURREF = $matches[1];
                                    if(substr($v['YURREF'], 0,strlen($YURREF)) == $YURREF ){
                                        $thirdId = $v1['id'];
                                        break;
                                    }
                                }
                            }
                        }else{
                            $thirdId = $xmlRequest[0]['id'];
                        }
                        if($thirdId){
                            $this->FBSDK_XML_Export($v['truename'], [$v['pay_log_id'],$v['query_log_id'],$thirdId]);
                        }
                    }
                }
            }
        }
    }
    
    
    protected function FBSDK_XML_Export($name,array $ids){
        $datas = \App\Models\Mcpay\FbsdkLog::whereIn('id',$ids)->get()->toArray();
        foreach ($datas as $data){
            $func_name = $data['func_name'];
            $send_xml = $data['send_xml'];
            $received_xml = $data['received_xml'];
            if($send_xml{0} != '<'){
                $send_xml = gzinflate(base64_decode($send_xml));
            }
            if($received_xml{0} != '<'){
                $received_xml = gzinflate(base64_decode($received_xml));
            }
            $name = iconv(detect_encoding($name), 'GBK', $name);
            file_put_contents($this->getAbsolutePath($name.''.$func_name.'-RequestXml.xml'), iconv('UTF-8', 'GBK', $send_xml));
            file_put_contents($this->getAbsolutePath($name.''.$func_name.'-RequestXml.xml'), iconv('UTF-8', 'GBK', $received_xml));
        }
    }
    
    
}
