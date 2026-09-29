<template>
	<div v-if="abilities" class="battle-abilities">
		<div class="battle-abilities__points">
			<div
				v-for="point in points"
				:key="point.key"
				v-tooltip="$t('battle.points.' + point.key)"
				class="battle-abilities__point"
				:class="'battle-abilities__point--' + point.key"
			>
				<component :is="point.icon" class="battle-abilities__point-icon" />
				<span class="sr-only">{{ $t('battle.points.' + point.key) }}</span>
				<span>{{ abilities.points[point.key] }}</span>
			</div>
		</div>
		<div class="text-center mt-2">
			<div v-if="abilities.wait === 0" class="battle-abilities__slots">
				<BattleAbilityItem v-for="index in 10" :key="index" :priem="abilities['list'][`p_${index}`]" @use="$emit('use', $event)" />
			</div>
			<div v-else>
				<div>
					Выбран приём
					<b>{{ abilities.ability }}</b>
				</div>
				<div>
					Ожидание/Действие:
					<b>{{ abilities.wait }}/{{ abilities.time }}</b>
					ходов.
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
	import BattleAbilityItem from './BattleAbilityItem.vue';
	import BlockIcon from '~/icons/battle/block.svg';
	import HitIcon from '~/icons/battle/hit.svg';
	import CriticalIcon from '~/icons/battle/critical.svg';
	import ParryIcon from '~/icons/battle/parry.svg';
	import HealthIcon from '~/icons/resources/health.svg';
	import MagicIcon from '~/icons/resources/mana.svg';

	const points = [
		{ key: 'blocks', icon: BlockIcon },
		{ key: 'hits', icon: HitIcon },
		{ key: 'crits', icon: CriticalIcon },
		{ key: 'parry', icon: ParryIcon },
		{ key: 'hp', icon: HealthIcon },
		{ key: 'magic', icon: MagicIcon },
	];

	const props = defineProps({
		abilities: {
			type: Object,
			default: null,
		},
	});

	defineEmits(['use']);
</script>
