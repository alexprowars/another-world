<template>
	<div class="flex items-center gap-2 justify-between">
		<div class="bdg stbox">
			<div :class="[color]" :style="{ width: width + '%' }">
				<img src="/assets/images/main/empty.gif" width="1" height="10" alt="">
			</div>
		</div>
		<div class="flex gap-1">
			<div class="fntc flex items-center">{{ current }}</div>
			<div class="intf">|</div>
			<div class="minf flex items-center w-8">{{ max }}</div>
		</div>
	</div>
</template>

<script setup>
	import { computed } from 'vue';

	const props = defineProps({
		color: String,
		current: Number,
		max: Number,
	})

	const width = computed(() => {
		if (props.max <= 0) {
			return 0;
		}

		return Math.min(100, Math.max(0, (props.current / props.max) * 100));
	})
</script>