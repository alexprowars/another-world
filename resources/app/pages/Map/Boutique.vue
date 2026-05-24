<template>
	<ContentBlock title="Бутик">
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
					<Link href="/map?section=100"><img src="/assets/images/images/shop_sale.gif" alt="Продать предметы"></Link>
					<Link href="/map"><img src="/assets/images/images/refresh.gif" alt="Обновить"></Link>
					<Link href="/map/change/29"><img src="/assets/images/images/back.gif" alt="Вернуться"></Link>
				</div>
				<div align="center" class="shopotdels">
					<table>
						<tr>
							<td width="100%" colspan="2" align="center"><b>Оружие</b></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="?section=1">Ножи</Link></td>
							<td width="50%" align="center"><Link href="?section=2">Мечи</Link></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="?section=3">Топоры</Link></td>
							<td width="50%" align="center"><Link href="?section=4">Дубины</Link></td>
						</tr>
						<tr>
							<td width="100%" colspan="2" align="center"><Link href="?section=5">Луки, Арбалеты</Link></td>
						</tr>
					</table>
				</div>
				<div align="center" class="shopotdels">
					<table>
						<tr>
							<td width="100%" colspan="2" align="center"><b>Амуниция</b></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="?section=6">Шлемы</Link></td>
							<td width="50%" align="center"><Link href="?section=7">Рубахи</Link></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="?section=8">Тяжёлая броня</Link></td>
							<td width="50%" align="center"><Link href="?section=16">Браслеты</Link></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="?section=16">Нарукавники</Link></td>
							<td width="50%" align="center"><Link href="?section=10">Щиты</Link></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="?section=11">Пояса</Link></td>
							<td width="50%" align="center"><Link href="?section=12">Обувь</Link></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="?section=9">Перчатки</Link></td>
							<td width="50%" align="center"><Link href="?section=17">Штаны</Link></td>
						</tr>
					</table>
				</div>
				<div align="center" class="shopotdels">
					<table>
						<tr>
							<td width="100%" colspan="2" align="center"><b>Ювелирные украшения</b></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="?section=13">Ожерелья</Link></td>
							<td width="50%" align="center"><Link href="?section=14">Кольца</Link></td>
						</tr>
						<tr>
							<td width="100%" colspan="2" align="center"><Link href="?section=15">Серьги</Link></td>
						</tr>
					</table>
				</div>
				<div align="center" class="shopotdels">
					<table>
						<tr>
							<td colspan="2" align="center"><b>Магические предметы</b></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="?section=18">Свитки</Link></td>
							<td width="50%" align="center"><Link href="?section=19">Зелья</Link></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import SellItem from '~/components/City/Shop/SellItem.vue';
	import { Link, router } from '@inertiajs/vue3';
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