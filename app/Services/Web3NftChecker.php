<?php

namespace App\Services;

use App\Models\Nft;
use App\Models\User;
use Web3\Web3;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Exception;
use Illuminate\Support\Facades\Log;

class Web3NftChecker
{
    private $web3;
    private $contract;
    private $nft;

    // ERC721 balanceOf ABI
    private $abi = '[
        {
            "constant": true,
            "inputs": [
                {
                    "name": "owner",
                    "type": "address"
                }
            ],
            "name": "balanceOf",
            "outputs": [
                {
                    "name": "",
                    "type": "uint256"
                }
            ],
            "payable": false,
            "stateMutability": "view",
            "type": "function"
        }
    ]';

    // List of RPC endpoints by chain ID
    private static function rpcEndpoints($chainId)
    {
        $chain = TokenHolderService::getBlockchainName($chainId);
        return "https://rpc.ankr.com/$chain/";
    }

    /**
     * Create a new Web3NftChecker instance
     * 
     * @param Nft $nft
     * @throws Exception
     */
    public function __construct(Nft $nft)
    {
        $this->nft = $nft;

        if (!$nft->contract) {
            throw new Exception("NFT contract address is required");
        }

        $this->initializeWeb3($nft->chainId);
        $this->initializeContract($nft->contract);
    }

    /**
     * Initialize Web3 for a specific chain
     * 
     * @param string $chainId
     * @throws Exception
     */
    private function initializeWeb3(string $chainId)
    {
        $networkUrl = static::rpcEndpoints($chainId) . config('evm.ankr_key');

        try {
            $this->web3 = new Web3(new HttpProvider(new HttpRequestManager($networkUrl, 10))); // 10 second timeout
        } catch (Exception $e) {
            Log::error('Failed to initialize Web3', [
                'chain_id' => $chainId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Initialize contract for a specific NFT
     * 
     * @param string $contractAddress
     * @throws Exception
     */
    private function initializeContract(string $contractAddress)
    {
        try {
            $this->contract = new Contract($this->web3->provider, $this->abi);
            $this->contract->at($contractAddress);
        } catch (Exception $e) {
            Log::error('Failed to initialize contract', [
                'contract_address' => $contractAddress,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if a user has any NFTs from the collection
     *
     * @param User $user
     * @return bool
     * @throws Exception
     */
    public function hasNft(User $user): bool
    {
        return $this->getBalance($user) > 0;
    }

    /**
     * Get the number of NFTs a user holds from the collection
     *
     * @param User $user
     * @return int
     * @throws Exception
     */
    public function getBalance(User $user): int
    {
        if (!$user->address) {
            return 0;
        }

        try {
            $result = null;
            $error = null;

            // Call balanceOf with retry logic
            $retries = 3;
            while ($retries > 0 && $result === null) {
                try {
                    $this->contract->call('balanceOf', $user->address, function ($err, $response) use (&$result, &$error) {
                        if ($err !== null) {
                            $error = $err;
                            return;
                        }
                        $result = $response[0]->toString();
                    });

                    if ($result !== null) {
                        break;
                    }
                } catch (Exception $e) {
                    $error = $e;
                }

                $retries--;
                if ($retries > 0) {
                    sleep(1); // Wait 1 second before retry
                }
            }

            if ($result === null) {
                throw new Exception("Failed to get NFT balance after retries. Error: " . ($error ? $error->getMessage() : "Unknown error"));
            }

            return (int)$result;
        } catch (Exception $e) {
            Log::error('Failed to check NFT balance', [
                'nft_id' => $this->nft->id,
                'user_id' => $user->id,
                'contract' => $this->nft->contract,
                'address' => $user->address,
                'chain_id' => $this->nft->chainId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
