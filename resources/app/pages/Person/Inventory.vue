<template>
	<section class="person-inventory">
		<header class="person-page-heading">
			<div>
				<h1>Рюкзак</h1>
				<p>Экипировка, припасы и всё, что вы собрали в путешествиях.</p>
			</div>
			<Link href="/person/inventory?unset=all" class="ui-button ui-button--compact ui-button--secondary" preserve-scroll>Снять всё</Link>
		</header>

		<InventoryNavigation :active="page.item_type" />

		<div v-if="equippedMagic.length" class="flex flex-wrap gap-2">
			<button v-for="item in equippedMagic" :key="item.id" type="button" class="ui-button ui-button--compact" @click="useItem(item)">
				Использовать: {{ item.title }}
			</button>
		</div>

		<div v-if="Object.keys(dropForm.errors).length" class="ui-notice ui-notice--red ui-notice--compact" role="alert">
			<p v-for="(error, field) in dropForm.errors" :key="field">{{ error }}</p>
		</div>

		<div class="inventory-section-heading inventory-section-heading--count-only">
			<span class="person-section-count">Предметов: {{ page.items.length }}</span>
		</div>

		<div v-if="page.items.length" class="inventory-list">
			<InventoryItem
				v-for="item in page.items"
				:key="item.id"
				:item="item"
				:player="user"
				:dropping="dropForm.processing"
				@wear="wearItem"
				@drop="dropItem"
				@use="useItem"
			/>
		</div>
		<div v-else class="person-empty-state">
			<b>В этом разделе пока пусто</b>
			<p>Здесь появятся предметы выбранного типа.</p>
		</div>
	</section>
</template>

<script setup>
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import { Link, router, useForm } from '@inertiajs/vue3';
	import InventoryItem from '~/components/Person/InventoryItem.vue';
	import InventoryNavigation from '~/components/Person/InventoryNavigation.vue';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import { openPopupModal } from '~/composables/useModals.js';
	import UseMagic from '~/components/Dialogs/UseMagic.vue';

	defineOptions({
		layout: [GameLayout, PersonLayout]
	});

	const props = defineProps({
		page: Object
	});

	const state = useState();
	const user = computed(() => state.user);

	const dropForm = useForm({
		id: null,
		item_type: null
	});

	const equippedMagic = computed(() => Object.values(user.value?.slots || {}).filter(item => item.can_use));

	function useItem(item) {
		openPopupModal(UseMagic, {
			title: 'Использовать магию',
			item,
			selfName: user.value.name,
			onUsed: () => router.reload(),
		});
	}

	function dropItem(item) {
		if (dropForm.processing) {
			return;
		}

		dropForm.id = item.id;
		dropForm.item_type = props.page.item_type;
		dropForm.post('/person/inventory/drop', { preserveScroll: true });
	}

	function wearItem(item) {
		router.get('/person/inventory', { onset: item.id }, { preserveScroll: true });
	}
</script>
