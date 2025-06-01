<script setup>
import { computed, nextTick, watch } from "vue";

import { useForm } from "@inertiajs/vue3";
import { useAccount, useChainId } from "@wagmi/vue";
import { parseEventLogs } from "viem";

import ChainInfo from "@/Components/ChainInfo.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import LogoInput from "@/Components/LogoInput.vue";
import LogoInputLocal from "@/Components/LogoInputLocal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import RadioSelect from "@/Components/RadioSelect.vue";
import TxStatus from "@/Components/TxStatus.vue";
import { useContractFees, useReactiveContractCall } from "@/hooks/useContractCall";

const props = defineProps({
	factory: { required: true, type: Object },
	types: { required: true, type: Object },
});

const chainId = useChainId();
const { address, chain } = useAccount();

const form = useForm({
	name: "",
	symbol: "",
	chainId: chainId.value,
	factory: "",
	contract: "",
	type: "",
	logo_uri: null,
	logo_path: null,
	logo_upload: false,
});

// Watch for chain changes to update defaults
watch(chainId, (chainId) => {
	form.chainId = chainId;
});

const save = () => form.post(window.route("admin.nfts.store"));

const supportedChains = computed(() => Object.keys(props.factory.addresses));

const factoryAddress = computed(() => props.factory.addresses[chainId.value]);

const state = useReactiveContractCall(props.factory.abi, factoryAddress);

const { fees, feesFormatted, loadFees } = useContractFees(
	props.factory.abi,
	factoryAddress,
	"deploymentFee"
);

nextTick(loadFees);

const deployContract = async () => {
	form.clearErrors();

	// Validate form fields
	if (!form.name) form.setError("name", "Collection name is required");
	if (!form.symbol) form.setError("symbol", "Symbol is required");

	if (form.hasErrors) {
		state.error = "Please check the form for errors";
		return;
	}

	await state.call("deployNft", [form.name, form.symbol, address.value], fees.value);

	if (state.error) return;

	const logs = parseEventLogs({
		abi: props.factory.abi,
		logs: state.receipt.logs,
		eventName: ["NftDeployed"],
	});

	form.factory = factoryAddress.value;
	form.contract = logs?.[0]?.args?.nftAddress;
	form.chainId = chainId.value;

	save();
};
</script>

<template>
	<div class="card sm:p-12 h-full border-0 card-border">
		<div class="card-body card-gutterless h-full">
			<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
				<div class="flex items-center">
					<appkit-network-button />
				</div>

				<FormInput
					label="Badge Name"
					v-model="form.name"
					class="sm:col-span-2"
					type="text"
					help="Enter the name of your NFT Badge"
					:error="form.errors.name"
				/>

				<FormInput
					label="Symbol"
					v-model="form.symbol"
					type="text"
					help="Token symbol (e.g. MYNFT)"
					:error="form.errors.symbol"
				/>

				<div class="sm:col-span-2 pt-4 gap-4 lg:col-span-4 grid sm:grid-cols-2">
					<div class="border p-3 border-gray-650 bg-gray-750/50">
						<h3 class="text-lg mb-4 !text-primary font-extralight">
							{{ $t("Token Logo") }}
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
										<label class="inline-flex items-center space-x-2">
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
								:src="form.logo_uri ?? form.logo ?? fakeLogo"
							/>
						</div>
						<p v-if="form.errors.logo" class="text-red-500 mt-2">
							{{ form.errors.logo }}
						</p>
						<p v-else class="text-xs mt-2">
							{{ $t("") }}
						</p>
					</div>
					<div>
						<label
							class="block mb-3 text-sm font-medium text-gray-900 dark:text-white"
							for="type"
						>
							NFT Type
						</label>
						<RadioSelect v-model="form.type" :options="types" :grid="2" />
					</div>
				</div>

				<div
					v-if="!factoryAddress"
					class="pt-5 sm:col-span-2 grid gap-3 lg:col-span-4"
				>
					<h3>{{ chain.name }} is not supported</h3>
					<p>Allowed chains are</p>
					<div class="flex items-center gap-3">
						<ChainInfo
							v-for="chId in supportedChains"
							:chain-id="chId"
							:key="chId"
						/>
					</div>
					<h3 class="text-sm">Switch Chain</h3>
					<appkit-network-button />
				</div>

				<div v-else class="pt-5 sm:col-span-2 lg:col-span-4">
					<TxStatus class="my-5" :state="state" />
					<div class="flex items-center gap-3">
						<PrimaryButton
							secondary
							as="button"
							:href="route('admin.nfts.index')"
							type="button"
							link
						>
							{{ $t("Cancel") }}
						</PrimaryButton>
						<PrimaryButton
							type="button"
							@click.prevent="deployContract"
							:disabled="form.processing"
						>
							<Loading
								class="mr-2 -ml-1 inline-block w-5 h-5"
								v-if="form.processing"
							/>
							{{ $t("Deploy NFT") }}
							{{ feesFormatted }}
							{{ chain?.nativeCurrency?.symbol }}
						</PrimaryButton>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
