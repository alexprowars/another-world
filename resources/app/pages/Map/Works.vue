<template>
	<ContentBlock title="Центр занятости">
		<template #actions>
			<button v-if="busy" type="button" class="ui-icon-button" disabled title="Возвращение доступно после окончания работы">
				<GameIcon name="back" />
			</button>
			<MovementLink v-else :to="location.exit" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="location.url" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<div v-if="busy" class="works-active">
			<div class="works-active-seal"><GameIcon name="hourglass" /></div>
			<h2>Работа идёт</h2>
			<p class="works-active-description">Ваш труд будет оплачен по окончании работы.</p>
			<div class="works-active-details">
				<div v-if="user.r_date" class="works-active-stat">
					<span>Осталось времени</span>
					<Timer :key="user.r_date" :value="user.r_date" :callback="onTimeout" class="works-countdown" />
				</div>
				<div v-if="page.salary !== null" class="works-active-stat">
					<span>Награда по завершении</span>
					<strong class="works-active-reward">
						<GameIcon name="coins" />
						{{ page.salary }}
						<small>зол.</small>
					</strong>
				</div>
			</div>
		</div>

		<div v-else class="works-board">
			<div class="works-board-heading">
				<p class="works-eyebrow">Городские поручения</p>
				<h2>Доска работ</h2>
			</div>

			<section v-for="type in page.types" :key="type.id" class="works-group">
				<header class="works-group-heading">
					<h3>
						<GameIcon name="work" />
						{{ type.title }}
					</h3>
					<div class="works-requirements">
						<span>
							<GameIcon name="shield" />
							От {{ type.level }} уровня
						</span>
						<span>
							<GameIcon name="energy" />
							{{ type.activity }} ед. сил / час
						</span>
					</div>
				</header>
				<ul v-if="type.works.length" class="works-list">
					<WorkEntry
						v-for="(work, index) in type.works"
						:key="work.id"
						:work="work"
						:number="index + 1"
						:processing="form.processing"
						@start="start(work)"
					/>
				</ul>
				<p v-else class="works-empty">В этой категории пока нет работы.</p>
			</section>
			<p v-if="!page.types.length" class="works-empty">Новых поручений пока нет. Загляните позже.</p>
		</div>
		<template #footer>
			<GameIcon name="book" />
			<span>
				{{ busy ? 'Вернуться в город можно после окончания работы.' : 'Для работы нужны силы. Восстановить их можно в боях.' }}
			</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import MovementLink from '~/components/City/MovementLink.vue';
	import useLocation from '~/composables/useLocation.js';

	import { computed } from 'vue';
	import { Link, router, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import WorkEntry from '~/components/Works/WorkEntry.vue';
	import Timer from '~/components/Timer.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	const location = useLocation();

	defineProps({
		page: Object
	});

	const state = useState();

	const user = computed(() => state.user);
	const busy = computed(() => !!user.value.r_date || !!user.value.r_type);

	const form = useForm({
		work_id: null
	});

	function start(work) {
		openConfirmModal('Подтвердите действие', 'Вы действительно хотите получить данную работу?', [
			{
				title: 'Нет',
			},
			{
				title: 'Да',
				handler() {
					form.work_id = work.id;
					form.post(location.value.actions.work);
				},
			},
		]);
	}

	function onTimeout() {
		router.reload();
	}
</script>
