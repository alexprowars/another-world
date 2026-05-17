<template>
	<table v-if="priems?.p">
		<tr>
			<td align="center">
				<div class="priem-res">
					<span v-for="point in points" :key="point.key">
						<img :title="point.title" :width="point.width" height="8" :src="`/assets/images/battle/priem/${point.icon}.gif`" alt=""><br>{{ point.value }}
					</span>
				</div>
			</td>
		</tr>
		<tr>
			<td align="center">
				<template v-if="priems.p.pa.w === 0">
					<BattlePriemIcon v-for="index in 8" :key="index" :priem="priems[`p_${index}`]" @use="$emit('use', $event)"/>
				</template>
				<template v-else>
					Выбран приём <b>{{ priems.p.pa.n }}</b><br>
					Ожидание/Действие: <b>{{ priems.p.pa.w }}/{{ priems.p.pa.t }}</b> ходов.
				</template>
			</td>
		</tr>
	</table>
</template>

<script setup>
	import { computed } from 'vue';
	import BattlePriemIcon from './BattlePriemIcon.vue';

	const props = defineProps({
		priems: {
			type: Object,
			default: () => ({}),
		},
	});

	defineEmits(['use']);

	const points = computed(() => [
		{ key: 'b', title: 'Блокирование удара', icon: 'block', width: 7, value: props.priems.p.points.b },
		{ key: 'h', title: 'Удар', icon: 'hit', width: 7, value: props.priems.p.points.h },
		{ key: 'k', title: 'Критический удар', icon: 'krit', width: 7, value: props.priems.p.points.k },
		{ key: 'p', title: 'Успешное парирование', icon: 'parry', width: 8, value: props.priems.p.points.p },
		{ key: 'hp', title: 'Нанесенный урон', icon: 'hp', width: 8, value: props.priems.p.points.hp },
		{ key: 'm', title: 'Магическая мощь', icon: 'spirit', width: 7, value: props.priems.p.points.m },
	]);
</script>
