<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'batch_id',
        'merchant_id',
        'url',
        'request_payload',
        'response_payload',
        'status_code',
    ];
}
