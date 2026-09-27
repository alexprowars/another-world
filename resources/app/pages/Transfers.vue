<template>
	<Head title="Передача предметов и золота"/>
	<ContentBlock title="Передача предметов и золота">
		<div class="mb-4 text-right"><Link href="/map">Вернуться в город</Link></div>
		<p v-if="page.message" class="message mb-4 bg-red-100 text-red-700" role="alert">{{ page.message }}</p>

		<template v-if="page.allowed">
			<form class="mb-4 flex flex-wrap items-end gap-3" @submit.prevent="findRecipient">
				<label class="block">
					Ник или ID получателя
					<input v-model.trim="searchForm.login" type="text" maxlength="100" required class="block w-full">
				</label>
				<button type="submit" class="button" :disabled="busy">Выбрать получателя</button>
			</form>
			<p v-for="(error, field) in searchForm.errors" :key="field" class="mb-2 text-red-700" role="alert">{{ error }}</p>

			<template v-if="page.recipient">
				<div class="mb-4 flex flex-wrap items-center gap-2">
					<span>Получатель:</span>
					<Name :player="page.recipient"/>
				</div>
				<p class="mb-4">У вас: <b>{{ user.gold }} зол.</b></p>
				<form class="mb-6 max-w-md space-y-3" @submit.prevent="transferGold">
					<h3 class="font-bold">Передать золото</h3>
					<label class="block">
						Сумма, зол.
						<input v-model="goldForm.amount" type="text" inputmode="decimal" maxlength="13" required class="block w-full" placeholder="0,00">
					</label>
					<label class="block">
						Причина передачи
						<input v-model.trim="goldForm.comment" type="text" maxlength="255" required class="block w-full">
					</label>
					<p v-for="(error, field) in goldForm.errors" :key="field" class="text-red-700" role="alert">{{ error }}</p>
					<button type="submit" class="button" :disabled="busy">Передать золото</button>
				</form>

				<h3 class="mb-3 font-bold">Передать предмет</h3>
				<p v-for="(error, field) in itemForm.errors" :key="field" class="mb-2 text-red-700" role="alert">{{ error }}</p>
				<div v-if="page.items.length" class="shop-items grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
					<SellItem v-for="entry in page.items" :key="entry.item.id" :item="entry.item">
						<template #actions>
							<button type="button" class="button mt-2" :disabled="busy || !!entry.restriction" @click="transferItem(entry.item)">Передать</button>
						</template>
						<template #details>
							<p v-if="entry.restriction" class="text-red-700">{{ entry.restriction }}</p>
						</template>
					</SellItem>
				</div>
				<p v-else>В рюкзаке нет предметов для передачи.</p>
			</template>
		</template>
	</ContentBlock>
</template>

<script setup>
	import { computed } from 'vue';
	import { Head, Link, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import Name from '~/components/Person/Name.vue';
	import SellItem from '~/components/City/Shop/SellItem.vue';
	import useState from '~/composables/useState.js';

	const props = defineProps({ page: Object });
	const state = useState();
	const user = computed(() => state.user);
	const searchForm = useForm({ login: props.page.login });
	const goldForm = useForm({ action: 'gold', recipient_id: null, amount: '', comment: '' });
	const itemForm = useForm({ action: 'item', recipient_id: null, item_id: null });
	const busy = computed(() => searchForm.processing || goldForm.processing || itemForm.processing);

	function findRecipient() {
		if (busy.value) return;
		searchForm.get('/transfers');
	}

	function transferGold() {
		if (busy.value || !props.page.recipient) return;
		goldForm.recipient_id = props.page.recipient.id;
		itemForm.clearErrors();
		goldForm.post('/transfers', {
			preserveScroll: true,
			onSuccess: () => goldForm.reset('amount', 'comment'),
		});
	}

	function transferItem(item) {
		if (busy.value || !props.page.recipient) return;
		itemForm.recipient_id = props.page.recipient.id;
		itemForm.item_id = item.id;
		goldForm.clearErrors();
		itemForm.post('/transfers', { preserveScroll: true });
	}
</script>