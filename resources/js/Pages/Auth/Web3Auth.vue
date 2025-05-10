<script setup>
import { computed, ref, watch } from "vue";

import { router, usePage } from "@inertiajs/vue3";
import { createAppKit, useAppKit } from "@reown/appkit/vue";
import { useAccount, useAccountEffect, useDisconnect, useSignMessage } from "@wagmi/vue";
import axios from "axios";
import { Power } from "lucide-vue-next";
import { blast, linea, sepolia } from "viem/chains";

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

defineProps({
	size: { type: String, default: "xs" },
	full: Boolean,
});
const authCheck = computed(() => !!usePage().props.auth.user);
const { address, isConnected } = useAccount();

const { disconnect } = useDisconnect();
const { signMessageAsync } = useSignMessage();

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
			}
		);
	} catch (error) {
		console.error("Verification failed:", error);
	}
};
const isSigningOut = ref(false);
const signOut = async () => {
	if (isSigningOut.value) return;
	isSigningOut.value = true;
	if (authCheck.value)
		router.post(
			window.route("logout"),
			{},
			{
				onFinish() {
					isSigningOut.value = false;
				},
			}
		);
};

const signIn = async () => {
	if (!authCheck.value) await handleVerify();
};
useAccountEffect({
	onConnect(data) {
		signIn();
	},
	onDisconnect() {
		signOut();
	},
});
watch([isConnected, authCheck], ([isConnected, authCheck]) => {
	if (isConnected && !authCheck) {
		return signIn();
	}
	if (!isConnected && authCheck) {
		return signOut();
	}
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
				@click="disconnect"
			>
				<span class="mr-2" v-if="full"> Logout </span>
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
			<DangerButton :size="size" :class="{ 'w-full': full }" @click="disconnect()">
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
	</div>
</template>
