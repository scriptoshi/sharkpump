<?php

namespace App\Services;

use App\Models\Rate as ModelsRate;
use Cache;
use Exception;
use Illuminate\Support\Facades\Http;

class Rate
{
    /**
     * Base URL for alternative.me API
     */
    protected static $baseUrl = "https://api.alternative.me/v2";

    /**
     * Call the alternative.me API
     */
    public static function api($endpoint, $params = [])
    {
        $url = static::$baseUrl . "/" . $endpoint;

        // Add query parameters if they exist
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $response = Http::get($url);

        if (!$response->successful()) {
            throw new \Exception("Failed to fetch api for $endpoint");
        }

        return $response->json();
    }

    /**
     * Cache the cryptocurrency symbols for easy retrieval
     */
    public static function symbols()
    {
        return Cache::remember('--rates--symbols--', 60 * 60, function () {
            $assets = [];

            // Get all crypto data - limit=0 returns all available data
            $cryptoData = static::api('ticker', ['limit' => 0]);

            if (isset($cryptoData['data'])) {
                // If data is returned as an object/dictionary
                if (is_array($cryptoData['data']) && !isset($cryptoData['data'][0])) {
                    foreach ($cryptoData['data'] as $id => $crypto) {
                        if (isset($crypto['symbol']) && isset($crypto['quotes']['USD']['price'])) {
                            $assets[$crypto['symbol']] = floatval($crypto['quotes']['USD']['price']);
                        }
                    }
                }
                // If data is returned as an array
                else {
                    foreach ($cryptoData['data'] as $crypto) {
                        if (isset($crypto['symbol']) && isset($crypto['quotes']['USD']['price'])) {
                            $assets[$crypto['symbol']] = floatval($crypto['quotes']['USD']['price']);
                        }
                    }
                }
            }

            return $assets;
        });
    }

    /**
     * Update the rates table for the chain currencies
     */
    public static function update()
    {
        // Get all crypto prices
        $tickerData = static::api('ticker', ['limit' => 0]);
        $assets = [];

        if (isset($tickerData['data'])) {
            // If data is returned as an object/dictionary
            if (is_array($tickerData['data']) && !isset($tickerData['data'][0])) {
                foreach ($tickerData['data'] as $id => $crypto) {
                    if (isset($crypto['symbol']) && isset($crypto['quotes']['USD']['price'])) {
                        $assets[$crypto['symbol']] = floatval($crypto['quotes']['USD']['price']);
                    }
                }
            }
            // If data is returned as an array
            else {
                foreach ($tickerData['data'] as $crypto) {
                    if (isset($crypto['symbol']) && isset($crypto['quotes']['USD']['price'])) {
                        $assets[$crypto['symbol']] = floatval($crypto['quotes']['USD']['price']);
                    }
                }
            }
        }

        ModelsRate::pluck('symbol')
            ->unique()
            ->each(function ($symbol) use ($assets) {
                $rate = $assets[$symbol] ?? null;
                if (!$rate) return;
                ModelsRate::where('symbol', $symbol)->update(['usd_rate' => $rate]);
            });
    }
}
