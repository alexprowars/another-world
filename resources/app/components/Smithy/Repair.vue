<template>
	<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
		<p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
	</div>
	<header class="storefront-catalog-header">
		<h2>
			Починка вещей
			<span class="ui-badge">{{ page.items.length }}</span>
		</h2>
	</header>
	<p class="storefront-description">Восстановите изношенное снаряжение полностью или на одну единицу.</p>

	<div v-if="page.items.length" class="storefront-grid">
		<CatalogItem v-for="entry in page.items" :key="entry.item.id" :item="entry.item" :player="user" inventory-item>
			<template #actions>
				<div class="smithy-item-actions">
					<button type="button" class="ui-button" :disabled="form.processing" @click="confirmRepair(entry, true)">
						Починить полностью · {{ entry.repair_price }} зол.
					</button>
					<button type="button" class="ui-button ui-button--secondary" :disabled="form.processing" @click="confirmRepair(entry, false)">
						Починить 1 ед. · {{ entry.repair_one_price }} зол.
					</button>
				</div>
			</template>
		</CatalogItem>
	</div>
	<div v-else class="ui-empty" role="status">
		<GameIcon name="tools" />
		<h3>Нет подходящих предметов</h3>
		<p>В инвентаре нет вещей, требующих починки.</p>
	</div>
</template>

<script setup>
	import useLocation from '~/composables/useLocation.js';

	import CatalogItem from '~/components/City/Shop/CatalogItem.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import { useForm } from '@inertiajs/vue3';
	import { openConfirmModal } from '~/composables/useModals.js';

	const location = useLocation();

	defineProps({
		page: Object,
		user: Object,
	});

	const form = useForm({
		action: '',
		id: null,
		full: true,
	});

	function confirmRepair(entry, full) {
		confirm('Починить предмет за ' + (full ? entry.repair_price : entry.repair_one_price) + ' зол.?', 'repair', entry.item.id, {
			full,
		});
	}

	function confirm(message, action, id, data = {}) {
		openConfirmModal('Подтвердите действие', message, [
			{ title: 'Нет' },
			{
				title: 'Да',
				handler() {
					if (form.processing) {
						return;
					}

					form.reset();
					form.clearErrors();

					Object.assign(form, { action, id }, data);

					form.post(location.value.actions.repair + '?section=1', {
						preserveScroll: true
					});
				},
			},
		]);
	}
</script>
