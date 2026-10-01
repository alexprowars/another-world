<template>
	<div class="person-equipment">
		<div class="person-equipment-column person-equipment-column--left">
			<PersonViewSlot v-for="position in [1, 21, 2, 3]" :key="position" :position="position" :item="slots['slot_' + position] || null" />
			<div class="person-equipment-rings">
				<PersonViewSlot v-for="position in [6, 7, 8]" :key="position" :position="position" :item="slots['slot_' + position] || null" />
			</div>
			<PersonViewSlot v-for="position in [4, 9]" :key="position" :position="position" :item="slots['slot_' + position] || null" />
		</div>
		<div class="person-view-center">
			<Link v-if="editableAvatar" href="/avatar">
				<img class="person-view-avatar" :src="avatar" width="120" height="220" :alt="name" v-tooltip="name" />
			</Link>
			<img v-else class="person-view-avatar" :src="avatar" width="120" height="220" :alt="name" />
			<div v-if="showScrolls" class="person-view-scrolls">
				<PersonViewSlot v-for="position in [17, 18]" :key="position" :position="position" :item="slots['slot_' + position] || null" />
			</div>
		</div>
		<div class="person-equipment-column person-equipment-column--right">
			<PersonViewSlot v-for="position in [14, 15, 5]" :key="position" :position="position" :item="slots['slot_' + position] || null" />
			<div class="person-equipment-rings">
				<PersonViewSlot v-for="position in [10, 11, 12]" :key="position" :position="position" :item="slots['slot_' + position] || null" />
			</div>
			<PersonViewSlot v-for="position in [22, 13]" :key="position" :position="position" :item="slots['slot_' + position] || null" />
		</div>
	</div>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import PersonViewSlot from './PersonViewSlot.vue';

	defineProps({
		slots: { type: Object, required: true },
		avatar: { type: String, required: true },
		name: { type: String, required: true },
		editableAvatar: { type: Boolean, default: false },
		showScrolls: { type: Boolean, default: true },
	});
</script>
