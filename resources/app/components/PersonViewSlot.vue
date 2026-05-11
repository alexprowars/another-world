<template>
	<img :src="image" :width="width" :height="height" class="tooltip script" alt="">
</template>

<script setup>
	import { computed } from 'vue';

	const props = defineProps({
		position: {
			type: Number,
		}
	});

	const width = computed(() => {
		switch (props.position) {
			case 1: case 2: case 3: case 4: case 5:
				return 60;
			case 6: case 7: case 8:
				return 20;
			case 9:
				return 60;
			case 10: case 11: case 12:
				return 20;
			case 13: case 14: case 15:
			case 16:
				return 60;
			case 17: case 18: case 19:
				return 40;
			case 20:
				return 120;
			case 21: case 22:
				return 60;
		}

		return 0;
	});

	const height = computed(() => {
		switch (props.position) {
			case 1: return 58;
			case 2: return 19;
			case 3: return 58;
			case 4: return 78;
			case 5: return 60;
			case 6: return 20;
			case 7: return 20;
			case 8: return 20;
			case 9: return 28;
			case 10: return 20;
			case 11: return 20;
			case 12: return 20;
			case 13: return 40;
			case 14: return 40;
			case 15: return 40;
			case 16: return 78;
			case 17: return 25;
			case 18: return 25;
			case 19: return 25;
			case 20: return 60;
			case 21: return 19;
			case 22: return 80;
		}

		return 0;
	});

	const image = computed(() => {
		return '/assets/images/items/w' + props.position + '.gif';
	});
</script>