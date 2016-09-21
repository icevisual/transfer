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
        
        $this->comment ( PHP_EOL. '--END--' . Inspiring::quote () . PHP_EOL );
	}
}




