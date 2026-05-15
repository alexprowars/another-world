<template>
	<div class="inventory-item">
		<table class="inventory-item__card" width="100%">
			<tr>
				<td align="center" width="150">
					<Popper placement="right-start">
						<img :src="'/assets/images/items/' + item.type + '/' + item.code + '.gif'" :alt="item.title" class="tooltip2 script">

						<template #content>
							<table width="250">
								<tr>
									<td style="font-size:10px;" width="22" height="25">
										<img src="/assets/images/header/stm1-tl.gif" width="22" height="25" alt="" border="0"><br>
									</td>
									<td style="font-size:10px;background: url('/assets/images/header/stm1-t.gif') repeat-x top" class="tabcata" align="center">
										<b style="font-size:10px;color:#ff0000">{{ item.title }}</b>
									</td>
									<td style="font-size:10px;" width="22">
										<img src="/assets/images/header/stm1-tr.gif" width="22" height="25" alt="" border="0"><br>
									</td>
								</tr>
								<tr>
									<td style="font-size:10px;background: url('/assets/images/header/stm1-l.gif') repeat-y top left"></td>
									<td style="font-size:10px;background: url('/assets/images/header/sand3.gif')">
										<table v-if="requirements.length" class="w-full text-xs">
											<tr>
												<td colspan="2"><b>Минимальные требования:</b></td>
											</tr>
											<tr v-for="(row, index) in requirements" :key="row.key" :style="zebraStyle(index)">
												<td>{{ row.label }}</td>
												<td align="right" :style="row.failed ? 'color: #d00000;' : ''">
													<b>{{ row.value }}</b>
												</td>
											</tr>
										</table>

										<table v-if="bonuses.length" class=" w-full text-xs mt-1">
											<tr>
												<td colspan="2"><b>Действие предмета:</b></td>
											</tr>
											<tr v-for="(row, index) in bonuses" :key="row.key" :style="zebraStyle(index)">
												<td>{{ row.label }}</td>
												<td align="right">
													<b>{{ row.value }}</b>
												</td>
											</tr>
										</table>

										<div v-if="item.magic" class="w-full my-1 p-1 border border-[#d8ad83]">
											<b>Встроенная магия:</b>
											<div v-html="item.magic"></div>
										</div>

										<div v-if="item.grav" class="w-full my-1 p-1 border border-[#d8ad83]">
											<b>Выгравирована надпись:</b>
											<div v-html="item.grav"></div>
										</div>

										<div v-if="item.about" class="w-full my-1 p-1 border border-[#d8ad83]">
											<b>Дополнительная информация:</b>
											<div v-html="item.about"></div>
										</div>
									</td>
									<td style="font-size:10px;background: url('/assets/images/header/stm1-r.gif') repeat-y top right"></td>
								</tr>
								<tr>
									<td style="font-size:1px;" height="5">
										<img src="/assets/images/header/stm1-bl.gif" width="22" height="5" alt="" border="0"><br>
									</td>
									<td style="font-size:1px;background: url('/assets/images/header/stm1-b.gif') repeat-x bottom"></td>
									<td style="font-size:1px;">
										<img src="/assets/images/header/stm1-br.gif" width="22" height="5" alt="" border="0"><br>
									</td>
								</tr>
							</table>
						</template>
					</Popper>
				</td>
				<td valign="top" style="padding:5px">
					<table width="100%" class="table sm">
						<tr>
							<td>
								<a href="#" style="color:#666666" class="b" @click.prevent>{{ item.title }}</a>
							</td>
							<td align="center" width="130">
								<a href="" class="butt2" @click.prevent="confirmWear">надеть</a>
							</td>
						</tr>
						<tr>
							<td title="Тип предмета">
								<img src="/assets/images/images/tbl-shp_item-icon.gif" width="11" height="10" align="absmiddle" alt="">
								{{ $t('weapon.' + item.type) }}
							</td>
							<td colspan="2" align="center" title="Требуемый уровень" nowrap>
								<img src="/assets/images/images/tbl-shp_level-icon.gif" width="11" height="10" align="absmiddle" alt="">
								Уровень <b class="red">{{ item.requirements.level || 0 }}</b>
							</td>
						</tr>
						<tr>
							<td title="Прочность предмета">
								<img src="/assets/images/images/tbl-shp_item-iznos.gif" width="11" height="10" align="absmiddle" alt="">
								<font color="red">{{ item.wearout }}</font>/{{ item.wearout_max }}
							</td>
							<td v-if="canUse" align="center">
								<a href="" class="butt2" @click.prevent="useItem">использовать</a>
							</td>
						</tr>
						<tr>
							<td class="b grnn" title="Цена">
								<span title="Кредиты">
									<img src="/assets/images/images/m_game3.gif" border="0" width="11" height="11" align="absmiddle" alt="">
								</span>&nbsp;{{ item.price }}
							</td>
							<td align="center">
								<a href="" class="butt2" @click.prevent="confirmDrop">выбросить</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</template>

