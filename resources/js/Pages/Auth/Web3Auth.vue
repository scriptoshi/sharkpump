<script setup>
import { computed, ref, watch } from "vue";

import { router, usePage } from "@inertiajs/vue3";
import { createAppKit, useAppKit } from "@reown/appkit/vue";
import { useLocalStorage } from "@vueuse/core";
import { useAccount, useDisconnect, useSignMessage } from "@wagmi/vue";
import axios from "axios";
import { Power } from "lucide-vue-next";
import { blast, linea, sepolia } from "viem/chains";

import BaseButton from "@/Components/BaseButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { shortenAddress } from "@/lib/wagmi";
import {
    networks,
    projectId,
    projectName,
    projectUrl,
    useWagmiAdapter,
} from "@/lib/wagmi.js";
import SignatureModal from "@/Pages/Auth/SignatureModal.vue";
createAppKit({
    adapters: [
        useWagmiAdapter({
            rpc: usePage().props.rpc ?? "ankr",
            ankr: usePage().props.ankr,
            infura: usePage().props.infura,
            blast: usePage().props.blast,
            activeChains: usePage().props.activeChains,
        }),
    ],
    networks: networks.filter((n) => usePage().props.activeChains.includes(n.id)),
    projectId: projectId ?? usePage().props.projectId,
    metadata: {
        name: projectName,
        description: `${projectName} Telegram Crypto Agents`,
        url: projectUrl,
        icons: [],
    },
    themeVariables: {
        "--w3m-color-mix": "#404040",
        "--w3m-color-mix-strength": 40,
    },
    chainImages: {
        [sepolia.id]: "https://icons.llamao.fi/icons/chains/rsz_ethereum.jpg",
        [linea.id]: "https://icons.llamao.fi/icons/chains/rsz_linea.jpg",
        [blast.id]: "https://icons.llamao.fi/icons/chains/rsz_blast.jpg",
    },
});
const { open: openConnectModal } = useAppKit();
const showSignatureModal = ref(false);
defineProps({
    size: { type: String, default: "xs" },
    full: Boolean,
});
const authCheck = computed(() => !!usePage().props.auth.user);
const { address, isConnected } = useAccount();

const { disconnect } = useDisconnect();
const { signMessageAsync } = useSignMessage();
const modalShown = useLocalStorage("modalShown", false);

const handleVerify = async () => {
    try {
        // Get auth code
        const { data } = await axios.post(window.route("auth.code"));
        const authCode = data.authCode;
        // Sign message
        const signature = await signMessageAsync({
            message: authCode,
        });

        // Verify signature and login
        router.post(
            window.route("login"),
            {
                address: address.value,
                signature,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onFinish() {
                    showSignatureModal.value = false;
                    modalShown.value = false;
                }
            }
        );
    } catch (error) {
        showSignatureModal.value = false;
        console.error("Verification failed:", error);
    }

};
const isSigningOut = ref(false);
const signOut = async () => {
    isSigningOut.value = true;
    if (authCheck.value)
        router.post(
            window.route("logout"),
            {},
            {
                preserveState: true,
                onFinish() {
                    isSigningOut.value = true;
                    disconnect();
                },
            }
        );
};

const signIn = async () => {
    if (!authCheck.value) await handleVerify();
};



const openSignatureModal = () => {
    if (authCheck.value || isSigningOut.value || modalShown.value) return;
    showSignatureModal.value = true;
    modalShown.value = true;
};


watch([isConnected, authCheck], ([isConnected, authCheck]) => {
    if (isConnected && !authCheck) {
        return openSignatureModal();
    }
    if (!isConnected && authCheck) {
        return signOut();
    }

    showSignatureModal.value = false;
});
</script>

<template>
    <div class="flex gap-2">
        <template v-if="$page.props.auth.user && isConnected">
            <SecondaryButton
                :size="size"
                :class="{ 'w-full': full }"
                @click="openConnectModal()"
                outlined
            >
                {{ shortenAddress(address) }}
            </SecondaryButton>
            <DangerButton
                size="sm"
                :class="{ 'w-full': full }"
                :icon-mode="!full"
                outlined
                @click="signOut"
            >
                <span
                    class="mr-2"
                    v-if="full"
                > Logout </span>
                <Power class="w-4 h-4 stroke-[3]" />
            </DangerButton>
        </template>
        <template v-else-if="isConnected">
            <SecondaryButton
                :size="size"
                :class="{ 'w-full': full }"
                @click="handleVerify"
            >
                Verify Signature
            </SecondaryButton>
            <DangerButton
                :size="size"
                :class="{ 'w-full': full }"
                @click="disconnect()"
            >
                Disconnect
            </DangerButton>
        </template>
        <template v-else>
            <PrimaryButton
                :size="size"
                outlined
                :class="{ 'w-full': full }"
                @click="openConnectModal"
            >
                Connect Wallet
            </PrimaryButton>
        </template>
        <SignatureModal v-model:show="showSignatureModal">
            <BaseButton
                :class="{ 'w-full': full }"
                @click="signIn"
            >
                Verify Signature
            </BaseButton>
        </SignatureModal>
    </div>
</template>
