<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class CancelledOrder
{
    use Dispatchable, SerializesModels;

    public $order;
    public $supplier;
    public $pharmacyEmail;
    public $cancellationReason;
    public $cancelledAt;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, $supplier, $pharmacyEmail, $cancellationReason = null)
    {
        $this->order = $order;
        $this->supplier = $supplier;
        $this->pharmacyEmail = $pharmacyEmail;
        $this->cancellationReason = $cancellationReason;
        $this->cancelledAt = now();
    }
}
