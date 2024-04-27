<?php

namespace App\Modules\Order\Tests\Feature;

use App\Modules\Coin\Models\Coin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
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
    public function occur_error_in_fetching_destination_coin_price()
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
                'قیمت ارز تغییر کرده است!'
            ]
        ]);
    }
}
