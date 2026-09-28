<template>
	<CityLocation
		title="Королевская улица"
		description="Колокольный звон, величественные стены и важные дела. Здесь бьётся сердце королевского города."
		:image-path="'/assets/images/world/city/' + page.city + '/'"
		:map="map"
		:places="places"
		places-title="Места на улице"
		:exits="exits"
		:decorations="decorations"
	/>
</template>

<script setup>
	import CityLocation from '~/components/City/CityLocation.vue';

	defineProps({ page: Object });

	const map = {
		image: '4_bg.jpg',
		alt: 'Карта Королевской улицы',
		width: 550,
		height: 300,
	};
	const places = [
		{
			room: 14,
			title: 'Замок святой инквизиции',
			description: 'Обитель инквизиции',
			number: 1,
			image: '4_castle.gif',
			x: 58,
			y: 36,
			width: 134,
			height: 170,
		},
		{
			room: 29,
			title: 'Бутик',
			description: 'Особые товары',
			number: 2,
			image: '4_trade.gif',
			x: 289,
			y: 134,
			width: 63,
			height: 75,
		},
		{
			room: 8,
			title: 'Больница',
			description: 'Восстановление здоровья',
			number: 3,
			image: '4_shop.gif',
			x: 221,
			y: 134,
			width: 59,
			height: 78,
		},
	];
	const exits = [
		{
			room: 103,
			title: 'Торговая площадь',
			image: '4_right.gif',
			x: 493,
			y: 215,
			width: 27,
			height: 24,
			direction: 'right',
		},
	];
	const decorations = [
		{
			title: 'Домик знахаря',
			image: '4_bkm.gif',
			x: 61,
			y: 192,
			width: 70,
			height: 77,
		},
		{
			title: 'Храм',
			image: '4_church.gif',
			x: 358,
			y: 4,
			width: 139,
			height: 206,
		},
		{
			title: 'Проход закрыт',
			image: '4_left.gif',
			x: 24,
			y: 205,
			width: 26,
			height: 24,
		},
		{
			title: 'Проход закрыт',
			image: '4_bottom.gif',
			x: 145,
			y: 234,
			width: 17,
			height: 19,
		},
	];
</script>
