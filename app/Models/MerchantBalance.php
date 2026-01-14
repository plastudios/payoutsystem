<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MerchantBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'type',
        'amount',
        'remarks',
    ];
}
