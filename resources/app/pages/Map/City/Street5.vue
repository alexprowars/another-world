<template>
	<CityLocation
		title="Промышленная улица"
		description="Грохот вагонеток, угольная пыль и чёрные горы на горизонте. В промышленном квартале работа не стихает."
		:image-path="'/assets/images/world/city/' + page.city + '/'"
		:map="map"
		:places="places"
		places-title="Места в квартале"
		:exits="exits"
	/>
</template>

<script setup>
	import CityLocation from '~/components/City/CityLocation.vue';

	defineProps({ page: Object });

	const map = {
		image: 'street5.jpg',
		alt: 'Карта промышленной улицы',
		width: 1200,
		height: 654,
	};

	const places = [
		{
			room: 200,
			title: 'Шахта',
			description: 'Добыча ресурсов',
			number: 1,
			x: 519,
			y: 173,
			width: 163,
			height: 124,
		},
		{
			room: 666,
			title: 'Тюрьма',
			description: 'Место заключения',
			number: 2,
			x: 770,
			y: 145,
			width: 343,
			height: 251,
		},
		{
			room: 28,
			title: 'Ломбард',
			description: 'Операции с вещами',
			number: 3,
			x: 410,
			y: 357,
			width: 276,
			height: 187,
		},
	];

	const exits = [
		{
			room: 104,
			title: 'Парк',
			number: '←',
			x: 0,
			y: 304,
			width: 71,
			height: 127,
			direction: 'left',
		},
	];
</script>
