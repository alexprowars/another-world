<template>
	<CityLocation
		title="Торговая площадь"
		description="Звон монет, голоса торговцев и стук кузнечного молота. Здесь город живёт своей жизнью."
		:image-path="'/assets/images/world/city/' + page.city + '/'"
		:map="map"
		:places="places"
		places-title="Места на площади"
		:exits="exits"
	/>
</template>

<script setup>
	import CityLocation from '~/components/City/CityLocation.vue';

	defineProps({
		page: Object
	});

	const map = {
		image: 'street2.jpg',
		alt: 'Карта торговой площади',
		width: 1200,
		height: 656,
	};

	const places = [
		{
			room: 23,
			title: 'Арена',
			description: 'Поединки и тренировки',
			number: 1,
			x: 39,
			y: 53,
			width: 583,
			height: 350,
		},
		{
			room: 17,
			title: 'Банк',
			description: 'Счета и сбережения',
			number: 2,
			x: 689,
			y: 133,
			width: 251,
			height: 287,
		},
		{
			room: 7,
			title: 'Магазин',
			description: 'Оружие и экипировка',
			number: 3,
			x: 986,
			y: 258,
			width: 205,
			height: 184,
		},
		{
			room: 11,
			title: 'Кузница',
			description: 'Ремонт и улучшение вещей',
			number: 4,
			x: 555,
			y: 449,
			width: 240,
			height: 184,
		},
		{
			room: 20,
			title: 'Рынок',
			description: 'Торговля между игроками',
			number: 5,
			x: 57,
			y: 414,
			width: 184,
			height: 149,
		},
	];

	const exits = [
		{
			room: 101,
			title: 'Королевская улица',
			number: '←',
			x: 0,
			y: 421,
			width: 60,
			height: 120,
			direction: 'left',
		},
		{
			room: 104,
			title: 'Парк',
			number: '→',
			x: 1134,
			y: 421,
			width: 66,
			height: 120,
			direction: 'right',
		},
	];
</script>
