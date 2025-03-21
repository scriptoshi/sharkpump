<script setup>
import { Link } from "@inertiajs/vue3";
import { useChainId } from "@wagmi/vue";
import { DollarSign, LineChart, Lock, Rocket } from "lucide-vue-next";

import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Dialog, DialogContent } from "@/Components/ui/dialog";
import ModalListItem from "@/Layouts/AppLayout/ModalListItem.vue";

defineProps({
    show: Boolean,
});
defineEmits(["update:show"]);
const chainId = useChainId();
</script>

<template>
    <Dialog
        :open="show"
        @update:open="(val) => $emit('update:show', val)"
    >
        <DialogContent class="max-h-screen overflow-x-auto sm:max-w-3xl">
            <div class="text-white">
                <Link
                    class="flex sm:mb-4 items-center justify-center"
                    href="/"
                >
                <ApplicationLogo class="block h-6 w-auto font-semibold" />
                </Link>
                <header class="text-center mb-6">
                    <h1 class="text-4xl font-bold">
                        {{ $t("Fair Launch Protocol") }}
                    </h1>
                    <p class="text-gray-300">
                        {{
                            $t("No pre-sale, no insiders, maximum transparency")
                        }}
                    </p>
                </header>
            </div>
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 text-white p-6">
                <div class="max-w-3xl mx-auto">
                    <div class="grid gap-6">
                        <div class="bg-gray-800 rounded-lg p-4 shadow-lg">
                            <h2 class="text-xl font-semibold mb-4">
                                {{ $t("Key Features") }}
                            </h2>
                            <ul class="space-y-3 grid sm:grid-cols-2">
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                    <span>{{ $t("Max 1B supply") }}</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                    <span>
                                        {{
                                            $t("Ownership renounced, immutable")
                                        }}
                                    </span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                    <span>
                                        {{
                                            $t("Fully audited smart contracts")
                                        }}
                                    </span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                    <span>
                                        {{ $t("Buy and sell at any time") }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-6 sm:grid-cols-2">
                        <div class="bg-gray-800 rounded-lg p-2 shadow-lg">
                            <h2 class="text-xl font-semibold mb-4 flex items-center">
                                <LineChart class="w-5 h-5 mr-2" />
                                {{ $t("Wen Moon?") }}
                            </h2>
                            <div class="space-y-4">
                                <p class="text-gray-300">
                                    {{ $t("Migration triggers at:") }}
                                </p>
                                <ul class="space-y-2">
                                    <ModalListItem
                                        :key="chainId"
                                        :chainId="chainId"
                                        v-if="$page.props.factories[chainId]"
                                        :rate="$page.props.usdRates[chainId]"
                                        :factory="$page.props.factories[chainId]"
                                    />
                                </ul>
                            </div>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-2 shadow-lg text-center">
                            <Lock class="w-8 h-8 mx-auto mb-4 text-purple-400" />
                            <h3 class="font-semibold mb-2">
                                {{ $t("Locked Liquidity") }}
                            </h3>
                            <p class="text-sm text-gray-300">
                                {{ $t("All liquidity is locked forever") }}
                            </p>
                        </div>

                        <div class="bg-gray-800 rounded-lg p-2 shadow-lg text-center">
                            <DollarSign class="w-8 h-8 mx-auto mb-4 text-green-400" />
                            <h3 class="font-semibold mb-2">
                                {{ $t("Launch, Lock & Earn") }}
                            </h3>
                            <p class="text-sm text-gray-300">
                                {{
                                    $t(
                                        "Launch on Uniswap and earn LP rewards for life!",
                                    )
                                }}
                            </p>
                        </div>

                        <div class="bg-gray-800 rounded-lg p-2 shadow-lg text-center">
                            <Rocket class="w-8 h-8 mx-auto mb-4 text-blue-400" />
                            <h3 class="font-semibold mb-2">
                                {{ $t("Enhanced Token Info") }}
                            </h3>
                            <p class="text-sm text-gray-300">
                                {{
                                    $t(
                                        "Free Enhanced Token Info for better DEX Tools visibility",
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
