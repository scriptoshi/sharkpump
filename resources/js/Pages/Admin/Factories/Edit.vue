<script setup>
import { ref, watch } from "vue";

import { useForm } from "@inertiajs/vue3";
import { useAccount, useBalance, useChainId } from "@wagmi/vue";
import { Cog, SlidersVertical } from "lucide-vue-next";
import { HiArrowLeft, HiCheckCircle, HiCog } from "oh-vue-icons/icons";
import { parseEther, zeroAddress } from "viem";

import ChainInfo from "@/Components/ChainInfo.vue";
import FormInput from "@/Components/FormInput.vue";
import FormSwitch from "@/Components/FormSwitch.vue";
import Loading from "@/Components/Loading.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import RadioSelect from "@/Components/RadioSelect.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import VueIcon from "@/Components/VueIcon.vue";
import { isAddress } from "@/hooks/explorers";
import {
	useContractFees,
	useFactoryConfig,
	useReactiveContractCall,
} from "@/hooks/useContractCall";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const props = defineProps({
	factory: { required: true, type: Object },
});
const chainId = useChainId();
const { address, chain } = useAccount();
const nameForm = useForm({
	name: props.factory.version,
	nft_type: props.factory.nft_type,
});
const save = () =>
	nameForm.put(
		window.route("admin.factories.update", {
			factory: props.factory.id,
		}),
		{ preserveScroll: true, preserveState: true }
	);
const form = useForm({
	virtualEth: 0,
	preBondingTarget: 0,
	bondingTarget: 0,
	minContribution: 0,
	poolFee: 0,
	sellFee: 0,
	buyFee: 0,
	uniswapV3Factory: zeroAddress,
	positionManager: zeroAddress,
	weth: zeroAddress,
	feeTo: zeroAddress,
});
const config = useFactoryConfig(props.factory.factory_abi, props.factory.contract);
const { data: balance, refetch } = useBalance({
	address: props.factory.contract,
});
watch(
	config,
	(config) => {
		// update defualts
		form.virtualEth = config.virtualEth;
		form.preBondingTarget = config.preBondingTarget;
		form.bondingTarget = config.bondingTarget;
		form.minContribution = config.minContribution;
		form.poolFee = config.poolFee;
		form.sellFee = config.sellFee;
		form.buyFee = config.buyFee;
		form.uniswapV3Factory = config.uniswapV3Factory;
		form.positionManager = config.positionManager;
		form.weth = config.weth;
		form.feeTo = config.feeTo;
	},
	{ deep: true }
);

const state = useReactiveContractCall(props.factory.factory_abi, props.factory.contract);
const uniswapV3 = ref(false);
const { feesFormatted } = useContractFees(
	props.factory.factory_abi,
	props.factory.contract,
	"getDeploymentFee"
);
const feesForm = useForm({ newFees: 0 });
watch(feesFormatted, (feesFormatted) => {
	feesForm.newFees = feesFormatted;
});
watch(
	() => form.virtualEth,
	(virtualEth) => {
		form.preBondingTarget = (virtualEth * 20) / 100;
	}
);
const updateFees = async () => {
	if (feesForm.newFees === "") {
		feesForm.setError("newFees", "Cannot be empty");
	}
	const fees = parseEther(`${feesForm.newFees}`);
	await state.call("updateDeploymentFee", [fees], 0n);
	config.load();
};
const withdrawForm = useForm({ to: address.value });
watch(address, (address) => {
	withdrawForm.to = address;
});
const withdrawFees = async () => {
	const withdrawTo = isAddress(withdrawForm.to);
	if (!withdrawTo) {
		withdrawForm.setError("to", "Invalid destination addres");
	}
	await state.call("withdrawFees", [withdrawTo], 0n);
	refetch();
};

