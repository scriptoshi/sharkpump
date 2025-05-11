<script setup>
import { defineProps } from "vue";

import { BiArrowRightShort, CoTelegram } from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/PrimaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
defineProps({
    launchpad: {
        type: Object,
        required: true,
    },
    bot: {
        type: Object,
        default: () => ({}),
    },
});

// Format number to show as 5k, 10k, 1M, etc.
const formatNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + "M";
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + "K";
    }
    return num;
};
</script>
<template>
    <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg border border-gray-800 transition-all duration-300">
        <!-- Bot Info -->
        <div class="p-5 relative">
            <div class="absolute top-3 right-3 bg-primary text-gray-900 font-semibold text-xs px-2 py-1 rounded-full">
                {{ "Utility" }}
            </div>
            <div class="flex items-center mb-4">
                <img
                    class="w-10 h-10 rounded-full mr-3"
                    :src="bot.logo ?? launchpad.logo"
                    alt="Bot avatar"
                />
                <div>
                    <h3 class="text-xl font-bold text-white">{{ bot.name }}</h3>
                    <p class="text-primary text-sm"><span>@</span>{{ bot.username }}</p>
                </div>
            </div>

            <p class="text-gray-300 mb-4 line-clamp-3">{{ bot.description }}</p>

            <!-- Stats -->
            <div class="flex justify-between text-gray-400 text-sm mb-5">
                <div class="flex items-center">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                        />
                    </svg>
                    {{ formatNumber(bot.users_count ?? 0) }} users
                </div>
                <div class="flex gap-2 text-gray-250 bg-gray-750 items-center px-2 py-1 rounded-full">
                    <span>{{ bot.credits_per_message.toFixed(1) }}
                        {{ launchpad.symbol }}</span>
                    <span>per message</span>
                </div>
            </div>

            <!-- Tags -->
            <div class="flex flex-wrap gap-2 mb-5">
                <span
                    v-for="(tool, index) in bot.tools"
                    :key="index"
                    class="px-2 py-1 bg-gray-800 text-amber-300 text-xs rounded-full"
                >
                    #{{ tool }}
                </span>
            </div>

            <!-- Commands -->
            <h3 class="text-sm font-semibold mb-2">Commands</h3>
            <div class="flex flex-wrap gap-3 mb-5">
                <a
                    v-for="(command, index) in bot.commands"
                    :key="index"
                    :href="`https://t.me/${bot.username}?start=${command.name}`"
                    class="px-3 font-mono font-semibold py-1 bg-gray-700 text-red-300 text-xs rounded-md"
                >
                    <span class="mr-1">/</span>{{ command.name }}
                </a>
            </div>
            <!-- Action Buttons -->
            <div class="flex  gap-3">
                <PrimaryButton
                    :key="bot.name"
                    outlined
                    :href="`https://t.me/${bot.username}`"
                >
                    <VueIcon
                        :icon="CoTelegram"
                        class="w-5 h-5 mr-1 -ml-1 inline-flex"
                    />
                    <span>Chat with {{ bot.name }} Agent</span>
                    <VueIcon
                        :icon="BiArrowRightShort"
                        class="w-6 h-6 ml-1 -mr-1 inline-flex"
                    />
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>
