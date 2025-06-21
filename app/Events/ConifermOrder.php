<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConifermOrder
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order_number;
    public $delivery_date;
    public $pharmacy_email;

    /**
     * Create a new event instance.
     */
    public function __construct($order_number, $delivery_date, $pharmacy_email)
    {
        $this->order_number = $order_number;
        $this->delivery_date = $delivery_date;
        $this->pharmacy_email = $pharmacy_email;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
