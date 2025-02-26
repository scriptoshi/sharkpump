<?php

namespace Database\Seeders;

use App\Models\Trade;
use App\Enums\TradeType;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TradeSeeder extends Seeder
{
    public function run(): void
    {
        Trade::truncate();
        Trade::factory()
            ->count(10000) // Number of trades per launchpad
            ->create(['launchpad_id' => 1]);
        $now = Carbon::now();

        // Create trades with specific timestamps and both positive and negative changes
        $trades = [
            // 24+ hours ago (baseline for 24h)
            [
                'created_at' => $now->copy()->subHours(25),
                'price' => 100
            ],
            // 24 hours ago - positive change from baseline
            [
                'created_at' => $now->copy()->subHours(24),
                'price' => 120
            ],
            // 7 hours ago
            [
                'created_at' => $now->copy()->subHours(7),
                'price' => 150
            ],
            // 6 hours ago - negative change
            [
                'created_at' => $now->copy()->subHours(6),
                'price' => 90
            ],
            // 2 hours ago
            [
                'created_at' => $now->copy()->subHours(2),
                'price' => 110
            ],
            // 1 hour ago - negative change
            [
                'created_at' => $now->copy()->subHours(1),
                'price' => 80
            ],
            // 6 minutes ago
            [
                'created_at' => $now->copy()->subMinutes(6),
                'price' => 95
            ],
            // 5 minutes ago - positive change
            [
                'created_at' => $now->copy()->subMinutes(5),
                'price' => 120
            ],
            // Recent trades with mixed changes
            [
                'created_at' => $now->copy()->subMinutes(3),
                'price' => 85  // sharp drop
            ],
            [
                'created_at' => $now->copy()->subMinutes(2),
                'price' => 100  // recovery
            ],
            [
                'created_at' => $now->copy()->subMinutes(1),
                'price' => 95  // small dip
            ],
        ];

        foreach ($trades as $trade) {
            Trade::create([
                'launchpad_id' => 1,
                'txid' => fake()->uuid(),
                'address' => "0x91F708a8D27F2BCcCe8c00A5f812e59B1A5e48E6",
                'qty' => fake()->randomFloat(6, 0.1, 10),
                'price' => $trade['price'],
                'usd_price' => $trade['price'],  // Assuming 1:1 for simplicity
                'amount' => fake()->randomFloat(6, 0.1, 10),
                'usd' => fake()->randomFloat(2, 100, 1000),
                'type' => fake()->randomElement(TradeType::cases()),
                'created_at' => $trade['created_at']
            ]);
        }
    }
}
