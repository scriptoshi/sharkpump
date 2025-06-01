<script setup>
import { ref } from "vue";

import { Head, router, useForm } from "@inertiajs/vue3";
import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
import { HiCog, HiTrash } from "oh-vue-icons/icons";

import AddressLink from "@/Components/AddressLink.vue";
import ChainInfo from "@/Components/ChainInfo.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import FormSwitch from "@/Components/FormSwitch.vue";
import Loading from "@/Components/Loading.vue";
import Pagination from "@/Components/Pagination.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SearchInput from "@/Components/SearchInput.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";

defineProps({
	nfts: Object,
	title: { required: false, type: String },
});

const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");
const deleteNftForm = useForm({});
const nftBeingDeleted = ref(null);

const deleteNft = () => {
	deleteNftForm.delete(window.route("admin.nfts.destroy", nftBeingDeleted.value?.id), {
		preserveScroll: true,
		preserveState: true,
		onSuccess: () => (nftBeingDeleted.value = null),
	});
};
debouncedWatch(
	[search],
	([search]) => {
		router.get(
			window.route("admin.nfts.index"),
			{ search },
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

const toggle = (nft) => {
	nft.busy = true;
	router.put(
		window.route("admin.nfts.toggle", nft.id),
		{},
		{
			preserveScroll: true,
			preserveState: true,
			onFinish: () => {
				nft.busy = false;
				nftBeingDeleted.value = null;
			},
		}
	);
};
</script>
<template>
	<Head :title="title ?? 'Nfts'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8"
			>
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ $t("Manage Soul bound Nfts") }}
							</h3>
							<p>{{ $t("Used for verification badge") }}</p>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<PrimaryButton
								secondary
								link
								:href="route('admin.nfts.create')"
							>
								{{ $t("Create New Nfts") }}
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
													{{ $t("Name") }}
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
													{{ $t("Contract") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
												>
													{{ $t("Type") }}
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
												v-for="nft in nfts.data"
												:key="nft.id"
												role="row"
											>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-300"
												>
													<div class="flex items-center gap-2">
														<img
															:src="nft.image"
															class="w-8 h-8 mr-2 rounded-full"
														/>
														<div>
															{{ nft.name }} ({{
																nft.symbol
															}})
														</div>
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-300"
												>
													<ChainInfo :chain-id="nft.chainId" />
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-300"
												>
													<AddressLink
														:chain-id="nft.chainId"
														:address="nft.contract"
														:len="10"
													/>
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-300"
												>
													{{ nft.type }}
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-300"
												>
													<div class="flex items-center gap-2">
														<Loading v-if="nft.busy" />
														<FormSwitch
															:model-value="nft.active"
															@update:model-value="
																toggle(nft)
															"
														/>
													</div>
												</td>
												<td role="cell">
													<div class="flex justify-end text-lg">
														<div
															class="flex justify-end items-center text-lg"
														>
															<PrimaryButton
																:href="
																	route(
																		'admin.nfts.edit',
																		nft.contract
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
																Settings
															</PrimaryButton>
															<PrimaryButton
																:href="
																	route(
																		'admin.nfts.metadata',
																		nft.contract
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
																Metadata
															</PrimaryButton>

															<a
																href="#"
																@click.prevent="
																	nftBeingDeleted = nft
																"
																class="cursor-pointer link p-2 hover:text-red-500"
															>
																<VueIcon
																	:icon="HiTrash"
																	class="w-4 h-4"
																/>
															</a>
														</div>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="nfts.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal :show="nftBeingDeleted" @close="nftBeingDeleted = null">
			<template #title>
				{{
					$t("Are you sure about deleting {nft} ?", {
						nft: nftBeingDeleted.name,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will remove the nft from the database and cannot be undone"
						)
					}}
				</p>
				<p>
					{{ $t("Its Recommended to Disable the nft Instead") }}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="nftBeingDeleted = null"
				>
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					secondary
					class="ml-2 uppercase text-xs font-semibold"
					v-if="nftBeingDeleted.active"
					@click="toggle(nftBeingDeleted)"
				>
					<Loading v-if="nftBeingDeleted.busy" />
					{{ $t("Disable") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="deleteNft"
					:class="{ 'opacity-25': deleteNftForm.processing }"
					:disabled="deleteNftForm.processing"
				>
					{{ $t("Delete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
