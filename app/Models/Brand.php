<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name' , 
        'company_name',
        'description'
    ];

    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }


}
