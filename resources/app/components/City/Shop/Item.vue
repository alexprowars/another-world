<template>
	<!--{% set t_price = (object.item.price * 0.9)|round(2) %}-->
	<div class="shop-item">
		<table class='table item'>
			<tr>
				<td align="center" width="40%" style="vertical-align: middle">
					<img :src="'/assets/images/items/' + item.item.type + '/' + item.item.code + '.gif'" :alt="item.item.title"><br>

					<template v-if="type === 1">
						<a href="" @click.prevent="buyItem"><b>Купить</b></a>
					</template>
					<template v-if="type === 2">
						<a href='javascript:;' onclick="present(object.shop.id)">Выгравировать надпись за 150 зол.</a><br><br>

						<a v-if="user.profession === 2" href="/map/?otdel={ otdel }&act=upgrade&id={ object.shop.id }">Увеличить урон предмета за 50 пл.<br>(мин +1 и мах +1)<br>(<i>Максимальная долговечноть -20</i>)</a>
						<font v-else color=red><b>Перековать может только Кузнец</b></font>
					</template>
					<template v-if="type === 3">
						<a href="#" onclick="confirmDialog('Подтвердите действие', 'Починить этот предмет за { t_price } зол.?', 'load(\'/map/?otdel={ otdel }&iznos={ object.shop.id }\')')"><b>Починить весь предмет</b></a><br>
						<a href="#" onclick="confirmDialog('Подтвердите действие', 'Починить за {vip } зол.?', 'load(\'/map/?otdel={ otdel }&iznos1={ object.shop.id }\')')"><b>Починить 1 ед.</b></a>
					</template>
					<template v-if="type === 4">
						<a href="#" onclick="confirmDialog('Подтвердите действие', 'Вы действительно хотите огранить &quot;{ object.item.title }&quot; ?', 'load(\'/map/?otdel={ otdel }&ogran={ object.shop.id }\')')"><b>Огранить</b></a><br>
					</template>
				</td>
				<td>
					<div class="text-xs">
						<div class="font-bold">
							{{ item.item.title }}
						</div>
						<div v-if="item.item.credits">
							Гос. цена: <b>{{ item.item.credits }}</b> пл.
						</div>
						<div v-if="item.item.price">
							Гос. цена: <b>{{ item.item.price }}</b> зол.
						</div>
						<div v-if="item.item.price_vip && type == 1 && user.vip">
							VIP. цена: <b>{{ item.item.price_vip }}</b> пл.
						</div>
						<div>
							Долговечность: <b>{{ item.item.iznos }}</b>
						</div>
						<div>
							Тип предмета: <i>{{ $t('weapon.' + item.item.type) }}</i>
						</div>
						<div v-if="item.item.mana">
							Затраты маны: <i>{{ item.item.mana }}</i>
						</div>
						<div>
							Остаток на складе: <b>{{ item.stock }}</b>
						</div>
						<div v-if="item.delivery">
							Завоз: <b>{{ item.delivery }}</b>.
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td v-if="item.item.demands" colspan="2">
					<small><b>Требования:</b><div v-html="item.item.demands"></div></small>
				</td>
			</tr>
			<tr>
				<td v-if="item.item.bonuses" colspan="2">
					<small><b>Действие предмета:</b><div v-html="item.item.bonuses"></div></small>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div v-if="item.item.magic"><small><b>Встроенная магия:</b><br>{{ item.item.magic }}</small></div>
					<div v-if="item.item.about"><small><b>Дополнительная информация:</b><br>{{ item.item.about }}</small></div>
				</td>
			</tr>
		</table>
	</div>
</template>

<script setup>
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import { openConfirmModal } from '~/composables/useModals.js';
	import { router } from '@inertiajs/vue3';

	const props = defineProps({
		item: Object,
	});

	const state = useState();
	const user = computed(() => state.user);
	const emit = defineEmits(['buy']);

	const type = 1;

	function buyItem() {
		openConfirmModal(
			'Подтвердите действие',
			'Купить предмет &quot;' + props.item.item.title + '&quot;?',
			[{
				title: 'Нет',
			}, {
				title: 'Да',
				handler() {
					emit('buy');
				}
			}]
		);
	}
</script>