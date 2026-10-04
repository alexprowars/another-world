<template>
	<ContentBlock title="Больница">
		<template #actions>
			<button v-if="user.r_date" type="button" class="ui-icon-button" disabled title="Возвращение доступно после окончания лечения">
				<GameIcon name="back" />
			</button>
			<MovementLink v-else :to="location.exit" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="location.url" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<div class="hospital">
			<section class="hospital-recovery">
				<header class="hospital-heading">
					<div class="hospital-emblem"><GameIcon :name="user.r_date ? 'hourglass' : 'health'" /></div>
					<div>
						<h2 v-if="!page.can_heal">Лечение недоступно</h2>
						<h2 v-else>{{ user.r_date ? 'Лечение идёт' : page.time > 0 ? 'Восстановление здоровья' : 'Лечение не требуется' }}</h2>
						<p v-if="!page.can_heal">Для лечения нужны положительные выносливость и максимум здоровья.</p>
						<p v-else-if="user.r_date">Отдыхайте. Лекари позаботятся о вашем здоровье.</p>
						<p v-else-if="page.time > 0">Лекари ускорят восстановление здоровья перед следующим сражением.</p>
						<p v-else>Ваше здоровье полностью восстановлено. Вы готовы к новым сражениям.</p>
					</div>
				</header>

				<div class="hospital-health">
					<span class="hospital-label">Уровень жизни</span>
					<HpLine :current="user.hp_now" :max="user.hp_max" :regeneration="user.hp_regeneration" color="g_line" />
				</div>

				<div v-if="user.r_date" class="hospital-treatment">
					<div>
						<span class="hospital-label">До окончания лечения</span>
						<Timer :key="user.r_date" :value="user.r_date" :callback="onTimeout" class="hospital-countdown" />
					</div>
					<p class="hospital-hint">После лечения вы вернётесь в общий зал.</p>
				</div>
				<div v-else-if="page.can_heal && page.time > 0" class="hospital-treatment">
					<div>
						<span class="hospital-label">Длительность лечения</span>
						<strong class="hospital-duration">
							<GameIcon name="hourglass" />
							{{ $formatTime(page.time) }}
						</strong>
						<p class="hospital-hint">Без лечения: {{ $formatTime(page.natural_time) }}</p>
					</div>
					<button type="button" class="ui-button" :disabled="processing" @click="healAction">
						<GameIcon name="health" />
						{{ healForm.processing ? 'Начинаем лечение…' : 'Восстановиться' }}
					</button>
				</div>
			</section>

			<section v-if="user.injury" class="hospital-injury">
				<div class="hospital-injury-copy">
					<h3>
						<GameIcon name="health" />
						Лечение травмы
					</h3>
					<p>В больнице можно вылечить травму. Стоимость выше, чем у лекарей.</p>
				</div>
				<div class="hospital-injury-action">
					<span class="hospital-price">
						<GameIcon name="coins" />
						<strong>200 зол.</strong>
					</span>
					<button type="button" class="ui-button" :disabled="processing || user.gold < 200" @click="injuryAction">
						{{ injuryForm.processing ? 'Лечим травму…' : 'Вылечить травму' }}
					</button>
					<p v-if="user.gold < 200" class="hospital-hint hospital-hint--error">Недостаточно золота</p>
				</div>
			</section>
		</div>

		<template #footer>
			<GameIcon name="health" />
			<span v-if="!page.can_heal">Дождитесь окончания ослабляющих эффектов.</span>
			<span v-else>{{ user.r_date ? 'Покинуть больницу можно после окончания лечения.' : 'Чем меньше здоровья осталось, тем дольше займёт лечение.' }}</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import MovementLink from '~/components/City/MovementLink.vue';
	import useLocation from '~/composables/useLocation.js';

	import { computed } from 'vue';
	import { Link, router, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import HpLine from '~/components/Person/HpLine.vue';
	import Timer from '~/components/Timer.vue';
	import useState from '~/composables/useState.js';

	const location = useLocation();

	defineProps({
		page: Object
	});

	const state = useState();
	const user = computed(() => state.user);

	const healForm = useForm({
		heal: 'Y'
	});

	const injuryForm = useForm({
		injury: 'Y'
	});

	const processing = computed(() => healForm.processing || injuryForm.processing);

	function healAction() {
		if (processing.value) {
			return;
		}

		healForm.post(location.value.actions.heal, {
			preserveScroll: true
		});
	}

	function injuryAction() {
		if (processing.value) {
			return;
		}

		injuryForm.post(location.value.actions.injury, {
			preserveScroll: true
		});
	}

	function onTimeout() {
		router.reload();
	}
</script>
