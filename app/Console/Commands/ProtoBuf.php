<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Models\Common\Bill;

class ProtoBuf extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ProtoBuf Command';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $str = 0xedb88320;
        // // $str = 'saddas';
        // $str = crc32($str);
        // printf("%x\n",$str);
        // exit;
        
        
        
        // dump(time());
        // dump( time() - strtotime('2015-01-01'));
        
        // dump( dechex(time()));
        // dump( dechex(20161212121212));
        
        $arr = [
            'f','ff','ffff','ffffffff'
        ];

        foreach ($arr as $key => $value) {
            $t = ceil(strlen($value)/4);
            $tr = '';
            $t = 2- $t + 1;
            while ($t --) $tr.="\t";
            echo($value."$tr  >> ".hexdec($value)).PHP_EOL;
        }

        // dump('f'. hexdec('f'));
        // dump( hexdec('ff'));
        // dump( hexdec('ffff'));
        // dump( hexdec('ffffffff').'');
        
//         echo "output: " . chr(0xe4) . chr(0xb8) . chr(0xad) . "\n";
        
        exit;
        
        
        $Test = new \Proto2\Test();
        $Test->setId(123);
        $Test->setEmail('12312132@qq.com');
        $Test->setName('123123312');
        $packed = $Test->serializeToString();
        
        try {
            $parsedFoo = new \Proto2\Test();
            $parsedFoo->parseFromString($packed);
        } catch (Exception $ex) {
            die('Oops.. there is a bug in this example, ' . $ex->getMessage());
        }
        var_dump($packed);
        $parsedFoo->dump();
        
        $this->comment(PHP_EOL . '--END--' . Inspiring::quote() . PHP_EOL);
    }
}




