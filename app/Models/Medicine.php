<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'medicine_name',
        'scientific_name',
        'arabic_name',
        'category_id',
        'quantity',
        'type',
        'alert_quantity',
        'supplier_price',
        'bar_code',
        'people_price',
        'tax_rate',
        // 'expiry_date',
        'alternative_ids'
    ];

    protected $casts = [
        'alternative_ids' => 'array'
    ];
 
    // العلاقة مع التصنيف
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // العلاقة مع المرفقات
    public function attachments()
    {
        return $this->hasMany(MedicineAttachment::class);
    }

    // العلاقة مع الطلبات
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
                    ->withPivot('quantity', 'unit_price', 'total_price')
                    ->withTimestamps();
    }

    // الحصول على الأدوية البديلة
    public function alternatives()
    {
        return Medicine::whereIn('id', $this->alternative_ids ?? []);
    }

    // إضافة دواء بديل
    public function addAlternative($medicines)
    {
        $medicines = collect($medicines);
        // 1. جمع جميع البدائل الحالية للدواء الحالي
        $currentAlternatives = $this->alternatives()->pluck('id')->toArray();
        // 2. جمع جميع البدائل الحالية للأدوية الجديدة
        $newAlternatives = [];
        foreach ($medicines as $medicine) {
            $medicineAlternatives = $medicine->alternatives()->pluck('id')->toArray();
            $newAlternatives = array_merge($newAlternatives, $medicineAlternatives);
        }
        
        // 3. دمج جميع المعرفات
        $allIds = array_unique(array_merge(
            $currentAlternatives,
            $newAlternatives,
            $medicines->pluck('id')->toArray()
        ));
        
        // 4. إزالة معرف الدواء الحالي
        $allIds = array_diff($allIds, [$this->id]);
        
        // 5. تحديث بدائل الدواء الحالي
        $this->alternative_ids = array_values($allIds);
        $this->save();

        // 6. تحديث بدائل جميع الأدوية المعنية
        // أولاً: تحديث الأدوية الجديدة التي تم إضافتها كبدائل
        foreach ($medicines as $medicine) {
            $medicineAlternatives = $medicine->alternative_ids ?? [];
            
            // إضافة الدواء الحالي إلى بدائل الدواء الجديد
            $medicineAlternatives = array_unique(array_merge($medicineAlternatives, [$this->id]));
            // إضافة باقي البدائل
            $medicineAlternatives = array_unique(array_merge($medicineAlternatives, $allIds));
            // إزالة معرف الدواء نفسه من بدائله
            $medicineAlternatives = array_diff($medicineAlternatives, [$medicine->id]);
            
            $medicine->alternative_ids = array_values($medicineAlternatives);
            $medicine->save();
        }

        // ثانياً: تحديث باقي البدائل الموجودة
        foreach ($allIds as $id) {
            // تخطي الأدوية التي تم تحديثها في الخطوة السابقة
            if ($medicines->contains('id', $id)) {
                continue;
            }

            $medicine = Medicine::find($id);
            if ($medicine) {
                $medicineAlternatives = $medicine->alternative_ids ?? [];
                
                // إضافة الدواء الحالي إلى بدائل الدواء
                $medicineAlternatives = array_unique(array_merge($medicineAlternatives, [$this->id]));
                // إضافة باقي البدائل
                $medicineAlternatives = array_unique(array_merge($medicineAlternatives, $allIds));
                // إزالة معرف الدواء نفسه من بدائله
                $medicineAlternatives = array_diff($medicineAlternatives, [$id]);
                
                $medicine->alternative_ids = array_values($medicineAlternatives);
                $medicine->save();
            }
        }
    }

    // إزالة دواء بديل
    public function removeAlternative($medicines)
    {
        try {
            // تحويل إلى Collection إذا كانت مصفوفة
            $medicines = collect($medicines);
            
            // التحقق من وجود الأدوية
            if ($medicines->isEmpty()) {
                throw new \Exception('لم يتم تحديد أي أدوية للإزالة');
            }

            // الحصول على معرفات الأدوية المراد حذفها
            $idsToRemove = $medicines->pluck('id')->toArray();
            
            // التحقق من أن الدواء الحالي ليس من ضمن الأدوية المراد حذفها
            if (in_array($this->id, $idsToRemove)) {
                throw new \Exception('لا يمكن إزالة الدواء من بدائله الخاصة');
            }

            // 1. إزالة البدائل من الدواء الحالي
            $currentAlternatives = $this->alternative_ids ?? [];
            $remainingAlternatives = array_diff($currentAlternatives, $idsToRemove);
            $this->alternative_ids = array_values($remainingAlternatives);
            $this->save();

            // 2. إزالة الدواء الحالي من بدائل الأدوية المحذوفة
            foreach ($medicines as $medicine) {
                $medicineAlternatives = $medicine->alternative_ids ?? [];
                if (($key = array_search($this->id, $medicineAlternatives)) !== false) {
                    unset($medicineAlternatives[$key]);
                    $medicine->alternative_ids = array_values($medicineAlternatives);
                    $medicine->save();
                }
            }

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // التحقق من وجود دواء بديل
    public function hasAlternative(Medicine $alternative)
    {
        return in_array($alternative->id, $this->alternative_ids ?? []);
    }

    // الحصول على الأدوية البديلة المتوفرة
    public function getAvailableAlternatives()
    {
        return Medicine::whereIn('id', $this->alternative_ids ?? [])
                      ->where('quantity', '>', 0)
                      ->get();
    }
}
