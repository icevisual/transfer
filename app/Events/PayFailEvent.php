<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PayFailEvent extends Event
{
    use SerializesModels;
    
    
    protected $condition = [];
    
    public function getCondition(){
        return $this->condition;
    }
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
