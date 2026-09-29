<template>
	<Storefront
		:page="page"
		title="Башня Мага"
		back-href="/map/change/10"
		:departments="departments"
		:inventory-action="null"
		catalog-icon="energy"
		description="Свитки, зелья и снаряжение для тех, кто владеет магией."
	/>
</template>

<script setup>
	import Storefront from '~/components/City/Shop/Storefront.vue';

	defineProps({
		page: {
			type: Object,
			required: true
		}
	});

	const departments = [
		{
			title: 'Свитки',
			icon: 'book',
			items: [
				{ id: 1, title: 'Здоровье' },
				{ id: 2, title: 'Мана' },
				{ id: 3, title: 'Лечение травм' },
				{ id: 4, title: 'Иммунитет' },
				{ id: 5, title: 'Нападение' },
				{ id: 6, title: 'Боевая магия' },
				{ id: 7, title: 'Прочее' },
			],
		},
		{
			title: 'Зелья',
			icon: 'energy',
			items: [
				{ id: 8, title: 'Зелья' },
				{ id: 12, title: 'Ингредиенты' },
			],
		},
		{
			title: 'Магические предметы',
			icon: 'gem',
			items: [
				{ id: 9, title: 'Посохи' },
				{ id: 10, title: 'Одеяние' },
				{ id: 11, title: 'Магические вещи' },
				{ id: 13, title: 'Руны' },
			],
		},
	];
</script>
