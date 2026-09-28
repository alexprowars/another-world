<template>
	<CityLocation
		title="Торговая площадь"
		description="Звон монет, голоса торговцев и стук кузнечного молота. Здесь город живёт своей жизнью."
		:image-path="'/assets/images/world/city/' + page.city + '/'"
		:map="map"
		:places="places"
		places-title="Места на площади"
		:exits="exits"
		:decorations="decorations"
	/>
</template>

<script setup>
	import CityLocation from '~/components/City/CityLocation.vue';

	defineProps({ page: Object });

	const map = { image: '1_bg.jpg', alt: 'Карта торговой площади', width: 550, height: 300 };
	const places = [
		{
			room: 23,
			title: 'Арена',
			description: 'Поединки и тренировки',
			number: 1,
			image: '1_Arena.gif',
			x: 17,
			y: 26,
			width: 278,
			height: 170,
		},
		{
			room: 17,
			title: 'Банк',
			description: 'Счета и сбережения',
			number: 2,
			image: '1_Bank.gif',
			x: 311,
			y: 87,
			width: 117,
			height: 117,
		},
		{
			room: 7,
			title: 'Магазин',
			description: 'Оружие и экипировка',
			number: 3,
			image: '1_Store.gif',
			x: 452,
			y: 141,
			width: 93,
			height: 77,
		},
		{
			room: 11,
			title: 'Кузница',
			description: 'Ремонт и улучшение вещей',
			number: 4,
			image: '1_Smith.gif',
			x: 249,
			y: 210,
			width: 149,
			height: 80,
		},
		{
			room: 20,
			title: 'Рынок',
			description: 'Торговля между игроками',
			number: 5,
			image: '1_market.gif',
			x: 39,
			y: 201,
			width: 70,
			height: 55,
		},
	];
	const exits = [
		{ room: 101, title: 'Королевская улица', image: '1_Left.gif', x: 6, y: 195, width: 22, height: 21, direction: 'left' },
		{ room: 104, title: 'Парк', image: '1_Right.gif', x: 520, y: 231, width: 24, height: 23, direction: 'right' },
	];
	const decorations = [{ title: 'Почта', image: '1_Steads.gif', x: 421, y: 237, width: 85, height: 59 }];
</script>
