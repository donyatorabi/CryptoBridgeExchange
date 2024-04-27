<?php

namespace App\Modules\Acc\Factories;

use App\Modules\Acc\Models\Transaction;
use App\Modules\Order\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Acc\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tracker_id' => Str::uuid(),
            'order_id' => Order::factory()->create()->id,
            'amount' => rand(10000000, 9999999),
        ];
    }
}
