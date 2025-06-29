<?php

namespace App\Listeners;

use App\Events\SendInvoiceEmail;
use App\Mail\SendInvoice;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmailListener
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
    public function handle(SendInvoiceEmail $event): void
    {
        try {
            // إرسال الإيميل مع الفاتورة
            Mail::to($event->pharmacyEmail)
                ->send(new SendInvoice($event->invoice));
                
        } catch (\Exception $e) {
            // تسجيل الخطأ في الـ logs
            \Log::error('Failed to send invoice email: ' . $e->getMessage(), [
                'invoice_id' => $event->invoice->id,
                'pharmacy_email' => $event->pharmacyEmail,
                'error' => $e->getMessage()
            ]);
        }
    }
}
