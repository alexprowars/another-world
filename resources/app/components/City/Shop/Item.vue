<template>
	<!--{% set t_price = (object.item.price * 0.9)|round(2) %}-->
	<div class="shop-item">
		<div class="flex gap-4 w-full">
			<div class="w-4/12 text-center">
				<img :src="'/assets/images/items/' + item.item.type + '/' + item.item.code + '.gif'" :alt="item.item.title"><br>

				<template v-if="type === 1">
					<a href="" @click.prevent="buyItem"><b>Купить</b></a>
				</template>
				<template v-if="type === 2">
					<a href='javascript:;' onclick="present(object.shop.id)">Выгравировать надпись за 150 зол.</a><br><br>

					<a v-if="user.profession === 2" href="/map/?otdel={ otdel }&act=upgrade&id={ object.shop.id }">Увеличить урон предмета за 50 пл.<br>(мин +1 и мах +1)<br>(<i>Максимальная долговечноть -20</i>)</a>
					<span v-else class="text-red-600"><b>Перековать может только Кузнец</b></span>
				</template>
				<template v-if="type === 3">
					<a href="#" onclick="confirmDialog('Подтвердите действие', 'Починить этот предмет за { t_price } зол.?', 'load(\'/map/?otdel={ otdel }&iznos={ object.shop.id }\')')"><b>Починить весь предмет</b></a><br>
					<a href="#" onclick="confirmDialog('Подтвердите действие', 'Починить за {vip } зол.?', 'load(\'/map/?otdel={ otdel }&iznos1={ object.shop.id }\')')"><b>Починить 1 ед.</b></a>
				</template>
				<template v-if="type === 4">
					<a href="#" onclick="confirmDialog('Подтвердите действие', 'Вы действительно хотите огранить &quot;{ object.item.title }&quot; ?', 'load(\'/map/?otdel={ otdel }&ogran={ object.shop.id }\')')"><b>Огранить</b></a><br>
				</template>
			</div>
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
				<div v-if="item.item.wearout">
					Долговечность: <b>{{ item.item.wearout }}</b>
				</div>
				<div v-if="item.item.life">
					Срок жизни: <b class="text-red-600">{{ item.item.life / 86400 }} дн.</b>
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
					Завоз: <b>{{ item.delivery }}</b>
				</div>
			</div>
		</div>
		<div v-if="Object.keys(item.item['requirements']).length > 0" class="text-xs mt-2">
			<div class="font-bold">Минимальные требования:</div>
			<div v-if="item.item['requirements']['level']" :class="{ 'text-red-600': user.level < item.item['requirements']['level'] }">
				Уровень: {{ item.item['requirements']['level'] }}
			</div>
			<div v-if="item.item['requirements']['profession']" :class="{ 'text-red-600': user.profession !== item.item['requirements']['profession'] }">
				Профессия: {{ $t('profession.' + item.item['requirements']['profession']) }}
			</div>
			<template v-for="stat in ['strength', 'dexterity', 'agility', 'vitality', 'magic', 'intelligence', 'battery']">
				<div v-if="item.item['requirements'][stat]" :class="{ 'text-red-600': user[stat] !== item.item['requirements'][stat] }">
					{{ $t('stats.' + stat) }}: {{ item.item['requirements'][stat] }}
				</div>
			</template>
		</div>
		<div v-if="item.item['bonuses'].length" class="text-xs mt-2">
			<div class="font-bold">Действие предмета:</div>

			<div v-for="(value, stat) in item.item['bonuses']">
				{{ $t('stats.' + stat) }}: {{ value > 0 ? '+' : '' }}{{ value }}
			</div>
		</div>
		<div class="text-xs mt-2">
			<div v-if="item.item.magic">
				<div class="font-bold">Встроенная магия:</div>
				{{ item.item.magic }}
			</div>
			<div v-if="item.item.about">
				<div class="font-bold">Дополнительная информация:</div>
				<div v-html="item.item.about"></div>
			</div>
		</div>
	</div>
</template>

<script setup>
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import { openConfirmModal } from '~/composables/useModals.js';
	import { router } from '@inertiajs/vue3';
	import { useI18n } from 'vue-i18n';

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