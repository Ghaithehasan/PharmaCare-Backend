<?php

namespace App\Listeners;

use App\Events\CancelledOrder;
use App\Mail\OrderCancelledMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderCancelledNotification;

class CancelledOrderListener
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
    public function handle(CancelledOrder $event): void
    {

        try {
            // إرسال بريد إلكتروني للصيدلاني
            Mail::to($event->pharmacyEmail)
                ->send(new OrderCancelledMail(
                    $event->order,
                    $event->supplier,
                    $event->cancellationReason,
                    $event->cancelledAt
                ));


        } catch (\Exception $e) {
            Log::error('Failed to send cancellation email to pharmacy', [
                'order_id' => $event->order->id,
                'pharmacy_email' => $event->pharmacyEmail,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(CancelledOrder $event, \Throwable $exception): void
    {
        Log::error('CancelledOrderListener failed', [
            'order_id' => $event->order->id,
            'pharmacy_email' => $event->pharmacyEmail,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
