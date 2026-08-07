<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'service_image',
        'cost',
        'price',
        'profit'
    ];

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

}

