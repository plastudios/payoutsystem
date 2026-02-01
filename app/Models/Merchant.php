<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $fillable = [
        'merchant_id', 'email', 'name', 'company_name', 'status', 'balance'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_merchant', 'merchant_id', 'user_id', 'merchant_id', 'id');
    }
}
