<template>
	<CityLocation
		title="Парк"
		description="Тихие аллеи, резные скамейки и свежий воздух. Место для отдыха и новых встреч на городских дорожках."
		:image-path="'/assets/images/world/city/' + page.city + '/'"
		:map="map"
		:places="places"
		places-title="Места в парке"
		:exits="exits"
		:decorations="decorations"
	/>
</template>

<script setup>
	import CityLocation from '~/components/City/CityLocation.vue';

	defineProps({ page: Object });

	const map = {
		image: '2_bg.jpg',
		alt: 'Карта парка',
		width: 550,
		height: 300,
	};
	const places = [
		{
			room: 10,
			title: 'Башня магов',
			description: 'Магия и заклинания',
			number: 1,
			image: '2_Mage.gif',
			x: 283,
			y: 20,
			width: 32,
			height: 101,
		},
		{
			room: 16,
			title: 'Центр занятости',
			description: 'Работа и заработок',
			number: 2,
			image: '2_Gift.gif',
			x: 246,
			y: 174,
			width: 91,
			height: 69,
		},
		{
			room: 13,
			title: 'Сувениры',
			description: 'Подарки для друзей',
			number: 3,
			image: '2_Loto.gif',
			x: 348,
			y: 204,
			width: 71,
			height: 49,
		},
	];
	const exits = [
		{
			room: 103,
			title: 'Торговая площадь',
			image: '2_Left.gif',
			x: 7,
			y: 230,
			width: 25,
			height: 24,
			direction: 'left',
		},
		{
			room: 105,
			title: 'Промышленная зона',
			image: '2_Right.gif',
			x: 502,
			y: 265,
			width: 29,
			height: 26,
			direction: 'right',
		},
	];
	const decorations = [
		{
			title: 'Игорный дом',
			image: '2_LV.gif',
			x: 37,
			y: 29,
			width: 187,
			height: 193,
		},
		{
			title: 'Таверна',
			image: '2_Tavern.gif',
			x: 28,
			y: 197,
			width: 96,
			height: 100,
		},
		{
			title: 'Проход закрыт',
			image: '2_Bottom.gif',
			x: 118,
			y: 273,
			width: 19,
			height: 20,
		},
	];
</script>
