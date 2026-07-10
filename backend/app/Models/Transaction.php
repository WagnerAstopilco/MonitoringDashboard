<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable= [
        'client_id',
        'user_id',
        'promotion_id',
        'transaction_date',
        'transaction_type',
        'amount',
        'responsible',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function transactionPayments()
    {
        return $this->hasMany(TransactionPayment::class);
    }
}
