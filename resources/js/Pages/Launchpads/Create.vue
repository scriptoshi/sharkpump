<script setup>
import { computed, ref } from "vue";

import { useForm } from "@inertiajs/vue3";
import { useChainId } from "@wagmi/vue";
import { ComponentIcon } from "lucide-vue-next";
import { parseEventLogs } from "viem";

import ChainSymbol from "@/Components/ChainSymbol.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import FormLabel from "@/Components/FormLabel.vue";
import FormSwitch from "@/Components/FormSwitch.vue";
import FormTextArea from "@/Components/FormTextArea.vue";
import Loading from "@/Components/Loading.vue";
import LogoInput from "@/Components/LogoInput.vue";
import LogoInputLocal from "@/Components/LogoInputLocal.vue";
import fakeLogo from "@/Components/no-image-available-icon.jpeg?url";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import { useContractFees, useReactiveContractCall } from "@/hooks/useContractCall";
import AppLayout from "@/Layouts/AppLayout.vue";
import Web3Auth from "@/Pages/Auth/Web3Auth.vue";
const props = defineProps({
	factories: Array,
});
const chainId = useChainId();
const factory = computed(() => props.factories[chainId.value]);
const factoryId = computed(() => factory.value?.id);
const form = useForm({
	chainId,
	factory_id: factoryId,
	contract: "",
	token: "",
	name: "",
	symbol: "",
	description: "",
	twitter: "",
	discord: "",
	telegram: "",
	website: "",
	logo_uri: null,
	logo_path: null,
	logo_upload: false,
});
const save = () =>
	form.post(window.route("launchpads.store"), {
		preserveState: true,
		preserveScroll: true,
	});
const addLinks = ref(false);
const abi = computed(() => factory.value.factory_abi);
const contract = computed(() => factory.value.contract);
const state = useReactiveContractCall(abi, contract);
const { fees, feesFormatted } = useContractFees(abi, contract, "getDeploymentFee");
const deploy = async () => {
	if (form.logo_upload && !form.logo_path)
		form.setError("logo_uri", "Logo is required");
	if (!form.logo_upload && !form.logo_uri)
		form.setError("logo_uri", "Logo is required");
	if (!form.name) form.setError("name", "Token name is required");
	if (!form.symbol) form.setError("symbol", "Token symbol is required");
	if (!form.symbol) form.setError("symbol", "A description is required");
	await state.call("deployBondingCurveSystem", [form.name, form.symbol], fees.value);
	if (state.error) return;
	const logs = parseEventLogs({
		abi: abi.value,
		logs: state.receipt.logs,
		eventName: ["BondingCurveSystemDeployed"],
	});
	console.log(logs);
	form.contract = logs?.[0]?.args?.bondingCurveAddress;
	form.token = logs?.[0]?.args?.tokenAddress;
	save();
};
</script>
<template>
	<Head :title="`New Launchpad`" />
	<AppLayout>
		<div
			class="card max-w-3xl w-full mb-6 mx-auto dark:bg-gray-850 sm:p-12 sm:!pt-6 h-full border-0 card-border"
		>
			<div class="card-body card-gutterless h-full">
				<h3 class="mb-3 flex items-center gap-2">
					<ComponentIcon class="w-7 h-7 stroke-[0.7] text-sky-400" />
					{{ $t("Launch Telegram Agent") }}
				</h3>
				<div class="grid gap-6">
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
					<FormInput
						:label="$t('Name')"
						v-model="form.name"
						type="text"
						:error="form.errors.name"
					/>

					<FormInput
						:label="$t('Symbol')"
						v-model="form.symbol"
						type="text"
						:error="form.errors.symbol"
					/>
					<div>
						<FormLabel class="mb-2">
							{{ $t("Description") }}
						</FormLabel>
						<FormTextArea :rows="3" v-model="form.description" />
						<p
							v-if="form.errors.description"
							class="text-xs font-semibold text-red-500"
						>
							{{ form.errors.description }}
						</p>
					</div>
					<FormSwitch v-model="addLinks">
						{{ $t("Add Project Links") }}
					</FormSwitch>
					<CollapseTransition>
						<div v-show="addLinks" class="grid gap-4">
							<FormInput
								:label="$t('Website')"
								v-model="form.website"
								type="text"
								:error="form.errors.website"
							/>
							<FormInput
								:label="$t('Twitter')"
								v-model="form.twitter"
								type="text"
								:error="form.errors.twitter"
							/>
							<FormInput
								:label="$t('Discord')"
								v-model="form.discord"
								type="text"
								:error="form.errors.discord"
							/>
							<FormInput
								:label="$t('Telegram')"
								v-model="form.telegram"
								type="text"
								:error="form.errors.telegram"
							/>
						</div>
					</CollapseTransition>

					<div v-if="$page.props.auth.user" class="pt-5">
						<div
							class="flex flex-col sm:flex-row items-center gap-3 justify-end"
						>
							<TxStatus class="w-full sm:w-[unset]" :state="state" />
							<PrimaryButton
								class="w-full sm:w-[unset]"
								@click="deploy"
								:disabled="state.busy || form.processing"
							>
								<Loading
									class="mr-2 -ml-1 inline-block w-5 h-5"
									v-if="state.busy || form.processing"
								/>
								<span>
									{{ $t("Deploy Launchpad") }}
									{{ feesFormatted }}
								</span>
								<ChainSymbol class="ml-1" :chain-id="chainId" />
							</PrimaryButton>
						</div>
					</div>
					<div v-else class="pt-5">
						<div
							class="flex flex-col sm:flex-row items-center gap-3 justify-end"
						>
							<Web3Auth />
						</div>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
