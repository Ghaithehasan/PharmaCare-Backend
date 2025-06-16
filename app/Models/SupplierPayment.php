<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{



    protected $fillable = [
        'supplier_id',
        'payment_date',
        'payment_method',
        'payment_status',
        'amount_paid'
    ];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
