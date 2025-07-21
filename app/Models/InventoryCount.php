<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCount extends Model
{
    protected $fillable = [
        'count_date',
        'count_number',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'count_date' => 'date'
    ];
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    /**
     * العلاقة مع عناصر الجرد
     */
    public function items(): HasMany
    {
        return $this->hasMany(InventoryCountItem::class);
    }

    /**
     * العلاقة مع المستخدم المنشئ
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }



    /**
     * الحصول على إجمالي الفروقات
     */
    public function getTotalDiscrepanciesAttribute()
    {
        return $this->items()->where('difference', '!=', 0)->count();
    }

    /**
     * الحصول على إجمالي القيمة المفقودة
     */
    public function getTotalValueLossAttribute()
    {
        return $this->items()->sum(function($item) {
            return abs($item->difference) * $item->medicine->supplier_price;
        });
    }

    /**
     * الحصول على نسبة الدقة
     */
    public function getAccuracyRateAttribute()
    {
        $totalItems = $this->items()->count();
        $discrepancies = $this->getTotalDiscrepanciesAttribute();

        return $totalItems > 0 ? (($totalItems - $discrepancies) / $totalItems) * 100 : 0;
    }

    /**
     * الحصول على مدة إنجاز الجرد بالساعات
     */
    public function getDurationHoursAttribute()
    {
        return $this->created_at->diffInHours($this->updated_at);
    }

    /**
     * الحصول على كفاءة الجرد (عدد العناصر في الساعة)
     */
    public function getEfficiencyScoreAttribute()
    {
        $duration = $this->getDurationHoursAttribute();
        $itemsCount = $this->items()->count();

        return $duration > 0 ? $itemsCount / $duration : 0;
    }
}