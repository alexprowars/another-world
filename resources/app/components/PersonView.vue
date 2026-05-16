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
						<HpLine :current="person.hp_now" :max="person.hp_max" color="g_line"/>
						<HpLine :current="person.energy_now" :max="person.energy_max" color="b_line"/>
						<HpLine :current="person.ustal_now" :max="person.ustal_max" color="h_line"/>
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
									<a href="/avatar">
										<img :src="avatar" width="120" height="220" :alt="person.nickname">
									</a>
									<div style="height:20px;"></div>
									<div class="text-xs-center flex justify-center gap-2">
										<PersonViewSlot :position="17"/>
										<PersonViewSlot :position="18"/>
									</div>
								</td>
								<td valign="top" class="right">
									<PersonViewSlot :position="14"/>
									<PersonViewSlot :position="15"/>
									<PersonViewSlot :position="5"/>
									<PersonViewSlot :position="10"/>
									<PersonViewSlot :position="11"/>
									<PersonViewSlot :position="12"/>
									<PersonViewSlot :position="22"/>
									<PersonViewSlot :position="13"/>
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
	import { computed } from 'vue';
	import Name from '~/components/Person/Name.vue';
	import HpLine from '~/components/Person/HpLine.vue';

	const props = defineProps({
		person: {
			type: Object,
		}
	});

	const avatar = computed(() => {
		if (props.person.avatar) {
			return '/assets/images/avatar/obraz/' + props.person.avatar + '.png';
		}

		return '/assets/images/avatar/1/' + (props.person.gender === 'F' ? '2' : '1') + '.png';
	});
</script>