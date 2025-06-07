<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function inventoryCount()
    {
        return $this->belongsTo(InventoryCount::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
} 