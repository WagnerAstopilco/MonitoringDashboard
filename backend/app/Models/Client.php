<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'company_name',
        'company_ruc'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
