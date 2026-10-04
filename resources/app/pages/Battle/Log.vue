<template>
	<Head :title="'Журнал боя №' + page.battle.id" />
	<main class="battle-log-page">
		<header class="battle-log-header">
			<div class="battle-log-header__title">
				<GameIcon name="book" />
				<div>
					<h1>Журнал боя №{{ page.battle.id }}</h1>
					<p>{{ battleTypes[page.battle.type] }}</p>
				</div>
			</div>
			<span class="battle-log-status" :class="{ 'battle-log-status--active': page.battle.status === 'active' }">
				{{ statuses[page.battle.status] }}
			</span>
		</header>

		<div class="battle-log-summary">
			<p>
				Начало поединка
				<strong>{{ page.battle.startedAt ? $formatDate(page.battle.startedAt, 'DD.MM.YYYY HH:mm') : 'Бой ещё не начался' }}</strong>
			</p>
			<p v-if="resultText" class="battle-log-result">{{ resultText }}</p>
		</div>

		<section class="battle-journal">
			<header class="battle-journal__heading">
				<h2><GameIcon name="swords" /> Ход поединка</h2>
				<span>Последние раунды сверху</span>
			</header>
			<BattleLogs v-if="page.logs.length" :logs="page.logs" />
			<div v-else class="battle-log-empty">
				<GameIcon name="hourglass" />
				<p>В журнале пока нет событий.</p>
			</div>
		</section>

		<section v-if="page.members.length" class="battle-log-teams">
			<div v-for="team in teams" :key="team.side" class="battle-log-team" :class="'battle-log-team--' + team.side">
				<h2>{{ team.title }}</h2>
				<div class="battle-log-team__members">
					<a v-for="member in team.members" :key="member.id" :href="'/info/' + member.id" target="_blank">
						{{ member.name }} <span>[{{ member.level }}]</span>
					</a>
					<span v-if="!team.members.length">Нет участников</span>
				</div>
			</div>
		</section>

		<footer class="battle-log-footer">
			<button type="button" class="ui-button" :disabled="refreshing" @click="refresh">
				<GameIcon name="refresh" /> {{ refreshing ? 'Обновляем…' : 'Обновить журнал' }}
			</button>
		</footer>
	</main>
</template>

<script setup>
	import { computed, ref } from 'vue';
	import { Head, router } from '@inertiajs/vue3';
	import BattleLogs from '~/components/Battle/BattleLogs.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineOptions({
		layout: [],
	});

	const props = defineProps({
		page: Object,
	});

	const refreshing = ref(false);

	const battleTypes = {
		1: 'Дуэль',
		2: 'Групповой бой',
		3: 'Хаотический бой',
		4: 'Бой за склонность',
	};

	const statuses = {
		waiting: 'Ожидание начала',
		active: 'Бой продолжается',
		finished: 'Бой завершён',
		cancelled: 'Бой отменён',
	};

	const resultText = computed(() => ({
		1: 'Поединок закончился вничью',
		2: 'Победила вторая команда',
		3: 'Победила первая команда',
	})[props.page.battle.result] || '');

	const teams = computed(() => [
		{ side: 0, title: 'Первая команда', members: props.page.members.filter(member => member.side === 0) },
		{ side: 1, title: 'Вторая команда', members: props.page.members.filter(member => member.side === 1) },
	]);

	function refresh() {
		if (refreshing.value) {
			return;
		}

		refreshing.value = true;

		router.reload({
			only: ['page'],
			onFinish: () => {
				refreshing.value = false;
			},
		});
	}
</script>
