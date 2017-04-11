<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WordVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'WordVideo';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->translate('dedicate');
        // 读取 文件 分析获取 单词
        // 接口查询、获取
        // 
    }
    
    public function translate($query){
        $res = curl_post('http://fanyi.baidu.com/v2transapi', [
            'from' => 'en',
            'to' => 'zh',
            'query' => $query,
            'transtype' => 'realtime',
            'simple_means_flag' => '3',
        ]);
        dd($res);
        $result = [];
        $data = $res;// json_decode($res,1);
        $resStr = '';
        if(isset($data['dict_result']['simple_means']['symbols'][0])){
            $symbols = $data['dict_result']['simple_means']['symbols'][0];
            $result['[En]'] = '['.$symbols['ph_en'].' ]';
            $result['[Am]'] = '['.$symbols['ph_am'].' ]';
            //echo -e "\e[1;31m skyapp exist \e[0m"
            $resStr .= PHP_EOL." [\e[1;31m{$query}\e[0m ]".PHP_EOL;
            if ($symbols['ph_en'])
                $resStr .=   ' '."【英】[{$symbols['ph_en']} ],【美】[{$symbols['ph_am']} ]".PHP_EOL;
            foreach ($symbols['parts'] as $k => $v){
                $result['means'] [$k] = $v['part'];
                foreach ($v['means'] as $k1 => $v1){
                    $result['means'] [$k].= ($k1 ? ",":'').$v1;
                }
                $resStr .= ' '.$result['means'] [$k].PHP_EOL;
            }
            $resStr .= PHP_EOL;
        }
        return $resStr;
    }
    
    
}
