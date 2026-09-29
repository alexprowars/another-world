<template>
	<ContentBlock title="Тренировочный зал для новичков" class="arena-training">
		<template #actions>
			<Link v-if="user.room === 2" href="/map/change/2" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</Link>
			<Link href="/map" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
			<p v-for="(error, field) in form.errors" :key="field">{{ error }}</p>
		</div>

		<div v-if="!page.players.length" class="ui-empty" role="status">
			<GameIcon name="shield" />
			<h3>Вы готовы к новым поединкам</h3>
			<p>В тренировочном зале больше нет подходящих соперников для вашего уровня.</p>
			<Link v-if="user.room === 2" href="/map/change/2" class="ui-button training-return">
				<GameIcon name="back" />
				Вернуться в общий зал
			</Link>
		</div>
		<div v-else class="training-layout">
			<section class="arena-section training-opponents">
				<header class="arena-section-heading">
					<h2>
						Выберите соперника
						<span class="arena-count">{{ page.players.length }}</span>
					</h2>
				</header>
				<p class="arena-hint">Отработайте удары и защиту в поединке с тренировочным ботом.</p>
				<ul class="ui-panel training-roster">
					<li v-for="player in page.players" :key="player.id" class="training-opponent">
						<div class="ui-emblem"><GameIcon name="character" /></div>
						<div class="training-opponent-info">
							<span class="training-opponent-label">Тренировочный бот</span>
							<h3>{{ player.name }}</h3>
							<span class="training-opponent-level">Уровень {{ player.level }}</span>
						</div>
						<button type="button" class="ui-button" :disabled="form.processing" @click="fightTo(player.id)">
							<GameIcon :name="form.processing && form.fight === player.id ? 'hourglass' : 'swords'" />
							{{ form.processing && form.fight === player.id ? 'Начинаем бой…' : 'Начать бой' }}
						</button>
					</li>
				</ul>
			</section>

			<aside class="training-guide">
				<header class="training-guide-heading">
					<GameIcon name="book" />
					<h2>Как вести бой</h2>
				</header>
				<ol class="training-steps">
					<li>
						<h3>Выберите атаку</h3>
						<p>Отметьте зоны удара в колонке «Атака». Счётчик показывает, сколько ударов осталось распределить.</p>
					</li>
					<li>
						<h3>Поставьте защиту</h3>
						<p>В колонке «Защита» выберите зоны, которые хотите закрыть блоком.</p>
					</li>
					<li>
						<h3>Сделайте ход</h3>
						<p>Нажмите «Ударить» и дождитесь ответа соперника. Затем выберите удары и блоки для следующего хода.</p>
					</li>
				</ol>
				<div class="training-guide-tip">
					<GameIcon name="users" />
					<p>Если соперников несколько, нажмите «Сменить», чтобы выбрать другого противника.</p>
				</div>
			</aside>
		</div>

		<template #footer>
			<GameIcon name="swords" />
			<span>В тренировочном зале можно сражаться с ботами своего уровня или выше.</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import { computed } from 'vue';
	import { Link, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import useState from '~/composables/useState.js';

	defineProps({
		page: Object
	});

	const state = useState();

	const user = computed(() => state.user);

	const form = useForm({
		fight: null
	});

	function fightTo(id) {
		if (form.processing) {
			return;
		}

		form.fight = id;
		form.clearErrors();
		form.post('', { preserveScroll: true });
	}
</script>
