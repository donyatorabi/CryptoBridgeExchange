<?php

namespace App\Modules\Order\Tests\Feature;

use App\Modules\Acc\Models\Transaction;
use App\Modules\Coin\Models\Coin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.update_currencies' => 'http://fakeurl.com',
        ]);
    }

    /** @test */
    public function return_tracker_id_and_save_order()
    {
        $srcCoin = Coin::factory()->create([
            'name_en' => 'IRR',
            'name_fa' => 'ریال',
            'price' => 1,
        ]);
        $destCoin = Coin::factory()->create([
            'name_en' => 'USDT',
            'name_fa' => 'تتر',
            'price' => 570_000,
        ]);

        Cache::shouldReceive('has')
            ->once()
            ->with('coins')
            ->andReturn(array_merge($srcCoin->toArray(), $destCoin->toArray()));

        $email = $this->faker->email;
        $this->post('order', [
            'email' => $email,
            'src_coin_id' => $srcCoin->id,
            'dest_coin_id' => $destCoin->id,
            'price' => $srcCoin->price,
            'quantity' => 2,
        ])
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'code',
                'tracker_id',
            ]);

        $this->assertDatabaseHas('orders', [
            'user_email' => $email,
            'src_coin_price' => $srcCoin->price,
            'src_coin_id' => $srcCoin->id,
            'dest_coin_id' => $destCoin->id,
            'quantity' => 2,
            'dest_coin_price' => $destCoin->price,
            'status' => 'ACCEPTED',
        ]);

        $this->assertDatabaseHas('transactions', [
            'order_id' => 1,
            'amount' => $destCoin->price * 2,
        ]);
    }

    /** @test */
    public function error_occurred_in_fetching_destination_coin_price()
    {
        $srcCoin = Coin::factory()->create([
            'name_en' => 'IRR',
            'name_fa' => 'ریال',
            'price' => 1,
        ]);
        $destCoin = Coin::factory()->create([
            'name_en' => 'USDT',
            'name_fa' => 'تتر',
            'price' => 570_000,
        ]);

        Cache::shouldReceive('has')
            ->once()
            ->with('coins')
            ->andReturn(array_merge($srcCoin->toArray(), $destCoin->toArray()));

        $email = $this->faker->email;
        $this->post('order', [
            'email' => $email,
            'src_coin_id' => $srcCoin->id,
            'dest_coin_id' => $destCoin->id,
            'price' => 400,
            'quantity' => 2,
        ])->assertInternalServerError()->assertJson([
            'status' => 'failed',
            'message' => [
                'قیمت ارز تغییر کرده است!',
            ],
        ]);
    }

    /** @test */
    public function error_occurred_when_the_selected_dest_coin_is_invalid()
    {
        $srcCoin = Coin::factory()->create([
            'id' => 5,
            'name_en' => 'sth1',
            'name_fa' => 'ریال',
            'price' => 1,
        ]);

        $email = $this->faker->email;
        $this->post('order', [
            'email' => $email,
            'src_coin_id' => $srcCoin->id,
            'dest_coin_id' => 4,
            'price' => $srcCoin->price,
            'quantity' => 2,
        ])->assertUnprocessable()->assertJson([
            'status' => 'failed',
            'message' => [
                'dest_coin_id' => [
                    'The selected dest coin id is invalid.',
                ],
            ],
        ]);
    }

    /** @test */
    public function error_occurred_when_tracker_id_not_found_in_order_details()
    {
        Transaction::factory()->create();

        $this->get('order/'.'333')->assertInternalServerError()->assertJson([
            'status' => 'failed',
            'message' => [
                'کدپیگیری موجود نیست!',
            ],
        ]);
    }

    /** @test */
    public function fetch_order_details_by_tracker_id_successfully()
    {
        $transaction = Transaction::factory()->create();

        $this->get('order/'.$transaction->tracker_id)->assertOk()->assertJson(
            [
                'data' => [
                    'srcCoin' => $transaction->order->srcCoin->name_en,
                    'srcCoinPrice' => $transaction->order->srcCoin->price,
                    'destCoin' => $transaction->order->destCoin->name_en,
                    'destCoinPrice' => $transaction->order->destCoin->price,
                    'quantity' => $transaction->order->quantity,
                    'email' => $transaction->order->user_email,
                ],
            ]);
    }
}
