<script setup>
import { Link } from "@inertiajs/vue3";

import ChainSymbol from "@/Components/ChainSymbol.vue";
import { useBillions } from "@/hooks";
import { shortenAddress } from "@/lib/wagmi";

defineProps({
    trade: Object,
});
</script>
<template>
    <Link
        class="block w-full"
        v-if="trade?.contract"
        :href="route('launchpads.show', { launchpad: trade?.contract })"
    >
    <div class="backdrop-blur-lg bg-gray-900/40 border border-gray-700/30 hover:bg-gray-800/50 p-4 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-primary/5 hover:-translate-y-0.5">
        <div class="flex flex-col gap-2">
            <!-- Top Section with Type Badge and Amount -->
            <div class="flex items-center justify-between">
                <span
                    v-if="trade.type == 'sell'"
                    class="text-red-300 rounded-lg text-xs uppercase px-2.5 py-1 border border-red-500/20 bg-red-500/10 font-medium tracking-wider backdrop-blur-sm"
                >
                    SELL
                </span>
                <span
                    v-if="trade.type == 'buy'"
                    class="text-emerald-300 rounded-lg text-xs uppercase px-2.5 py-1 border border-emerald-500/20 bg-emerald-500/10 font-medium tracking-wider backdrop-blur-sm"
                >
                    BUY
                </span>
                <span
                    v-if="trade.type == 'prebond'"
                    class="text-primary rounded-lg text-xs uppercase px-2.5 py-1 border border-primary/20 bg-primary/10 font-medium tracking-wider backdrop-blur-sm"
                >
                    BOND
                </span>
                
                <div class="text-xs px-2 py-0.5 text-gray-400 border rounded-full border-gray-600/30 backdrop-blur-sm">
                    {{ trade.date }}
                </div>
            </div>

            <!-- Token Info and Address Section -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="relative group">
                        <img
                            :alt="trade.name"
                            class="w-8 h-8 rounded-lg shadow-lg transition-transform group-hover:scale-105"
                            :src="trade.logo"
                            loading="lazy"
                        />
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-300">
                            {{ useBillions(trade.qty) }} {{ trade.symbol }}
                        </span>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="font-medium text-primary">
                                {{ parseFloat(trade.amount).toFixed(4) * 1 }}
                                <ChainSymbol :chain-id="trade.chainId" />
                            </span>
                            <span class="text-gray-400">
                                (~${{ useBillions(trade.usd) }})
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-xs text-gray-400 bg-gray-800/50 px-2 py-0.5 rounded-full backdrop-blur-sm border border-gray-700/30">
                    {{ shortenAddress(trade.address, 10) }}
                </div>
            </div>
        </div>
    </div>
    </Link>
</template>
