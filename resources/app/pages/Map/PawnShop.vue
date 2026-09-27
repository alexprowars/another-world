<template>
	<ContentBlock title="Ломбард">
		<div class="w-full text-right">
			<Link :href="'/map?section=' + page.section"><img src="/assets/images/images/refresh.gif" alt="Обновить"></Link>
			<Link href="/map/change/28"><img src="/assets/images/images/back.gif" alt="Вернуться"></Link>
		</div>

		<p v-if="page.message" class="message bg-red-100 text-red-700 mb-4" v-html="page.message"></p>
		<p v-for="(error, key) in form.errors" :key="key" class="text-red-700 mb-2">{{ error }}</p>
		<p class="mb-2">При сдаче предмета в залог вы получаете {{ page.deposit_percent }}% его государственной цены. Выкуп стоит {{ page.withdraw_percent }}% цены.</p>
		<p class="mb-4">Расчёты проводятся в валюте предмета. У вас: <b>{{ user.gold }} зол.</b> и <b>{{ user.credits }} пл.</b></p>

		<div class="flex gap-4 border-b mb-4 pb-2">
			<Link href="/map?section=50" :class="{ 'font-bold': page.section === 50 }">Мои вещи в ломбарде</Link>
			<Link href="/map?section=100" :class="{ 'font-bold': page.section === 100 }">Заложить предмет</Link>
		</div>

		<div v-if="page.items.length" class="shop-items grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
			<SellItem v-for="entry in page.items" :key="entry.item.id" :item="entry.item">
				<template #actions>
					<button type="button" class="button mt-2" :disabled="form.processing" @click="confirmAction(entry)">
						{{ page.section === 100 ? 'Заложить предмет' : 'Выкупить' }}
					</button>
				</template>
				<template #details>
					<div v-if="page.section === 100">Вы получите: <b>{{ entry.deposit_price }} {{ currency(entry) }}</b></div>
					<div>Выкуп: <b>{{ entry.withdraw_price }} {{ currency(entry) }}</b></div>
				</template>
			</SellItem>
		</div>
		<p v-else>{{ page.section === 100 ? 'Нет вещей для сдачи в залог.' : 'В ломбарде нет ваших вещей.' }}</p>
	</ContentBlock>
</template>

<script setup>
	import { computed } from 'vue';
	import { Link, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import SellItem from '~/components/City/Shop/SellItem.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	const props = defineProps({ page: Object });
	const state = useState();
	const user = computed(() => state.user);
	const form = useForm({ action: '', id: null });

	function currency(entry) {
		return entry.item.price_type === 1 ? 'пл.' : 'зол.';
	}

	function confirmAction(entry) {
		const deposit = props.page.section === 100;
		const message = deposit
			? 'Заложить предмет и получить ' + entry.deposit_price + ' ' + currency(entry) + '? Выкуп будет стоить ' + entry.withdraw_price + ' ' + currency(entry)
			: 'Выкупить предмет за ' + entry.withdraw_price + ' ' + currency(entry) + '?';

		openConfirmModal('Подтвердите действие', message, [
			{ title: 'Нет' },
			{ title: 'Да', handler() {
				if (form.processing) return;
				form.action = deposit ? 'deposit' : 'withdraw';
				form.id = entry.item.id;
				form.clearErrors();
				form.post('/map?section=' + props.page.section, { preserveScroll: true });
			} },
		]);
	}
</script>