<template>
	<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
		<p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
	</div>
	<header class="storefront-catalog-header">
		<h2>
			Огранка камней
			<span class="ui-badge">{{ page.items.length }}</span>
		</h2>
	</header>
	<p class="storefront-description">Ограните необработанные камни, чтобы использовать их для улучшения снаряжения.</p>

	<div class="smithy-conditions">
		<div class="smithy-condition">
			<GameIcon name="hourglass" />
			<div>
				<span>Время работы</span>
				<strong>{{ page.work_seconds / 60 }} мин.</strong>
			</div>
		</div>
		<div class="smithy-condition">
			<GameIcon name="energy" />
			<div>
				<span>Затраты сил</span>
				<strong>{{ page.work_stamina }} ед.</strong>
			</div>
		</div>
		<div class="smithy-condition">
			<GameIcon name="tools" />
			<div>
				<span>Инструмент профессии</span>
				<strong>Должен быть надет · +1 к износу</strong>
			</div>
		</div>
	</div>

	<div v-if="page.items.length" class="storefront-grid">
		<CatalogItem v-for="entry in page.items" :key="entry.item.id" :item="entry.item" :player="user" inventory-item>
			<template #actions>
				<div class="smithy-item-actions">
					<button
						type="button"
						class="ui-button"
						:disabled="form.processing"
						@click="confirmCut(entry)"
					>
						<GameIcon name="gem" />
						Огранить камень
					</button>
				</div>
			</template>
		</CatalogItem>
	</div>
	<div v-else class="ui-empty" role="status">
		<GameIcon name="gem" />
		<h3>Нет подходящих предметов</h3>
		<p>Здесь появятся снятые предметы, подходящие для этой операции.</p>
	</div>
</template>

<script setup>
	import useLocation from '~/composables/useLocation.js';

	import CatalogItem from '~/components/City/Shop/CatalogItem.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import { useForm } from '@inertiajs/vue3';
	import { openConfirmModal } from '~/composables/useModals.js';

	const location = useLocation();

	const props = defineProps({
		page: Object,
		user: Object,
	});

	const form = useForm({
		action: '',
		id: null,
	});

	function confirmCut(entry) {
		confirm('Огранить камень? Работа займёт ' + props.page.work_seconds / 60 + ' минут.', 'cut', entry.item.id);
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

					form.post(location.value.actions.cut + '?section=2', {
						preserveScroll: true
					});
				},
			},
		]);
	}
</script>
