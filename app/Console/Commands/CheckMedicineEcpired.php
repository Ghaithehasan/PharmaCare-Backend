<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medicine;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExpiredMedicineAlert;
use Carbon\Carbon;

class CheckMedicineEcpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medicines:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تنبيه الأدوية المنتهية الصلاحية';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // جلب الأدوية التي ستنتهي خلال 30 يوم
        $medicines = Medicine::where('expiry_date', '>', $now)
            ->where('expiry_date', '<=', $now->copy()->addDays(30))
            ->where(function ($query) use ($now) {
                $query->whereNull('last_notification_date')
                    ->orWhere(function ($q) use ($now) {
                        // التحقق من الفترات الزمنية للإشعارات
                        $q->where(function ($subQuery) use ($now) {
                            // الإشعار الأول: عند 30 يوم
                            $subQuery->where('expiry_date', '<=', $now->copy()->addDays(30))
                                    ->whereNull('last_notification_date');
                        })
                        ->orWhere(function ($subQuery) use ($now) {
                            // الإشعار الثاني: عند 15 يوم
                            $subQuery->where('expiry_date', '<=', $now->copy()->addDays(15))
                                    ->where('last_notification_date', '<', $now->copy()->subDays(10));
                        })
                        ->orWhere(function ($subQuery) use ($now) {
                            // الإشعار الثالث: عند 3 أيام
                            $subQuery->where('expiry_date', '<=', $now->copy()->addDays(3))
                                    ->where('last_notification_date', '<', $now->copy()->subDays(5));
                        });
                    });
            })
            ->get();

        foreach ($medicines as $medicine) {
            $daysUntilExpiry = $now->diffInDays($medicine->expiry_date);
            
            // تحديد نوع الإشعار بناءً على الأيام المتبقية
            $notificationType = match(true) {
                $daysUntilExpiry <= 3 => 'عاجل',
                $daysUntilExpiry <= 15 => 'متوسط',
                default => 'تنبيه'
            };
            
            // إرسال الإشعار
            Mail::to('matrex663@gmail.com')
                ->send(new ExpiredMedicineAlert($medicine, $notificationType));
            
            // تحديث تاريخ آخر إشعار
            $medicine->update(['last_notification_date' => $now]);
            
            // تسجيل الإشعار في السجلات
        }
    }
}
