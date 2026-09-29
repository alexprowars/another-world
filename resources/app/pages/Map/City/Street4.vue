<template>
	<CityLocation
		title="Прихожая"
		description="За каменными сводами ждёт арена. Выберите путь к поединкам или отправляйтесь в академию."
		:image-path="'/assets/images/world/city/' + page.city + '/'"
		:map="map"
		:places="places"
		places-title="Места на арене"
		:exits="exits"
	/>
</template>

<script setup>
	import CityLocation from '~/components/City/CityLocation.vue';

	defineProps({
		page: Object
	});

	const map = {
		image: 'street4.jpg',
		alt: 'Прихожая арены',
		width: 1200,
		height: 495,
	};

	const places = [
		{
			room: 1,
			title: 'Вход на арену',
			description: 'Поединки и тренировки',
			number: 1,
			x: 516,
			y: 0,
			width: 184,
			height: 317,
		},
		{
			room: 9,
			title: 'Академия',
			description: 'Обучение профессиям',
			number: 2,
			x: 92,
			y: 114,
			width: 184,
			height: 261,
		},
	];

	const exits = [
		{
			room: 103,
			title: 'Торговая площадь',
			number: '←',
			x: 0,
			y: 234,
			width: 58,
			height: 129,
			direction: 'left',
			subtitle: 'Выход в город',
		},
	];
</script>
