<template>
	<table class="tmain personBlock">
		<tr>
			<td colspan="2" style="width:245px;">
				<div class="personName">
					<div v-html="info"></div>
				</div>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div>
					<div class="dlfr">
						<table id="slotable">
							<tr>
								<td>
									<div class="bdg stbox">
										<div id="life" class="g_line" :style="{ width: getPercent(person.hp_now, person.hp_max) + '%' }">
											<img src="/assets/images/main/empty.gif" width="1" height="10" alt="">
										</div>
									</div>
								</td>
								<td align="right" class="fntc">
									<span id="text_life">{{ person.hp_now }}</span>
								</td>
								<td class="intf">|</td>
								<td class="minf">{{ person.hp_max }}</td>
							</tr>
							<tr>
								<td>
									<div class="bdg stbox">
										<div id="mana" class="b_line" :style="{ width: getPercent(person.energy_now, person.energy_max) + '%' }">
											<img src="/assets/images/main/empty.gif" width="1" height="10" alt="">
										</div>
									</div>
								</td>
								<td align="right" class="fntc">
									<span id="text_mana">{{ person.energy_now }}</span>
								</td>
								<td class="intf">|</td>
								<td class="minf">{{ person.energy_max }}</td>
							</tr>
							<tr>
								<td>
									<div class="bdg stbox">
										<div id="ustal" class="h_line" :style="{ width: getPercent(person.ustal_now, person.ustal_max) + '%' }">
											<img src="/assets/images/main/empty.gif" width="1" height="10" alt="">
										</div>
									</div>
								</td>
								<td align="right" class="fntc">
									{{ person.ustal_now }}
								</td>
								<td class="intf">|</td>
								<td class="minf">{{ person.ustal_max }}</td>
							</tr>
						</table>
					</div>
					<div>
						<table class="person_slots" style="border:solid #e1d0b0 1.5pt;" bgcolor=bfbfbf>
							<tr>
								<td valign="top" class="left">
									<PersonViewSlot :position="1"/>
									<PersonViewSlot :position="21"/>
									<PersonViewSlot :position="2"/>
									<PersonViewSlot :position="3"/>
									<PersonViewSlot :position="4"/>
									<PersonViewSlot :position="9"/>
									<PersonViewSlot :position="6"/>
									<PersonViewSlot :position="7"/>
									<PersonViewSlot :position="8"/>
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

	function getPercent(current, max) {
		if (max <= 0) {
			return 0;
		}

		return Math.min(100, Math.max(0, (current / max) * 100));
	}

	const info = computed(() => {
		var result = '';

		if (props.person.rank > 0) {
			var hint_rank;

			if (props.person.rank === 0) {
				hint_rank = 'Смертные';
			} else if ((props.person.rank >= 10 && props.person.rank <= 14) || props.person.rank === 99) {
				hint_rank = 'Орден Инквизиции';
			} else if (props.person.rank === 20) {
				hint_rank = 'Тьма';
			} else if (props.person.rank === 30) {
				hint_rank = 'Дилер';
			} else if (props.person.rank === 31) {
				hint_rank = 'Наставник';
			} else if (props.person.rank === 60) {
				hint_rank = 'Бот';
			} else if (props.person.rank === 100) {
				hint_rank = 'Божество';
			}

			result += '<img src="/images/rank/' + props.person.rank + '.gif" height="15" alt="' + hint_rank + '">';
		}

		//if (klan != '' && klan != '0') {
		//	result += '<img src="/images/tribe/' + klan + '.gif" height="15" alt="Клан ' + klan + '">';
		//}

		result += '<a href="/info?id=' + props.person.id + '" target=_blank>' + props.person.name + '</a> [' + props.person.level + ']';

		return result;
	});
</script>