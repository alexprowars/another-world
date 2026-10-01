<template>
	<p class="ui-notice ui-notice--red" role="alert">
		<strong>Персонаж заблокирован!</strong>
		<span v-if="reason">Причина: {{ reason }}</span>
	</p>
</template>

<script setup>
	defineProps({
		reason: String,
	});
</script>
