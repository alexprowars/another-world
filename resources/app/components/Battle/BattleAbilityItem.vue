<template>
	<div v-if="!priem || priem.id === 0">
		<img width="40" height="25" src="/assets/images/battle/abilities/clear.gif" title="Пустой слот приёма" alt="" />
	</div>
	<Popper v-else placement="top" popper-class="battle-ability-popper">
		<img
			:class="{ 'cursor-pointer': priem.w === 0 }"
			width="40"
			height="25"
			:src="`/assets/images/battle/abilities/${priem.id}${priem.w === 1 ? 'n' : ''}.gif`"
			:alt="priem.n"
			@click="use"
		/>

		<template #content>
			<article class="battle-ability-tooltip">
				<header class="battle-ability-tooltip__heading">
					<h3>{{ priem.n }}</h3>
					<span>Боевой приём</span>
				</header>
				<div class="battle-ability-tooltip__body">
					<section>
						<h4>Минимальные требования</h4>
						<dl class="battle-ability-tooltip__requirements">
							<div v-for="requirement in requirements" :key="requirement.key">
								<dt>{{ requirement.label }}</dt>
								<dd>{{ priem[requirement.key] }}</dd>
							</div>
						</dl>
					</section>
					<section v-if="priem.a">
						<h4>Описание</h4>
						<p>{{ priem.a }}</p>
					</section>
				</div>
				<footer class="battle-ability-tooltip__footer" :class="{ 'is-unavailable': priem.w !== 0 }">
					{{ priem.w === 0 ? 'Нажмите на приём, чтобы использовать' : 'Приём сейчас недоступен' }}
				</footer>
			</article>
		</template>
	</Popper>
</template>

<script setup>
	import Popper from '~/components/Popper.vue';

	const requirements = [
		{ key: 'b', label: 'Блокирование' },
		{ key: 'h', label: 'Удар' },
		{ key: 'k', label: 'Крит' },
		{ key: 'p', label: 'Парирование' },
		{ key: 'd', label: 'Урон' },
		{ key: 'm', label: 'Магия' },
	];

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
