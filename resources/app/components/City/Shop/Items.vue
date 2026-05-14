<template>
	<div v-if="items.length" class="shop-items grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
		<div v-for="item in items">
			<Item :item="item" :key="item.id" @buy="onBuyItem(item)"/>
		</div>
	</div>
	<div v-else>
		В данном отделе нет товаров.
	</div>
</template>

<script setup>
	import Item from '~/components/City/Shop/Item.vue';
	import { router } from '@inertiajs/vue3';

	const props = defineProps({
		items: Array,
		section: Number,
	});

	function onBuyItem(item) {
		router.get('', {
			section: props.section,
			buy: item.id,
		});
	}
</script>