<template>
	<CityLocation
		title="Прихожая"
		description="За каменными сводами ждёт арена. Выберите путь к поединкам или отправляйтесь в академию."
		image-path="/assets/images/world/"
		:map="map"
		:places="places"
		places-title="Места на арене"
		:exits="exits"
		:decorations="decorations"
	/>
</template>

<script setup>
	import CityLocation from '~/components/City/CityLocation.vue';

	const map = {
		image: 'arena.jpg',
		alt: 'Прихожая арены',
		width: 690,
		height: 285,
	};
	const places = [
		{
			room: 1,
			title: 'Вход на арену',
			description: 'Поединки и тренировки',
			number: 1,
			x: 320,
			y: 30,
			width: 141,
			height: 185,
		},
		{
			room: 9,
			title: 'Академия',
			description: 'Обучение профессиям',
			number: 2,
			x: 100,
			y: 75,
			width: 70,
			height: 165,
		},
	];
	const exits = [
		{
			room: 103,
			title: 'Торговая площадь',
			x: 0,
			y: 90,
			width: 60,
			height: 165,
			direction: 'left',
			subtitle: 'Выход в город',
		},
	];
	const decorations = [
		{
			title: 'Ломбард',
			image: '0.gif',
			x: 610,
			y: 80,
			width: 70,
			height: 165,
		},
	];
</script>
