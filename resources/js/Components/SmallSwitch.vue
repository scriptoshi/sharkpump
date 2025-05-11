<script setup>
import { useSlots } from "vue";
defineProps(["modelValue", "name"]);
defineEmits(["update:modelValue"]);
const slots = useSlots();
</script>
<template>
	<label :for="name" class="inline-flex relative items-center cursor-pointer">
		<input
			type="checkbox"
			:name="name"
			:id="name"
			:value="modelValue"
			:checked="modelValue"
			@input="(event) => $emit('update:modelValue', event.target.checked)"
			class="sr-only peer"
		/>
		<div
			class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 dark:peer-focus:ring-amber-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-amber-600"
		></div>
		<span
			v-if="slots.default"
			class="ml-2 text-sm font-medium text-gray-600 dark:text-gray-300"
			><slot
		/></span>
	</label>
</template>
