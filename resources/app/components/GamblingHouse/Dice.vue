<template>
	<div class="gambling-layout">
		<section class="gambling-dice-game">
			<div class="gambling-table" :class="{ 'is-rolling': form.processing }">
				<div class="gambling-table-title">Стол для игры в кости</div>
				<div class="gambling-players">
					<div class="gambling-player">
						<span class="gambling-player-label">Ваш бросок</span>
						<h3>{{ user.name }}</h3>
						<div class="gambling-dice-pair">
							<Die v-for="index in 2" :key="index" :value="result?.player[index - 1]" />
						</div>
						<p class="gambling-total">Сумма <strong>{{ playerTotal ?? '—' }}</strong></p>
					</div>
					<div class="gambling-versus">против</div>
					<div class="gambling-player">
						<span class="gambling-player-label">Бросок соперника</span>
						<h3>Тень</h3>
						<div class="gambling-dice-pair">
							<Die v-for="index in 2" :key="index" :value="result?.opponent[index - 1]" />
						</div>
						<p class="gambling-total">Сумма <strong>{{ opponentTotal ?? '—' }}</strong></p>
					</div>
				</div>
				<div class="gambling-table-note">{{ result ? 'Последний бросок · ставка ' + result.stake + ' зол.' : 'Выберите ставку и бросьте кубики' }}</div>
			</div>

			<div v-if="result" class="ui-notice gambling-result" :class="resultClass" role="status">
				<strong>{{ resultTitle }}</strong>
				<p>{{ resultText }}</p>
			</div>

			<form class="ui-panel gambling-bet" @submit.prevent="roll">
				<div class="gambling-bet-heading">
					<h3>Ваша ставка</h3>
					<span>Выигрыш: +{{ form.stake }} зол.</span>
				</div>
				<div class="gambling-stakes">
					<button
						v-for="stake in stakes"
						:key="stake"
						type="button"
						class="ui-button ui-button--secondary"
						:class="{ 'is-active': form.stake === stake }"
						:disabled="form.processing"
						@click="form.stake = stake"
					>
						{{ stake }} <small>зол.</small>
					</button>
				</div>
				<p v-for="(error, field) in form.errors" :key="field" class="service-error" role="alert">{{ error }}</p>
				<p v-if="!canPlay" class="service-error">Для этой ставки нужно {{ form.stake }} зол. Выберите меньшую ставку или пополните запас золота.</p>
				<button type="submit" class="ui-button gambling-roll-button" :disabled="form.processing || !canPlay">
					<GameIcon name="dice" />
					{{ form.processing ? 'Кубики брошены…' : result ? 'Бросить ещё раз' : 'Бросить кости' }}
				</button>
			</form>
		</section>

		<aside class="ui-panel gambling-rules">
			<header class="service-panel-heading">
				<GameIcon name="book" />
				<h2>Правила игры</h2>
			</header>
			<div class="service-panel-body">
				<p>Вы и Тень бросаете по два шестигранных кубика. Побеждает тот, чья сумма больше.</p>
				<dl class="gambling-rule-list">
					<div>
						<dt>Победа</dt>
						<dd>Получаете сумму ставки</dd>
					</div>
					<div>
						<dt>Поражение</dt>
						<dd>Теряете сумму ставки</dd>
					</div>
					<div>
						<dt>Ничья</dt>
						<dd>Золото остаётся у вас</dd>
					</div>
				</dl>
				<p class="service-hint">У обоих игроков одинаковые кубики: на каждом может выпасть от 1 до 6. Каждый бросок — новая игра с выбранной ставкой.</p>
			</div>
		</aside>
	</div>
</template>

<script setup>
	import useLocation from '~/composables/useLocation.js';

	import { computed } from 'vue';
	import { useForm } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import Die from './Die.vue';
	import useState from '~/composables/useState.js';

	const location = useLocation();

	const props = defineProps({
		stakes: Array,
		result: Object,
	});

	const state = useState();
	const user = computed(() => state.user);
	const form = useForm({
		action: 'dice',
		stake: props.result?.stake ?? props.stakes[0],
	});

	const canPlay = computed(() => Number(user.value.gold) >= form.stake);
	const playerTotal = computed(() => props.result?.player.reduce((sum, value) => sum + value, 0));
	const opponentTotal = computed(() => props.result?.opponent.reduce((sum, value) => sum + value, 0));
	const resultClass = computed(() => ({
		'ui-notice--green': props.result?.outcome === 'win',
		'ui-notice--red': props.result?.outcome === 'loss',
		'ui-notice--yellow': props.result?.outcome === 'draw',
	}));
	const resultTitle = computed(() => ({
		win: 'Удача на вашей стороне!',
		loss: 'Этот бросок за Тенью',
		draw: 'Ничья',
	})[props.result?.outcome]);
	const resultText = computed(() => {
		if (props.result?.outcome === 'win') {
			return 'Вы выиграли ' + props.result.stake + ' зол. Выигрыш уже зачислен.';
		}

		if (props.result?.outcome === 'loss') {
			return 'Вы проиграли ' + props.result.stake + ' зол. Ставка списана.';
		}

		return 'Суммы совпали. Ваше золото осталось при вас.';
	});

	function roll() {
		if (form.processing || !canPlay.value) {
			return;
		}

		form.post(location.value.actions.dice, {
			preserveScroll: true,
		});
	}
</script>
