<script setup>
import { Link, } from "@inertiajs/vue3";

import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {
    Dialog,
    DialogHeader,
} from "@/Components/ui/dialog";
import DialogContentOverlay from "@/Components/ui/dialog/DialogContentOverlay.vue";

defineProps({
    show: Boolean,
});

defineEmits(["update:show"]);


</script>

<template>
    <Dialog
        :open="show"
        @update:open="(val) => $emit('update:show', val)"
    >
        <DialogContentOverlay class="sm:max-w-[425px]">
            <DialogHeader>
                <Link
                    class="flex mb-4 items-center"
                    href="/"
                >
                <ApplicationLogo class="block h-8 w-auto font-semibold" />
                </Link>
                <h3 class="text-2xl">Provide A signature</h3>
                <p>
                    To complete login, provide a valid signature with your connected wallet.
                    <br />
                    This will not cost you any gas fees.
                    <br />
                </p>
            </DialogHeader>
            <slot></slot>
        </DialogContentOverlay>
    </Dialog>
</template>
