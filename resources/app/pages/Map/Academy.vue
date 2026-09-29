<template>
	<ContentBlock title="Академия">
		<template #actions>
			<Link href="/map/change/9" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</Link>
			<Link href="/map" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
			<p v-for="(error, field) in form.errors" :key="field">{{ error }}</p>
		</div>
		<section v-if="user.r_date" class="ui-panel service-panel service-panel--narrow">
			<header class="service-panel-heading">
				<GameIcon name="book" />
				<h2>Обучение идёт</h2>
			</header>
			<div class="service-panel-body">
				<p class="service-hint">Осваивайте новое ремесло. Обучение завершится по истечении указанного времени.</p>
				<div class="service-timer">
					<span>До окончания обучения</span>
					<Timer :key="user.r_date" :value="user.r_date" :callback="onTimeout" class="service-countdown" />
				</div>
			</div>
		</section>
		<template v-else>
			<header class="service-heading">
				<h2>Выберите профессию</h2>
				<p>Освойте ремесло, которое откроет новые возможности в мире игры.</p>
			</header>
			<div v-if="page.professions.length" class="academy-professions">
				<ProfessionCard
					v-for="item in page.professions"
					:key="item.id"
					:profession="item"
					:processing="form.processing"
					:learning="form.processing && form.learn === item.id"
					@learn="learn(item)"
				/>
			</div>
			<div v-else class="ui-empty" role="status">
				<GameIcon name="book" />
				<h3>Нет доступных профессий</h3>
				<p>Загляните в академию позже.</p>
			</div>
		</template>

		<template #footer>
			<GameIcon name="book" />
			<span>Стоимость и длительность обучения зависят от выбранной профессии.</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import ContentBlock from '~/components/ContentBlock.vue';
	import ProfessionCard from '~/components/Academy/ProfessionCard.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import Timer from '~/components/Timer.vue';
	import { openConfirmModal } from '~/composables/useModals.js';
	import { Link, router, useForm } from '@inertiajs/vue3';

	defineProps({
		page: Object,
	});

	const state = useState();

	const user = computed(() => state.user);
	const form = useForm({
		learn: null
	});

	function learn(item) {
		if (form.processing) {
			return;
		}

		openConfirmModal('Подтвердите действие', 'Вы действительно хотите получить данную профессию?', [
			{
				title: 'Нет',
			},
			{
				title: 'Да',
				handler() {
					if (form.processing) {
						return;
					}

					form.learn = item.id;
					form.post('/map', { preserveScroll: true });
				},
			},
		]);
	}

	function onTimeout() {
		router.reload();
	}
</script>
