<script setup>
import { computed, ref } from "vue";

import { useChainId } from "@wagmi/vue";
import { HiChevronDown } from "oh-vue-icons/icons";
import { parseEventLogs } from "viem";

import BaseButton from "@/Components/BaseButton.vue";
import ChainSymbol from "@/Components/ChainSymbol.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import Loading from "@/Components/Loading.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {
	useContractFees,
	useFactoryConfig,
	useReactiveContractCall,
} from "@/hooks/useContractCall";
const props = defineProps({
	factory: Object,
	form: Object,
	canDeploy: Boolean,
});
const chainId = useChainId();
const factoryId = computed(() => props.factory.id);
const showing = ref(false);
const save = (info) =>
	props.form
		.transform((data) => ({
			...data,
			...info,
			factory_id: factoryId.value,
		}))
		.post(window.route("launchpads.store"), {
			preserveState: true,
			preserveScroll: true,
		});
const abi = computed(() => props.factory.factory_abi);
const contract = computed(() => props.factory.contract);
const state = useReactiveContractCall(abi, contract);
const { fees, feesFormatted } = useContractFees(abi, contract, "getDeploymentFee");
const config = useFactoryConfig(props.factory.factory_abi, props.factory.contract);
const deploy = async () => {
	props.form.clearErrors();
	if (props.form.logo_upload && !props.form.logo_path)
		props.form.setError("logo_uri", "Logo is required");
	if (!props.form.logo_upload && !props.form.logo_uri)
		props.form.setError("logo_uri", "Logo is required");
	if (!props.form.name) props.form.setError("name", "Token name is required");
	if (!props.form.symbol) props.form.setError("symbol", "Token symbol is required");
	if (!props.form.symbol)
		props.form.setError("description", "A description is required");
	await state.call(
		"deployBondingCurveSystem",
		[props.form.name, props.form.symbol],
		fees.value
	);
	if (state.error) return;
	const logs = parseEventLogs({
		abi: abi.value,
		logs: state.receipt.logs,
		eventName: ["BondingCurveSystemDeployed"],
	});
	save({
		contract: logs?.[0]?.args?.bondingCurveAddress,
		token: logs?.[0]?.args?.tokenAddress,
	});
};
</script>
<template>
	<div>
		<div class="flex items-center py-2 justify-between">
			<h3 class="mb-3 text-lg font-semibold flex items-center gap-2">
				{{ factory.version }}
			</h3>
			<div class="flex items-center gap-4">
				<div>
					{{ config?.bondingTarget }}
					<ChainSymbol :chain-id="factory.chainId" />
				</div>
				<BaseButton
					secondary
					:outlined="!showing"
					@click="showing = !showing"
					size="xs"
				>
					{{ $t("Details") }}
					<VueIcon
						:icon="HiChevronDown"
						:class="showing ? 'rotate-180' : ''"
						class="w-4 h-4 ml-2 transition-transform duration-200"
					/>
				</BaseButton>
			</div>
		</div>
		<CollapseTransition>
			<div v-if="showing" class="w-full grid mb-6 gap-2">
				<div class="flex items-center p-2 bg-gray-750 justify-between gap-2">
					<p>Bonding Target</p>
					<h3 class="text-sm text-right">
						{{ config?.bondingTarget }}
						<ChainSymbol :chain-id="chainId" />
					</h3>
				</div>
				<div class="flex items-center p-2 bg-gray-750 justify-between gap-2">
					<p>Pre Bonding Target</p>
					<h3 class="text-sm text-right">
						{{ config?.preBondingTarget }}
						<ChainSymbol :chain-id="chainId" />
					</h3>
				</div>
				<div class="flex items-center p-2 bg-gray-750 justify-between gap-2">
					<p>Virtual ETH</p>
					<h3 class="text-sm text-right">
						{{ config?.virtualEth }}
						<ChainSymbol :chain-id="chainId" />
					</h3>
				</div>
				<div class="flex items-center p-2 bg-gray-750 justify-between gap-2">
					<p>Min Contribution</p>
					<h3 class="text-sm text-right">
						{{ config?.minContribution }}
						<ChainSymbol :chain-id="chainId" />
					</h3>
				</div>
				<div class="flex items-center p-2 bg-gray-750 justify-between gap-2">
					<p>UniswapV3 Pool Fee</p>
					<h3 class="text-sm text-right">
						{{ (config?.poolFee / 10000).toFixed(2) }}%
					</h3>
				</div>
				<div class="flex items-center p-2 bg-gray-750 justify-between gap-2">
					<p>Bonding curve Sell Fee</p>
					<h3 class="text-sm text-right">
						{{ (config?.sellFee / 100).toFixed(2) }}%
					</h3>
				</div>
				<div class="flex items-center p-2 bg-gray-750 justify-between gap-2">
					<p>Bonding curve Buy Fee</p>
					<h3 class="text-sm text-right">
						{{ (config?.buyFee / 100).toFixed(2) }}%
					</h3>
				</div>
			</div>
		</CollapseTransition>
		<div
			v-if="canDeploy"
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
					{{ $t("CREATE") }} {{ factory.version }}
					{{ feesFormatted }}
				</span>
				<ChainSymbol class="ml-1" :chain-id="factory.chainId" />
			</PrimaryButton>
		</div>
		<div v-else class="pt-5">
			<div class="flex flex-col sm:flex-row items-center gap-3 justify-end">
				<p>
					You need to mint a
					<a
						href="/nfts"
						class="text-primary-600 font-semibold hover:text-primary-400"
						>{{ factory.nft_type }} NFT</a
					>
					on order to Create this Launchpad
				</p>
			</div>
		</div>
	</div>
</template>
