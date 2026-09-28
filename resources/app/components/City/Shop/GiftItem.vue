<template>
	<CatalogItem :item="item" :player="state.user" inventory-item>
		<template #actions>
			<button type="button" class="ui-button" @click="giftItem">Подарить</button>
		</template>
	</CatalogItem>
</template>

<script setup>
	import useState from '~/composables/useState.js';
	import { openPopupModal } from '~/composables/useModals.js';
	import Form from '~/components/City/Gift/Form.vue';
	import CatalogItem from './CatalogItem.vue';

	const props = defineProps({
		item: {
			type: Object,
			required: true
		},
	});

	const state = useState();

	function giftItem() {
		openPopupModal(Form, {
			item: props.item,
			title: 'Подарить подарок'
		});
	}
</script>
