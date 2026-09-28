<template>
	<Storefront :page="page" title="Государственный магазин" back-href="/map/change/7" :departments="departments" />
</template>

<script setup>
	import Storefront from '~/components/City/Shop/Storefront.vue';

	defineProps({
		page: { type: Object, required: true },
	});

	const departments = [
		{
			title: 'Оружие',
			icon: 'swords',
			items: [
				{ id: 1, title: 'Ножи' },
				{ id: 2, title: 'Мечи' },
				{ id: 3, title: 'Топоры' },
				{ id: 4, title: 'Дубины' },
				{ id: 5, title: 'Луки и арбалеты' },
			],
		},
		{
			title: 'Амуниция',
			icon: 'armor',
			items: [
				{ id: 6, title: 'Шлемы' },
				{ id: 7, title: 'Рубахи' },
				{ id: 8, title: 'Тяжёлая броня' },
				{ id: 16, title: 'Браслеты и нарукавники' },
				{ id: 10, title: 'Щиты' },
				{ id: 11, title: 'Пояса' },
				{ id: 12, title: 'Обувь' },
				{ id: 9, title: 'Перчатки' },
				{ id: 17, title: 'Штаны' },
			],
		},
		{
			title: 'Украшения',
			icon: 'gem',
			items: [
				{ id: 13, title: 'Ожерелья' },
				{ id: 14, title: 'Кольца' },
				{ id: 15, title: 'Серьги' },
			],
		},
		{
			title: 'Работа',
			icon: 'tools',
			items: [
				{ id: 20, title: 'Инструменты' },
				{ id: 32, title: 'Документы' },
			],
		},
	];
</script>
