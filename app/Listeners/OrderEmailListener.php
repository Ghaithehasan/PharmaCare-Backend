<?php

namespace App\Listeners;

use App\Events\OrderEmailEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderEmail;
use App\Models\SupplierNotification;
use Carbon\Carbon;



class OrderEmailListener
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
    public function handle(OrderEmailEvent $event): void
    {

        $order = $event->order;
        $medicines = $event->medicines;
        $totalMedicines = collect($medicines)->sum('quantity');
        
        // حساب إجمالي قيمة الطلب
        $totalAmount = collect($medicines)->sum('total_price');

        // إنشاء رسالة إشعار تفصيلية
        $notificationMessage = sprintf(
            "طلبية جديدة #%s\n" .
            "• عدد الأدوية: %d\n" .
            "• القيمة الإجمالية: %s ريال\n" .
            "• تاريخ الطلب: %s\n" .
            "يرجى مراجعة التفاصيل في لوحة التحكم",
            $order->order_number,
            $totalMedicines,
            number_format($totalAmount, 2),
            Carbon::parse($order->created_at)->format('Y-m-d H:i')
        );
        $notificationData = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total_amount' => $totalAmount,
            'total_medicines' => $totalMedicines,
            'status' => $order->status,
            'medicines' => $medicines->map(function($medicine) {
                return [
                    'name' => $medicine['medicine_name'],
                    'quantity' => $medicine['quantity'],
                    'unit_price' => $medicine['unit_price'],
                    'total_price' => $medicine['total_price']
                ];
            })->toArray()
        ];

        $notification = SupplierNotification::create([
            'supplier_id' => $order->supplier->id,
            'notification_type' => 'new_order',
            'message' => $notificationMessage,
            'data' => $notificationData,
            'is_read' => false,
        ]);

        Mail::to($order->supplier->email)->send(new OrderEmail($order, $medicines));
    }
}
