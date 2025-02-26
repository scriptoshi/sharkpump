
import { ref, watch } from "vue";

import { usePage } from '@inertiajs/vue3';
import { get } from "@vueuse/core";
import { useChains } from "@wagmi/vue";
import groupBy from 'lodash/groupBy';
import { createPublicClient, fallback, formatEther, http } from "viem";

import { multicall } from "@/hooks/multicall";
import {
    ankrTransports,
    blastapiTransports,
    infuraTransports
} from "@/lib/wagmi.js";

// Function to get all possible transport URLs for a chain
function getTransportUrls(chainId, rpcConfig) {
    const { ankrKey, infuraKey, blastKey } = rpcConfig;
    const urls = [];
    // Add additional transport URLs from different providers based on configuration
    if (ankrKey && ankrTransports(ankrKey)[chainId]) {
        urls.push(ankrTransports(ankrKey)[chainId]);
    }
    if (infuraKey && infuraTransports(infuraKey)[chainId]) {
        urls.push(infuraTransports(infuraKey)[chainId]);
    }
    if (blastKey && blastapiTransports(blastKey)[chainId]) {
        urls.push(blastapiTransports(blastKey)[chainId]);
    }

    return [...new Set(urls)]; // Remove duplicates
}

// Simple async function to fetch launchpad data for a specific chain
async function getLaunchpadDataByChainId(launchpads, rates, chainId, chains, rpcConfig) {
    try {
        const transportUrls = getTransportUrls(chainId, rpcConfig);

        if (transportUrls.length === 0) {
            console.warn(`No transport URLs configured for chain ${chainId}`);
            return [];
        }
        // Create transport array with fallback options
        const transports = transportUrls.map(url => http(url));
        // Create a client specific to this chain using fallback transport
        const client = createPublicClient({
            chain: chains.find(chain => chain.id === chainId),
            transport: fallback(transports, {
                rank: false,
                retryCount: 2,
                timeout: 10000
            })
        });

        const calls = launchpads.map(s => ({
            ...s,
            contract: {
                abi: s.factory.abi,
                address: s.contract
            },
            currentPhase: { functionName: 'currentPhase', args: [] },
            isFinalized: { functionName: 'isFinalized', args: [] },
            totalETHCollected: { functionName: 'totalETHCollected', args: [] },
            getBondingCurveSettings: { functionName: 'getBondingCurveSettings', args: [] }
        }));

        const [results] = await multicall([calls], { client }).catch(e => {
            console.error(`Error in multicall for chain ${chainId}:`, e);
            return [];
        });

        return results.map(r => {
            const rate = rates[chainId] ?? 1;
            const totalETHCollected = formatEther(r?.totalETHCollected ?? '0');
            const targetEther = formatEther(r.getBondingCurveSettings?.bondingTarget ?? '0');
            return {
                ...r,
                chainId,
                status: r.currentPhase === 0 ? 'prebond' : r.currentPhase === 1 ? 'bonding' : 'finalized',
                totalETHCollected,
                targetEther,
                marketCap: (totalETHCollected * 2 * rate).toFixed(6) * 1,
                percentage: (totalETHCollected / targetEther * 100).toFixed(2) * 1
            };
        });
    } catch (error) {
        console.error(`Error fetching launchpad data for chain ${chainId}:`, error);
        return [];
    }
}

// Main hook to handle all launchpads across different chains
export const useLaunchpadsData = (launchpads, rates) => {
    const allStakes = ref([]);
    const loading = ref(false);
    const chains = useChains();
    // Create RPC configuration object
    const rpcConfig = {
        ankrKey: usePage().props.ankr,
        infuraKey: usePage().props.infura,
        blastKey: usePage().props.blast,
    };

    const update = async () => {
        loading.value = true;
        allStakes.value = [];
        try {
            // Group launchpads by chainId
            const groupedLaunchpads = groupBy(get(launchpads), 'chainId');
            // Process each chain's launchpads
            const chainPromises = Object.entries(groupedLaunchpads).map(([chainId, chainLaunchpads]) =>
                getLaunchpadDataByChainId(chainLaunchpads, rates, Number(chainId), chains.value, rpcConfig)
            );
            // Wait for all chain updates to complete
            const results = await Promise.all(chainPromises);
            // Combine and set results from all chains
            allStakes.value = results.flat();
        } catch (error) {
            console.error('Error updating all launchpads:', error);
        } finally {
            loading.value = false;
        }
        return allStakes.value;
    };

    // Watch for changes in the launchpads data
    watch([launchpads], ([newLaunchpads]) => {
        if (newLaunchpads && newLaunchpads.length > 0) {
            update();
        }
    }, { immediate: true });

    return {
        launchpads: allStakes,
        update,
        loading
    };
};