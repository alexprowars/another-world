<template>
	<div v-if="!priem || priem.id === 0">
		<img width="40" height="25" src="/assets/images/battle/abilities/clear.gif" title="Пустой слот приёма" alt="">
	</div>
	<Popper v-else placement="top">
		<img
			:style="priem.w === 1 ? '' : 'cursor: pointer;'"
			width="40"
			height="25"
			:src="`/assets/images/battle/abilities/${priem.id}${priem.w === 1 ? 'n' : ''}.gif`"
			:title="priem.w === 1 ? '' : 'Нажмите для использования'"
			alt=""
			@click="use"
		>

		<template #content>
			<div class="w-[200px]">
				<div class="text-blue-600 font-bold">{{ priem.n }}</div>
				<div class="text-xs">
					<span class="text-red-600">Мин. треб:<br></span>
					<span>
						Блокирование: {{ priem.b }}<br>
						Удар: {{ priem.h }}<br>
						Крит: {{ priem.k }}<br>
						Парирование: {{ priem.p }}<br>
						Урон: {{ priem.d }}<br>
						Магия: {{ priem.m }}<br>
					</span>
					<span class="text-red-600">Описание:<br></span>
					<span>{{ priem.a }}</span>
				</div>
			</div>
		</template>
	</Popper>
</template>

<script setup>
	import Popper from '~/components/Popper.vue';

	const props = defineProps({
		priem: {
			type: Object,
			default: null,
		},
	});

	const emit = defineEmits(['use']);

	function use() {
		if (props.priem?.w === 0) {
			emit('use', props.priem.id);
		}
	}
</script>
