<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $fillable = [
        'merchant_id', 'email', 'name', 'company_name', 'status', 'balance'
    ];
    

}
