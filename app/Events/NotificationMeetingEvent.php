<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NotificationMeetingEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId, $data;

    public function __construct($user,$data)
    {
        $this->userId = $user;
        $this->data = $data;
    }

    public function broadcastOn()
    { 
        return new Channel('User' . $this->userId);
    }
    public function broadcastWith()
    {
        return ['data' => $this->data];
    }
}
