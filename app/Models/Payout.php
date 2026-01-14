<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'batch_id',
        'amount',
        'referenceKey',
        'currency',
        'remarks',
        'bankCode',
        'bankShortCode',
        'benType',
        'txnChannel',
        'beneficiaryAcc',
        'beneficiaryName',
        'beneficiaryEmail',
        'routingNumber',
        'txnChannelCode',
        'merchant_id',
        'api_response',
        'status',
    ];
}
