<script setup>
import { HiArrowLeft } from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/PrimaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ManageForm from "@/Pages/Admin/Nfts/ManageForm.vue";
defineProps({
    title: { required: false, type: String },
    nft: { type: Object, required: true },
    types: { type: Object, required: true },
});
</script>
<template>
    <AdminLayout>

        <Head :title="title ?? `Edit Nft`" />
        <main class="h-full">
            <div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
                <div class="flex flex-col gap-4 h-full">
                    <div class="lg:flex items-center justify-between mb-4 gap-3">
                        <div class="mb-4 lg:mb-0">
                            <h3 class="h3">Edit {{ nft?.name }} Nft</h3>
                        </div>
                        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                            <PrimaryButton
                                secondary
                                link
                                :href="route('admin.nfts.index')"
                            >
                                <VueIcon
                                    :icon="HiArrowLeft"
                                    class="w-4 h-4 -ml-1 mr-2 inline-block"
                                />
                                {{ $t("Back to nfts list") }}
                            </PrimaryButton>
                        </div>
                    </div>
                    <ManageForm
                        :nft="nft"
                        :types="types"
                    />
                </div>
            </div>
        </main>
    </AdminLayout>
</template>
