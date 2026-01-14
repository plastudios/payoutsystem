<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class FinancialInstitution extends Model
{
    protected $fillable = [
        'fiType',
        'fiName',
        'fiCode',
        'fiShortCod',
        'fiStatus',
        'cardRoutingNo'
    ];
}
