<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";



import { usePage } from "@inertiajs/vue3";

import RecentTrade from "./RecentTrade.vue";

const props = defineProps({
    mine: Boolean,
    initialTrades: { type: Array, default: () => [] },
});
const recentTrades = ref([...props.initialTrades]);
const MAX_TRADES = 3;
const userAddress = computed(() => usePage().props.auth.user?.address ?? '');
const addTradeToRecent = (trade) => {
    // Add isNew flag for shake animation
    trade.isNew = true;
    if (props.mine && `${trade.address}`.toLowerCase() !== `${userAddress.value}`.toLowerCase()) return;
    // Add new trade to the beginning
    recentTrades.value.unshift({
        ...trade,
        isNew: true,
    });

    // Remove isNew flag after animation
    setTimeout(() => {
        const index = recentTrades.value.findIndex(
            (t) => t.id === trade.id,
        );
        if (index !== -1) {
            recentTrades.value[index].isNew = false;
        }
    }, 700); // Match shake animation duration

    // Keep only the latest 3 trades
    if (recentTrades.value.length > MAX_TRADES) {
        recentTrades.value.pop();
    }
};

onMounted(() => {
    window.Echo.channel("trades").listen("NewTradeEvent", addTradeToRecent);
});

onUnmounted(() => {
    window.Echo.channel("trades").stopListening("NewTradeEvent");
});
</script>

<template>
    <TransitionGroup
        name="trade-list"
        tag="div"
        class="grid md:grid-cols-3 lg:grid-cols-3 gap-5 w-full"
    >
        <div
            v-for="trade in recentTrades"
            :key="trade.id"
            :class="[
                'transition-all duration-500 ease-in-out',
                trade.isNew ? 'animate-shake' : '',
            ]"
        >
            <RecentTrade :trade="trade" />
        </div>
    </TransitionGroup>
</template>

<style>
.trade-list-move,
.trade-list-enter-active,
.trade-list-leave-active {
    transition: all 0.5s ease;
}

.trade-list-enter-from {
    opacity: 0;
    transform: translateX(-30px);
}

.trade-list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

@keyframes shake {
    0% {
        transform: translateX(0);
        background-color: #ff0;
    }

    10% {
        transform: translateX(-25px);
        background-color: #ff0;
    }

    20% {
        transform: translateX(25px);
        background-color: #ff0;
    }

    30% {
        transform: translateX(-25px);
    }

    40% {
        transform: translateX(25px);
    }

    50% {
        transform: translateX(-25px);
    }

    60% {
        transform: translateX(25px);
    }

    70% {
        transform: translateX(-25px);
    }

    80% {
        transform: translateX(25px);
    }

    90% {
        transform: translateX(-25px);
    }

    to {
        transform: translateX(0);
    }
}

.animate-shake {
    animation: shake 0.7s ease-in-out;
}
</style>
