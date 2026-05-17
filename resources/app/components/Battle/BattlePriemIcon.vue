<template>
	<img v-if="!priem || priem.id === 0" width="40" height="25" src="/assets/images/battle/priem/clear.gif" title="Пустой слот приёма" alt="">
	<Popper v-else placement="top" theme="borderless">
		<img
			:style="priem.w === 1 ? '' : 'cursor: pointer;'"
			width="40"
			height="25"
			:src="`/assets/images/battle/priem/${priem.id}${priem.w === 1 ? 'n' : ''}.gif`"
			:title="priem.w === 1 ? '' : 'Нажмите для использования'"
			alt=""
			@click="use"
		>

		<template #content>
			<table width="200">
				<tr>
					<td><font color="blue"><b>{{ priem.n }}</b></font></td>
				</tr>
				<tr>
					<td>
						<font color="red" size="1">Мин. треб:<br></font>
						<font size="1">
							Блокирование: {{ priem.b }}<br>
							Удар: {{ priem.h }}<br>
							Крит: {{ priem.k }}<br>
							Парирование: {{ priem.p }}<br>
							Урон: {{ priem.d }}<br>
							Магия: {{ priem.m }}<br>
						</font>
						<font color="red" size="1">Описание:<br></font>
						<font size="1">{{ priem.a }}</font>
					</td>
				</tr>
			</table>
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
