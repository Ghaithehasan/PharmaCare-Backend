<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderEmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;


    public array $medicines;

    /**
     * إنشاء حدث جديد
     *
     * @param Order $order بيانات الطلبية
     * @param array $medicines قائمة الأدوية
     */
    public function __construct(Order $order, array $medicines)
    {
        $this->order = $order;
        $this->medicines = $medicines;
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
