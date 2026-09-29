<template>
	<div class="battle-page">
		<div v-if="data" class="battle battle-layout">
			<div class="battle-layout__fighter battle-layout__fighter--left">
				<BattleFighter v-if="data?.user" :fighter="data.user" :current="true" />
			</div>

			<div class="battle-layout__center">
				<div class="battle-form">
					<header class="battle-form__title">
						<h1><GameIcon name="swords" /> Бой</h1>
						<span>{{ isFinished ? 'Завершён' : 'Раунд ' + data.round }}</span>
					</header>
					<div class="battle-form__content">
						<div v-if="message" id="msg" class="ui-notice ui-notice--red battle-message">
							{{ message }}
						</div>

						<div class="battle-form__selection">
							<div v-if="data.action === 'finishBattle'" class="battle-status" :class="{ 'battle-status--win': data.result === 'win' }">
								<GameIcon name="swords" />
								<p class="battle-status__label">Ваш бой окончен</p>
								<h2>
									<template v-if="data.result === 'win'">Победа за вами!</template>
									<template v-if="data.result === 'draw'">Ничья!</template>
									<template v-if="data.result === 'lose'">Вы проиграли!</template>
								</h2>
								<Link href="/person" class="ui-button ui-button--compact">Вернуться</Link>
							</div>
							<div v-if="data.action === 'waitImpact'" class="battle-status">
								<GameIcon name="hourglass" />
								<h2>Ход противника</h2>
								<p>Ваш ход сделан. Ожидаем ответа противника…</p>
							</div>
							<div v-if="data.action === 'userDead'" class="battle-status">
								<GameIcon name="shield" />
								<h2>Для вас бой окончен</h2>
								<p>Дождитесь, пока остальные игроки закончат поединок.</p>
							</div>

							<template v-if="data.action === 'impactForm' && !isFinished">
								<BattleImpactForm
									ref="impactForm"
									v-model:auto="autoGo"
									:blocks-count="data.blocks"
									:impacts-count="data.kicks"
									@complete="gofight"
								/>
								<BattleAbilities :abilities="data.abilities || null" @use="useAbility" />
								<div v-if="magicItems.length" class="battle-magic">
									<button v-for="item in magicItems" :key="item.id" type="button" class="ui-button ui-button--compact" @click="useMagic(item)">
										{{ item.title }}
									</button>
								</div>
							</template>
						</div>

						<div v-show="loading" class="battle-loading">
							<GameIcon name="refresh" /> Обновляем бой…
						</div>

						<div v-show="!isFinished" class="battle-timer">
							<GameIcon name="hourglass" />
							<span>До тайм-аута</span>
							<strong>{{ timeoutText || '—' }}</strong>
						</div>

						<div v-if="!isFinished" class="battle-actions">
							<div class="text-center">
								<button type="button" class="ui-button" :disabled="loading || data.action !== 'impactForm'" @click="gofight">
									<GameIcon name="swords" /> Ударить
								</button>
							</div>
							<div v-if="data.opponents.length > 1" class="text-center battle-change">
								<button type="button" class="ui-button ui-button--secondary" @click="toggleEnemyList">Сменить</button>
								<div v-if="showEnemyList" id="oMen" class="battle-change__menu">
									<div class="battle-change__title">Выберите противника:</div>
									<div v-for="opponent in data.opponents" :key="opponent.id" class="battle-change__item">
										<button type="button" class="battle-change__option" @click="selectEnemy(opponent.id)">{{ opponent.name }}</button>
									</div>
								</div>
							</div>
							<div v-show="!loading" class="text-center" id="refresh_b">
								<button type="button" class="ui-button ui-button--secondary" @click="loaderRefresh">
									<GameIcon name="refresh" /> Обновить
								</button>
							</div>
						</div>
					</div>
				</div>

				<BattleUsers v-show="!isFinished" :users="data.teams" />

				<div v-show="!isFinished" id="centerInfo" class="battle-info">
					<div class="battle-info__item">
						<span>Нанесено урона</span>
						<strong>{{ data.damage }} <small>HP</small></strong>
					</div>
					<div class="battle-info__item">
						<span>Тайм-аут</span>
						<strong>{{ data.timeout / 60 }} <small>мин.</small></strong>
					</div>
				</div>

				<a class="battle-log-link" :href="'/battle/log/' + page.id" target="_blank">
					<GameIcon name="book" /> Полный журнал боя <GameIcon name="forward" />
				</a>
			</div>

			<div class="battle-layout__fighter battle-layout__fighter--right">
				<BattleFighter v-if="data?.opponent && !isFinished" :fighter="data.opponent" />
				<div v-else class="battle-no-enemy">
					<p v-if="showNoEnemy">Нет противника в зоне досягаемости…</p>
					<img src="/assets/images/battle/1.gif" width="210" :height="showNoEnemy ? 277 : 230" alt="" />
				</div>
			</div>
		</div>

		<section v-if="logs.length" class="battle-journal">
			<header class="battle-journal__heading">
				<h2><GameIcon name="book" /> Ход поединка</h2>
				<span>События по раундам</span>
			</header>
			<BattleLogs :logs="logs" />
		</section>
	</div>
</template>

