<?php

namespace App\Listeners;

use App\Events\SupplierRegistered;
use App\Mail\SupplierWelcomeEmail;
use App\Models\SupplierNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendSupplierWelcomeNotification
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
    public function handle(SupplierRegistered $event): void
    {
        $notification = SupplierNotification::create([
            'supplier_id' => $event->supplier->id,
            'notification_type' => 'welcome',
            'message' => "مرحبًا {$event->supplier->company_name}! نحن سعداء بالتعامل معك.",
            'data' => json_encode([
                'message' => "مرحبًا {$event->supplier->company_name}! نحن سعداء بالتعامل معك.",
                'features' => [
                    'إدارة الطلبيات بسهولة',
                    'متابعة المخزون بشكل مباشر',
                    'تقارير مالية مفصلة',
                    'دعم فني على مدار الساعة',
                    'تحديثات مستمرة للنظام'
                ]
            ],JSON_UNESCAPED_UNICODE),
            'is_read' => false,
        ]);

        // ✅ 2️⃣ إرسال إشعار ترحيبي عبر البريد الإلكتروني
        if (!empty($event->supplier->email)) {
            Mail::to($event->supplier->email)->send(new SupplierWelcomeEmail($event->supplier));
        }
    }
}
