<template>
	<Storefront
		:page="page"
		title="Сувенирная лавка"
		:back-location="location.exit"
		:departments="departments"
		:inventory-action="inventoryAction"
		:show-suitable-filter="false"
		catalog-icon="gift"
		description="Открытки, цветы и подарки для дорогих вам персонажей."
	>
		<template v-if="page.section === 4" #item="{ item }">
			<GiftItem :item="item" />
		</template>
	</Storefront>
</template>

<script setup>
	import useLocation from '~/composables/useLocation.js';

	import Storefront from '~/components/City/Shop/Storefront.vue';
	import GiftItem from '~/components/City/Shop/GiftItem.vue';

	const location = useLocation();

	defineProps({
		page: {
			type: Object,
			required: true
		}
	});

	const departments = [
		{
			title: 'Сувениры',
			icon: 'gift',
			items: [
				{ id: 1, title: 'Открытки' },
				{ id: 2, title: 'Цветы' },
				{ id: 3, title: 'Подарки' },
			],
		},
	];
	const inventoryAction = {
		section: 4,
		label: 'Подарить',
		icon: 'gift',
		title: 'Ваши подарки',
		description: 'Выберите подарок и укажите получателя.',
		emptyTitle: 'У вас нет подарков',
		emptyDescription: 'Купите подарок в лавке, чтобы передать его другому персонажу.',
	};
</script>
