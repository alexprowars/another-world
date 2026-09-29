<template>
	<Head :title="error.title" />
	<main class="error-page">
		<div class="error-page__content">
			<a href="/" class="error-page__brand">
				<GameIcon name="city" />
				<span>Another World</span>
			</a>

			<section class="error-card">
				<div class="error-card__emblem">
					<GameIcon :name="error.icon" />
					<span class="error-card__code">{{ page.status }}</span>
					<span class="error-card__label">Ошибка</span>
				</div>
				<div class="error-card__body">
					<p class="error-card__eyebrow">{{ error.caption }}</p>
					<h1>{{ error.title }}</h1>
					<p class="error-card__description">{{ error.description }}</p>
					<p v-if="accessMessage" class="error-card__notice">{{ accessMessage }}</p>

					<div class="error-card__actions">
						<a href="/" class="ui-button">
							<GameIcon name="city" /> На главную
						</a>
						<button v-if="page.status >= 500" type="button" class="ui-button ui-button--secondary" @click="reload">
							<GameIcon name="refresh" /> Попробовать снова
						</button>
						<button v-else type="button" class="ui-button ui-button--secondary" @click="goBack">
							<GameIcon name="back" /> Вернуться назад
						</button>
					</div>
				</div>
			</section>

			<details v-if="page.trace" class="error-page__details">
				<summary>Технические подробности</summary>
				<div class="error-page__diagnostics">
					<p v-if="page.message">{{ page.message }}</p>
					<pre>{{ page.trace }}</pre>
				</div>
			</details>
		</div>
	</main>
</template>

<script setup>
	import { computed } from 'vue';
	import { Head } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineOptions({
		layout: [],
	});

	const props = defineProps({
		page: Object,
	});

	const errors = {
		403: {
			icon: 'shield',
			caption: 'Проход закрыт',
			title: 'Доступ запрещён',
			description: 'У вас нет доступа к этой странице. Вернитесь назад или продолжите с главной.',
		},
		404: {
			icon: 'pin',
			caption: 'За пределами карты',
			title: 'Страница не найдена',
			description: 'Возможно, ссылка устарела или в адресе есть ошибка. Вернитесь назад или начните путь с главной страницы.',
		},
		500: {
			icon: 'tools',
			caption: 'Непредвиденная преграда',
			title: 'Ошибка сервера',
			description: 'Не удалось загрузить страницу. Попробуйте ещё раз через некоторое время.',
		},
		503: {
			icon: 'hourglass',
			caption: 'Небольшая передышка',
			title: 'Мир временно недоступен',
			description: 'Сервер временно не принимает запросы. Попробуйте вернуться немного позже.',
		},
	};

	const error = computed(() => errors[props.page.status] || {
		icon: 'shield',
		caption: 'Преграда на пути',
		title: 'Не удалось открыть страницу',
		description: 'Вернитесь на предыдущую страницу или продолжите с главной.',
	});

	const accessMessage = computed(() => {
		const message = props.page.message?.trim();

		return Number(props.page.status) === 403 && message && !['Forbidden', 'Forbidden.', 'This action is unauthorized.'].includes(message)
			? message
			: '';
	});

	function reload() {
		window.location.reload();
	}

	function goBack() {
		if (window.history.length > 1) {
			window.history.back();
			return;
		}

		window.location.assign('/');
	}
</script>