const updateSettings = async () => {
	form.clearErrors();
	const settings = {
		virtualEth: parseEther(`${form.virtualEth}`),
		preBondingTarget: parseEther(`${form.preBondingTarget}`),
		bondingTarget: parseEther(`${form.bondingTarget}`),
		minContribution: parseEther(`${form.minContribution}`),
		poolFee: `${form.poolFee}`,
		sellFee: `${form.sellFee}`,
		buyFee: `${form.buyFee}`,
		uniswapV3Factory: isAddress(form.uniswapV3Factory),
		positionManager: isAddress(form.positionManager),
		weth: isAddress(form.weth),
		feeTo: isAddress(form.feeTo),
	};

	if (!settings.virtualEth) form.setError("virtualEth", "Cannot be empty or 0");
	if (!settings.preBondingTarget)
		form.setError("preBondingTarget", "Cannot be empty or 0");
	if (!settings.bondingTarget) form.setError("bondingTarget", "Cannot be empty or 0");
	if (!settings.minContribution)
		form.setError("minContribution", "Cannot be empty or 0");
	if (!settings.poolFee) form.setError("poolFee", "Cannot be empty or 0");
	if (!settings.feeTo) form.setError("feeTo", "Invalid address");
	if (!settings.uniswapV3Factory) form.setError("uniswapV3Factory", "Invalid address");
	if (!settings.positionManager) form.setError("positionManager", "Invalid address");
	if (!settings.weth) form.setError("weth", "Invalid address");
	if (form.hasErrors) {
		state.error = "It seems you missed you stuff we need. check the form!";
		return;
	}
	await state.call("updateBondingCurveSettings", [settings], 0n);
	config.load();
};
</script>
<template>
	<Head :title="`New Factory`" />
	<AdminLayout>
		<main class="h-full container sm:p-8">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8"
			>
				<div class="flex flex-col gap-4 h-full">
					<div class="p-6 grid sm:grid-cols-3 border border-gray-750">
						<div class="lg:flex items-center justify-between mb-4 gap-3">
							<div class="mb-4 lg:mb-0">
								<h3 class="h3 flex items-center gap-2">
									<SlidersVertical class="w-6 h-6 inline-flex" />
									Manage your factory
								</h3>
								<p>
									All setting are stored on chain and require gas to
									make changes!
								</p>
							</div>
						</div>
						<div class="flex justify-end items-center gap-5 sm:col-span-2">
							<div class="flex justify-end gap-5 items-center">
								<TxStatus
									v-if="state.called === 'withdrawFees'"
									:state="state"
								/>
								<div v-else>
									<p>Available fees</p>
									<h3>
										{{ balance?.formatted }}
										{{ chain?.nativeCurrency?.symbol }}
									</h3>
								</div>
								<PrimaryButton @click="withdrawFees">
									Withdraw Fees
								</PrimaryButton>
							</div>
							<SecondaryButton
								size="sm"
								link
								:href="route('admin.factories.index')"
							>
								<VueIcon
									:icon="HiArrowLeft"
									class="w-4 h-4 -ml-1 mr-2 inline-block"
								/>
								{{ $t("Back ") }}
							</SecondaryButton>
						</div>
					</div>
					<div class="card sm:p-12 h-full border-0 card-border">
						<div class="card-body card-gutterless h-full">
							<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
								<div
									class="sm:col-span-2 lg:col-span-4 grid md:grid-cols-2 lg:grid-cols-4 gap-3 p-6 border border-gray-750"
								>
									<h3 class="sm:col-span-2 lg:col-span-4">
										Factory Information.
									</h3>
									<FormInput
										label="Factory Name. (A simple label for your private use)"
										v-model="nameForm.name"
										class="sm:col-span-2"
										type="text"
										help="This name is meant for local use"
										:error="nameForm.errors.name"
									/>

									<label
										class="sm:col-span-2 lg:col-span-4 block mb-3 text-sm font-medium text-gray-900 dark:text-white"
										for="type"
									>
										Minimum NFT Badge (required to deploy);
									</label>
									<div class="lg:col-span-4 p-6 rounded-lg bg-gray-750">
										<RadioSelect
											v-model="nameForm.nft_type"
											:options="$page.props.types"
											:grid="4"
										/>
									</div>
									<div class="lg:col-span-4 flex justify-end">
										<SecondaryButton
											class="self-center"
											size="sm"
											:outlined="nameForm.recentlySuccessful"
											:class="{
												'!text-emerald-500':
													nameForm.recentlySuccessful,
											}"
											:disabled="nameForm.processing"
											@click="save"
										>
											<VueIcon
												v-if="nameForm.recentlySuccessful"
												:icon="HiCheckCircle"
												class="w-4 h-4 -ml-1 mr-2 inline-block"
											/>
											<Loading
												class="!w-4 !h-4 -ml-1 mr-2"
												v-else-if="nameForm.processing"
											/>
											<VueIcon
												:icon="HiCog"
												v-else
												class="w-4 h-4 -ml-1 mr-2 inline-block"
											/>

											{{
												nameForm.recentlySuccessful
													? "Saved Successfully"
													: $t("Update Factory Label ")
											}}
										</SecondaryButton>
									</div>
								</div>
								<div
									class="sm:col-span-2 lg:col-span-4 grid md:grid-cols-2 lg:grid-cols-4 gap-6 p-6 border border-gray-750"
								>
									<FormInput
										label="Deployment Fees"
										v-model="feesForm.newFees"
										help="Price of launching on your platform"
										type="text"
										:error="feesForm.errors.newFees"
									>
										<template #trail>
											{{ chain?.nativeCurrency?.symbol }}
										</template>
									</FormInput>
									<div
										class="flex sm:col-span-2 items-center justify-end"
									>
										<TxStatus
											:state="state"
											v-if="state.called == 'updateDeploymentFee'"
										/>
										<p v-else>Pending</p>
									</div>
									<PrimaryButton
										:disabled="state.busy"
										@click.prevent="updateFees"
										class="self-center"
									>
										<Loading
											class="mr-2 -ml-1"
											v-if="
												state.busy &&
												state.called == 'updateDeploymentFee'
											"
										/>
										Update Deployment Fees
									</PrimaryButton>
								</div>
								<h3
									class="sm:col-span-2 !text-primary font-thin mt-8 lg:col-span-4"
								>
									<Cog class="w-7 h-7 stroke-[0.6] inline-flex" />
									Update Spawnpad Configuration
								</h3>
								<div
									class="sm:col-span-2 dark:bg-gray-750/50 lg:col-span-4 grid md:grid-cols-2 lg:grid-cols-4 gap-3 p-6 border border-gray-750"
								>
									<FormInput
										label="Virtual Liquidity"
										v-model="form.virtualEth"
										:error="form.errors.virtualEth"
										type="text"
									>
										<template #trail>
											{{ chain?.nativeCurrency?.symbol }}
										</template>
									</FormInput>
									<FormInput
										label="Pre Bond Target"
										disabled=""
										v-model="form.preBondingTarget"
										:error="form.errors.preBondingTarget"
										type="text"
									>
										<template #trail>
											{{ chain?.nativeCurrency?.symbol }}
										</template>
									</FormInput>
									<FormInput
										label="Bonding Target"
										v-model="form.bondingTarget"
										:error="form.errors.bondingTarget"
										type="text"
									>
										<template #trail>
											{{ chain?.nativeCurrency?.symbol }}
										</template>
									</FormInput>
									<FormInput
										label="Min Contribution"
										v-model="form.minContribution"
										:error="form.errors.minContribution"
										type="text"
									>
										<template #trail>
											{{ chain?.nativeCurrency?.symbol }}
										</template>
									</FormInput>
									<FormInput
										label="Pool Fees (Percent x 10000)"
										class="md:col-span-2"
										v-model="form.poolFee"
										:error="form.errors.poolFee"
										type="text"
									>
										<template #trail>
											{{ (form.poolFee / 10000).toFixed(4) * 1 }}%
										</template>
									</FormInput>
									<FormInput
										label="Buy Token Fees (Percent x 100)"
										help="A fee charged when users buy tokens on the bonding curve"
										v-model="form.buyFee"
										:error="form.errors.buyFee"
										type="text"
									>
										<template #trail>
											{{ (form.buyFee / 100).toFixed(4) * 1 }}%
										</template>
									</FormInput>
									<FormInput
										label="Sell Token Fees (Percent x 100)"
										help="A fee charged when users sell tokens on the bonding curve"
										v-model="form.sellFee"
										:error="form.errors.sellFee"
										type="text"
									>
										<template #trail>
											{{ (form.sellFee / 100).toFixed(4) * 1 }}%
										</template>
									</FormInput>
									<div class="sm:col-span-2 lg:col-span-4">
										<h3 class="!text-red-500">Important !</h3>
										<p class="mb-3">
											Donot Edit edit the UniswapV3 addresses unless
											you know what you are doing. Wrong addresses
											could lead to contributions getting stuck for
											good on the launchpad pool.
										</p>
										<FormSwitch v-model="uniswapV3">
											I want to change the UniswapV3 addresses
										</FormSwitch>
									</div>
									<FormInput
										label="UniswapV3 Factory"
										class="md:col-span-2"
										:disabled="!uniswapV3"
										v-model="form.uniswapV3Factory"
										:error="form.errors.uniswapV3Factory"
										type="text"
									/>
									<FormInput
										label="UniswapV3 Nonfungible Position Manager"
										class="md:col-span-2"
										:disabled="!uniswapV3"
										v-model="form.positionManager"
										:error="form.errors.positionManager"
										type="text"
									/>
									<FormInput
										label="UniswapV3 WETH"
										class="md:col-span-2"
										:disabled="!uniswapV3"
										v-model="form.weth"
										:error="form.errors.weth"
										type="text"
									/>
									<FormInput
										label="Pool Fees sent to"
										class="md:col-span-2"
										v-model="form.feeTo"
										:error="form.errors.feeTo"
										type="text"
									/>
								</div>

								<div
									v-if="chainId != factory.chainId"
									class="pt-5 sm:col-span-2 grid gap-3 lg:col-span-4"
								>
									<h3>Incorrect chain in your wallet</h3>
									<p>Thsi factory is deployed to:</p>
									<div class="flex items-center gap-3">
										<ChainInfo :chain-id="factory.chainId" />
									</div>
									<h3 class="text-sm">Switch Your Chain</h3>
									<appkit-network-button />
								</div>
								<div v-else class="pt-5 sm:col-span-2 lg:col-span-4">
									<TxStatus class="my-5" :state="state" />
									<div class="flex items-center justify-end gap-3">
										<SecondaryButton
											secondary
											as="button"
											:href="route('admin.factories.index')"
											type="button"
											link
										>
											{{ $t("Cancel") }}
										</SecondaryButton>
										<PrimaryButton
											type="button"
											@click.prevent="updateSettings"
											:disabled="form.processing"
										>
											<Loading
												class="mr-2 -ml-1 inline-block w-5 h-5"
												v-if="form.processing"
											/>
											{{ $t("Update launchpads config") }}
										</PrimaryButton>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
