<template>
	<ContentBlock title="Банк">
		<div class="w-full text-right">
			<Link :href="'/map?section=' + page.section"><img src="/assets/images/images/refresh.gif" alt="Обновить"></Link>
			<Link href="/map/change/17"><img src="/assets/images/images/back.gif" alt="Вернуться"></Link>
		</div>

		<p v-if="page.message" class="message bg-red-100 text-red-700 mb-4" v-html="page.message"></p>
		<p class="mb-4">У вас: <b>{{ user.gold }} зол.</b> и <b>{{ user.credits }} пл.</b></p>

		<div class="flex gap-4 border-b mb-4 pb-2">
			<Link href="/map?section=1" :class="{ 'font-bold': page.section === 1 }">Пожертвования</Link>
			<Link href="/map?section=2" :class="{ 'font-bold': page.section === 2 }">Обмен валюты</Link>
		</div>

		<div v-if="page.section === 1">
			<h3 class="font-bold mb-2">Пожертвования</h3>
			<p class="mb-4">Вы можете поддержать создателей игры добровольным пожертвованием золота.</p>
			<form class="max-w-md space-y-3" @submit.prevent="donate">
				<label class="block">
					Сумма, зол.
					<input v-model="donationForm.amount" type="text" inputmode="decimal" maxlength="13" required class="block w-full" placeholder="0,00">
				</label>
				<p v-if="donationForm.errors.amount" class="text-red-700">{{ donationForm.errors.amount }}</p>
				<label class="block">
					Ваше пожелание
					<input v-model="donationForm.comment" type="text" maxlength="100" class="block w-full">
				</label>
				<p v-if="donationForm.errors.comment" class="text-red-700">{{ donationForm.errors.comment }}</p>
				<button type="submit" class="button" :disabled="donationForm.processing">Пожертвовать</button>
			</form>

			<h3 class="font-bold mt-6 mb-3">Последние 20 пожертвований</h3>
			<table v-if="page.donations.length" class="table w-full">
				<thead>
					<tr><th>Игрок</th><th>Сумма</th><th>Комментарий</th></tr>
				</thead>
				<tbody>
					<tr v-for="donation in page.donations" :key="donation.id">
						<td>{{ donation.user }}</td>
						<td class="whitespace-nowrap">{{ donation.amount }} зол.</td>
						<td class="break-words">{{ donation.comment }}</td>
					</tr>
				</tbody>
			</table>
			<p v-else>Пожертвований пока нет.</p>
		</div>
		<div v-else>
			<h3 class="font-bold mb-2">Обмен валюты</h3>
			<p class="mb-4">Курс обмена: 1 пл. = {{ page.exchange_rate }} зол.</p>
			<form class="max-w-md space-y-3" @submit.prevent="exchange">
				<label class="block">
					Сумма платины для обмена
					<input v-model="exchangeForm.amount" type="text" inputmode="decimal" maxlength="13" required class="block w-full" placeholder="0,00">
				</label>
				<p v-if="exchangeForm.errors.amount" class="text-red-700">{{ exchangeForm.errors.amount }}</p>
				<p v-if="exchangeGold > 0">Вы получите: <b>{{ exchangeGold }} зол.</b></p>
				<button type="submit" class="button" :disabled="exchangeForm.processing">Обменять</button>
			</form>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { computed } from 'vue';
	import { Link, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import useState from '~/composables/useState.js';

	const props = defineProps({ page: Object });
	const state = useState();
	const user = computed(() => state.user);
	const donationForm = useForm({ action: 'donate', amount: '', comment: '' });
	const exchangeForm = useForm({ action: 'exchange', amount: '' });
	const exchangeGold = computed(() => {
		const amount = Number(exchangeForm.amount.replace(',', '.'));
		return Number.isFinite(amount) && amount > 0 ? Math.round(amount * props.page.exchange_rate * 100) / 100 : 0;
	});

	function donate() {
		if (donationForm.processing) return;
		donationForm.post('/map?section=1', { preserveScroll: true });
	}

	function exchange() {
		if (exchangeForm.processing) return;
		exchangeForm.post('/map?section=2', { preserveScroll: true });
	}
</script>