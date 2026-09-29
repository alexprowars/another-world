<template>
	<div class="service-split">
		<section class="ui-panel service-panel">
			<header class="service-panel-heading">
				<GameIcon name="justice" />
				<h2>Проверка инквизиторов</h2>
			</header>
			<div class="service-panel-body">
				<p class="service-hint">Обычно рассмотрение занимает около 24 часов. Если вы будете в игре, сообщение о результате придёт в чат.</p>
				<dl class="service-facts">
					<div>
						<dt>Стоимость заявки</dt>
						<dd>{{ requestPrice }} пл.</dd>
					</div>
					<div>
						<dt>Минимальный уровень</dt>
						<dd>{{ minLevel }}</dd>
					</div>
				</dl>
				<p v-if="hasRequest" class="service-status">
					<GameIcon name="hourglass" />
					Заявка подана
				</p>
				<form class="service-form" @submit.prevent="sendRequest">
					<button type="submit" class="ui-button" :class="{ 'ui-button--secondary': hasRequest }" :disabled="requestForm.processing">
						{{ hasRequest ? 'Отозвать заявку' : 'Подать заявку' }}
					</button>
					<p v-if="hasRequest" class="service-hint">При отзыве заявки плата не возвращается.</p>
					<p v-for="(error, field) in requestForm.errors" :key="field" class="service-error" role="alert">{{ error }}</p>
				</form>
			</div>
		</section>
		<section class="service-section">
			<header class="service-heading">
				<h2>Последние заявки</h2>
				<p>Последние 15 обращений игроков.</p>
			</header>
			<div v-if="requests.length" class="ui-table-wrap service-table-wrap">
				<table class="ui-table service-table">
					<thead>
						<tr>
							<th>№</th>
							<th>Игрок</th>
							<th>Состояние</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="entry in requests" :key="entry.id">
							<td>{{ entry.id }}</td>
							<td>{{ entry.user }}</td>
							<td>
								<span class="ui-badge">{{ statuses[entry.status] ?? 'Неизвестно' }}</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div v-else class="ui-empty" role="status">
				<GameIcon name="work" />
				<h3>Заявок пока нет</h3>
				<p>Здесь появятся обращения на проверку.</p>
			</div>
		</section>
	</div>
</template>

<script setup>
	import { useForm } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	const props = defineProps({
		requestPrice: Number,
		minLevel: Number,
		hasRequest: Boolean,
		requests: Array,
	});

	const statuses = ['На рассмотрении', 'Принято', 'Отклонено'];

	const requestForm = useForm({
		action: ''
	});

	function sendRequest() {
		if (requestForm.processing) {
			return;
		}

		requestForm.action = props.hasRequest ? 'withdraw' : 'submit';
		requestForm.post('/map?section=2', {
			preserveScroll: true
		});
	}
</script>
