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

        <div class=" text-white mb-8 max-w-3xl">
            <div class="text-2xl font-bold mb-4">
                KYC NFT Minting
            </div>
            <h3 class="text-lg font-semibold mb-2">SHARK TOKEN 2025 AIRDROP</h3>
            SHARK TOKEN NFTS will be airdropped to users. SHARK represent the share of platform fees you can
            collect. All fees collected by the platform will be distributed to members.
            To collect fees, you will need to burn your share of sharktokens. This mechanism will ensure that token is
            deflationary over time.
            <br />
            To recieve the airdrop, ensure to collect all our NFTS below.
        </div>
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
