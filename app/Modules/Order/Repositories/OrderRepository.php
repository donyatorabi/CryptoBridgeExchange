<?php

namespace App\Modules\Order\Repositories;

use App\Modules\Acc\Models\Transaction;
use App\Modules\BaseRepository;
use App\Modules\Order\Models\Order;

class OrderRepository extends BaseRepository
{
    public function getByTrackerId(string $trackerId): ?array
    {
        return Transaction::query()
            ->where('tracker_id', $trackerId)
            ->first()
            ->with(['order', 'order.srcCoin', 'order.destCoin'])
            ->toArray();
    }
}
