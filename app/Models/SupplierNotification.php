<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierNotification extends Model
{
    protected $fillable = ['supplier_id','notification_type','message','is_read','read_at','data'];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
