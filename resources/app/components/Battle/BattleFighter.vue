<template>
	<section class="battle-fighter" :class="{ 'battle-fighter--current': current }">
		<header class="battle-fighter__heading">
			<span class="battle-fighter__label">{{ current ? 'Ваш персонаж' : 'Противник' }}</span>
			<Name :player="fighter" />
		</header>
		<div class="battle-fighter__resources">
			<HpLine :current="fighter.hp" :max="fighter.hp_max" color="g_line" />
			<HpLine :current="fighter.energy" :max="fighter.energy_max" color="b_line" />
		</div>
		<PersonEquipment :slots="fighter.items" :avatar="fighter.avatar" :name="fighter.name" :show-scrolls="current" />
	</section>
</template>

<script setup>
	import PersonEquipment from '~/components/PersonEquipment.vue';
	import HpLine from '~/components/Person/HpLine.vue';
	import Name from '~/components/Person/Name.vue';

	defineProps({
		fighter: {
			type: Object,
			required: true,
		},
		current: {
			type: Boolean,
			default: false,
		},
	});
</script>
