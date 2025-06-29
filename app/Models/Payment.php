<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $fillable = [
        'invoice_id',
        'paid_amount',
        'payment_method',
        'payment_date',
        'payment_proof',
        'status',
        'notes'
    ];
    public function invoice()
    {
        return $this->belongsTo(invoices::class);
    }

}
