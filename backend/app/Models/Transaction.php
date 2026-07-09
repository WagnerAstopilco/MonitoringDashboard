<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable= [
        'transaction_date',
        'transaction_type',
        'service_id',
        'client_id',
        'user_id',
        'price',
        'cost',
        'profit',
        'status'
    ];
}
