<template>
	<CityLocation
		title="Промышленная улица"
		description="Грохот вагонеток, угольная пыль и чёрные горы на горизонте. В промышленном квартале работа не стихает."
		:image-path="'/assets/images/world/city/' + page.city + '/'"
		:map="map"
		:places="places"
		places-title="Места в квартале"
		:exits="exits"
		:decorations="decorations"
	/>
</template>

<script setup>
	import CityLocation from '~/components/City/CityLocation.vue';

	defineProps({ page: Object });

	const map = {
		image: '3_bg.jpg',
		alt: 'Карта промышленной улицы',
		width: 550,
		height: 300,
	};
	const places = [
		{
			room: 200,
			title: 'Шахта',
			description: 'Добыча ресурсов',
			number: 1,
			image: '3_Mine.gif',
			x: 247,
			y: 135,
			width: 30,
			height: 26,
		},
		{
			room: 666,
			title: 'Тюрьма',
			description: 'Место заключения',
			number: 2,
			image: '3_Prison.gif',
			x: 340,
			y: 108,
			width: 130,
			height: 105,
		},
		{
			room: 28,
			title: 'Ломбард',
			description: 'Операции с вещами',
			number: 3,
			image: '3_Storage.gif',
			x: 195,
			y: 206,
			width: 102,
			height: 79,
		},
	];
	const exits = [
		{
			room: 104,
			title: 'Парк',
			image: '3_Left.gif',
			x: 72,
			y: 191,
			width: 24,
			height: 23,
			direction: 'left',
		},
	];
	const decorations = [
		{
			title: 'Проход закрыт',
			image: '3_Tower.gif',
			x: 14,
			y: 134,
			width: 54,
			height: 74,
		},
		{
			title: 'Проход закрыт',
			image: '3_Right.gif',
			x: 504,
			y: 219,
			width: 26,
			height: 25,
		},
	];
</script>
