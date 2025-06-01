<script setup>
import { watch } from "vue";

import { useForm } from "@inertiajs/vue3";
import { useAccount } from "@wagmi/vue";
import { formatEther, parseEther } from "viem";

import BaseButton from "@/Components/BaseButton.vue";
import FormInput from "@/Components/FormInput.vue";
import FormSwitch from "@/Components/FormSwitch.vue";
import Loading from "@/Components/Loading.vue";
import LogoInput from "@/Components/LogoInput.vue";
import LogoInputLocal from "@/Components/LogoInputLocal.vue";
import fakeLogo from "@/Components/no-image-available-icon.jpeg?url";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import { useNftInfo } from "@/hooks/useNftInfo";

const props = defineProps({
	nft: { required: true, type: Object },
	types: { required: true, type: Object },
});

const { address, chain } = useAccount();

const form = useForm({
	baseUri: "",
	maxSupply: "0",
	mintPrice: "0",
	recipient: "",
	image: props.nft.image,
	logo_uri: null,
	logo_path: null,
	logo_upload: false,
});

const save = () => form.put(window.route("admin.nfts.update", props.nft.id));

const {
	state,
	isPaused,
	totalSupply,
	maxSupply,
	mintPrice,
	baseUri,
	isLoading,
	error,
	refresh,
	contractBalance,
} = useNftInfo(props.nft);

// Keep form in sync with contract state
watch([baseUri, maxSupply, mintPrice], () => {
	form.baseUri = baseUri.value;
	form.maxSupply = maxSupply.value;
	form.mintPrice = mintPrice.value;
});

// Management functions
const setBaseUri = async () => {
	if (!form.baseUri) {
		state.error = "Base URI is required";
		return;
	}
	await state.call("setBaseURI", [form.baseUri]);
	refresh();
};

const setMaxSupply = async () => {
	const maxSupplyNum = parseInt(form.maxSupply);
	if (isNaN(maxSupplyNum) || maxSupplyNum < 0) {
		state.error = "Invalid max supply value";
		return;
	}
	await state.call("setMaxSupply", [maxSupplyNum]);
	refresh();
};

const setMintPrice = async () => {
	if (!form.mintPrice || parseFloat(form.mintPrice) < 0) {
		state.error = "Invalid mint price";
		return;
	}
	await state.call("setMintPrice", [parseEther(form.mintPrice)]);
	refresh();
};

const mintToken = async () => {
	if (!form.recipient) {
		state.error = "Recipient address is required";
		return;
	}
	await state.call("safeMint", [form.recipient], parseEther(mintPrice.value));
	refresh();
};

const togglePause = async () => {
	await state.call(isPaused.value ? "unpause" : "pause");
	refresh();
};

const withdrawFees = async () => {
	if (!address) {
		state.error = "Wallet not connected";
		return;
	}
	await state.call("withdrawFees", [address.value]);
	refresh();
};
</script>

