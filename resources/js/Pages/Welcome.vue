<!-- eslint-disable import/order -->
<script setup>
import { computed, ref } from "vue";

import { router } from "@inertiajs/vue3";
import { debouncedWatch, useLocalStorage, useUrlSearchParams } from "@vueuse/core";
import {
	AlarmClockCheck,
	BellPlus,
	ChartCandlestick,
	Flame,
	LayoutGrid,
	LayoutList,
	LoaderCircle,
	Search,
	SquareArrowUp,
} from "lucide-vue-next";

import BaseButton from "@/Components/BaseButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Pagination from "@/Components/Pagination.vue";
import SmallSwitch from "@/Components/SmallSwitch.vue";
import { ucfirst } from "@/hooks";
import { useLaunchpadsData } from "@/hooks/useLaunchpadsData";
import AppLayout from "@/Layouts/AppLayout.vue";
import HowItWorksModal from "@/Layouts/AppLayout/HowItWorksModal.vue";
import AnimationsRow from "@/Pages/Launchpads/AnimationsRow.vue";
import BarButton from "@/Pages/Launchpads/BarButton.vue";
import IndexCard from "@/Pages/Launchpads/IndexCard.vue";
import { useChainId } from "@wagmi/vue";
import IndexTable from "./Launchpads/IndexTable.vue";

const props = defineProps({
	launchpads: [Array, Object],
	top: Array,
	usdRates: [Array, Object],
	type: String,
});
const launchpadsList = computed(() => props.launchpads.data);
const launchpadsInfo = useLaunchpadsData(launchpadsList, props.usdRates);
const showHowItWorks = ref(false);
const filters = [
	{ id: "trending", icon: Flame },
	{ id: "top", icon: ChartCandlestick },
	{ id: "rising", icon: SquareArrowUp },
	{ id: "new", icon: BellPlus },
	{ id: "finalized", icon: AlarmClockCheck },
];
const filterNames = {
	trending: "Trend",
	top: "Top",
	rising: "Rising",
	new: "New",
	finalized: "Done",
};
const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");
const currentSort = ref(params.sort ?? "");
const currentDir = ref(params.dir ?? "asc");
const chainId = useChainId();
debouncedWatch(
	[search, currentDir, currentSort],
	([search, currentDir, currentSort]) => {
		router.get(
			window.route("launchpads.index"),
			{
				...(search ? { search } : {}),
				...(currentDir ? { dir: currentDir } : {}),
				...(currentSort ? { sort: currentSort } : {}),
			},
			{
				preserveState: true,
				preserveScroll: true,
			}
		);
	},
	{
		maxWait: 500,
	}
);
const animate = ref(true);
const tableMode = useLocalStorage("tableMode", false);
</script>

