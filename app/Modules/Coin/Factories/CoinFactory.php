<?php

namespace App\Modules\Coin\Factories;

use App\Modules\Coin\Models\Coin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Coin>
 */
class CoinFactory extends Factory
{
    protected $model = Coin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_en' => $this->faker->name,
            'name_fa' => $this->faker->name,
            'price' => $this->faker->randomNumber(),
        ];
    }
}
