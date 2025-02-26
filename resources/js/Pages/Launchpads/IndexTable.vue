# IndexTable.vue
<script setup>

import { computed } from "vue";

import { Link } from "@inertiajs/vue3";
import { ChevronDown, ChevronUp, LoaderCircle } from "lucide-vue-next";

import ChainSymbol from "@/Components/ChainSymbol.vue";
import ChangeComponent from "@/Components/ChangeComponent.vue";
import { useBillions } from "@/hooks";
import NetworkIcon from "@/Icons/NetworkIcon.vue";

const props = defineProps({
    launchpads: {
        type: Array,
        required: true,
    },
    loading: Boolean,
    currentSort: {
        type: String,
        default: ''
    },
    currentDir: {
        type: String,
        default: 'asc'
    }
});

const emit = defineEmits(['update:currentSort', 'update:currentDir']);
const currentSort = computed({
    get: () => props.currentSort,
    set: (val) => emit('update:currentSort', val)
});
const currentDir = computed({
    get: () => props.currentDir,
    set: (val) => emit('update:currentDir', val)
});
const columns = [
    { id: 'symbol', label: 'TOKEN', sortable: true },
    { id: 'percentage', label: '%', sortable: true },
    { id: 'marketCap', label: 'CAP', sortable: true },
    { id: 'age', label: 'AGE', sortable: true },
    { id: 'trades_count', label: 'TXS', sortable: true },
    { id: 'volume24h', label: 'VOL', sortable: true },
    { id: 'makers', label: 'MKRS', sortable: true },
    { id: 't5m', label: '5M', sortable: true },
    { id: 't1h', label: '1H', sortable: true },
    { id: 't6h', label: '6H', sortable: true },
    { id: 't24h', label: '24H', sortable: true }
];

const handleSort = (column) => {
    if (!column.sortable) return;
    currentSort.value = column.id;
    currentDir.value = props.currentSort === column.id && props.currentDir === 'asc' ? 'desc' : 'asc';
};


</script>

<template>
    <div class="w-full text-sm bg-gray-850 rounded-lg overflow-hidden">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-gray-400 text-sm">
                    <th class="px-4 py-3">

                        <span>#</span>
                    </th>
                    <th
                        v-for="column in columns"
                        :key="column.id"
                        class="px-4 py-3 cursor-pointer hover:text-gray-200"
                        @click="handleSort(column)"
                    >
                        <div
                            :class="{ '!text-primary': currentSort === column.id }"
                            class="flex items-center gap-1"
                        >
                            {{ column.label }}
                            <template v-if="currentSort === column.id">
                                <ChevronUp
                                    v-if="currentDir === 'asc'"
                                    class="w-4 h-4"
                                />
                                <ChevronDown
                                    v-else
                                    class="w-4 h-4"
                                />
                            </template>

                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="loading">
                    <td
                        class="px-4 py-2 text-gray-500"
                        colspan="11"
                    >
                        <LoaderCircle class="w-5  text-white h-5  animate-spin" />
                    </td>
                </tr>
                <tr
                    v-for="(lpd, rank) in launchpads"
                    :key="lpd.name"
                    :id="lpd.contract"
                    class="border-t border-gray-750 text-gray-300"
                >
                    <td class="px-4 py-2 text-gray-500">#{{ rank + 1 }}</td>
                    <td class="px-4 py-2 font-mono">
                        <Link
                            class="flex items-center gap-2"
                            :href="route('launchpads.show', { launchpad: lpd.contract })"
                        >
                        <NetworkIcon
                            class="w-5 h-5 rounded-full"
                            :chainId="lpd.chainId"
                        />
                        <img
                            :src="lpd.logo"
                            :alt="lpd.name"
                            class="w-5 h-5 rounded-lg"
                        />
                        <div class="flex items-center gap-1">
                            <div class="font-bold uppercase">{{ lpd.symbol }}</div>
                            <div class="text-gray-550 text-xs flex items-center">
                                /
                                <ChainSymbol :chainId="lpd.chainId" />
                            </div>
                        </div>
                        <div class="font-bold max-w-[200px] truncate">{{ lpd.name }}</div>
                        </Link>
                    </td>
                    <td class="px-4 py-2">{{ lpd.percentage }}%</td>
                    <td class="px-4 py-2">${{ useBillions(lpd.marketCap) }}</td>
                    <td class="px-4 py-2">{{ lpd.age }}</td>
                    <td class="px-4 py-2">{{ lpd.trades_count }}</td>
                    <td class="px-4 py-2">${{ useBillions(lpd.volume24h) }}</td>
                    <td class="px-4 py-2">{{ lpd.makers }}</td>
                    <ChangeComponent :change="lpd.t5m" />
                    <ChangeComponent :change="lpd.t1h" />
                    <ChangeComponent :change="lpd.t6h" />
                    <ChangeComponent :change="lpd.t24h" />
                </tr>
            </tbody>
        </table>

    </div>
</template>