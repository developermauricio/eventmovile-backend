<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ProbeEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $activity, $data;

    
    public function __construct($activity, $data)
    {
        $this->activity = $activity;
        $this->data = $data;
    }

    
    public function broadcastOn()
    {
        
        return new Channel('Chat' . $this->activity);
    }
    public function broadcastWith()
    {
        return ['data' => $this->data];
    }
}
