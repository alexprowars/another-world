<template>
	<CityLocation
		title="Королевская улица"
		description="Колокольный звон, величественные стены и важные дела. Здесь бьётся сердце королевского города."
		:image-path="'/assets/images/world/city/' + page.city + '/'"
		:map="map"
		:places="places"
		places-title="Места на улице"
		:exits="exits"
	/>
</template>

<script setup>
	import CityLocation from '~/components/City/CityLocation.vue';

	defineProps({
		page: Object
	});

	const map = {
		image: 'street1.jpg',
		alt: 'Карта Королевской улицы',
		width: 1200,
		height: 654,
	};

	const places = [
		{
			room: 14,
			title: 'Замок святой инквизиции',
			description: 'Обитель инквизиции',
			number: 1,
			x: 99,
			y: 53,
			width: 346,
			height: 346,
		},
		{
			room: 29,
			title: 'Бутик',
			description: 'Особые товары',
			number: 2,
			x: 636,
			y: 226,
			width: 155,
			height: 180,
		},
		{
			room: 8,
			title: 'Больница',
			description: 'Восстановление здоровья',
			number: 3,
			x: 470,
			y: 226,
			width: 159,
			height: 180,
		},
		{
			room: 22,
			title: 'Церковь',
			description: 'Заключение брака и развод',
			number: 4,
			x: 824,
			y: 12,
			width: 275,
			height: 400,
		},
	];

	const exits = [
		{
			room: 103,
			title: 'Торговая площадь',
			number: '→',
			x: 1134,
			y: 410,
			width: 66,
			height: 120,
			direction: 'right',
		},
	];
</script>