<script setup>
	import { computed } from 'vue';
	import { openConfirmModal } from '~/composables/useModals.js';
	import Popper from '~/components/Popper.vue';
	import { useI18n } from 'vue-i18n';

	const props = defineProps({
		item: {
			type: Object,
			required: true,
		},
		player: {
			type: Object,
			default: () => ({}),
		},
	});

	const { t } = useI18n();
	const emit = defineEmits(['wear', 'drop', 'use']);
	const canUse = computed(() => [12, 13, 14].includes(props.item.type));

	const requirements = computed(() => [
		requirement('profession', 'Профессия', (value) => t('profession.' + value)),
		requirement('level', 'Уровень'),
		requirement('strength', t('stats.strength')),
		requirement('dexterity', t('stats.dexterity')),
		requirement('agility', t('stats.agility')),
		requirement('vitality', t('stats.vitality')),
		requirement('intelligence', t('stats.intelligence')),
	].filter(Boolean));

	const bonuses = computed(() => [
		bonus('poison', 'Отравление'),
		bonus('hp', 'Уровень жизни'),
		bonus('energy', 'Уровень энергии'),
		damageBonus(),
		bonus('strength', 'Сила'),
		bonus('dexterity', 'Удача'),
		bonus('agility', 'Ловкость'),
		bonus('vitality', 'Выносливость'),
		bonus('intelligence', 'Разум'),
		bonus('armor1', 'Броня головы'),
		bonus('armor2', 'Броня корпуса'),
		bonus('armor3', 'Броня живота'),
		bonus('armor4', 'Броня пояса'),
		bonus('armor5', 'Броня ног'),
		bonus('krit', 'Крит'),
		bonus('unkrit', 'Антикрит'),
		bonus('uv', 'Уворот'),
		bonus('unuv', 'Антиуворот'),
		bonus('mkrit', 'Мощность крита'),
		bonus('pblock', 'Пробой блока'),
		bonus('mblock', 'Мощность блока'),
		bonus('pbr', 'Пробой брони'),
		bonus('kbr', 'Крепость брони'),
		bonus('metk', 'Меткость'),
	].filter(Boolean));

	function requirement(key, label, format = null) {
		const required = props.item.requirements[key];

		if (!required) {
			return null;
		}

		const current = props.player[key] ?? 0;
		const failed = key === 'profession' ? required !== current : current > 0 && required > current;

		return {
			key,
			label,
			value: format ? format(required) : required,
			failed,
		};
	}

	function bonus(key, label) {
		const value = props.item[key];

		if (!value) {
			return null;
		}

		return {
			key,
			label,
			value: signed(value),
		};
	}

	function damageBonus() {
		if (!props.item.min && !props.item.max) {
			return null;
		}

		return {
			key: 'damage',
			label: 'Урон',
			value: `min: ${signed(props.item.min)}...max: ${signed(props.item.max)}`,
		};
	}

	function signed(value) {
		return value > 0 ? `+${value}` : String(value);
	}

	function zebraStyle(index) {
		return index % 2 === 0 ? 'background-color: #F4BB8A;' : '';
	}

	function confirmWear() {
		openConfirmModal(
			'Рюкзак',
			'Вы действительно хотите надеть эту вещь?',
			[{ title: 'Нет' }, {
				title: 'Да',
				handler() {
					emit('wear', props.item);
				},
			},
		]);
	}

	function confirmDrop() {
		openConfirmModal(
			'Рюкзак',
			`Вы действительно хотите выбросить ${props.item.title}?`,
			[{ title: 'Нет' }, {
				title: 'Да',
				handler() {
					emit('drop', props.item);
				},
			},
		]);
	}

	function useItem() {
		emit('use', props.item);
	}
</script>