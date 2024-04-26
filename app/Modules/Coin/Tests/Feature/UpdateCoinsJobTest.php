<?php

namespace App\Modules\Coin\Tests\Feature;

use App\Modules\Coin\Jobs\UpdateCoinsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UpdateCoinsJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.update_currencies' => 'http://fakeurl.com',
        ]);
    }

    /** @test */
    public function update_prices_from_external_url()
    {
        $this->fakeSuccessUpdate();

        $updateCoinJob = new UpdateCoinsJob();
        $updateCoinJob->handle();

        $this->assertDatabaseCount('coins', 3);

        $this->assertDatabaseHas('coins', [
            'name_en' => 'IRR',
            'name_fa' => 'ریال',
            'price' => 1,
        ]);

        $this->assertDatabaseHas('coins', [
            'name_en' => 'USDT',
            'name_fa' => 'تتر',
            'price' => 570_000,
        ]);

        $this->assertDatabaseHas('coins', [
            'name_en' => 'BTC',
            'name_fa' => 'بیتکوین',
            'price' => 4_069_000_000_000,
        ]);
    }

    /** @test */
    public function save_cache_prices_from_external_url()
    {
        $this->fakeSuccessUpdate();

        $updateCoinJob = new UpdateCoinsJob();
        $updateCoinJob->handle();

        $coins = Cache::get('coins');
        $this->assertEquals($coins, [
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
        ]);
    }

    /** @test */
    public function log_failure_price_update_from_external_url()
    {
        $log = Log::spy();

        // Mock the failure update
        $this->fakeFailureUpdate();

        // Create and handle the job
        $updateCoinJob = new UpdateCoinsJob();
        $updateCoinJob->handle();

        $log->shouldHaveReceived('error')->once();
    }

    public function fakeSuccessUpdate(): void
    {
        Http::fake([
            config('services.update_currencies') => Http::response($this->fakeSuccessResponse()),
        ]);
    }

    private function fakeSuccessResponse(): array
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

    public function fakeFailureUpdate(): void
    {
        Http::fake([
            config('services.update_currencies') => Http::response($this->fakeFailureResponse()),
        ]);
    }

    private function fakeFailureResponse(): array
    {
        return [
            'status' => false,
            'message' => 'An error occurred!',
            'data' => [],
        ];
    }
}
