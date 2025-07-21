<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineBatch extends Model
{
    protected $fillable = [
        'medicine_id',
        'batch_number',
        'quantity',
        'expiry_date',
        'unit_price',
        'order_id',
        'is_active',
    ];


    protected $casts = [
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
