<template>
	<div v-if="!priem || priem.id === 0" class="battle-ability-empty" title="Пустой слот приёма"></div>
	<Popper v-else placement="top" popper-class="info-popover-popper">
		<img
			class="ability-icon battle-ability-icon"
			:class="{ 'cursor-pointer': priem.w === 0, 'is-unavailable': priem.w !== 0 }"
			width="32"
			height="20"
			:src="`/assets/images/battle/abilities/${priem.id}.png`"
			:alt="priem.n"
			@click="use"
		/>

		<template #content>
			<InfoPopoverContent :title="priem.n" subtitle="Боевой приём">
				<section v-if="visibleRequirements.length" class="info-popover-section">
					<h4>Минимальные требования</h4>
					<dl class="info-popover-stats">
						<div v-for="requirement in visibleRequirements" :key="requirement.key">
							<dt>{{ requirement.label }}</dt>
							<dd>{{ priem[requirement.key] }}</dd>
						</div>
					</dl>
				</section>
				<section v-if="priem.a" class="info-popover-section">
					<h4>Описание</h4>
					<p>{{ priem.a }}</p>
				</section>
				<template #footer>
					<span :class="{ 'is-unavailable': priem.w !== 0 }">
						{{ priem.w === 0 ? 'Нажмите на приём, чтобы использовать' : 'Приём сейчас недоступен' }}
					</span>
				</template>
			</InfoPopoverContent>
		</template>
	</Popper>
</template>

<script setup>
	import { computed } from 'vue';
	import Popper from '~/components/Popper.vue';
	import InfoPopoverContent from '~/components/InfoPopoverContent.vue';

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

	const visibleRequirements = computed(() => requirements.filter(requirement => props.priem?.[requirement.key] > 0));

	function use() {
		if (props.priem?.w === 0) {
			emit('use', props.priem.id);
		}
	}
</script>
