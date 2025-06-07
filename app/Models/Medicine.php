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
        'expiry_date',
        'last_notification_date',
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

    // إضافة دواء بديل (علاقة أحادية الاتجاه)
    public function addAlternative($medicines)
    {
        $medicines = collect($medicines);
        
        foreach ($medicines as $medicine) {
            // إضافة الدواء الجديد كبديل للدواء الحالي فقط
            $currentAlternatives = $this->alternative_ids ?? [];
            if (!in_array($medicine->id, $currentAlternatives)) {
                $currentAlternatives[] = $medicine->id;
                $this->alternative_ids = array_values($currentAlternatives);
                $this->save();
            }
        }
    }

    // إضافة بديل مع العلاقة المتبادلة (إذا كانت مطلوبة)
    public function addBidirectionalAlternative($medicines)
    {
        $medicines = collect($medicines);
        
        foreach ($medicines as $medicine) {
            // 1. إضافة الدواء الجديد كبديل للدواء الحالي
            $currentAlternatives = $this->alternative_ids ?? [];
            if (!in_array($medicine->id, $currentAlternatives)) {
                $currentAlternatives[] = $medicine->id;
                $this->alternative_ids = array_values($currentAlternatives);
                $this->save();
            }
            
            // 2. إضافة الدواء الحالي كبديل للدواء الجديد
            $medicineAlternatives = $medicine->alternative_ids ?? [];
            if (!in_array($this->id, $medicineAlternatives)) {
                $medicineAlternatives[] = $this->id;
                $medicine->alternative_ids = array_values($medicineAlternatives);
                $medicine->save();
            }
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
