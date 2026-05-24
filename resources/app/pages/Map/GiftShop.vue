<template>
	<ContentBlock title="Сувенирная Лавка">
		<div class="flex flex-wrap">
			<div class="w-9/12 flex-none xl:w-10/12 pr-4">
				<div v-if="page.section === 4">
					<div v-if="page.items.length" class="shop-items grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
						<div v-for="item in page.items">
							<GiftItem :item="item" :key="item.id" @sell="onGiftItem(item)"/>
						</div>
					</div>
					<div v-else>
						У вас нет подарков
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
					<Link href="/map"><img src="/assets/images/images/refresh.gif" alt="Обновить"></Link>
					<Link href="/map/change/13"><img src="/assets/images/images/back.gif" alt="Вернуться"></Link>
				</div>
				<div align="center" class="shopotdels">
					<table>
						<tr>
							<td colspan="2" align="center"><b>Отделы магазина</b></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="?section=1">Открытки</Link></td>
							<td align="center"><Link href="?section=2">Цветы</Link></td>
						</tr>
						<tr>
							<td colspan="2" align="center"><Link href="?section=3">Подарки</Link></td>
						</tr>
						<tr>
							<td width="100%" colspan="2" align="center">
								<Link href="?section=4">Подарить</Link>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import ContentBlock from '~/components/ContentBlock.vue';
	import { Link, router } from '@inertiajs/vue3';
	import Item from '~/components/City/Shop/Item.vue';
	import SellItem from '~/components/City/Shop/SellItem.vue';
	import GiftItem from '~/components/City/Shop/GiftItem.vue';

	defineProps({
		page: Object,
	});

	function onGiftItem(item) {
		router.post('', {
			gift: item.id
		});
	}

	function onBuyItem(item) {
		router.post('', {
			buy: item.id,
		});
	}
</script>