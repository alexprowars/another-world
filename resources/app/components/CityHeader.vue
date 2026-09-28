<template>
	<header class="location-card-header">
		<h1 class="location-card-heading">{{ title }}</h1>
		<div v-if="$slots.actions" class="location-card-actions">
			<slot name="actions" />
		</div>
	</header>
</template>

<script setup>
	defineProps({
		title: {
			type: String,
			default: '',
		},
	});
</script>
