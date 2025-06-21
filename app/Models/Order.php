<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'order_number',
        'order_date',
        'note',
        'status',
        'delevery_date',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delevery_date'=>'date'
        // 'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'order_items')
                    ->withPivot('quantity', 'unit_price', 'total_price')
                    ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calculate the total amount of the order
     */
    public function calculateTotal()
    {
        return $this->orderItems()->sum('total_price');
    }

    /**
     * Get the total amount of the order
     */
    public function getTotalAttribute()
    {
        return $this->calculateTotal();
    }
}
