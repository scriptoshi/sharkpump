<script setup>


import { useChainId } from "@wagmi/vue";

import AppLayout from "@/Layouts/AppLayout.vue";
import NftMint from "@/Pages/Nfts/NftMint.vue";

defineProps({
    kycNfts: Object,
    planktonNfts: Object,
    fishNfts: Object,
    piranhaNfts: Object,
    barracudaNfts: Object,
    sharkNfts: Object,
    whaleNfts: Object,
    balances: Object,
});
const chainId = useChainId();
</script>

<template>

    <Head title="KYC NFT Minting" />
    <AppLayout>
        <NftMint
            v-if="kycNfts && kycNfts[chainId]"
            :balances="balances"
            :nfts="kycNfts"
        />
        <NftMint
            v-if="planktonNfts && planktonNfts[chainId]"
            :balances="balances"
            :nfts="planktonNfts"
        />
        <NftMint
            v-if="fishNfts && fishNfts[chainId]"
            :balances="balances"
            :nfts="fishNfts"
        />
        <NftMint
            v-if="piranhaNfts && piranhaNfts[chainId]"
            :balances="balances"
            :nfts="piranhaNfts"
        />
        <NftMint
            v-if="barracudaNfts && barracudaNfts[chainId]"
            :balances="balances"
            :nfts="barracudaNfts"
        />
        <NftMint
            v-if="sharkNfts && sharkNfts[chainId]"
            :balances="balances"
            :nfts="sharkNfts"
        />
        <NftMint
            v-if="whaleNfts && whaleNfts[chainId]"
            :balances="balances"
            :nfts="whaleNfts"
        />
    </AppLayout>
</template>
