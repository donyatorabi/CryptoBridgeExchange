<?php

namespace App\Modules\Coin\Models;

use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    protected $fillable = [
        'name_en',
        'name_fa',
        'price',
    ];

    const FA_NAMES = [
        'IRR' => 'ریال',
        'USDT' => 'تتر',
        'BTC' => 'بیتکوین',
    ];
}
