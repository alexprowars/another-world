<template>
	<CityLocation
		title="Парк"
		description="Тихие аллеи, резные скамейки и свежий воздух. Место для отдыха и новых встреч на городских дорожках."
		:image-path="'/assets/images/world/city/' + page.city + '/'"
		:map="map"
		:places="places"
		places-title="Места в парке"
		:exits="exits"
	/>
</template>

<script setup>
	import CityLocation from '~/components/City/CityLocation.vue';

	defineProps({
		page: Object
	});

	const map = {
		image: 'street3.jpg',
		alt: 'Карта парка',
		width: 1200,
		height: 654,
	};

	const places = [
		{
			room: 10,
			title: 'Башня магов',
			description: 'Магия и заклинания',
			number: 1,
			x: 625,
			y: 14,
			width: 102,
			height: 201,
		},
		{
			room: 16,
			title: 'Центр занятости',
			description: 'Работа и заработок',
			number: 2,
			x: 569,
			y: 321,
			width: 205,
			height: 155,
		},
		{
			room: 13,
			title: 'Сувениры',
			description: 'Подарки для друзей',
			number: 3,
			x: 777,
			y: 371,
			width: 198,
			height: 113,
		},
	];

	const exits = [
		{
			room: 103,
			title: 'Торговая площадь',
			number: '←',
			x: 0,
			y: 448,
			width: 60,
			height: 120,
			direction: 'left',
		},
		{
			room: 105,
			title: 'Промышленная зона',
			number: '→',
			x: 1134,
			y: 448,
			width: 66,
			height: 120,
			direction: 'right',
		},
	];
</script>
