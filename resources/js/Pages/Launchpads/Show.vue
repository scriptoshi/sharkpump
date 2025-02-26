<script setup>
import { ref } from "vue";

import { useAccount, useChainId } from "@wagmi/vue";
import { ClipboardCopy, MessageSquare, TvMinimalPlay, UsersRound } from "lucide-vue-next";
import { RiExchangeLine } from "oh-vue-icons/icons";
import { uid } from "uid";

import AddressLink from "@/Components/AddressLink.vue";
import BaseButton from "@/Components/BaseButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
import WeCopy from "@/Components/WeCopy.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import BarButton from "@/Pages/Launchpads/BarButton.vue";
import Chat from "@/Pages/Launchpads/Chat.vue";
import BuyCard from "@/Pages/Launchpads/TradingView/BuyCard.vue";
import DevStream from "@/Pages/Launchpads/TradingView/DevStream.vue";
import Holders from "@/Pages/Launchpads/TradingView/Holders.vue";
import Info from "@/Pages/Launchpads/TradingView/Info.vue";
import LockCard from "@/Pages/Launchpads/TradingView/LockCard.vue";
import Trades from "@/Pages/Launchpads/TradingView/Trades.vue";
import TradingViewChart from "@/Pages/Launchpads/TradingView/TradingViewChart.vue";

defineProps({
    launchpad: Object,
    top: Array,
    stats: Object,
    rate: Object,
    poolstats: Object,
});

const tabs = [
    { name: "Chat", icon: MessageSquare },
    { name: "Trades", icon: RiExchangeLine, vueicon: true },
    { name: "Holders", icon: UsersRound },
    { name: "Dev Stream", icon: TvMinimalPlay },
];
const activeTab = ref("Chat");
const chainId = useChainId();
const { address } = useAccount();
const random = ref(uid());
const update = () => random.value = uid();
</script>
<template>
    <AppLayout compact>
        <template #header>
            <div class="flex items-center w-full bg-gray-850 h-12 relative overflow-x-hidden">
                <div class="flex w-full items-center overflow-x-auto [scrollbar-width:none]">
                    <div class="flex w-full items-center">
                        <BarButton
                            v-for="launch in top"
                            :key="launch.id"
                            :launch="launch"
                            :active="launch.id === launchpad.id"
                        />
                    </div>
                </div>
                <div
                    class="h-12 w-20 absolute right-0 pointer-events-none bg-gradient-to-r from-transparent via-gray-850/50 to-gray-850">
                </div>
            </div>
        </template>
        <div class="flex flex-col items-center md:items-start md:flex-row gap-8 mt-4 md:justify-center">
            <div class="flex flex-col gap-2 w-full px-4 md:px-0 md:w-2/3">
                <div class="flex items-center gap-3">
                    <img
                        class="w-5 h-5 rounded-full"
                        :src="launchpad.logo"
                    />
                    <h3 class="uppercase max-w-xs truncate">{{ launchpad.name }}</h3>
                    <AddressLink
                        :chainId="launchpad.chainId"
                        :address="launchpad.token"
                        :len="12"
                        v-slot="{ etherScanLink, shortAddress }"
                    >
                        <a
                            :href="etherScanLink"
                            target="_blank"
                            class="cursor-pointer text-gray-100 select-none font-semibold hover:text-primary"
                        >
                            {{ shortAddress }}
                        </a>
                    </AddressLink>
                    <WeCopy
                        after
                        :text="launchpad.token"
                    >
                        <ClipboardCopy class="w-4 h-4 text-white" />
                    </WeCopy>
                </div>
                <div class="h-4/8">
                    <TradingViewChart :launchpad="launchpad" />
                </div>
                <div class="flex gap-2 h-fit mt-6">
                    <BaseButton
                        v-for="tab in tabs"
                        :key="tab.name"
                        :outlined="tab.name != activeTab"
                        @click="activeTab = tab.name"
                        size="xs"
                    >
                        <VueIcon
                            :icon="RiExchangeLine"
                            v-if="tab.vueicon"
                            class="w-4 h-4 mr-1 -ml-1 inline-flex"
                        />
                        <component
                            v-else
                            class="w-4 h-4 mr-1 -ml-1 inline-flex"
                            :is="tab.icon"
                        />

                        {{ tab.name }}
                    </BaseButton>
                </div>

                <div class="text-gray-300 mt-4 grid gap-1 relative h-full mb-8">
                    <Chat
                        v-if="activeTab == 'Chat'"
                        :launchpadId="launchpad.id"
                        :devId="launchpad.user_id"
                        :initial-messages="$page.props.msgs"
                        class="md:w-full"
                    />
                    <Trades
                        :trades="$page.props.trades"
                        :chainId="launchpad.chainId"
                        :bcurve="launchpad.contract"
                        v-if="activeTab == 'Trades'"
                    />
                    <Holders
                        :holders="$page.props.holders"
                        :chainId="launchpad.chainId"
                        :launchpad="launchpad"
                        :usdRate="rate.usd_rate"
                        v-if="activeTab == 'Holders'"
                    />
                    <DevStream
                        :launchpad="launchpad"
                        v-if="activeTab == 'Dev Stream'"
                    />
                </div>
            </div>
            <div class="grid mb-12 gap-4 w-full px-4 h-fit md:w-1/3 md:mx-auto md:px-3">
                <BuyCard
                    v-if="chainId"
                    @tx="update"
                    :key="`trade-${chainId}-${launchpad.chaind}-${address}`"
                    :launchpad="launchpad"
                />
                <template v-if="launchpad.isOwner">

                    <LockCard
                        v-if="chainId"
                        :key="`lock-${chainId}-${launchpad.chaind}-${address}`"
                        :launchpad="launchpad"
                    />
                </template>

                <Info
                    :rank="stats.rank"
                    :rate="rate"
                    v-if="chainId"
                    :totalVolume="stats.totalVolume"
                    :key="`info-${chainId}-${launchpad.chaind}-${address}-${random}`"
                    :totalLaunchpads="stats.totalLaunchpads"
                    :launchpad="launchpad"
                />
            </div>
        </div>
    </AppLayout>
</template>
