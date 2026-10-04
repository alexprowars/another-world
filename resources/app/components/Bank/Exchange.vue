<template>
	<section class="ui-panel service-panel service-panel--narrow">
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
					{{ exchangeRate }} зол.
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
</template>

<script setup>
	import useLocation from '~/composables/useLocation.js';

	import { computed } from 'vue';
	import { useForm } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	const location = useLocation();

	const props = defineProps({
		exchangeRate: Number,
	});

	const exchangeForm = useForm({
		action: 'exchange',
		amount: ''
	});

	const exchangeGold = computed(() => {
		const amount = Number(exchangeForm.amount.replace(',', '.'));
		return Number.isFinite(amount) && amount > 0 ? Math.round(amount * props.exchangeRate * 100) / 100 : 0;
	});

	function exchange() {
		if (exchangeForm.processing) {
			return;
		}

		exchangeForm.post(location.value.actions.exchange, {
			preserveScroll: true
		});
	}
</script>