<template>
	<AppLayout compact>
		<template #header>
			<div
				class="flex items-center w-full bg-gray-850 h-12 relative overflow-x-hidden"
			>
				<div
					class="flex w-full items-center overflow-x-auto [scrollbar-width:none]"
				>
					<div class="flex w-full items-center">
						<BarButton
							v-for="(launch, i) in top"
							:key="launch.id"
							:launch="launch"
							:active="i === 0"
						/>
					</div>
				</div>
				<div
					class="h-12 w-20 absolute right-0 pointer-events-none bg-gradient-to-r from-transparent via-gray-850/50 to-gray-850"
				></div>
			</div>
		</template>
		<div class="grid my-8 container">
			<div v-if="type === 'mine'" class="flex flex-col justify-center">
				<h3 class="flex items-center">
					<LoaderCircle
						v-if="launchpadsInfo.loading.value"
						class="w-6 h-6 mr-2 animate-spin"
					/>
					{{ $t("My Launchpads") }}
				</h3>
				<div class="flex items-center mt-4 gap-4">
					<BaseButton link href="/launch" outlined>
						{{ $t("Launch a new token") }}
					</BaseButton>
				</div>
			</div>
			<template v-else>
				<div
					class="flex flex-col items-center sm:flex-row sm:items-start justify-center sm:justify-between"
				>
					<div class="flex flex-col justify-center items-center sm:items-start">
						<h3 class="text-xl font-extralight">
							{{ $t("Create no code Agentic Telegram crypto bots") }}
						</h3>
						<h3>
							{{
								$t(
									"Trading agents, crypto signals, travel agents, and more!"
								)
							}}
						</h3>
						<div class="flex items-center mt-4 gap-4">
							<BaseButton
								@click="showHowItWorks = !showHowItWorks"
								secondary
								outlined
							>
								{{ $t("How Does it work") }}
							</BaseButton>
							<BaseButton link href="/launch" outlined>
								{{ $t("Launch your ai agent") }}
							</BaseButton>
						</div>
					</div>
					<div class="flex flex-col sm:flex-row sm:items-center gap-3">
						<a v-for="ad in $page.props.ads" :key="ad.id" :href="ad.url">
							<img
								class="w-auto h-40 border border-gray-650 rounded"
								:src="ad.image"
							/>
						</a>
					</div>
				</div>
				<div
					class="flex gap-4 items-center my-8 mx-4 justify-center sm:justify-start sm:mx-[unset] flex-wrap"
				>
					<appkit-network-button v-if="chainId" />
					<BaseButton
						@click="animate = !animate"
						size="xss"
						class="font-semibold !px-2 py-1"
					>
						{{ $t("Animation") }}
						<SmallSwitch :modelValue="animate" class="ml-1"></SmallSwitch>
					</BaseButton>
					<BaseButton
						v-for="filter in filters"
						:key="filter.id"
						:href="
							route('launchpads.index', {
								type: filter.id == 'trending' ? '' : filter.id,
							})
						"
						:secondary="filter.id != type"
						link
						size="xs"
						class="font-semibold !px-4"
					>
						<component
							:is="filter.icon"
							class="w-4 h-4 mr-1 -ml-1 inline-flex"
						/>
						{{ filterNames[filter.id] }}
					</BaseButton>
					<BaseButton
						size="xs"
						icon-mode
						outlined
						:secondary="tableMode"
						@click="tableMode = false"
						class="!h-7 !w-7"
					>
						<LayoutGrid class="w-5 h-5" />
					</BaseButton>
					<BaseButton
						size="xs"
						icon-mode
						:secondary="!tableMode"
						outlined
						@click="tableMode = true"
						class="!h-7 !w-7"
					>
						<LayoutList class="w-5 h-5" />
					</BaseButton>
					<FormInput v-model="search" class="ml-auto" size="sm">
						<template #lead>
							<Search class="w-4 h-4 ml-1 text-gray-400" />
						</template>
					</FormInput>
				</div>
			</template>
			<LoaderCircle
				v-if="!tableMode && type !== 'mine' && launchpadsInfo.loading.value"
				class="w-8 mt-5 text-white h-8 mr-2 animate-spin"
			/>
			<CollapseTransition>
				<AnimationsRow
					class="my-4"
					:initialTrades="$page.props.initialTrades"
					v-show="animate"
					:mine="type == 'mine'"
				/>
			</CollapseTransition>

			<div v-if="!tableMode" class="grid my-6 md:grid-col-3 lg:grid-cols-3 gap-5">
				<IndexCard
					v-for="lpd in launchpadsInfo.launchpads.value"
					:key="lpd.name"
					:id="lpd.contract"
					:launchpad="lpd"
				/>
			</div>
			<IndexTable
				v-if="tableMode"
				class="mt-5"
				v-model:current-sort="currentSort"
				:loading="launchpadsInfo.loading.value"
				v-model:current-dir="currentDir"
				:launchpads="launchpadsInfo.launchpads.value"
			/>
			<Pagination :meta="launchpads.meta" />
			<HowItWorksModal v-model:show="showHowItWorks" />
		</div>
	</AppLayout>
</template>
