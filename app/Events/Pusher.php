<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Medicine;

class Pusher implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

     public $medicine;
    public function __construct(Medicine $medicine)
    {
        // dd();

        $this->medicine=$medicine;
    }


    public function broadcastOn()
    {
        return 
            new Channel('low-stock');
    }

    public function broadcastAs()
    {
        // dd();

        return 'low-stock-event';
    }
}
