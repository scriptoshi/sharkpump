<script setup>

import {
    Building2,
    Coins,
    LineChart,
    SquareActivity,
    TrendingDown,
    TrendingUp,
    Users
} from "lucide-vue-next";

import AddressLink from "@/Components/AddressLink.vue";
import BaseButton from "@/Components/BaseButton.vue";
import { useBillions } from "@/hooks";
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({
    leads: Object,
    launchpad: Object,
    type: String,
    period: String,
});

const filters = [
    { id: 'volume', label: 'Volume', icon: SquareActivity },
    { id: 'buyers', label: 'Top Buyers', icon: TrendingUp },
    { id: 'sellers', label: 'Top Sellers', icon: TrendingDown },
    { id: 'prebond', label: 'Prebond', icon: Coins },
    { id: 'profits', label: 'Profits', icon: LineChart },
    { id: 'trades', label: 'Trades', icon: Users },
    { id: 'launchpads', label: 'Launchpads', icon: Building2 }
];

const label = {
    'D': '1 day',
    'W': ' 1 week',
    '7D': ' 7 days',
    '2W': ' 2 weeks',
    '1M': ' 1 month (30 days)',
    '3M': ' 3 months (90 days)',
    '6M': ' 6 months (180 days)',
    '1Y': ' 1 year (365 days)',
};
const periods = [
    { id: 'D', label: '24H' },
    { id: '7D', label: '7D' },
    { id: '2W', label: '2W' },
    { id: '1M', label: '1M' },
    { id: '3M', label: '3M' },
    { id: '6M', label: '6M' },
    { id: '1Y', label: '1Y' }
];


</script>

<template>
    <AppLayout>
        <div class="container my-8">
            <div
                v-if="launchpad"
                class="mb-6"
            >
                <h2 class="text-2xl !text-gray-150 font-bold">{{ launchpad.name }} {{ period }} Leaderboard</h2>
            </div>
            <h2
                v-else
                class="text-2xl mb-6  !text-gray-150 font-bold "
            >{{ label[period] }} Leaderboard</h2>
            <!-- Filter Buttons -->
            <div class="flex gap-4 items-center mb-6 overflow-x-auto">
                <BaseButton
                    v-for="filter in filters"
                    :key="filter.id"
                    :href="route('trades.leaderboard', {
                        type: filter.id,
                        ...(launchpad ? { pool: launchpad.contract } : {})
                    })"
                    :secondary="filter.id !== type"
                    link
                    size="xs"
                    class="font-semibold !px-4 whitespace-nowrap"
                >
                    <component
                        :is="filter.icon"
                        class="w-4 h-4 mr-1 -ml-1 inline-flex"
                    />
                    {{ filter.label }}
                </BaseButton>
            </div>

            <!-- Period Buttons -->
            <div class="flex gap-3 items-center mb-6 overflow-x-auto">
                <BaseButton
                    v-for="prd in periods"
                    :key="prd.id"
                    :secondary="prd.id !== period"
                    :href="route('trades.leaderboard', {
                        type,
                        period: prd.id,
                        ...(launchpad ? { pool: launchpad.contract } : {})
                    })"
                    link
                    size="xss"
                    class="font-semibold !px-3"
                >
                    {{ prd.label }}
                </BaseButton>
            </div>

            <!-- Leaderboard Table -->
            <div class="w-full text-sm bg-gray-850 rounded-lg overflow-hidden">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="text-left text-gray-400 text-sm">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Address</th>
                            <th class="px-4 py-3">
                                {{ type === 'trades' ? 'Trade Count' :
                                    type === 'launchpads' ? 'Total Pools Joined ' :
                                        'Volume' }}
                            </th>
                            <th
                                v-if="['buyers', 'sellers', 'trades'].includes(type)"
                                class="px-4 py-3"
                            >
                                Count
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(lead, index) in leads.data"
                            :key="lead.address"
                            class="border-t border-gray-750 text-gray-300"
                        >
                            <td class="px-4 py-2 text-gray-500">#{{ index + 1 }}</td>
                            <td class="px-4 py-2 font-mono">
                                <AddressLink
                                    :chain-id="56"
                                    :address="lead.address"
                                />
                            </td>
                            <td class="px-4 py-2">
                                <template v-if="type === 'trades'">
                                    {{ lead.trade_count }}
                                </template>
                                <template v-else-if="type === 'launchpads'">
                                    {{ lead.launchpad_count }}
                                </template>
                                <template v-else>
                                    ${{ useBillions(lead.total_volume || lead.total_purchased ||
                                        lead.total_sold || lead.total_prebond || lead.total_profit) }}
                                </template>
                            </td>
                            <td
                                v-if="['buyers', 'sellers', 'trades'].includes(type)"
                                class="px-4 py-2"
                            >
                                {{ lead.buy_count || lead.sell_count || lead.trade_count }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>