<template>
	<div class="card sm:p-12 h-full border-0 card-border">
		<div class="card-body card-gutterless h-full">
			<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
				<div class="sm:col-span-2 lg:col-span-4 mb-6">
					<h2 class="text-xl font-semibold mb-2">Contract Information</h2>
					<div v-if="isLoading" class="text-center p-4">
						<Loading class="w-8 h-8" />
					</div>
					<div v-else-if="error" class="!text-red-500 p-4">
						{{ error }}
					</div>
					<div v-else class="grid sm:grid-cols-4 gap-4 border-gray-650">
						<div>
							<p class="text-sm !text-primary">Name</p>
							<p>{{ props.nft.name }}</p>
						</div>
						<div>
							<p class="text-sm !text-primary">Symbol</p>
							<p>{{ props.nft.symbol }}</p>
						</div>
						<div>
							<p class="text-sm !text-primary">Total Supply</p>
							<p>{{ totalSupply }}</p>
						</div>
						<div>
							<p class="text-sm !text-primary">Contract Status</p>
							<p :class="isPaused ? '!text-red-500' : '!text-green-500'">
								{{ isPaused ? "Paused" : "Active" }}
							</p>
						</div>
					</div>
				</div>

				<!-- Base URI Management -->
				<div class="sm:col-span-2 border border-gray-750 p-4 rounded-lg">
					<h3 class="text-lg font-medium mb-4">Metadata URI</h3>
					<FormInput
						label="Base URI for NFT metadata (e.g., IPFS URL)"
						v-model="form.baseUri"
						type="text"
					/>
					<div class="mt-2 flex items-center gap-3">
						<PrimaryButton
							size="xss"
							@click.prevent="
								form.baseUri = route('nfts.meta', { nft: nft.contract })
							"
							>Set Default
						</PrimaryButton>
						<BaseButton
							outlined
							danger
							size="xs"
							@click.prevent="form.baseUri = ''"
							>Clear
						</BaseButton>
					</div>

					<div
						class="flex mt-4 justify-center sm:justify-end sm:flex-row sm:items-center gap-3"
					>
						<TxStatus :state="state" v-show="state.called == 'setBaseURI'" />
						<PrimaryButton
							@click="setBaseUri"
							:disabled="state.busy == 'setBaseURI'"
						>
							<Loading
								class="mr-1 -ml-1"
								v-if="state.busy == 'setBaseURI'"
							/>
							Update Base URI
						</PrimaryButton>
					</div>
				</div>

				<!-- Supply Management -->
				<div class="sm:col-span-2 border border-gray-750 p-4 rounded-lg">
					<h3 class="text-lg font-medium mb-4">Supply Management</h3>
					<FormInput
						label="Max Supply"
						v-model="form.maxSupply"
						type="number"
						help="Maximum number of tokens (0 for unlimited)"
					/>
					<div
						class="flex mt-4 justify-center sm:justify-end sm:flex-row sm:items-center gap-3"
					>
						<TxStatus
							:state="state"
							v-show="state.called == 'setMaxSupply'"
						/>
						<PrimaryButton
							@click="setMaxSupply"
							:disabled="state.busy == 'setMaxSupply'"
						>
							<Loading
								class="mr-1 -ml-1"
								v-if="state.busy == 'setMaxSupply'"
							/>
							Update Max Supply
						</PrimaryButton>
					</div>
				</div>

				<!-- Minting Management -->
				<div class="sm:col-span-2 border border-gray-750 p-4 rounded-lg">
					<h3 class="text-lg font-medium mb-4">Minting</h3>
					<FormInput
						label="Mint Price"
						v-model="form.mintPrice"
						type="text"
						help="Price per token in ETH"
					>
						<template #trail>
							{{ chain?.nativeCurrency?.symbol }}
						</template>
					</FormInput>
					<div
						class="flex mt-4 justify-center sm:justify-end sm:flex-row sm:items-center gap-3"
					>
						<TxStatus
							:state="state"
							v-show="state.called == 'setMintPrice'"
						/>
						<PrimaryButton
							@click="setMintPrice"
							:disabled="state.busy == 'setMintPrice'"
						>
							<Loading
								class="mr-1 -ml-1"
								v-if="state.busy == 'setMintPrice'"
							/>
							Update Mint Price
						</PrimaryButton>
					</div>
				</div>

				<!-- Token Minting -->
				<div class="sm:col-span-2 border border-gray-750 p-4 rounded-lg">
					<h3 class="text-lg font-medium mb-4">Mint Token</h3>
					<FormInput
						label="Recipient Address"
						v-model="form.recipient"
						type="text"
						help="Address to receive the new token"
					/>
					<div
						class="flex mt-4 justify-center sm:justify-end sm:flex-row sm:items-center gap-3"
					>
						<TxStatus :state="state" v-show="state.called == 'safeMint'" />
						<PrimaryButton
							@click="mintToken"
							:disabled="state.busy == 'safeMint' || isPaused"
						>
							<Loading class="mr-1 -ml-1" v-if="state.busy == 'safeMint'" />
							Mint Token
						</PrimaryButton>
					</div>
				</div>

				<!-- Contract Status -->
				<div
					class="sm:col-span-2 lg:col-span-4 border border-gray-750 p-4 rounded-lg"
				>
					<div class="flex items-center justify-between">
						<div>
							<h3 class="text-lg font-medium">
								Contract Paused?
								<span
									:class="
										isPaused
											? 'text-red-500 border-red-500'
											: 'text-green-500 border-green-500'
									"
									class="ml-3 border border-gray-650 px-2 rounded !text-sm"
									>{{ isPaused ? "Yes" : "No" }}</span
								>
							</h3>
							<p class="text-sm text-gray-500">
								Pausing contract will stop minting!
							</p>
						</div>
						<div class="flex items-center gap-2">
							<TxStatus
								:state="state"
								v-show="['pause', 'unpause'].includes(state.called)"
							/>
							<FormSwitch :model-value="isPaused" disabled />
							<PrimaryButton
								@click="togglePause"
								:disabled="['pause', 'unpause'].includes(state.busy)"
							>
								<Loading
									class="mr-1 -ml-1"
									v-if="['pause', 'unpause'].includes(state.busy)"
								/>
								{{ isPaused ? "Unpause" : "Pause" }}
							</PrimaryButton>
						</div>
					</div>
				</div>
				<div
					class="sm:col-span-2 lg:col-span-4 border border-gray-750 p-4 rounded-lg"
				>
					<div class="flex items-center justify-between">
						<div>
							<h3 class="text-lg font-medium">Contract Balance</h3>
							<p class="text-sm text-primary">
								{{ contractBalance ? formatEther(contractBalance) : "0" }}
								{{ chain?.nativeCurrency?.symbol }}
							</p>
						</div>
						<div class="flex items-center gap-3">
							<TxStatus
								:state="state"
								v-show="state.called == 'withdrawFees'"
							/>
							<PrimaryButton
								@click="withdrawFees"
								:disabled="
									state.busy == 'withdrawFees' ||
									!contractBalance ||
									contractBalance === '0'
								"
							>
								<Loading
									class="mr-1 -ml-1"
									v-if="state.busy == 'withdrawFees'"
								/>
								Withdraw Fees
							</PrimaryButton>
						</div>
					</div>
				</div>

				<div
					class="sm:col-span-2 lg:col-span-4 border border-gray-750 p-4 rounded-lg"
				>
					<div class="flex items-center gap-6 justify-between">
						<div class="flex-1 p-6 bg-gray-750 rounded-lg">
							<h3 class="text-lg mb-4 !text-primary font-extralight">
								Change Image
							</h3>
							<div class="gap-x-3 grid gap-3">
								<FormInput
									v-model="form.logo_uri"
									:disabled="form.logo_upload"
									placeholder="https://"
									:error="form.errors.logo_uri"
									:help="$t('Supports png, jpeg or svg')"
								>
									<template #label>
										<div class="flex mb-3">
											<span class="mr-3">
												{{ $t("Logo") }}
											</span>
											<label
												class="inline-flex items-center space-x-2"
											>
												<input
													v-model="form.logo_upload"
													class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-emerald-600 checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
													type="checkbox"
												/>
												<span>
													{{ $t("Upload to server") }}
												</span>
											</label>
										</div>
									</template>
								</FormInput>
								<div class="flex items-center justify-between gap-3">
									<template v-if="form.logo_upload">
										<LogoInput
											v-if="$page.props.s3"
											v-model="form.logo_uri"
											v-model:file="form.logo_path"
											auto
										/>
										<LogoInputLocal
											v-else
											v-model="form.logo_uri"
											v-model:file="form.logo_path"
										/>
									</template>
									<img
										v-else
										class="w-12 h-12 my-auto rounded-full b-0"
										:src="
											form.logo_uri ?? props.nft.image ?? fakeLogo
										"
									/>
									<PrimaryButton
										@click="save"
										:disabled="form.processing"
									>
										<Loading
											class="mr-1 -ml-1"
											v-if="form.processing"
										/>
										Update Image
									</PrimaryButton>
								</div>
							</div>
							<p v-if="form.errors.logo" class="text-red-500 mt-2">
								{{ form.errors.logo }}
							</p>
							<p v-else class="text-xs mt-2">
								{{ $t("") }}
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
