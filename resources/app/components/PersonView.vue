<template>
	<section class="person-view">
		<Name :player="person" />
		<div class="person-lines">
			<HpLine :current="person.hp_now" :max="person.hp_max" :regeneration="person.hp_regeneration" color="g_line" v-tooltip="'Здоровье'" />
			<HpLine :current="person.energy_now" :max="person.energy_max" color="b_line" v-tooltip="'Мана'" />
			<HpLine v-if="person.stamina_max" :current="person.stamina_now" :max="person.stamina_max" color="h_line" v-tooltip="'Запас сил'" />
		</div>
		<PersonEquipment
			:slots="person.slots"
			:avatar="person.avatar"
			:name="person.name"
			:editable-avatar="!readonly"
			:can-use="canUse && !readonly"
			@use="emit('use', $event)"
		/>
	</section>
</template>

<script setup>
	import PersonEquipment from './PersonEquipment.vue';
	import Name from '~/components/Person/Name.vue';
	import HpLine from '~/components/Person/HpLine.vue';

	defineProps({
		readonly: {
			type: Boolean,
			default: false,
		},
		person: {
			type: Object,
		},
		canUse: { type: Boolean, default: false },
	});

	const emit = defineEmits(['use']);
</script>
