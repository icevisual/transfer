<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushMessage extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $uid;
    public $message;
    public $extras;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uid,$message,$extras = [])
    {
        //
        $this->uid = $uid;
        $this->message = $message;
        $this->extras = $extras;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $date = date('H:i:s');
        \LRedis::SETEX('PUSH-'.$date.'-'.$this->uid,300,$this->message);
//         pushMessage($this->uid, $this->message,$this->extras);
    }
}
