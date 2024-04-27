<?php

namespace App\Modules\Order\Factories;

use App\Modules\Coin\Models\Coin;
use App\Modules\Order\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Order\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $srcCoin = Coin::factory()->create();
        $destCoin = Coin::factory()->create();

        return [
            'user_email' => $this->faker->email,
            'src_coin_price' => $srcCoin->price,
            'src_coin_id' => $srcCoin->id,
            'dest_coin_id' => $destCoin->id,
            'quantity' => $this->faker->randomNumber(),
            'dest_coin_price' => $destCoin->price,
            'status' => $this->faker->randomElement(Order::STATUSES),
        ];
    }
}