<script setup>
	import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
	import { Link, router, useHttp } from '@inertiajs/vue3';
	import { toast } from 'vue3-toastify';
	import BattleFighter from '~/components/Battle/BattleFighter.vue';
	import BattleImpactForm from '~/components/Battle/BattleImpactForm.vue';
	import BattleLogs from '~/components/Battle/BattleLogs.vue';
	import BattleAbilities from '~/components/Battle/BattleAbilities.vue';
	import BattleUsers from '~/components/Battle/BattleUsers.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import UseMagic from '~/components/Dialogs/UseMagic.vue';
	import { openPopupModal } from '~/composables/useModals.js';

	defineProps({
		page: Object,
	});

	const data = ref(null);
	const logs = ref([]);
	const selectedEnemy = ref(0);
	const message = ref('');
	const autoGo = ref(false);
	const loading = ref(false);
	const showEnemyList = ref(false);
	const timeoutText = ref('');
	const impactForm = ref(null);

	let refreshTimer;
	let timeoutTimer;

	const isFinished = computed(() => data.value?.action === 'finishBattle');
	const showNoEnemy = computed(() => !isFinished.value);
	const magicItems = computed(() => Object.values(data.value?.user?.items || {}).filter(item => item.can_use));

	function useMagic(item) {
		openPopupModal(UseMagic, {
			title: 'Использовать магию',
			item,
			selfName: data.value.user.name,
			opponentName: data.value.opponent?.name || '',
			battle: data.value.id,
			round: data.value.round,
			onUsed: loaderRefresh,
		});
	}

	const lastLogId = computed(() => {
		let last = -1;

		logs.value.forEach(item => {
			if (item.id > last) {
				last = item.id;
			}
		});

		return last;
	});

	onMounted(() => {
		loaderRefresh();
	});

	onBeforeUnmount(() => {
		clearTimeout(refreshTimer);
		clearTimeout(timeoutTimer);
	});

	function loaderRefresh() {
		refresh();
		clearTimeout(refreshTimer);
		refreshTimer = setTimeout(loaderRefresh, 45000);
	}

	async function refresh(extra = {}) {
		if (isFinished.value) {
			return;
		}

		loading.value = true;

		try {
			const result = await useHttp({
				lastLogId: lastLogId.value || 0,
				round: data.value?.round || 0,
				opponent: selectedEnemy.value || 0,
				...extra,
			}).get('/battle');

			await actionRefresh(result);
		} catch (e) {
			alert('Произошла ошибка при получении ответа от сервера');
			//window.location.href = '/battle/';
		}
	}

	async function useAbility(id) {
		await refresh({ ability: id });
		clearTimeout(refreshTimer);
		refreshTimer = setTimeout(loaderRefresh, 45000);
	}

	async function gofight() {
		const form = impactForm.value;

		if (!form?.isImpactsComplete()) {
			toast.warning('Поставьте удары');
			return false;
		}

		if (!form?.isBlocksComplete()) {
			toast.warning('Поставьте блоки');
			return false;
		}

		data.value.kicks = 0;
		data.value.blocks = 0;

		await refresh({
			opponent: selectedEnemy.value,
			...form.payload(),
			rnd: Math.random(),
		});

		clearTimeout(refreshTimer);
		refreshTimer = setTimeout(loaderRefresh, 45000);

		return true;
	}

	async function actionRefresh(res) {
		if (res.action === 'reload') {
			clearTimeout(refreshTimer);
			clearTimeout(timeoutTimer);
			router.visit('/battle');
			return;
		}

		if (res.action === 'refresh') {
			loaderRefresh();
			return;
		}

		data.value = res;

		message.value = res.m || '';

		if (res.opponent_id) {
			selectedEnemy.value = res.opponent_id;
		}

		if (res.logs.length) {
			logs.value = [...logs.value, ...res.logs];
		}

		if (res.action === 'finishBattle') {
			selectedEnemy.value = 0;

			clearTimeout(refreshTimer);
			clearTimeout(timeoutTimer);
			loading.value = false;
			return;
		}

		if (res.timeout_left) {
			startTimeout(res.timeout_left);
		}

		if (res.action === 'userDead') {
			selectedEnemy.value = 0;
		}

		loading.value = false;
		await nextTick();
	}

	function startTimeout(leftTime) {
		clearTimeout(timeoutTimer);
		tickTimeout(leftTime);
	}

	function tickTimeout(leftTime) {
		const next = leftTime - 1;

		if (next <= 0) {
			timeoutText.value = '';
			refresh();
			clearTimeout(refreshTimer);
			refreshTimer = setTimeout(loaderRefresh, 45000);
			return;
		}

		let sec = next % 60;
		let min = Math.floor(next / 60);

		if (sec < 10) {
			sec = `0${sec}`;
		}

		if (min > 60) {
			min -= Math.floor(min / 60) * 60;
		}

		if (min === 60) {
			min = 0;
		}

		timeoutText.value = `${min} мин. ${sec} сек.`;
		timeoutTimer = setTimeout(() => tickTimeout(next), 1000);
	}

	function toggleEnemyList() {
		showEnemyList.value = !showEnemyList.value;
	}

	function selectEnemy(id) {
		selectedEnemy.value = id;
		showEnemyList.value = false;
		refresh();
	}
</script>
