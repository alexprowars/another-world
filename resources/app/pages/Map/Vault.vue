<template>
	<ContentBlock title="Территория подземелья">
		<template #actions>
			<MovementLink v-if="page.is_entrance && !busy" :to="location.exit" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="location.url" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<header class="service-heading">
			<h2>{{ page.vault.title }}</h2>
			<p>Исследуйте переходы подземелья и добывайте полезные ископаемые.</p>
		</header>
		<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
			<p v-for="(error, field) in form.errors" :key="field">{{ error }}</p>
		</div>
		<div class="vault-layout" :class="{ 'vault-layout--busy': busy }">
			<section class="ui-panel service-panel vault-navigation">
				<header class="service-panel-heading">
					<GameIcon :name="busy ? 'hourglass' : 'pin'" />
					<h2>{{ busy ? 'В процессе' : 'Навигация' }}</h2>
				</header>
				<div class="service-panel-body">
					<template v-if="!busy">
						<div class="vault-compass">
							<button
								v-for="direction in directions"
								:key="direction.key"
								type="button"
								class="vault-direction"
								:class="direction.position"
								:disabled="!page.directions[direction.key] || form.processing || movementForm.processing"
								:title="page.directions[direction.key] ? 'Перейти в ' + page.directions[direction.key].title : 'Нет прохода'"
								@click="move(direction.key)"
							>
								<GameIcon name="forward" />
								<span>{{ direction.label }}</span>
							</button>
							<div class="vault-compass-center" title="Текущая комната"><GameIcon name="pin" /></div>
						</div>
						<p class="service-hint">Выберите открытый проход, чтобы перейти в соседнюю комнату.</p>
					</template>
					<template v-else>
						<p v-if="user.r_type === 10" class="service-hint">
							Переходим в
							<strong>{{ page.destination }}</strong>
							.
						</p>
						<p v-else-if="user.r_type === 8" class="service-hint">Добываем руду. Дождитесь окончания работы, чтобы получить находку.</p>
						<p v-else class="service-hint">Вы заняты работой.</p>
						<div v-if="user.r_date" class="service-timer">
							<span>Осталось времени</span>
							<Timer :key="user.r_date" :value="user.r_date" :callback="onTimeout" class="service-countdown" />
						</div>
						<button
							v-if="user.r_type === 8"
							type="button"
							class="ui-button ui-button--secondary"
							:disabled="form.processing"
							@click="act('unwork')"
						>
							Отменить добычу
						</button>
					</template>
				</div>
			</section>
			<section class="ui-panel service-panel vault-room">
				<header class="service-panel-heading">
					<GameIcon name="book" />
					<h2>Окрестности</h2>
				</header>
				<div class="service-panel-body vault-description" v-html="page.vault.text"></div>
			</section>
			<div v-if="!busy" class="vault-actions">
				<section class="ui-panel service-panel">
					<header class="service-panel-heading">
						<GameIcon name="health" />
						<h2>Колодец Жизни</h2>
					</header>
					<div class="service-panel-body">
						<p class="service-hint">{{ page.canHeal ? 'Вода из колодца полностью восстанавливает здоровье.' : 'Сейчас колодец пуст.' }}</p>
						<button type="button" class="ui-button" :disabled="!page.canHeal || form.processing || movementForm.processing" @click="act('heal')">
							Восстановить здоровье
						</button>
					</div>
				</section>
				<section class="ui-panel service-panel">
					<header class="service-panel-heading">
						<GameIcon name="tools" />
						<h2>Добыча руды</h2>
					</header>
					<div class="service-panel-body">
						<p class="service-hint">Для работы нужна надетая исправная кирка и 15 единиц сил.</p>
						<form class="service-form" @submit.prevent="dig">
							<img :src="page.captcha" width="140" height="48" alt="Код для добычи руды" class="vault-captcha" />
							<label class="service-field">
								<span>Код с картинки</span>
								<input
									class="ui-input"
									v-model="form.captcha"
									type="text"
									inputmode="numeric"
									autocomplete="off"
									maxlength="5"
									required
									:disabled="form.processing"
								/>
							</label>
							<button type="submit" class="ui-button" :disabled="form.processing">Добывать руду</button>
						</form>
					</div>
				</section>
			</div>
		</div>

		<template #footer>
			<GameIcon name="tools" />
			<span>При добыче руда или драгоценный камень появятся в инвентаре после завершения работы.</span>
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
	import Timer from '~/components/Timer.vue';
	import useState from '~/composables/useState.js';

	const location = useLocation();

	const props = defineProps({
		page: Object
	});

	const state = useState();

	const user = computed(() => state.user);
	const busy = computed(() => !!user.value.r_date || !!user.value.r_type);

	const form = useForm({
		captcha: ''
	});

	const movementForm = useForm({ location: '' });

	const directions = [
		{ key: 'top', label: 'Вперёд', position: 'vault-direction--top' },
		{ key: 'left', label: 'Налево', position: 'vault-direction--left' },
		{ key: 'right', label: 'Направо', position: 'vault-direction--right' },
		{ key: 'bottom', label: 'Назад', position: 'vault-direction--bottom' },
	];

	function act(action, data = {}) {
		if (form.processing) {
			return;
		}

		form.clearErrors();
		form.transform(() => data).post(location.value.actions[action], { onFinish: () => form.reset() });
	}

	function dig() {
		act('dig', { captcha: form.captcha });
	}

	function move(direction) {
		const destination = props.page.directions[direction];

		if (!destination || movementForm.processing) {
			return;
		}

		movementForm.location = destination.location;
		movementForm.post('/movement');
	}

	function onTimeout() {
		router.reload();
	}
</script>
