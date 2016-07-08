<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Transfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer {word}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    protected $outputEncoding = 'GBK//IGNORE';
    
    protected $usingEncoding = 'UTF-8';
    

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
    
    public function info($string, $verbosity = null)
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
        $query = $this->argument('word');
        $res = translate($query);
        $this->info(PHP_EOL.$res.PHP_EOL);
    }
}
