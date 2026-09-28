<template>
	<ContentBlock title="Центр занятости">
		<template #actions>
			<button v-if="busy" type="button" class="ui-icon-button" disabled title="Возвращение доступно после окончания работы">
				<GameIcon name="back" />
			</button>
			<Link v-else href="/map/change/16" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</Link>
			<Link href="/map" class="ui-icon-button" title="Обновить">
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
					<li v-for="(work, index) in type.works" :key="work.id" class="works-entry">
						<div class="works-entry-title">
							<span class="works-entry-number">{{ String(index + 1).padStart(2, '0') }}</span>
							<h4>{{ work.title }}</h4>
						</div>
						<div class="works-entry-stat">
							<span class="works-stat-label">Срок работы</span>
							<span class="works-stat-value">
								<GameIcon name="hourglass" />
								{{ $formatTime(work.duration) }}
							</span>
						</div>
						<div class="works-entry-stat">
							<span class="works-stat-label">Награда</span>
							<strong class="works-stat-value works-reward">
								<GameIcon name="coins" />
								{{ work.price }} зол.
							</strong>
						</div>
						<button type="button" class="ui-button" :disabled="form.processing" @click="start(work)">
							Работать
							<GameIcon name="forward" />
						</button>
					</li>
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
	import { computed } from 'vue';
	import { Link, router, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import Timer from '~/components/Timer.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	defineProps({ page: Object });

	const state = useState();
	const user = computed(() => state.user);
	const busy = computed(() => !!user.value.r_date || !!user.value.r_type);
	const form = useForm({ work: null });

	function start(work) {
		openConfirmModal('Подтвердите действие', 'Вы действительно хотите получить данную работу?', [
			{
				title: 'Нет',
			},
			{
				title: 'Да',
				handler() {
					form.work = work.id;
					form.post('/map');
				},
			},
		]);
	}

	function onTimeout() {
		router.reload();
	}
</script>
