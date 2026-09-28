<template>
	<div class="hp-line" :class="'hp-line--' + color">
		<slot name="icon" />
		<div class="hp-line-track">
			<div class="hp-line-fill" :style="{ width: width + '%' }"></div>
		</div>
		<span class="hp-line-value">{{ current }} / {{ max }}</span>
	</div>
</template>

<script setup>
	import { computed } from 'vue';

	const props = defineProps({
		color: String,
		current: Number,
		max: Number,
	});

	const width = computed(() => {
		if (props.max <= 0) {
			return 0;
		}

		return Math.min(100, Math.max(0, (props.current / props.max) * 100));
	});
</script>
