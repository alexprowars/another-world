<template>
	<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
		<p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
	</div>
	<header class="storefront-catalog-header">
		<h2>
			Гравировка и модернизация
			<span class="ui-badge">{{ page.items.length }}</span>
		</h2>
	</header>
	<p class="storefront-description">Нанесите памятную надпись или увеличьте урон снаряжения. Предмет нужно снять.</p>

	<div class="smithy-conditions">
		<div class="smithy-condition">
			<GameIcon name="book" />
			<div>
				<span>Гравировка · {{ page.engraving_price }} зол.</span>
				<strong>До 25 символов, без замены надписи</strong>
			</div>
		</div>
		<div class="smithy-condition">
			<GameIcon name="swords" />
			<div>
				<span>Модернизация · {{ page.upgrade_price }} пл. · только для кузнеца</span>
				<strong>+1 к мин. и макс. урону, −20 к макс. долговечности</strong>
			</div>
		</div>
	</div>

	<div v-if="page.items.length" class="storefront-grid">
		<CatalogItem v-for="entry in page.items" :key="entry.item.id" :item="entry.item" :player="user" inventory-item>
			<template #actions>
				<div class="smithy-item-actions">
					<form v-if="!entry.item.engraving" class="smithy-form" @submit.prevent="engrave(entry)">
						<label class="smithy-field">
							<span>
								Текст гравировки
								<small>{{ texts[entry.item.id]?.length || 0 }} / 25</small>
							</span>
							<input
								class="ui-input"
								v-model="texts[entry.item.id]"
								type="text"
								required
								maxlength="25"
								placeholder="Ваша надпись"
								:disabled="form.processing"
							/>
						</label>
						<button type="submit" class="ui-button" :disabled="form.processing">Гравировать · {{ page.engraving_price }} зол.</button>
					</form>
					<button
						v-if="user.profession === 2 && entry.item.wearout_max > 20"
						type="button"
						class="ui-button ui-button--secondary"
						:disabled="form.processing"
						@click="confirmUpgrade(entry)"
					>
						Модернизировать · {{ page.upgrade_price }} пл.
					</button>
					<p v-if="entry.item.engraving && (user.profession !== 2 || entry.item.wearout_max <= 20)" class="smithy-item-hint">
						{{
							user.profession === 2
								? 'Недостаточно долговечности для модернизации.'
								: 'Гравировка уже нанесена. Модернизация доступна кузнецу.'
						}}
					</p>
				</div>
			</template>
		</CatalogItem>
	</div>
	<div v-else class="ui-empty" role="status">
		<GameIcon name="swords" />
		<h3>Нет подходящих предметов</h3>
		<p>Здесь появятся снятые предметы, подходящие для этой операции.</p>
	</div>
</template>

<script setup>
	import useLocation from '~/composables/useLocation.js';

	import { reactive } from 'vue';
	import CatalogItem from '~/components/City/Shop/CatalogItem.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import { useForm } from '@inertiajs/vue3';
	import { openConfirmModal } from '~/composables/useModals.js';

	const location = useLocation();

	const props = defineProps({
		page: Object,
		user: Object,
	});

	const texts = reactive({});

	const form = useForm({
		action: '',
		id: null,
		text: null,
	});

	function engrave(entry) {
		confirm('Выгравировать надпись за ' + props.page.engraving_price + ' зол.? Гравировку нельзя заменить.', 'engrave', entry.item.id, {
			text: texts[entry.item.id],
		});
	}

	function confirmUpgrade(entry) {
		confirm(
			'Увеличить минимальный и максимальный урон на 1 за ' + props.page.upgrade_price + ' пл.? Максимальная долговечность уменьшится на 20.',
			'upgrade',
			entry.item.id,
		);
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

					form.post(location.value.actions[action] + '?section=3', {
						preserveScroll: true
					});
				},
			},
		]);
	}
</script>
