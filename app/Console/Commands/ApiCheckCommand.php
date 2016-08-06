<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ApiCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'pay api check';
    
    protected $FBSdk = null;
    
    protected $log = null;
    
    public function init(){
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        
        $test23Config = [
            'ip_address' => '127.0.0.1',
            'port' => '8080',
            'login_name' => '银企直连专用普通1',
        ];
        $config = $test23Config;
        
        $this->FBSdk = \App\Services\Merchants\FBSdkService::getInstance($config,[
            CURLOPT_TIMEOUT => 15,
        ]);
        
        $this->log = \App\Services\PayLogs::getInstance();
        $instanceId = substr(sha1(microtime()), 0,6);
        
        $this->log->setInstanceId($instanceId);
        
        $this->log->setTypeName('find', 'FRMCOD');
        
//         $this->log->showLog();
    }
    
    public function printLn($str){
        return $this->comment(PHP_EOL.$str.PHP_EOL);
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        dump($this->argument());
        
        return dump('ad');
        $this->init();
        
        $now = time();
        $date = date('Ymd');
        $date = '20150107';
//         '20150811'

        //20150107 find
        $res = [];
        $max = 200;
        $i = 0;
        $step = 1;
        $BGNDAT = date('Ymd',strtotime("-{$step} days",strtotime($date)));
        $ENDDAT = date('Ymd',strtotime($date));
        $log = $this->log;
        while(!isset($res['NTQTSINFZ'])){
            
            $isStop = \LRedis::get('D23STOP');
            if($isStop == 1){
                break;
            }
            $this->printLn("$i . $BGNDAT");
            $log->setInfo('log:',"$BGNDAT");
            $res = $this->FBSdk->D23_GetTransInfo([
                'BBKNBR' => '59',//分行号 N(2) 附录A.1 可 分行号和分行名称不能同时为空
                'C_BBKNBR' => '',// 分s行名称 Z(1,62) 附录A.1 可
                'ACCNBR' => '591902896910604',//账号 C(1,35) 否
                'BGNDAT' => $BGNDAT,//起始日期 D 否
                'ENDDAT' => $BGNDAT,//结束日期 D 否 与结束日期的间隔不能超过100天
                'LOWAMT' => '0',//最小金额 M 可 默认0.00
                'HGHAMT' => '',//最大金额 M 可 默认9999999999999.99
                'AMTCDR' => 'C',//借贷码 C(1) C：收入 D：支出 可
            ]);
           
            
            if(isset($res['NTQTSINFZ'])){
                $this->printLn('Result Count '.count($res['NTQTSINFZ']));
                $log->setInfo('log:','Result Count '.count($res['NTQTSINFZ']));
                $NTQTSINFZ = isset($res['NTQTSINFZ'][0]) ? $res['NTQTSINFZ'] : [$res['NTQTSINFZ']];
                foreach ($NTQTSINFZ as $v){
                    if(isset($v['FRMCOD'])){
                        $log->setInfo('log:',$v);
                        dump('Find It');
                        edump($v);
                    }
                }         
                $res = [];
            }else {
                $this->printLn('Result Count 0');
                $log->setInfo('log:','Result Count 0');
            }
            
            if($i > $max){
                break;
            }
            $ENDDAT = $BGNDAT;
            $BGNDAT = date('Ymd',strtotime("-{$step} days",strtotime($ENDDAT)));
            $i ++;
        }
        $this->printLn("Last Date $BGNDAT -- $ENDDAT");
    }
    

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('bill', InputArgument::OPTIONAL, 'bill_no', null),
            array('date', InputArgument::OPTIONAL, 'date', null),
        );
    }
    
    
    
}
