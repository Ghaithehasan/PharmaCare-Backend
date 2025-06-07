<?php

namespace App\Listeners;

use App\Events\StockCheck;
use App\Mail\LowStockAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class LowStockNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StockCheck $event): void
    {
        $medicine = $event->medicine;
        
        // التحقق فقط من الدواء الذي تم تحديثه
        if ($medicine->quantity <= $medicine->alert_quantity) {
            Mail::to('matrex663@gmail.com')
                ->queue(new LowStockAlert($medicine));
        }
    }
}
