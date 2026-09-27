<template>
	<p class="rounded bg-red-100 p-3 text-center text-red-700">
		Персонаж заблокирован!<span v-if="reason"> Причина: {{ reason }}</span>
	</p>
</template>

<script setup>
	defineProps({
		reason: String,
	});
</script>
