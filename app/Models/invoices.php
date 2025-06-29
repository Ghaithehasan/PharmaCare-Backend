<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoices extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'notes',
        'total_amount',
        'is_archived'

    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }
}
