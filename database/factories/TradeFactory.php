<?php

namespace Database\Factories;

use App\Enums\TradeType;
use App\Models\Trade;
use Illuminate\Database\Eloquent\Factories\Factory;

class TradeFactory extends Factory
{
    protected $model = Trade::class;

    public function definition(): array
    {
        return [
            'launchpad_id' => 1,
            'txid' => $this->faker->uuid,
            'address' => $this->faker->sha256,
            'amount' => $this->faker->randomFloat(8, 0.0001, 10000),
            'qty' => $this->faker->randomFloat(8, 100, 10000000000),
            'price' => $this->faker->randomFloat(4, 0.0001, 1000),
            'usd_price' => $this->faker->randomFloat(2, 0.01, 10000),
            'usd' => $this->faker->randomFloat(2, 0.01, 10000),
            'type' => $this->faker->randomElement([TradeType::BUY, TradeType::SELL]),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
