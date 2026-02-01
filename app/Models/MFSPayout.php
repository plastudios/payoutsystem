<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MFSPayout extends Model
{
    protected $table = 'mfs_payouts'; // ✅ Correct your actual table name
    protected $guarded = []; // Allow mass assignment
    
    protected $fillable = [
        'batch_id', 'reference_key', 'amount', 'wallet_number',
        'method', 'merchant_id', 'status',
        'completed_at', 'mfs_transaction_id', 'agent_id', 'remarks'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
