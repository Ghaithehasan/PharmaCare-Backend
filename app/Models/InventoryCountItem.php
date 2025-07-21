<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCountItem extends Model
{
    protected $fillable = [
        'inventory_count_id',
        'medicine_id',
        'system_quantity',
        'actual_quantity',
        'difference',
        'notes'
    ];

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    /**
     * العلاقة مع جرد المخزون
     */
    public function inventoryCount(): BelongsTo
    {
        return $this->belongsTo(InventoryCount::class);
    }

    /**
     * العلاقة مع الدواء
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * الحصول على نسبة الفرق
     */
    public function getDiscrepancyPercentageAttribute()
    {
        return $this->system_quantity > 0 ?
            (abs($this->difference) / $this->system_quantity) * 100 : 0;
    }

    /**
     * الحصول على القيمة المفقودة
     */
    public function getValueLossAttribute()
    {
        return abs($this->difference) * $this->medicine->supplier_price;
    }

    /**
     * التحقق من كون العنصر مفقود بالكامل
     */
    public function getIsCompletelyMissingAttribute()
    {
        return $this->difference < 0 && $this->actual_quantity == 0;
    }

    /**
     * التحقق من كون العنصر متسرب جزئياً
     */
    public function getIsPartiallyLeakedAttribute()
    {
        return $this->difference < 0 && $this->actual_quantity > 0;
    }

    /**
     * التحقق من كون العنصر زائد عن الحاجة
     */
    public function getIsOverstockedAttribute()
    {
        return $this->difference > 0;
    }

    /**
     * الحصول على مستوى خطورة الفرق
     */
    public function getRiskLevelAttribute()
    {
        if (abs($this->difference) >= 10) {
            return 'critical';
        } elseif (abs($this->difference) >= 5) {
            return 'moderate';
        } else {
            return 'minor';
        }
    }

    /**
     * الحصول على وصف الفرق
     */
    public function getDiscrepancyDescriptionAttribute()
    {
        if ($this->is_completely_missing) {
            return 'مفقود بالكامل';
        } elseif ($this->is_partially_leaked) {
            return 'متسرب جزئياً';
        } elseif ($this->is_overstocked) {
            return 'زائد عن الحاجة';
        } else {
            return 'مطابق';
        }
    }
}