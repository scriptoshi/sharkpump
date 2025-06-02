<script setup>
defineProps({
	nft: Object,
	factory: Object,
});
</script>

<template>
	<div
		class="card w-full mb-6 mx-auto dark:bg-gray-850 sm:p-4 h-full border-0 card-border"
	>
		<div class="card-body card-gutterless h-full">
			<div class="flex items-center justify-between">
				<h3 class="!text-primary flex items-center gap-2">
					<img
						v-if="nft.image"
						:src="nft.image"
						alt="NFT Image"
						class="w-8 h-8 rounded-lg object-cover"
					/>
					{{ nft?.name }} SPAWNPAD
				</h3>
				<p>{{ factory.version }} ({{ new Date().getFullYear() }})</p>
			</div>
		</div>
	</div>
</template>
