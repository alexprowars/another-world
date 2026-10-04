<template>
	<div class="gambling-layout">
		<div class="service-section">
			<section class="ui-panel gambling-lottery">
				<header class="service-panel-heading">
					<GameIcon name="ticket" />
					<h2>Городская лотерея</h2>
				</header>
				<div class="service-panel-body">
					<div class="gambling-jackpot">
						<span>Призовой фонд</span>
						<strong><img src="/assets/images/currencies/gold.png" alt="" width="32" height="32" />{{ formatGold(lottery.prize) }} <small>зол.</small></strong>
						<p>Весь фонд достанется одному счастливому билету</p>
					</div>
					<div class="gambling-lottery-facts">
						<div>
							<span>Стоимость билета</span>
							<strong>{{ lottery.price }} зол.</strong>
						</div>
						<div>
							<span>Осталось билетов</span>
							<strong>{{ lottery.limit - lottery.sold }} <small>из {{ lottery.limit }}</small></strong>
						</div>
						<div>
							<span>Розыгрыш · время сервера</span>
							<strong>{{ lottery.draw_date }}</strong>
						</div>
					</div>
					<div class="gambling-ticket-progress"><span :style="{ width: (lottery.sold / lottery.limit) * 100 + '%' }"></span></div>
					<form class="service-form" @submit.prevent="buy">
						<button type="submit" class="ui-button" :disabled="form.processing || soldOut || !canBuy || closed">
							<GameIcon name="ticket" />
							{{ form.processing ? 'Покупаем билет…' : soldOut ? 'Все билеты проданы' : closed ? 'Розыгрыш закрыт' : 'Купить билет за ' + lottery.price + ' зол.' }}
						</button>
						<p v-if="!canBuy" class="service-error">Недостаточно золота для покупки билета.</p>
						<p v-if="closed" class="service-hint">Обновите страницу, чтобы перейти к следующему розыгрышу.</p>
						<p v-for="(error, field) in form.errors" :key="field" class="service-error" role="alert">{{ error }}</p>
					</form>
				</div>
			</section>

			<section class="ui-panel">
				<header class="service-panel-heading">
					<GameIcon name="ticket" />
					<h2>Ваши билеты <span class="ui-badge">{{ lottery.tickets.length }}</span></h2>
				</header>
				<div class="service-panel-body">
					<div v-if="lottery.tickets.length" class="gambling-tickets">
						<span v-for="ticket in lottery.tickets" :key="ticket" class="gambling-ticket">№ {{ ticket }}</span>
					</div>
					<p v-else class="service-hint">У вас пока нет билетов на этот розыгрыш.</p>
					<p v-if="lottery.pending_tickets" class="service-hint">Билетов в предыдущих розыгрышах, ожидающих результата: {{ lottery.pending_tickets }}. Выигрыш будет зачислен автоматически.</p>
				</div>
			</section>
		</div>

		<aside class="service-section">
			<section class="ui-panel gambling-rules">
				<header class="service-panel-heading">
					<GameIcon name="book" />
					<h2>Правила лотереи</h2>
				</header>
				<div class="service-panel-body">
					<p>Купите билет и получите свой номер. Можно купить несколько билетов: каждый участвует в розыгрыше.</p>
					<p>Каждый понедельник в 00:00 по времени сервера продажи закрываются. Среди купленных билетов случайно выбирается один победитель.</p>
					<p>Весь призовой фонд автоматически зачисляется победителю. Присутствовать в игре не нужно.</p>
				</div>
			</section>
		</aside>

		<section class="gambling-history service-section">
			<header class="service-heading">
				<h2>Победители прошлых розыгрышей</h2>
				<p>Последние десять счастливых билетов городской лотереи.</p>
			</header>
			<div v-if="lottery.history.length" class="ui-table-wrap">
				<table class="ui-table service-table">
					<thead>
						<tr>
							<th>Дата · время сервера</th>
							<th>Победитель</th>
							<th>Билет</th>
							<th>Выигрыш</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="draw in lottery.history" :key="draw.id">
							<td class="service-number">{{ draw.date }}</td>
							<td>
								<a v-if="draw.winner_id" :href="'/info/' + draw.winner_id" target="_blank" rel="noopener">{{ draw.winner }}</a>
								<span v-else>{{ draw.winner }}</span>
							</td>
							<td class="service-number">№ {{ draw.number }}</td>
							<td class="service-number"><strong>{{ formatGold(draw.prize) }} зол.</strong></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div v-else class="ui-empty">
				<GameIcon name="ticket" />
				<h3>Первый победитель ещё впереди</h3>
				<p>После розыгрыша здесь появятся имя победителя, номер билета и выигрыш.</p>
			</div>
		</section>
	</div>
</template>

<script setup>
	import useLocation from '~/composables/useLocation.js';

	import { computed, onBeforeUnmount, ref } from 'vue';
	import { useForm } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import useState from '~/composables/useState.js';

	const location = useLocation();

	const props = defineProps({
		lottery: Object,
	});

	const state = useState();
	const formatGold = value => Number(value).toLocaleString('ru-RU', { maximumFractionDigits: 2 });
	const currentTime = ref(Date.now());
	const clock = setInterval(() => currentTime.value = Date.now(), 1000);
	onBeforeUnmount(() => clearInterval(clock));

	const form = useForm({
		draw_id: props.lottery.id,
	});
	const canBuy = computed(() => Number(state.user.gold) >= props.lottery.price);
	const soldOut = computed(() => props.lottery.sold >= props.lottery.limit);
	const closed = computed(() => currentTime.value >= Date.parse(props.lottery.draws_at));

	function buy() {
		if (form.processing || soldOut.value || !canBuy.value || closed.value) {
			return;
		}

		form.draw_id = props.lottery.id;
		form.post(location.value.actions.ticket, {
			preserveScroll: true,
		});
	}
</script>
