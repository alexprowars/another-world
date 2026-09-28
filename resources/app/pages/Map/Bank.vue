<template>
	<ContentBlock title="Банк">
		<template #actions>
			<Link href="/map/change/17" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</Link>
			<Link :href="'/map?section=' + page.section" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<p v-if="page.message" class="ui-notice ui-notice--blue" role="status" v-html="page.message"></p>
		<nav class="ui-tabs ui-tabs--stacked">
			<Link class="ui-tab" href="/map?section=1" :class="{ 'is-active': page.section === 1 }">
				<GameIcon name="gift" />
				Пожертвования
			</Link>
			<Link class="ui-tab" href="/map?section=2" :class="{ 'is-active': page.section === 2 }">
				<GameIcon name="transfer" />
				Обмен валюты
			</Link>
		</nav>
		<div v-if="page.section === 1" class="service-split">
			<section class="ui-panel service-panel">
				<header class="service-panel-heading">
					<GameIcon name="gift" />
					<h2>Поддержать игру</h2>
				</header>
				<div class="service-panel-body">
					<p class="service-hint">Вы можете поддержать создателей игры добровольным пожертвованием золота.</p>
					<form class="service-form" @submit.prevent="donate">
						<label class="service-field">
							<span>Сумма, зол.</span>
							<input
								class="ui-input"
								v-model="donationForm.amount"
								type="text"
								inputmode="decimal"
								maxlength="13"
								required
								placeholder="0,00"
								:disabled="donationForm.processing"
							/>
						</label>
						<p v-if="donationForm.errors.amount" class="service-error" role="alert">{{ donationForm.errors.amount }}</p>
						<label class="service-field">
							<span>Ваше пожелание</span>
							<input
								class="ui-input"
								v-model="donationForm.comment"
								type="text"
								maxlength="100"
								placeholder="Необязательно"
								:disabled="donationForm.processing"
							/>
						</label>
						<p v-if="donationForm.errors.comment" class="service-error" role="alert">{{ donationForm.errors.comment }}</p>
						<button type="submit" class="ui-button" :disabled="donationForm.processing">
							{{ donationForm.processing ? 'Отправляем…' : 'Пожертвовать' }}
						</button>
					</form>
				</div>
			</section>
			<section class="service-section">
				<header class="service-heading">
					<h2>Последние пожертвования</h2>
					<p>Последние 20 взносов игроков.</p>
				</header>
				<div v-if="page.donations.length" class="ui-table-wrap service-table-wrap">
					<table class="ui-table service-table">
						<thead>
							<tr>
								<th>Игрок</th>
								<th>Сумма</th>
								<th>Комментарий</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="donation in page.donations" :key="donation.id">
								<td>{{ donation.user }}</td>
								<td class="service-number">{{ donation.amount }} зол.</td>
								<td>{{ donation.comment || '—' }}</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div v-else class="ui-empty" role="status">
					<GameIcon name="gift" />
					<h3>Пожертвований пока нет</h3>
					<p>Здесь появятся взносы и пожелания игроков.</p>
				</div>
			</section>
		</div>
		<section v-else class="ui-panel service-panel service-panel--narrow">
			<header class="service-panel-heading">
				<GameIcon name="transfer" />
				<h2>Обмен платины на золото</h2>
			</header>
			<div class="service-panel-body">
				<div class="bank-exchange-rate">
					<span>
						<img src="/assets/images/currencies/platinum.png" alt="" />
						1 пл.
					</span>
					<GameIcon name="forward" />
					<span>
						<img src="/assets/images/currencies/gold.png" alt="" />
						{{ page.exchange_rate }} зол.
					</span>
				</div>
				<form class="service-form" @submit.prevent="exchange">
					<label class="service-field">
						<span>Сумма платины для обмена</span>
						<input
							class="ui-input"
							v-model="exchangeForm.amount"
							type="text"
							inputmode="decimal"
							maxlength="13"
							required
							placeholder="0,00"
							:disabled="exchangeForm.processing"
						/>
					</label>
					<p v-if="exchangeForm.errors.amount" class="service-error" role="alert">{{ exchangeForm.errors.amount }}</p>
					<div v-if="exchangeGold > 0" class="service-result">
						<span>Вы получите</span>
						<strong>{{ exchangeGold }} зол.</strong>
					</div>
					<button type="submit" class="ui-button" :disabled="exchangeForm.processing">
						{{ exchangeForm.processing ? 'Обмениваем…' : 'Обменять' }}
					</button>
				</form>
			</div>
		</section>
	</ContentBlock>
</template>

<script setup>
	import { computed } from 'vue';
	import { Link, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	const props = defineProps({ page: Object });
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
