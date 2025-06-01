import { ref } from 'vue';

import { usePage } from '@inertiajs/vue3';
import { useChains } from '@wagmi/vue';
import { createPublicClient, fallback, formatEther, http } from 'viem';

import { useReactiveContractCall } from '@/hooks/useContractCall';
import {
    ankrTransports,
    blastapiTransports,
    infuraTransports
} from '@/lib/wagmi.js';

// Get transport URLs for a specific chain
function getTransportUrls(chainId, rpcConfig) {
    const { ankrKey, infuraKey, blastKey } = rpcConfig;
    const urls = [];

    if (ankrKey && ankrTransports(ankrKey)[chainId]) {
        urls.push(ankrTransports(ankrKey)[chainId]);
    }
    if (infuraKey && infuraTransports(infuraKey)[chainId]) {
        urls.push(infuraTransports(infuraKey)[chainId]);
    }
    if (blastKey && blastapiTransports(blastKey)[chainId]) {
        urls.push(blastapiTransports(blastKey)[chainId]);
    }

    return [...new Set(urls)];
}

export function useNftInfo(nft) {
    const chains = useChains();
    const state = useReactiveContractCall(nft.abi, nft.contract);

    // Get RPC configuration from page props
    const rpcConfig = {
        ankrKey: usePage().props.ankr,
        infuraKey: usePage().props.infura,
        blastKey: usePage().props.blast,
    };

    const isPaused = ref(false);
    const totalSupply = ref('0');
    const maxSupply = ref('0');
    const mintPrice = ref('0');
    const baseUri = ref('');
    const isLoading = ref(true);
    const error = ref(null);
    const contractBalance = ref('0');

    const loadInfo = async () => {
        try {
            isLoading.value = true;
            error.value = null;

            const transportUrls = getTransportUrls(nft.chainId, rpcConfig);

            if (transportUrls.length === 0) {
                throw new Error(`No transport URLs configured for chain ${nft.chainId}`);
            }

            // Create transport array with fallback options
            const transports = transportUrls.map(url => http(url));

            // Create a client specific to this chain using fallback transport
            const client = createPublicClient({
                chain: chains.value.find(chain => chain.id === nft.chainId),
                transport: fallback(transports, {
                    rank: false,
                    retryCount: 2,
                    timeout: 10000
                })
            });

            // Get contract balance
            const balance = await client.getBalance({
                address: nft.contract,
            });
            contractBalance.value = balance;

            const calls = {
                'paused': [],
                'totalSupply': [],
                'maxSupply': [],
                'mintPrice': [],
                'baseURI': []
            };

            const results = await client.multicall({
                contracts: Object.keys(calls).map((functionName) => ({
                    address: nft.contract,
                    abi: nft.abi,
                    functionName,
                    args: calls[functionName]
                }))
            });

            const [
                pausedResult,
                totalSupplyResult,
                maxSupplyResult,
                mintPriceResult,
                baseUriResult
            ] = results;

            if (pausedResult.status === 'success') {
                isPaused.value = pausedResult.result;
            }

            if (totalSupplyResult.status === 'success') {
                totalSupply.value = totalSupplyResult.result.toString();
            }

            if (maxSupplyResult.status === 'success') {
                maxSupply.value = maxSupplyResult.result.toString();
            }

            if (mintPriceResult.status === 'success') {
                mintPrice.value = formatEther(mintPriceResult.result.toString());;
            }

            if (baseUriResult.status === 'success') {
                baseUri.value = baseUriResult.result;
            }

        } catch (err) {
            error.value = err.message;
            console.error('Error loading NFT info:', err);
        } finally {
            isLoading.value = false;
        }
    };

    const refresh = () => loadInfo();

    // Initial load
    loadInfo();

    return {
        state,
        isPaused,
        totalSupply,
        maxSupply,
        mintPrice,
        baseUri,
        isLoading,
        error,
        contractBalance,
        refresh
    };
}
