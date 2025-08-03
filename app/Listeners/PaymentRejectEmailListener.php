<?php

namespace App\Listeners;

use App\Events\PaymentRejectEmailEvent;
use App\Mail\PaymentRejectEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class PaymentRejectEmailListener
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
    public function handle(PaymentRejectEmailEvent $event): void
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;
        $order = $invoice->order;
        $supplier = $order->supplier;
        $email = 'matrex663@gmail.com';
        Mail::to($email)->send(new PaymentRejectEmail($payment));
    }
}
