<template>
	<div class="gambling-die" :title="value ? 'Выпало: ' + value : 'Кубик ещё не брошен'">
		<template v-if="value">
			<span v-for="position in 9" :key="position" class="gambling-pip" :class="{ 'is-visible': pips[value].includes(position) }"></span>
		</template>
		<span v-else class="gambling-die-unknown">?</span>
	</div>
</template>

<script setup>
	defineProps({
		value: Number,
	});

	const pips = {
		1: [5],
		2: [1, 9],
		3: [1, 5, 9],
		4: [1, 3, 7, 9],
		5: [1, 3, 5, 7, 9],
		6: [1, 3, 4, 6, 7, 9],
	};
</script>
