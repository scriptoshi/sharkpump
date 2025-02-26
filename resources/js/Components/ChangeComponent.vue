<script setup>
defineProps({
    change: [String, Number]
});
</script>
<template>
    <td
        v-if="parseFloat(change) == 0"
        class="px-4 py-2"
    >-</td>
    <td
        class="px-4 py-2"
        v-else
        :class="parseFloat(change) > 0 ? 'text-green-500' : 'text-red-500'"
    >{{ parseFloat(change).toFixed(4) * 1 }}%
    </td>

</template>