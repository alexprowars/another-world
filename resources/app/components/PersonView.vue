<template>
	<table class="tmain personBlock">
		<tr>
			<td colspan="2" style="width:245px;">
				<Name :player="person"/>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div>
					<div class="dlfr">
						<HpLine :current="person.hp_now" :max="person.hp_max" color="g_line" v-tooltip="'Здоровье'"/>
						<HpLine :current="person.energy_now" :max="person.energy_max" color="b_line" v-tooltip="'Мана'"/>
						<HpLine v-if="person.ustal_max" :current="person.ustal_now" :max="person.ustal_max" color="h_line"/>
					</div>
					<div>
						<table class="person_slots" style="border:solid #e1d0b0 1.5pt;" bgcolor=bfbfbf>
							<tr>
								<td valign="top" class="left">
									<PersonViewSlot :position="1" :item="person.slots.slot_1 || null"/>
									<PersonViewSlot :position="21" :item="person.slots.slot_21 || null"/>
									<PersonViewSlot :position="2" :item="person.slots.slot_2 || null"/>
									<PersonViewSlot :position="3" :item="person.slots.slot_3 || null"/>
									<PersonViewSlot :position="4" :item="person.slots.slot_4 || null"/>
									<PersonViewSlot :position="9" :item="person.slots.slot_9 || null"/>
									<!--<PersonViewSlot :position="6" :item="person.slots.slot_6 || null"/>
									<PersonViewSlot :position="7" :item="person.slots.slot_7 || null"/>
									<PersonViewSlot :position="8" :item="person.slots.slot_8 || null"/>-->
								</td>
								<td width="120" valign="top">
									<Link href="/avatar">
										<img :src="person.avatar" width="120" height="220" :alt="person.name" v-tooltip="person.name">
									</Link>
									<div style="height:20px;"></div>
									<div class="text-xs-center flex justify-center gap-2">
										<PersonViewSlot :position="17" :item="person.slots.slot_17 || null"/>
										<PersonViewSlot :position="18" :item="person.slots.slot_18 || null"/>
									</div>
								</td>
								<td valign="top" class="right">
									<PersonViewSlot :position="14" :item="person.slots.slot_14 || null"/>
									<PersonViewSlot :position="15" :item="person.slots.slot_15 || null"/>
									<PersonViewSlot :position="5" :item="person.slots.slot_5 || null"/>
									<PersonViewSlot :position="10" :item="person.slots.slot_10 || null"/>
									<PersonViewSlot :position="11" :item="person.slots.slot_11 || null"/>
									<PersonViewSlot :position="12" :item="person.slots.slot_12 || null"/>
									<PersonViewSlot :position="22" :item="person.slots.slot_22 || null"/>
									<PersonViewSlot :position="13" :item="person.slots.slot_13 || null"/>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>
</template>

<script setup>
	import PersonViewSlot from './PersonViewSlot.vue';
	import Name from '~/components/Person/Name.vue';
	import HpLine from '~/components/Person/HpLine.vue';
	import { Link } from '@inertiajs/vue3';

	const props = defineProps({
		person: {
			type: Object,
		}
	});
</script>