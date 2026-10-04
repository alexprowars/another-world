<template>
	<section class="service-section">
		<header class="service-heading">
			<h2>Передача предметов и золота</h2>
			<p>Выберите получателя и отправьте ему золото или предмет из рюкзака. Передачи доступны с 6 уровня.</p>
		</header>
		<p v-if="transfer.message" class="ui-notice ui-notice--red" role="alert">{{ transfer.message }}</p>

		<template v-if="transfer.allowed">
			<form class="mb-4 flex flex-wrap items-end gap-3" @submit.prevent="findRecipient">
				<label class="service-field">
					Ник или ID получателя
					<input
						v-model.trim="searchForm.login"
						type="text"
						maxlength="100"
						required
						class="ui-input ui-input--compact"
						:disabled="busy"
					/>
				</label>
				<button type="submit" class="ui-button ui-button--compact" :disabled="busy">Выбрать получателя</button>
			</form>
			<p v-for="(error, field) in searchForm.errors" :key="field" class="mb-2 service-error" role="alert">{{ error }}</p>

			<template v-if="transfer.recipient">
				<div class="mb-4 flex flex-wrap items-center gap-2">
					<span>Получатель:</span>
					<Name :player="transfer.recipient" />
				</div>
				<form class="service-form mb-6 max-w-md" @submit.prevent="transferGold">
					<h3 class="font-bold">Передать золото</h3>
					<label class="service-field">
						Сумма, зол.
						<input
							v-model="goldForm.amount"
							type="text"
							inputmode="decimal"
							maxlength="13"
							required
							class="ui-input ui-input--compact"
							placeholder="0,00"
							:disabled="busy"
						/>
					</label>
					<label class="service-field">
						Причина передачи
						<input
							v-model.trim="goldForm.comment"
							type="text"
							maxlength="255"
							required
							class="ui-input ui-input--compact"
							:disabled="busy"
						/>
					</label>
					<p v-for="(error, field) in goldForm.errors" :key="field" class="service-error" role="alert">{{ error }}</p>
					<button type="submit" class="ui-button ui-button--compact" :disabled="busy">Передать золото</button>
				</form>

				<h3 class="mb-3 font-bold">Передать предмет</h3>
				<p v-for="(error, field) in itemForm.errors" :key="field" class="mb-2 service-error" role="alert">{{ error }}</p>
				<div v-if="transfer.items.length" class="shop-items grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
					<SellItem v-for="entry in transfer.items" :key="entry.item.id" :item="entry.item">
						<template #actions>
							<button
								type="button"
								class="ui-button ui-button--compact mt-2"
								:disabled="busy || !!entry.restriction"
								@click="transferItem(entry.item)"
							>
								Передать
							</button>
						</template>
						<template #details>
							<p v-if="entry.restriction" class="service-error">{{ entry.restriction }}</p>
						</template>
					</SellItem>
				</div>
				<p v-else>В рюкзаке нет предметов для передачи.</p>
			</template>
		</template>
	</section>
</template>

<script setup>
	import useLocation from '~/composables/useLocation.js';

	import { computed } from 'vue';
	import { useForm } from '@inertiajs/vue3';
	import Name from '~/components/Person/Name.vue';
	import SellItem from '~/components/City/Shop/SellItem.vue';

	const location = useLocation();

	const props = defineProps({
		transfer: Object,
	});

	const searchForm = useForm({
		section: 'transfers',
		login: props.transfer.login,
	});

	const goldForm = useForm({
		action: 'gold',
		recipient_id: null,
		amount: '',
		comment: '',
	});

	const itemForm = useForm({
		action: 'item',
		recipient_id: null,
		item_id: null,
	});

	const busy = computed(() => searchForm.processing || goldForm.processing || itemForm.processing);

	function findRecipient() {
		if (busy.value) {
			return;
		}

		searchForm.get(location.value.url, {
			preserveState: 'errors',
			preserveScroll: true,
		});
	}

	function transferGold() {
		if (busy.value || !props.transfer.recipient) {
			return;
		}

		goldForm.recipient_id = props.transfer.recipient.id;

		itemForm.clearErrors();

		goldForm.post(location.value.actions.gold, {
			preserveScroll: true,
			onSuccess: () => goldForm.reset('amount', 'comment'),
		});
	}

	function transferItem(item) {
		if (busy.value || !props.transfer.recipient) {
			return;
		}

		itemForm.recipient_id = props.transfer.recipient.id;
		itemForm.item_id = item.id;

		goldForm.clearErrors();

		itemForm.post(location.value.actions.item, {
			preserveScroll: true,
		});
	}
</script>
