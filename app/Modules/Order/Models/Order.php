<?php

namespace App\Modules\Order\Models;

use App\Modules\Acc\Models\Transaction;
use App\Modules\Coin\Models\Coin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_email',
        'src_coin_price',
        'src_coin_id',
        'dest_coin_id',
        'quantity',
        'dest_coin_price',
        'status',
    ];

    const STATUSES = [
        'PENDING' => 'PENDING',
        'REJECTED' => 'REJECTED',
        'ACCEPTED' => 'ACCEPTED',
    ];

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    public function srcCoin(): BelongsTo
    {
        return $this->belongsTo(Coin::class, 'src_coin_id');
    }

    public function destCoin(): BelongsTo
    {
        return $this->belongsTo(Coin::class, 'dest_coin_id');
    }
}
