<script setup>
import { ref } from "vue";

import { Head, router, useForm } from "@inertiajs/vue3";
import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
import { Plus } from "lucide-vue-next";
import { HiCog, HiTrash } from "oh-vue-icons/icons";

import ChainInfo from "@/Components/ChainInfo.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import DangerButton from "@/Components/DangerButton.vue";
import FormSwitch from "@/Components/FormSwitch.vue";
import Loading from "@/Components/Loading.vue";
import Pagination from "@/Components/Pagination.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SearchInput from "@/Components/SearchInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
import WeCopy from "@/Components/WeCopy.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { shortenAddress } from "@/lib/wagmi";

const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");
const deleteFactoryForm = useForm({});
const factoryBeingDeleted = ref(null);
defineProps({
	factories: Object,
	title: { required: false, type: String },
});
const deleteFactory = () => {
	deleteFactoryForm.delete(
		window.route("admin.factories.destroy", factoryBeingDeleted.value?.id),
		{
			preserveScroll: true,
			preserveState: true,
			onSuccess: () => (factoryBeingDeleted.value = null),
		}
	);
};
debouncedWatch(
	[search],
	([search]) => {
		router.get(
			window.route("admin.factories.index"),
			{ ...(search ? { search } : {}) },
			{
				preserveState: true,
				preserveScroll: true,
			}
		);
	},
	{
		maxWait: 700,
	}
);

const toggle = (factory) => {
	factory.busy = true;
	router.put(
		window.route("admin.factories.toggle", factory.id),
		{},
		{
			preserveScroll: true,
			preserveState: true,
			onFinish: () => {
				factory.busy = false;
				factoryBeingDeleted.value = null;
			},
		}
	);
};
</script>
<template>
	<Head :title="'Factories'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8"
			>
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ $t("Manage Factories") }}
							</h3>
							<p>
								{{
									$t(
										"Factories are used to deploy the launchpads and collect fees"
									)
								}}
							</p>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<PrimaryButton
								size="sm"
								link
								:href="route('admin.factories.create')"
							>
								<Plus class="w-5 h-5 mr-2 -ml-1" />
								{{ $t("Create New Factory") }}
							</PrimaryButton>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body card-gutterless h-full">
							<div class="lg:flex items-center justify-end mb-4 px-6">
								<div class="flex gap-x-3 sm:w-1/2 lg:w-1/4">
									<SearchInput class="max-w-md" v-model="search" />
								</div>
							</div>
							<div>
								<div class="overflow-x-auto">
									<table class="table-default table-hover" role="table">
										<thead>
											<tr role="row">
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
												>
													{{ $t("Version") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
												>
													{{ $t("Chainid") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
												>
													{{ $t("Spawnpads") }}
												</th>

												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
												>
													{{ $t("Contract") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
												>
													{{ $t("Contract") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
												>
													{{ $t("Lock") }}
												</th>

												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
												>
													{{ $t("Active") }}
												</th>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="factory in factories.data"
												:key="factory.id"
												role="row"
											>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200"
												>
													{{ factory.version }}
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200"
												>
													<ChainInfo
														:chainId="factory.chainId"
													/>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200"
												>
													{{ factory.launchpads_count }}
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200"
												>
													<WeCopy
														after
														:text="factory.contract"
													>
														{{
															shortenAddress(
																factory.contract
															)
														}}
													</WeCopy>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200"
												>
													<WeCopy after :text="factory.lock">
														{{ shortenAddress(factory.lock) }}
													</WeCopy>
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200"
												>
													<div class="flex items-center gap-2">
														<Loading v-if="factory.busy" />
														<FormSwitch
															:model-value="factory.active"
															@update:model-value="
																toggle(factory)
															"
														/>
													</div>
												</td>
												<td role="cell">
													<div
														class="flex justify-end items-center text-lg"
													>
														<PrimaryButton
															:href="
																route(
																	'admin.factories.edit',
																	factory.id
																)
															"
															link
															class="mr-3"
															size="xss"
														>
															<VueIcon
																:icon="HiCog"
																class="w-4 h-4 mr-2 -ml-1"
															/>
															Manage
														</PrimaryButton>

														<a
															href="#"
															@click.prevent="
																factoryBeingDeleted = factory
															"
															class="cursor-pointer link p-2 hover:text-red-500"
														>
															<VueIcon
																:icon="HiTrash"
																class="w-4 h-4"
															/>
														</a>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="factories.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="factoryBeingDeleted"
			@close="factoryBeingDeleted = null"
		>
			<template #title>
				{{
					$t("Are you sure about deleting {factory} ?", {
						factory: factoryBeingDeleted.name,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will remove the factory from the database and cannot be undone"
						)
					}}
				</p>
				<p>
					{{ $t("Its Recommended to Disable the factory Instead") }}
				</p>
			</template>

			<template #footer>
				<SecondaryButton
					outlined
					class="uppercase text-xs font-semibold"
					@click="factoryBeingDeleted = null"
				>
					{{ $t("Cancel") }}
				</SecondaryButton>

				<SecondaryButton
					class="ml-2 uppercase text-xs font-semibold"
					v-if="factoryBeingDeleted.active"
					@click="toggle(factoryBeingDeleted)"
				>
					<Loading
						class="!h-5 !w-5 mr-2 -ml-1"
						v-if="factoryBeingDeleted.busy"
					/>
					{{ $t("Disable") }}
				</SecondaryButton>

				<DangerButton
					class="ml-2 uppercase text-xs font-semibold"
					@click="deleteFactory"
					:class="{ 'opacity-25': deleteFactoryForm.processing }"
					:disabled="deleteFactoryForm.processing"
				>
					<Loading
						v-if="deleteFactoryForm.processing"
						class="!h-4 !w-4 mr-2 -ml-1"
					/>
					{{ $t("Delete") }}
				</DangerButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
