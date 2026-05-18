<template>
	<ContentBlock title="Государственный Магазин">
		<div class="flex flex-wrap">
			<div class="w-9/12 flex-none xl:w-10/12 pr-4">
				<p v-if="page.message" class="message bg-red-100 text-red-700 mb-4" v-html="page.message"></p>

				<div v-if="page.section === 100">
					<div v-if="page.items.length" class="shop-items grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
						<div v-for="item in page.items">
							<SellItem :item="item" :key="item.id" @sell="onSellItem(item)"/>
						</div>
					</div>
					<div v-else>
						Нет вещей для продажи
					</div>
				</div>
				<div v-else>
					<div v-if="page.items.length" class="shop-items grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
						<div v-for="item in page.items">
							<Item :item="item" :key="item.id" @buy="onBuyItem(item)"/>
						</div>
					</div>
					<div v-else>
						В данном отделе нет товаров.
					</div>
				</div>
			</div>
			<div class="w-3/12 flex-none xl:w-2/12">
				<div class="shopnav">
					<a href="?section=100"><img src='/assets/images/images/shop_sale.gif' alt='Продать предметы'></a>
					<a :href="'?section=' + page.section"><img src='/assets/images/images/refresh.gif' alt='Обновить'></a>
					<a href="/map/change/7"><img src='/assets/images/images/back.gif' alt='Вернуться'></a>
				</div>
				<div align='center' class="shopotdels">
					<table>
						<tr>
							<td width='100%' colspan='2' align='center'><b>Оружие</b></td>
						</tr>
						<tr>
							<td width='50%' align='center'><a href='?section=1'>Ножи</a></td>
							<td width='50%' align='center'><a href='?section=2'>Мечи</a></td>
						</tr>
						<tr>
							<td width='50%' align='center'><a href='?section=3'>Топоры</a></td>
							<td width='50%' align='center'><a href='?section=4'>Дубины</a></td>
						</tr>
						<tr>
							<td width='100%' colspan='2' align='center'><a href='?section=5'>Луки, Арбалеты</a></td>
						</tr>
					</table>
				</div>
				<div align='center' class="shopotdels">
					<table>
						<tr>
							<td width='100%' colspan='2' align='center'><b>Амуниция</b></td>
						</tr>
						<tr>
							<td width='50%' align='center'><a href='?section=6'>Шлемы</a></td>
							<td width='50%' align='center'><a href='?section=7'>Рубахи</a></td>
						</tr>
						<tr>
							<td width='50%' align='center'><a href='?section=8'>Тяжёлая броня</a></td>
							<td width='50%' align='center'><a href='?section=16'>Браслеты</a></td>
						</tr>
						<tr>
							<td width='50%' align='center'><a href='?section=16'>Нарукавники</a></td>
							<td width='50%' align='center'><a href='?section=10'>Щиты</a></td>
						</tr>
						<tr>
							<td width='50%' align='center'><a href='?section=11'>Пояса</a></td>
							<td width='50%' align='center'><a href='?section=12'>Обувь</a></td>
						</tr>
						<tr>
							<td width='50%' align='center'><a href='?section=9'>Перчатки</a></td>
							<td width='50%' align='center'><a href='?section=17'>Штаны</a></td>
						</tr>
					</table>
				</div>
				<div align='center' class="shopotdels">
					<table>
						<tr>
							<td width='100%' colspan='2' align='center'><b>Ювелирные украшения</b></td>
						</tr>
						<tr>
							<td width='50%' align='center'><a href='?section=13'>Ожерелья</a></td>
							<td width='50%' align='center'><a href='?section=14'>Кольца</a></td>
						</tr>
						<tr>
							<td width='100%' colspan='2' align='center'><a href='?section=15'>Серьги</a></td>
						</tr>
					</table>
				</div>
				<div align='center' class="shopotdels">
					<table>
						<tr>
							<td width='100%' colspan='4' align='center'><b>Работа</b></td>
						</tr>
						<tr>
							<td width='50%' align='center' colspan='2'><a href='?section=20'>Инструменты</a></td>
							<td width='50%' align='center' colspan='2'><a href='?section=32'>Документы</a></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import SellItem from '~/components/City/Shop/SellItem.vue';
	import { router } from '@inertiajs/vue3';
	import Item from '~/components/City/Shop/Item.vue';
	import ContentBlock from '~/components/ContentBlock.vue';

	defineProps({
		page: Object,
	});

	function onSellItem(item) {
		router.post('', {
			sell: item.id
		});
	}

	function onBuyItem(item) {
		router.post('', {
			buy: item.id,
		});
	}
</script>