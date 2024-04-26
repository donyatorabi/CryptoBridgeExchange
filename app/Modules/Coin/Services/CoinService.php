<?php

namespace App\Modules\Coin\Services;

use App\Modules\Coin\Models\Coin;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CoinService
{
    public function fetch()
    {

    }

    public function updatePrices(array $data): bool
    {
        DB::beginTransaction();
        try {
            Coin::query()->upsert($data, 'name_en');

            Cache::set('coins', $data);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            logger()->error('**error in coin price updating** '.$exception->getMessage());

            return false;
        }

        return true;
    }
}
