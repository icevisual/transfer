<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class Apidoc extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apidoc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate api document using apidoc';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        $ApidocAnnGener = new  \App\Services\Adag\ApidocAnnGener();
        $ApidocAnnGener->run();
        $runOutput = base_path('doc'.DS.'apidocRunOutput.txt');
        system('apidoc -i doc/ -o public/doc/ > '.$runOutput, $return_var);
        $c = file_get_contents($runOutput);
        dump($c);
        $this->comment(PHP_EOL . '--END--' . PHP_EOL);
    }
    
}
