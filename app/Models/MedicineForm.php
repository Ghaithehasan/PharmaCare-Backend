<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineForm extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    // العلاقة مع الأدوية
    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }
} 