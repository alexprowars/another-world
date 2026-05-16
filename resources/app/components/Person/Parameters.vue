<template>
	<table class="parameters" width="100%" style="width:100%;border:solid #e1d0b0 1.5pt;" bgcolor=efdcb8>
		<tbody>
		<tr>
			<td class="tc_b">Уровень:</td>
			<td class="tc_b text-nowrap" align="right">{{ player.level }} [{{ player.level_up.up || 0 }}]</td>
		</tr>
		<tr>
			<td class="tc_b" width="30%">Опыт:</td>
			<td class="tc_b text-nowrap" align="right" width="70%">
				{{ player.exp }}
			</td>
		</tr>
		<tr>
			<td class="tc_b" width="30%">До уровня:</td>
			<td class="tc_b text-nowrap" align="right" width="70%" :title="'Осталось ' + (player.level_up.exp || 0) - player.experience + ' очков опыта'">
				{{ player.level_up.exp || 0 }}
			</td>
		</tr>
		<tr v-if="player.profession">
			<td class="tc_b">Профессия:</td>
			<td class="tc_b" align="right">
				<b>{{ player.profession }}</b>
			</td>
		</tr>
		<tr v-if="player.tribe">
			<td class="tc_b">Клан:</td>
			<td class="tc_b" align="right">
				<small>{{ player.tribe.name }}</small>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<hr/>
			</td>
		</tr>
		<tr>
			<td class="tc_r">Золото:</td>
			<td class="tc_r" align="right">{{ player.moneys }}</td>
		</tr>
		<tr>
			<td class="tc_r">Платина:</td>
			<td class="tc_r" align="right">{{ player.credits }}</td>
		</tr>
		<tr>
			<td colspan="2">
				<hr/>
			</td>
		</tr>
		<tr v-for="stat in ['strength', 'dexterity', 'agility', 'vitality', 'magic', 'intelligence', 'battery']">
			<td class="tc_dbl">{{ $t('stats.' + stat) }}</td>
			<td align="right">
				<Popper>
					<a href="#" style="font-size:11px;color:#E03504;" class="text">
						{{ player[stat] }}
					</a>

					<template #content>
						<div class="w-[120px] text-xs">
							<div class="flex justify-between">
								<div><b>{{ $t('stats.' + stat) }}:</b></div>
								<div>{{ player[stat] }}</div>
							</div>
							<div class="flex justify-between">
								<div><b>Своя:</b></div>
								<div>{{ player['s_' + stat] }}</div>
							</div>
							<div class="flex justify-between">
								<div><b>Эффекты:</b></div>
								<div>{{ player[stat] - player['s_' + stat] }}</div>
							</div>
						</div>
					</template>
				</Popper>
			</td>
		</tr>
		<tr v-if="player.updates">
			<td colspan="2" height="25" align="center">
				<Link href="/person/updates">
					<small><span style="color:red;">Свободные статы!</span></small>
				</Link>
			</td>
		</tr>
		<template v-if="player.otravl">
			<tr>
				<td colspan="2"><hr/></td>
			</tr>
			<tr>
				<td>Отравление</td>
				<td align="right">
					<span color="{% if parse['otravl'] < 25 %}green{% elseif parse['otravl'] < 50 %}yellow{% else %}red{% endif %}">
						{{ player.otravl }}%
					</span>
				</td>
			</tr>
		</template>
		<tr>
			<td colspan="2">
				<hr/>
			</td>
		</tr>
		<tr>
			<td>Физ.урон</td>
			<td align="right" class="text-nowrap">
				<small>{{ player.damage_min }} - {{ player.damage_max }}</small>
			</td>
		</tr>
		<tr>
			<td>Маг.урон</td>
			<td align="right" class="text-nowrap">
				<small>{{ player.magic_min }} - {{ player.magic_max }}</small>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">Броня:</td>
		</tr>
		<tr>
			<td colspan="2" align="center" class="text-nowrap">
				<small>
					<b title="Броня головы">{{ player.armor1 }}</b>/<b title="Броня груди">{{ player.armor2 }}</b>/<b title="Броня живота">{{ player.armor3 }}</b>/<b title="Броня пояса">{{ player.armor4 }}</b>/<b title="Броня ног">{{ player.armor5 }}</b>
				</small>
			</td>
		</tr>
		<tr>
			<td width="40">Крит:</td>
			<td align="right">
				<small><b>{{ player.krit }}</b></small>
			</td>
		</tr>
		<tr>
			<td width="40">Мощн.крита:</td>
			<td align="right">
				<small><b>{{ player.mkrit }}</b></small>
			</td>
		</tr>
		<tr>
			<td width="40">Антикрит:</td>
			<td align="right">
				<small><b>{{ player.unkrit }}</b></small>
			</td>
		</tr>
		<tr>
			<td width="40">Уворот:</td>
			<td align="right">
				<small><b>{{ player.uv }}</b></small>
			</td>
		</tr>
		<tr>
			<td width="40">Антиуворот:</td>
			<td align="right">
				<small><b>{{ player.unuv }}</b></small>
			</td>
		</tr>
		<tr>
			<td width="40">Проб.блока:</td>
			<td align="right">
				<small><b>{{ player.pblock }}</b></small>
			</td>
		</tr>
		<tr>
			<td width="40">Мощн.блока:</td>
			<td align="right">
				<small><b>{{ player.mblock }}</b></small>
			</td>
		</tr>
		<tr>
			<td width="40">Проб.брони:</td>
			<td align="right">
				<small><b>{{ player.pbr }}</b></small>
			</td>
		</tr>
		<tr>
			<td width="40">Крепк.брони:</td>
			<td align="right">
				<small><b>{{ player.kbr }}</b></small>
			</td>
		</tr>
		</tbody>
	</table>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import Popper from '~/components/Popper.vue';

	defineProps({
		player: Object,
	})
</script>