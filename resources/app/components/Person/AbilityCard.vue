<template>
	<article class="person-ability" :class="{ 'is-equipped': item.onset }">
		<img :src="'/assets/images/battle/abilities/' + abilityId + '.gif'" class="person-ability-icon" alt="" />
		<div class="person-ability-description">
			<div class="person-ability-heading">
				<h3>{{ item.name }}</h3>
				<span>Уровень {{ item.level }}</span>
			</div>
			<p>{{ item.about }}</p>
			<dl class="person-ability-costs">
				<template v-for="cost in costs" :key="cost.key">
					<div v-if="item[cost.key]" :title="cost.title">
						<dt>{{ cost.label }}</dt>
						<dd>{{ item[cost.key] }}</dd>
					</div>
				</template>
			</dl>
		</div>
		<span v-if="item.onset" class="person-ability-equipped">Выбран</span>
		<button v-else type="button" class="ui-button ui-button--compact" :disabled="processing" @click="emit('activate')">Добавить</button>
	</article>
</template>

<script setup>
	defineProps({
		item: Object,
		abilityId: [String, Number],
		processing: Boolean,
	});

	const emit = defineEmits(['activate']);

	const costs = [
		{ key: 'block', label: 'Блок', title: 'Очки блока' },
		{ key: 'hit', label: 'Пробой', title: 'Очки пробоя' },
		{ key: 'crit', label: 'Крит', title: 'Очки крита' },
		{ key: 'parry', label: 'Уворот', title: 'Очки уворота' },
		{ key: 'damage', label: 'Повреждения', title: 'Очки повреждений' },
		{ key: 'magic', label: 'Магия', title: 'Очки магии' },
	];
</script>
