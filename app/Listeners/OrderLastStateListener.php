<?php

namespace App\Listeners;

use App\Events\OrderLastState;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\DeliveryTodayMail;
use Illuminate\Support\Facades\Mail;

class OrderLastStateListener
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
    public function handle(OrderLastState $event): void
    {
        $order = $event->order;
        $pharmacyEmail = 'matrex663@gmail.com'; // أو من قاعدة البيانات
    
        Mail::to($pharmacyEmail)->send(new DeliveryTodayMail($order));
    }
}
