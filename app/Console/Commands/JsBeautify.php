<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use App\Gather\Utils\JsBeautify AS JsFormat;

class JsBeautify extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'js';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'JsBeautify';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $JsBeautify = new JsFormat();
        
        $content = file_get_contents(public_path('Core.js'));
        
        $c =  $JsBeautify->js_beautify($content);
        
        
        file_put_contents(public_path('DevMsghandler1.js'),$c);
        
//         dump($c);
    }
    
}
