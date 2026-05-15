<template>
	<table class="table np mx-2">
		<tr>
			<td width="245" valign=top>
				<PersonView :person="user"/>
			</td>
			<td width="200" valign="top" style="padding:0 10px;">
				<table class="parameters" style="width:100%;border:solid #e1d0b0 1.5pt;" bgcolor=efdcb8>
					<tbody>
					<tr>
						<td class="tc_b">Уровень:</td>
						<td class="tc_b text-nowrap" align="right">{{ user.level }} [{{ user.level_up.up || 0 }}]</td>
					</tr>
					<tr>
						<td class="tc_b" width="30%">Опыт:</td>
						<td class="tc_b text-nowrap" align="right" width="70%">
							{{ user.exp }}
						</td>
					</tr>
					<tr>
						<td class="tc_b" width="30%">До уровня:</td>
						<td class="tc_b text-nowrap" align="right" width="70%" :title="'Осталось ' + (user.level_up.exp || 0) - user.experience + ' очков опыта'">
							{{ user.level_up.exp || 0 }}
						</td>
					</tr>
					<tr v-if="user.profession">
						<td class="tc_b">Профессия:</td>
						<td class="tc_b" align="right">
							<b>{{ user.profession }}</b>
						</td>
					</tr>
					<tr v-if="user.tribe">
						<td class="tc_b">Клан:</td>
						<td class="tc_b" align="right">
							<small>{{ user.tribe.name }}</small>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<hr/>
						</td>
					</tr>
					<tr>
						<td class="tc_r">Золото:</td>
						<td class="tc_r" align="right">{{ user.moneys }}</td>
					</tr>
					<tr>
						<td class="tc_r">Платина:</td>
						<td class="tc_r" align="right">{{ user.credits }}</td>
					</tr>
					<tr>
						<td colspan="2">
							<hr/>
						</td>
					</tr>
					<tr v-for="stat in ['strength', 'dexterity', 'agility', 'vitality', 'magic', 'intelligence', 'battery']">
						<td class="tc_dbl">{{ $t('stats.' + stat) }}</td>
						<td align="right">
							<a href="#" style="font-size:11px;color:#E03504;" class="tooltip text" data-content="<table width=120><tr><td width=50% align=left><b>Сила:</b></td><td width=50% align=right> parse[code] </td></tr><tr><td width=50% align=left><b>Своя:</b></td><td width=50% align=right> parse['~'~code] </td></tr><tr><td width=50% align=left><b>Эффекты:</b></td><td width=50% align=right>{ $user->getAttribute($stat) } - parse['~'~code] </td></tr></table>">
								{{ user[stat] || 0 }}
							</a>
						</td>
					</tr>
					<tr v-if="user.updates">
						<td colspan="2" height="25" align="center">
							<Link href="/person/updates">
								<small><span style="color:red;">Свободные статы!</span></small>
							</Link>
						</td>
					</tr>
					<template v-if="user.otravl">
						<tr>
							<td colspan="2"><hr/></td>
						</tr>
						<tr>
							<td>Отравление</td>
							<td align="right">
								<span color="{% if parse['otravl'] < 25 %}green{% elseif parse['otravl'] < 50 %}yellow{% else %}red{% endif %}">
									{{ user.otravl }}%
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
							<small>{{ user.damage_min }} - {{ user.damage_max }}</small>
						</td>
					</tr>
					<tr>
						<td>Маг.урон</td>
						<td align="right" class="text-nowrap">
							<small>{{ user.magic_min }} - {{ user.magic_max }}</small>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">Броня:</td>
					</tr>
					<tr>
						<td colspan="2" align="center" class="text-nowrap">
							<small>
								<b title="Броня головы">{{ user.armor1 }}</b>/<b title="Броня груди">{{ user.armor2 }}</b>/<b title="Броня живота">{{ user.armor3 }}</b>/<b title="Броня пояса">{{ user.armor4 }}</b>/<b title="Броня ног">{{ user.armor5 }}</b>
							</small>
						</td>
					</tr>
					<tr>
						<td width="40">Крит:</td>
						<td align="right">
							<small><b>{{ user.krit }}</b></small>
						</td>
					</tr>
					<tr>
						<td width="40">Мощн.крита:</td>
						<td align="right">
							<small><b>{{ user.mkrit }}</b></small>
						</td>
					</tr>
					<tr>
						<td width="40">Антикрит:</td>
						<td align="right">
							<small><b>{{ user.unkrit }}</b></small>
						</td>
					</tr>
					<tr>
						<td width="40">Уворот:</td>
						<td align="right">
							<small><b>{{ user.uv }}</b></small>
						</td>
					</tr>
					<tr>
						<td width="40">Антиуворот:</td>
						<td align="right">
							<small><b>{{ user.unuv }}</b></small>
						</td>
					</tr>
					<tr>
						<td width="40">Проб.блока:</td>
						<td align="right">
							<small><b>{{ user.pblock }}</b></small>
						</td>
					</tr>
					<tr>
						<td width="40">Мощн.блока:</td>
						<td align="right">
							<small><b>{{ user.mblock }}</b></small>
						</td>
					</tr>
					<tr>
						<td width="40">Проб.брони:</td>
						<td align="right">
							<small><b>{{ user.pbr }}</b></small>
						</td>
					</tr>
					<tr>
						<td width="40">Крепк.брони:</td>
						<td align="right">
							<small><b>{{ user.kbr }}</b></small>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
			<td valign="top" class="w-full">
				<div v-if="page.message" class="text-center">
					<span style="color:red"><b v-html="page.message"></b></span>
				</div>
				<div class="personMenu">
					<table>
						<tbody>
						<tr>
							<td valign="top" class="tm_p"><Link href="/person/inventory" class="tm">Рюкзак</Link></td>
							<td class="delem"></td>
							<td valign="top" class="tm_p"><Link href="/person/settings" class="tm">Настройки</Link></td>
							<td class="delem"></td>
							<td valign="top" class="tm_p"><Link href="/person/updates" class="tm">Умения</Link></td>
							<td class="delem"></td>
							<td valign="top" class="tm_p"><Link href="/person/abilities" class="tm">Приёмы</Link></td>
							<td class="delem"></td>
							<td valign="top" class="tm_p"><Link href="/person/friends" class="tm">Друзья</Link></td>
							<td class="delem"></td>
							<td valign="top" class="tm_p"><Link href="/person/anketa" class="tm">Анкета</Link></td>
						</tr>
						</tbody>
					</table>
				</div>
				<slot/>
			</td>
		</tr>
	</table>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import PersonView from '../components/PersonView.vue';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';

	defineProps({
		page: Object,
	});

	const state = useState();
	const user = computed(() => state.user);
</script>