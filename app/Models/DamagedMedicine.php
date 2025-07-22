<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedMedicine extends Model
{
    protected $fillable = [
        'medicine_batch_id',
        'quantity_talif',
        'reason',
        'notes',
        'damaged_at'
    ];

    protected $casts = [
        'damaged_at' => 'datetime'
    ];

    /**
     * العلاقة مع الدواء
     */
    public function batch()
    {
        return $this->belongsTo(MedicineBatch::class, 'medicine_batch_id');
    }
}
