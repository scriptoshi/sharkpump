<script setup>
import { computed } from "vue";

import { useForm } from "@inertiajs/vue3";
import { useAccount, useChainId } from "@wagmi/vue";
import { ShieldCheckIcon } from "lucide-vue-next";

import ChainSymbol from "@/Components/ChainSymbol.vue";
import Loading from "@/Components/Loading.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import { useContractFees, useReactiveContractCall } from "@/hooks/useContractCall";
import Web3Auth from "@/Pages/Auth/Web3Auth.vue";

const props = defineProps({
    nfts: Object,
    balances: Object,
});

const chainId = useChainId();
const nft = computed(() => props.nfts[chainId.value]);
const nftId = computed(() => nft.value?.id);
const { address } = useAccount();
// Form for verification
const verifyForm = useForm({
    nft_id: nftId,
});

// Contract interaction state
const abi = computed(() => nft.value?.abi);
const contract = computed(() => nft.value?.contract);
const state = useReactiveContractCall(abi, contract);

// Get minting fees if any
const { fees, feesFormatted } = useContractFees(abi, contract, "mintPrice");

// Handle NFT minting
const mint = async () => {
    await state.call("safeMint", [address.value], fees.value);
    if (state.error) return;
    // After successful minting, verify the user
    verify();
};

// Handle manual verification
const verify = () => {
    verifyForm.post(window.route("nfts.verify", { nft: nftId.value }), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="card w-full mb-6 mx-auto dark:bg-gray-850 sm:p-12 sm:!pt-6 h-full border-0 card-border">
        <div class="card-body card-gutterless h-full">
            <div class="flex items-center justify-between mb-5">
                <h3 class="w0 flex items-center gap-2">
                    <ShieldCheckIcon class="w-7 h-7 stroke-[0.7] text-sky-400" />
                    {{ $t("Claim Your") }} {{ nft?.name }} NFT
                </h3>
            </div>

            <div class="grid gap-6">
                <!-- NFT Information -->
                <div
                    :class="balances[nft?.id] > 0 ? 'bg-emerald-700/10' : 'bg-gray-750/50'"
                    class="border p-6 border-gray-650  rounded-lg"
                >
                    <div class="mb-4 flex justify-between">
                        <h4 class="text-lg !text-primary font-medium">
                            Balance : {{ balances[nft.id] ?? 0 }} {{ nft.symbol }}
                        </h4>
                        <div class="flex items-center space-x-2">
                            <div :class="[
                                'w-3 h-3 rounded-full',
                                balances[nft?.id] > 0
                                    ? 'bg-green-500'
                                    : 'bg-gray-500',
                            ]"></div>
                            <span>{{ nft.type }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <img
                            v-if="nft.image"
                            :src="nft.image"
                            alt="NFT Image"
                            class="w-36 h-36 rounded-lg object-cover"
                        />
                        <div>

                            <div class="space-y-2">
                                <p class="text-gray-300">
                                    {{
                                        nft.description
                                    }}
                                </p>
                            </div>
                            <div
                                v-if="$page.props.auth.user"
                                class="pt-5"
                            >
                                <div class="flex flex-col sm:flex-row items-center gap-3 justify-end">
                                    <TxStatus
                                        class="w-full sm:w-[unset]"
                                        :state="state"
                                    />
                                    <!-- Mint Button -->
                                    <PrimaryButton
                                        class="w-full sm:w-[unset]"
                                        @click="mint"
                                        :disabled="state.busy || verifyForm.processing"
                                    >
                                        <Loading
                                            class="mr-2 -ml-1 inline-block w-5 h-5"
                                            v-if="state.busy"
                                        />
                                        <span>
                                            {{ $t("Mint NFT") }}
                                            {{ parseFloat(feesFormatted).toFixed(3) * 1 }}
                                        </span>
                                        <ChainSymbol
                                            class="ml-1"
                                            :chain-id="chainId"
                                        />
                                    </PrimaryButton>
                                    <PrimaryButton
                                        v-if="!$page.props.auth.user.hasKyc"
                                        class="w-full sm:w-[unset]"
                                        @click="verify"
                                        outlined
                                        :disabled="verifyForm.processing"
                                    >
                                        <Loading
                                            class="mr-2 -ml-1 inline-block w-5 h-5"
                                            v-if="verifyForm.processing"
                                        />
                                        <span>{{ $t("Update Balance Info") }}</span>
                                    </PrimaryButton>
                                </div>
                            </div>
                            <div
                                v-else
                                class="pt-5"
                            >
                                <div class="flex flex-col sm:flex-row items-center gap-3 justify-end">
                                    <Web3Auth />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
