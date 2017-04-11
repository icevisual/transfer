<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\Transfer::class,
        \App\Console\Commands\IotCommand::class,
        \App\Console\Commands\RabbitMQ::class,
        \App\Console\Commands\RabbitMQCtl::class,
        \App\Console\Commands\AliyunSms::class,
        \App\Console\Commands\LongConnection::class,
        \App\Console\Commands\WebSocketServer::class,
        \App\Console\Commands\ProtoBuf::class,
        \App\Console\Commands\Emqtt::class,
        \App\Console\Commands\CatchCmd::class,
        
        \App\Console\Commands\JsBeautify::class,
        
        \App\Console\Commands\ScanDir::class,
        
        \App\Console\Commands\ImgCatcher::class,
        \App\Console\Commands\DumpStructs::class,
        
        \App\Console\Commands\WordVideo::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
        
        // TODO : check profit cal
        
    }
}
