<?php

namespace App\Listeners;

use App\Events\PaymentEmailEvent;
use App\Models\SupplierNotification;
use App\Mail\PaymentNotificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentEmailListener
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
    public function handle(PaymentEmailEvent $event): void
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;
        $order = $invoice->order;
        $supplier = $order->supplier;

        // إنشاء إشعار جديد للمورد
        SupplierNotification::create([
            'supplier_id' => $supplier->id,
            'notification_type' => 'payment',
            'message' => 'تم إضافة مدفوعة جديدة للفاتورة رقم: ' . $invoice->invoice_number,
            'data' => json_encode([
                'invoice_number' => $invoice->invoice_number,
                'amount' => $payment->paid_amount,
                'payment_method' => $payment->payment_method,
                'payment_date' => $payment->payment_date,
                'payment_status' => $payment->status,
                'invoice_id' => $invoice->id,
                'order_number' => $order->order_number ?? null,
                'total_invoice_amount' => $invoice->total_amount,
                'remaining_amount' => $invoice->total_amount - $invoice->payments()->where('status', 'pending')->sum('paid_amount')
            ]),
            'is_read' => false
        ]);

        // إرسال إيميل للمورد
        try {
            Mail::to($supplier->email)->send(new PaymentNotificationMail($payment, $supplier));
        } catch (\Exception $e) {
            // تسجيل الخطأ في اللوج إذا فشل إرسال الإيميل
        }
    }
}
