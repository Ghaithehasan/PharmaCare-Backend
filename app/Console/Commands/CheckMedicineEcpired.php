<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExpiredMedicineAlert;
use App\Models\MedicineBatch;
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

        // جلب الدفعات التي ستنتهي خلال 30 يوم ولم يتم إشعارها مؤخراً
        $batches = MedicineBatch::where('is_active', true)
            ->where('quantity', '>', 0)
            ->where('expiry_date', '>', $now)
            ->where('expiry_date', '<=', $now->copy()->addDays(30))
            ->where(function ($query) use ($now) {
                $query->whereNull('last_notification_date')
                    ->orWhere('last_notification_date', '<', $now->copy()->subDays(5)); // مثال: لا ترسل إشعار لنفس الدفعة أكثر من مرة كل 5 أيام
            })
            ->get();

        foreach ($batches as $batch) {
            $daysUntilExpiry = $now->diffInDays($batch->expiry_date);
            $expire_date = $batch->expiry_date;

            // تحديد نوع الإشعار بناءً على الأيام المتبقية
            $notificationType = match(true) {
                $daysUntilExpiry <= 3 => 'عاجل',
                $daysUntilExpiry <= 15 => 'متوسط',
                default => 'تنبيه'
            };

            // إرسال الإشعار مع تفاصيل الدواء والدفعة
            Mail::to('matrex663@gmail.com')
                ->send(new ExpiredMedicineAlert(
                    $batch->medicine, // علاقة الدواء مع الدفعة
                    $expire_date,
                    $notificationType,
                    $batch // أرسل الدفعة نفسها إذا أردت تفاصيلها في الإيميل
                ));

            // تحديث تاريخ آخر إشعار داخل الدفعة
            $batch->update(['last_notification_date' => $now]);
        }
        }
        // $now = Carbon::now();
        // $medicines = Medicine::where('expiry_date', '>', $now)
        //     ->where('expiry_date', '<=', $now->copy()->addDays(30))
        //     ->where(function ($query) use ($now) {
        //         $query->whereNull('last_notification_date')
        //             ->orWhere(function ($q) use ($now) {
        //                 // التحقق من الفترات الزمنية للإشعارات
        //                 $q->where(function ($subQuery) use ($now) {
        //                     // الإشعار الأول: عند 30 يوم
        //                     $subQuery->where('expiry_date', '<=', $now->copy()->addDays(30))
        //                             ->whereNull('last_notification_date');
        //                 })
        //                 ->orWhere(function ($subQuery) use ($now) {
        //                     // الإشعار الثاني: عند 15 يوم
        //                     $subQuery->where('expiry_date', '<=', $now->copy()->addDays(15))
        //                             ->where('last_notification_date', '<', $now->copy()->subDays(10));
        //                 })
        //                 ->orWhere(function ($subQuery) use ($now) {
        //                     // الإشعار الثالث: عند 3 أيام
        //                     $subQuery->where('expiry_date', '<=', $now->copy()->addDays(3))
        //                             ->where('last_notification_date', '<', $now->copy()->subDays(5));
        //                 });
        //             });
        //     })
        //     ->get();

        // foreach ($medicines as $medicine) {
        //     $daysUntilExpiry = $now->diffInDays($medicine->expiry_date);

        //     // تحديد نوع الإشعار بناءً على الأيام المتبقية
        //     $notificationType = match(true) {
        //         $daysUntilExpiry <= 3 => 'عاجل',
        //         $daysUntilExpiry <= 15 => 'متوسط',
        //         default => 'تنبيه'
        //     };

        //     // إرسال الإشعار
        //     Mail::to('matrex663@gmail.com')
        //         ->send(new ExpiredMedicineAlert($medicine, $notificationType));

        //     // تحديث تاريخ آخر إشعار
        //     $medicine->update(['last_notification_date' => $now]);

        //     // تسجيل الإشعار في السجلات
        // }
    }

