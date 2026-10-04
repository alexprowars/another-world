<template>
	<article class="info-popover">
		<header class="info-popover-header">
			<h3>{{ title }}</h3>
			<span v-if="subtitle">{{ subtitle }}</span>
		</header>
		<div class="info-popover-body">
			<slot />
		</div>
		<footer v-if="$slots.footer" class="info-popover-footer">
			<slot name="footer" />
		</footer>
	</article>
</template>

<script setup>
	defineProps({
		title: { type: String, required: true },
		subtitle: { type: String, default: '' },
	});
</script>
