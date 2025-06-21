<?php

namespace App\Listeners;

use App\Events\ConifermOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\OrderAcceptedMail;
use Illuminate\Support\Facades\Mail;

class ConifermOrderListener
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
    public function handle(ConifermOrder $event): void
    {
        // إرسال الإيميل عند قبول الطلبية
        Mail::to($event->pharmacy_email)->send(
            new OrderAcceptedMail($event->order_number, $event->delivery_date, $event->pharmacy_email)
        );
    }
}
