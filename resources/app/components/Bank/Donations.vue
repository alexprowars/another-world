<template>
	<div class="service-split">
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
			<div v-if="donations.length" class="ui-table-wrap service-table-wrap">
				<table class="ui-table service-table">
					<thead>
						<tr>
							<th>Игрок</th>
							<th>Сумма</th>
							<th>Комментарий</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="donation in donations" :key="donation.id">
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
</template>

<script setup>
	import { useForm } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		donations: Array,
	});

	const donationForm = useForm({
		action: 'donate',
		amount: '',
		comment: ''
	});

	function donate() {
		if (donationForm.processing) {
			return;
		}

		donationForm.post('/map?section=1', {
			preserveScroll: true
		});
	}
</script>
