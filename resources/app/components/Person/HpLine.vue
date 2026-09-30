<template>
	<div class="hp-line" :class="'hp-line--' + color">
		<slot name="icon" />
		<div class="hp-line-track">
			<div class="hp-line-fill" :style="{ width: width + '%' }"></div>
		</div>
		<span class="hp-line-value">{{ displayedCurrent }} / {{ max }}</span>
	</div>
</template>

<script setup>
	import { computed, ref, watch } from 'vue';
	import { useIntervalFn } from '@vueuse/core';

	const props = defineProps({
		color: String,
		current: Number,
		max: Number,
		regeneration: {
			type: Object,
			default: null,
		},
	});

	const elapsed = ref(0);

	let receivedAt = 0;

	const { pause, resume } = useIntervalFn(() => {
		elapsed.value = (performance.now() - receivedAt) / 1000;

		if (elapsed.value >= props.regeneration.remaining) {
			pause();
		}
	}, 100, { immediate: false });

	watch(() => [props.current, props.max, props.regeneration], () => {
		pause();

		elapsed.value = 0;

		receivedAt = performance.now();

		if (props.regeneration?.remaining > 0) {
			resume();
		}
	}, { immediate: true });

	const secondsRemaining = computed(() => {
		if (!props.regeneration) {
			return 0;
		}

		return Math.max(0, props.regeneration.remaining - elapsed.value);
	});

	const currentValue = computed(() => {
		if (!props.regeneration) {
			return props.current;
		}

		const healthRemaining = secondsRemaining.value * props.max / props.regeneration.duration;

		return Math.max(0, Math.min(props.max, props.max - healthRemaining));
	});

	const displayedCurrent = computed(() => {
		if (!props.regeneration) {
			return props.current;
		}

		if (props.regeneration.hospital) {
			const healthRemaining = Math.floor(secondsRemaining.value) * props.max / props.regeneration.duration;

			return Math.max(0, props.max - Math.round(healthRemaining));
		}

		return Math.floor(Math.round(currentValue.value * 10000) / 10000);
	});

	const width = computed(() => {
		if (props.max <= 0) {
			return 0;
		}

		return Math.min(100, Math.max(0, (currentValue.value / props.max) * 100));
	});
</script>
