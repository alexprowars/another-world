<template>
	<div v-if="abilities">
		<div class="flex justify-center gap-5">
			<div v-for="point in ['blocks', 'hits', 'crits', 'parry', 'hp', 'magic']">
				<img :alt="$t('battle.points.' + point)" v-tooltip="$t('battle.points.' + point)" height="8" :src="`/assets/images/battle/abilities/${point}.gif`">
				<div>{{ abilities.points[point] }}</div>
			</div>
		</div>
		<div class="text-center mt-2">
			<div v-if="abilities.wait === 0" class="flex gap-1">
				<BattleAbilityItem v-for="index in 10" :key="index" :priem="abilities['list'][`p_${index}`]" @use="$emit('use', $event)"/>
			</div>
			<div v-else>
				<div>Выбран приём <b>{{ abilities.ability }}</b></div>
				<div>Ожидание/Действие: <b>{{ abilities.wait }}/{{ abilities.time }}</b> ходов.</div>
			</div>
		</div>
	</div>
</template>

<script setup>
	import BattleAbilityItem from './BattleAbilityItem.vue';

	const props = defineProps({
		abilities: {
			type: Object,
			default: null,
		},
	});

	defineEmits(['use']);
</script>
