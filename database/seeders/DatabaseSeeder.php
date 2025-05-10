<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Setting::query()->firstOrCreate([
            'id' => 1
        ], [
            'name' => 'SharkPump',
            'twitter' => "https://x.com/sharkpumpio",
            'youtube' => "https://youtube.com/@sharkpumpio",
            'telegram_group' => "https://t.me/sharkpumpio",
            'telegram_channel' => "https://t.me/sharkpumpio",
            'discord' => "https://discord.gg/sharkpumpio",
            'documentation' => "https://docs.sharkpump.io",
        ]);
        $this->call(RatesTableSeeder::class);
        $this->call(BotCommandSeeder::class);
    }
}
