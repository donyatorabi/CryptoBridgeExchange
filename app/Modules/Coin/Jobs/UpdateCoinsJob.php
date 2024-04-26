<?php

namespace App\Modules\Coin\Jobs;

use App\Modules\Coin\DTOs\CoinResponseDTO;
use App\Modules\Coin\Services\CoinService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class UpdateCoinsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private CoinService $coinService;

    public function __construct()
    {
        $this->coinService = new CoinService();
    }

    public function handle(): bool
    {
        $response = $this->fetchCurrenciesPrices();

        $coinResponseDto = new CoinResponseDTO(status: $response['status'], data: $response['data']);

        if ($coinResponseDto->fetchFailed()) {
            logger()->error('fetching coin prices has error.');

            return false;
        }

        return $this->coinService->updatePrices(data: $coinResponseDto->data);
    }

    private function fetchCurrenciesPrices(): mixed
    {
        return Http::get(config('services.update_currencies'))->json();
    }
}
