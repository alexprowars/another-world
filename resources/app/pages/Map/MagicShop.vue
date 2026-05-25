<template>
	<ContentBlock title="Башня Мага">
		<div class="flex flex-wrap">
			<div class="w-9/12 flex-none xl:w-10/12 pr-4">
				<div v-if="page.items.length" class="shop-items grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
					<div v-for="item in page.items">
						<Item :item="item" :key="item.id" @buy="onBuyItem(item)"/>
					</div>
				</div>
				<div v-else>
					В данном отделе нет товаров.
				</div>
			</div>
			<div class="w-3/12 flex-none xl:w-2/12">
				<div class="shopnav">
					<Link href="/map"><img src="/assets/images/images/refresh.gif" alt="Обновить"></Link>
					<Link href="/map/change/10"><img src="/assets/images/images/back.gif" alt="Вернуться"></Link>
				</div>
				<div align="center" class="shopotdels">
					<table>
						<tr>
							<td width="100%" colspan="2" align="center"><u>Свитки:</u></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="/map?section=1">Восст. здоровья</Link></td>
							<td width="50%" align="center"><Link href="/map?section=2">Восст. маны</Link></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="/map?section=3">Лечение травм</Link></td>
							<td width="50%" align="center"><Link href="/map?section=4">Иммунитет</Link></td>
						</tr><tr>
							<td width="50%" align="center"><Link href="/map?section=5">Нападение</Link></td>
							<td width="50%" align="center"><Link href="/map?section=6">Боевая магия</Link></td>
						</tr>
						<tr>
							<td width="100%" colspan="2" align="center"><Link href="/map?section=7">Прочее</Link></td>
						</tr>
					</table>
				</div>
				<div align="center" class="shopotdels">
					<table>
						<tr>
							<td width="100%" colspan="2" align="center"><u>Зелья:</u></td>
						</tr>
						<tr>
							<td width="50%" align="center"><Link href="/map?section=8">Зелья</Link></td>
							<td width="50%" align="center"><Link href="/map?section=12">Ингридиенты</Link></td>
						</tr>
					</table>
				</div>
				<div align="center" class="shopotdels">
					<table>
						<tr>
							<td width="100%" colspan="2" align="center"><u>Магические предметы:</u></td>
						</tr>
						<tr>
							<td width="33%" align="center"><Link href="/map?section=9">Посохи</Link></td>
							<td width="33%" align="center"><Link href="/map?section=10">Одеяние</Link></td>
						</tr>
						<tr>
							<td width="100%" colspan="2" align="center"><Link href="/map?section=11">Маг. вещи</Link></td>
						</tr>
						<tr>
							<td width="100%" colspan="2" align="center"><Link href="/map?section=13">Руны</Link></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { Link, router } from '@inertiajs/vue3';
	import Item from '~/components/City/Shop/Item.vue';
	import ContentBlock from '~/components/ContentBlock.vue';

	defineProps({
		page: Object,
	});

	function onBuyItem(item) {
		router.post('', {
			buy: item.id,
		});
	}
</script>
