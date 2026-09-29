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
		<table
			class="person_slots battle-fighter__equipment"
			border="0"
			cellspacing="0"
			cellpadding="0"
		>
			<tbody>
				<tr>
					<td width="60" valign="top" class="left">
						<PersonViewSlot :position="1" :item="fighter.items.slot_1 || null" />
						<PersonViewSlot :position="21" :item="fighter.items.slot_21 || null" />
						<PersonViewSlot :position="2" :item="fighter.items.slot_2 || null" />
						<PersonViewSlot :position="3" :item="fighter.items.slot_3 || null" />
						<PersonViewSlot :position="4" :item="fighter.items.slot_4 || null" />
						<PersonViewSlot :position="9" :item="fighter.items.slot_9 || null" />
					</td>
					<td width="120" align="center" valign="top">
						<img :src="fighter.avatar" width="120" height="240" :alt="fighter.name" />
						<div class="battle-fighter__slot-spacer"></div>
						<template v-if="current">
							<PersonViewSlot :position="17" :item="fighter.items.slot_17 || null" />
							<PersonViewSlot :position="18" :item="fighter.items.slot_18 || null" />
						</template>
					</td>
					<td width="60" valign="top" class="right">
						<PersonViewSlot :position="14" :item="fighter.items.slot_14 || null" />
						<PersonViewSlot :position="15" :item="fighter.items.slot_15 || null" />
						<PersonViewSlot :position="5" :item="fighter.items.slot_5 || null" />
						<PersonViewSlot :position="10" :item="fighter.items.slot_10 || null" />
						<PersonViewSlot :position="11" :item="fighter.items.slot_11 || null" />
						<PersonViewSlot :position="12" :item="fighter.items.slot_12 || null" />
						<PersonViewSlot :position="22" :item="fighter.items.slot_22 || null" />
						<PersonViewSlot :position="13" :item="fighter.items.slot_13 || null" />
					</td>
				</tr>
			</tbody>
		</table>
	</section>
</template>

<script setup>
	import PersonViewSlot from '~/components/PersonViewSlot.vue';
	import HpLine from '~/components/Person/HpLine.vue';
	import Name from '~/components/Person/Name.vue';

	const props = defineProps({
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
