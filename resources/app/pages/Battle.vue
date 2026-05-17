<template>
	<div class="max-w-[1000px] m-auto">
		<div v-if="data" class="battle battle-layout">
			<div class="battle-layout__fighter battle-layout__fighter--left">
				<BattleFighter v-if="data?.user" :fighter="data.user" :current="true"/>
			</div>

			<div class="battle-layout__center">
				<div class="impackForm">
					<div class="battle-form__title">Бой</div>
					<div class="battle-form__content">
						<div v-if="message" id="msg" class="text-center text-red-600 font-bold">
							{{ message }}
						</div>

						<div class="flex flex-col items-center gap-2 m-4 mt-2">
							<div v-if="data.action === 'finishBattle'" class="text-red-600 font-bold">
								<div>
									Ваш бой окончен.
									<template v-if="data.result === 'win'">Победа за вами!</template>
									<template v-if="data.result === 'draw'">Ничья!</template>
									<template v-if="data.result === 'lose'">Вы проиграли!</template>
								</div>
								<Link href="/person" class="standbut">
									Вернуться
								</Link>
							</div>
							<div v-if="data.action === 'waitImpact'" class="text-red-600 font-bold">
								Ожидаем хода противника...
							</div>
							<div v-if="data.action === 'userDead'" class="text-red-600 font-bold">
								Для вас бой окончен, подождите пока остальные игроки закончат поединок
							</div>

							<template v-if="data.action === 'impactForm' && !isFinished">
								<BattleImpactForm ref="impactForm"
									v-model:auto="autoGo"
									:blocks-count="data.blocks"
									:impacts-count="data.kicks"
									@complete="gofight"
								/>
								<BattleAbilities :abilities="data.abilities || null" @use="useAbility"/>
							</template>
						</div>

						<div v-show="loading" class="mt-3">
							<img src="/assets/images/refresh.gif" alt="">
						</div>

						<div v-show="!isFinished" class="text-center mt-4">
							До тайм-аута: <b>{{ timeoutText }}</b>
							<hr color="e2e0e0">
						</div>

						<div v-if="!isFinished" class="flex justify-center gap-4">
							<div class="text-center">
								<input type="button" value="Ударить" class="standbut" @click="gofight">
							</div>
							<div v-if="data.opponents.length > 1" class="text-center battle-change">
								<input type="button" value="Сменить" class="standbut" @click="toggleEnemyList">
								<div v-if="showEnemyList" id="oMen" class="battle-change__menu">
									<div class="battle-change__title">Выберите противника:</div>
									<div v-for="opponent in data.opponents" :key="opponent.id" class="battle-change__item">
										<a class="menuItem" href="" @click.prevent="selectEnemy(opponent.id)">{{ opponent.name }}</a>
									</div>
								</div>
							</div>
							<div v-show="!loading" class="text-center" id="refresh_b">
								<input type="button" value="Обновить" class="standbut" @click="loaderRefresh">
							</div>
						</div>
					</div>
				</div>

				<BattleUsers v-show="!isFinished" :users="data.teams"/>

				<div v-show="!isFinished" id="centerInfo" class="battle-info">
					<div class="battle-info__item">Нанесено урона: <u>{{ data.damage }}</u> HP</div>
					<div class="battle-info__item">Тайм-аут: <u>{{ data.timeout / 60 }}</u> мин.</div>
				</div>

				<div v-show="!isFinished" class="text-center mt-4">
					<b>Полный лог боя <a :href="'/logs/' + page.id" target="_blank"> тут</a></b>
					<hr color="e2e0e0">
				</div>
			</div>

			<div class="battle-layout__fighter battle-layout__fighter--right">
				<BattleFighter v-if="data?.opponent && !isFinished" :fighter="data.opponent"/>
				<div v-else class="battle-no-enemy">
					<b v-if="showNoEnemy">Нет противника в зоне досягаемости...</b><br v-if="showNoEnemy">
					<img src="/assets/images/battle/1.gif" width="210" :height="showNoEnemy ? 277 : 230" alt="">
				</div>
			</div>
		</div>

		<BattleLogs :logs="logs"/>
	</div>
</template>

<script setup>
	import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
	import { Link, useHttp } from '@inertiajs/vue3';
	import { toast } from 'vue3-toastify';
	import BattleFighter from '~/components/Battle/BattleFighter.vue';
	import BattleImpactForm from '~/components/Battle/BattleImpactForm.vue';
	import BattleLogs from '~/components/Battle/BattleLogs.vue';
	import BattleAbilities from '~/components/Battle/BattleAbilities.vue';
	import BattleUsers from '~/components/Battle/BattleUsers.vue';

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

	const lastLogId = computed(() => {
		let last = -1;

		data.value?.['logs'].forEach((item) => {
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