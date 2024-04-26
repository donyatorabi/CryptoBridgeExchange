<?php

namespace App\Modules\Coin;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class CoinServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    public function boot(): void
    {
        Http::fake([
            config('services.update_currencies') => Http::response($this->fakeSuccessResponse()),
        ]);
    }

    private function fakeSuccessResponse()
    {
        return [
            'status' => true,
            'data' => [
                [
                    'name_en' => 'IRR',
                    'name_fa' => 'ریال',
                    'price' => 1,
                ],
                [
                    'name_en' => 'USDT',
                    'name_fa' => 'تتر',
                    'price' => 570_000,
                ],
                [
                    'name_en' => 'BTC',
                    'name_fa' => 'بیتکوین',
                    'price' => 4_069_000_000_000,
                ],
            ],
        ];
    }
}
