<?php

namespace App\Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_email',
        'src_coin_price',
        'src_coin_id',
        'dest_coin_id',
        'amount',
        'dest_coin_price',
        'status',
    ];

    const STATUSES = [
        'PENDING' => 'PENDING',
        'REJECTED' => 'REJECTED',
        'SUCCEEDED' => 'SUCCEEDED',
    ];
}
