<?php

namespace App\Modules\Acc\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'tracker_id',
    ];
}
