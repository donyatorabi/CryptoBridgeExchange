<?php

namespace App\Modules\Order\Tests\Unit;

use App\Exceptions\ApiErrorException;
use App\Modules\Coin\Models\Coin;
use App\Modules\Order\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function throw_exception_if_coin_doesnt_exist_in_cache()
    {
        $srcCoin = Coin::factory()->create();
        $destCoin = Coin::factory()->create();

        $this->expectException(ApiErrorException::class);

        $service = new OrderService();
        $service->store([
            'src_coin_id' => $srcCoin->id,
            'dest_coin_id' => $destCoin->id,
            'email' => fake()->email,
            'price' => fake()->randomNumber(),
        ]);

        $this->expectExceptionMessage('خطا در گرفتن قیمت ارز مقصد!');
        $this->expectExceptionCode(500);
    }
}
