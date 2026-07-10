<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'status',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
    
    public function transactionDetail()
    {
        return $this->hasOne(TransactionDetail::class);
    }
}
