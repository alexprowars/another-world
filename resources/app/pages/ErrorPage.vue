<template>
	<div class="error-page text-center">
		<div class="text-lg text-red-600">{{ page.status }}</div>
		<div class="text-base text-red-600">{{ page.message }}</div>
		<div v-if="page.trace" class="w-full text-left">{{ page.trace }}</div>
	</div>
</template>

<script setup>
	defineOptions({
		layout: 'empty',
	});

	defineProps({
		page: Object,
	})
</script>