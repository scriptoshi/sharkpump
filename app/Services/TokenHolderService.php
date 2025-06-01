<?php

namespace App\Services;

use App\Models\Launchpad;
use App\Models\Holder;
use App\Models\Nft;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Exception;

class TokenHolderService
{
    protected $apiKey;
    protected $baseUrl = 'https://rpc.ankr.com/multichain';
    protected const CHUNK_SIZE = 1000; // Number of records to process at once

    public function __construct()
    {
        $this->apiKey = config('evm.ankr_key');
    }

    /**
     * Update holders for all active launchpads
     */
    public function updateAllHolders()
    {
        try {
            $launchpads = Launchpad::query()
                ->whereNotNull('token')
                ->get();

            foreach ($launchpads as $launchpad) {
                $this->updateHolders($launchpad);
            }
        } catch (Exception $e) {
            Log::error('Failed to update all holders: ' . $e->getMessage());
        }
    }

    /**
     * Update holders for a specific launchpad using bulk upsert
     */
    public function updateHolders(Launchpad $launchpad)
    {
        try {
            $response = $this->fetchHolders($launchpad->token, $launchpad->chainId);

            if (!isset($response['holders'])) {
                Log::error("Invalid response for token {$launchpad->token}");
                return;
            }

            // Get all user addresses and IDs once
            $users = User::query()->pluck('id', 'address');
            $checksumAddresses = $users->keys()->mapWithKeys(function ($address) use ($users) {
                return [strtolower($address) => $users[$address]];
            });
            $userAddresses = $users->union($checksumAddresses);

            // Prepare holder records for bulk upsert
            $holders = collect($response['holders'])->map(function ($holder) use ($launchpad, $userAddresses) {
                return [
                    'launchpad_id' => $launchpad->id,
                    'address' => strtolower($holder['holderAddress']),
                    'qty' => $holder['balance'],
                    'user_id' => $userAddresses[$holder['holderAddress']] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            });

            // Process in chunks to avoid memory issues
            $holders->chunk(self::CHUNK_SIZE)->each(function ($chunk) {
                Holder::upsert(
                    $chunk->toArray(),
                    ['launchpad_id', 'address'], // Unique keys for matching
                    ['qty', 'user_id', 'updated_at'] // Columns to update if record exists
                );
            });

            // Clean up old holders that no longer exist
            $currentHolderAddresses = collect($response['holders'])->pluck('holderAddress')->toArray();
            Holder::where('launchpad_id', $launchpad->id)
                ->whereNotIn('address', $currentHolderAddresses)
                ->delete();
        } catch (Exception $e) {
            Log::error("Failed to update holders for launchpad {$launchpad->id}: " . $e->getMessage());
        }
    }

    /**
     * Update holders for all active launchpads
     */
    public function updateAllNftHolders()
    {
        try {
            $nfts = Nft::query()
                ->whereNotNull('contract')
                ->get();
            foreach ($nfts as $nft) {
                $this->updateNftHolders($nft);
            }
        } catch (Exception $e) {
            Log::error('Failed to update all holders: ' . $e->getMessage());
        }
    }

    public function updateNftHolders(Nft $nft)
    {
        try {
            $response = $this->fetchNftHolders($nft->contract, $nft->chainId);
            if (!isset($response['holders'])) {
                Log::error("Invalid response for NFT holders {$nft->contract}");
                return;
            }
            // Convert all holder addresses to lowercase
            $holders = array_map('strtolower', $response['holders']);
            // Update users where address matches (case-insensitive)
            User::query()
                ->whereNull('verified_address')
                ->where(function ($query) use ($holders) {
                    $query->whereRaw('LOWER(address) IN (?)', [array_values($holders)])
                        ->orWhereIn('address', $holders);
                })
                ->update([
                    'verified_address' => true
                ]);
        } catch (Exception $e) {
            Log::error("Failed to update holders for NFT {$nft->id}: " . $e->getMessage());
        }
    }

    /**
     * Fetch holders from Ankr API
     */
    protected function fetchHolders(string $tokenAddress, string $chainId, int $page = 1): array
    {
        $response = Http::post($this->baseUrl . '/' . $this->apiKey, [
            'jsonrpc' => '2.0',
            'method' => 'ankr_getTokenHolders',
            'params' => [
                'blockchain' => static::getBlockchainName($chainId),
                'contractAddress' => $tokenAddress,
                'pageSize' => 10000,
                'pageNumber' => $page
            ],
            'id' => 1
        ]);

        if ($response->failed()) {
            throw new Exception('Ankr API request failed: ' . $response->body());
        }

        return $response->json()['result'] ?? [];
    }

    /**
     * Fetch holders from Ankr API
     */
    protected function fetchNftHolders(string $tokenAddress, string $chainId, int $page = 1): array
    {
        $response = Http::post($this->baseUrl . '/' . $this->apiKey, [
            'jsonrpc' => '2.0',
            'method' => 'ankr_getNFTHolders',
            'params' => [
                'blockchain' => static::getBlockchainName($chainId),
                'contractAddress' => $tokenAddress,
            ],
            'id' => 1
        ]);
        if ($response->failed()) {
            throw new Exception('Ankr API request failed: ' . $response->body());
        }
        return $response->json()['result'] ?? [];
    }

    /**
     * Convert chainId to blockchain name for Ankr API
     */
    public static function getBlockchainName(string $chainId): string
    {
        return match ($chainId) {
            '1' => 'eth',
            '5' => 'eth_holesky',
            '10' => 'optimism',
            '11155111' => 'eth_sepolia',
            '56' => 'bsc',
            '100' => 'gnosis',
            '137' => 'polygon',
            '420' => 'optimism_testnet',
            '8453' => 'base',
            '84532' => 'base_sepolia',
            '42161' => 'arbitrum',
            '43114' => 'avalanche',
            '43113' => 'avalanche_fuji',
            '250' => 'fantom',
            '1101' => 'polygon_zkevm',
            '59144' => 'linea',
            '534352' => 'scroll',
            '57' => 'syscoin',
            '40' => 'telos',
            '14' => 'flare',
            '570' => 'rollux',
            '660279' => 'xai',
            default => throw new Exception("Unsupported chainId: {$chainId}")
        };
    }
}
