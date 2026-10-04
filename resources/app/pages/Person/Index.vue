<template>
	<div class="person-overview">
		<header class="person-page-heading">
			<div>
				<h1>Обзор персонажа</h1>
				<p>Результаты ваших боёв и боевая статистика.</p>
			</div>
		</header>

		<section class="person-overview-section">
			<div class="person-section-heading-row">
				<h2 class="person-section-heading"><GameIcon name="swords" />Последние бои</h2>
				<span v-if="page.recent_battles.length" class="person-section-count">Последние {{ page.recent_battles.length }}</span>
			</div>
			<ul v-if="page.recent_battles.length" class="person-recent-battles">
				<li v-for="battle in page.recent_battles" :key="battle.id" class="person-recent-battle">
					<div class="person-recent-battle-heading">
						<a :href="'/battle/log/' + battle.id" target="_blank" rel="noopener">{{ battleTypes[battle.type] }} №{{ battle.id }}</a>
						<span class="person-battle-result" :class="'person-battle-result--' + battle.result">{{ battleResults[battle.result] }}</span>
					</div>
					<div class="person-recent-battle-details">
						<time v-if="battle.started_at" :datetime="battle.started_at">{{ $formatDate(battle.started_at, 'DD.MM.YYYY HH:mm') }}</time>
						<span>Урон: <b>{{ battle.damage }} HP</b></span>
						<span>Опыт: <b>+{{ battle.experience_reward }}</b></span>
						<a :href="'/battle/log/' + battle.id" target="_blank" rel="noopener">Журнал боя <GameIcon name="forward" /></a>
					</div>
				</li>
			</ul>
			<div v-else class="person-empty-state">
				<b>Завершённых боёв пока нет</b>
				<p>После первого поединка здесь появятся его результат и журнал.</p>
			</div>
		</section>

		<section class="person-overview-section">
			<h2 class="person-section-heading"><GameIcon name="shield" />Боевая статистика</h2>
			<dl class="person-battle-statistics">
				<div>
					<dt>Всего боёв</dt>
					<dd>{{ totalBattles }}</dd>
				</div>
				<div class="person-battle-statistic--win">
					<dt>Победы</dt>
					<dd>{{ page.statistics.wins }}</dd>
				</div>
				<div class="person-battle-statistic--lose">
					<dt>Поражения</dt>
					<dd>{{ page.statistics.losses }}</dd>
				</div>
				<div>
					<dt>Ничьи</dt>
					<dd>{{ page.statistics.draws }}</dd>
				</div>
				<div>
					<dt>Доля побед</dt>
					<dd>{{ winRate }}<small>%</small></dd>
				</div>
				<div>
					<dt>Крутизна</dt>
					<dd>{{ page.statistics.rating }}</dd>
				</div>
			</dl>
		</section>
	</div>
</template>

<script setup>
	import { computed } from 'vue';
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineOptions({
		layout: [GameLayout, PersonLayout],
	});

	const props = defineProps({
		page: Object,
	});

	const totalBattles = computed(() => props.page.statistics.wins + props.page.statistics.losses + props.page.statistics.draws);
	const winRate = computed(() => (totalBattles.value > 0 ? Math.round(props.page.statistics.wins / totalBattles.value * 100) : 0));

	const battleTypes = {
		1: 'Дуэль',
		2: 'Групповой бой',
		3: 'Хаотический бой',
		4: 'Бой за склонность',
	};

	const battleResults = {
		win: 'Победа',
		lose: 'Поражение',
		draw: 'Ничья',
	};
</script>
