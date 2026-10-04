<template>
	<ContentBlock title="Домик Знахаря">
		<template #actions>
			<MovementLink :to="location.exit" class="ui-icon-button" title="Вернуться на Королевскую улицу">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="tabUrl" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<p class="storefront-description">Измените характеристики, рассеяйте магию тени или приготовьте зелья по старинным рецептам.</p>
		<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
			<p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
		</div>

		<nav class="ui-tabs healer-tabs">
			<Link
				v-for="tab in tabs"
				:key="tab.id"
				:href="location.url + '/' + tab.id"
				class="ui-tab"
				:class="{ 'is-active': page.tab === tab.id }"
			>
				<GameIcon :name="tab.icon" />
				{{ tab.title }}
			</Link>
		</nav>

		<div v-if="page.tab === 'services'" class="service-section">
			<section class="service-panel healer-panel">
				<header class="service-panel-heading">
					<GameIcon name="shuffle" />
					<h2>Перераспределение характеристик</h2>
				</header>
				<div class="service-panel-body">
					<p class="service-hint">Перенесите одно очко из одной базовой характеристики в другую. Исходный параметр должен остаться не ниже 1.</p>
					<div class="healer-stat-list">
						<div v-for="stat in page.stats" :key="stat" class="healer-stat">
							<img :src="'/assets/images/stats/' + stat + '.png'" alt="" />
							<span>{{ $t('stats.' + stat) }}</span>
							<strong>{{ user.base_stats[stat] }}</strong>
						</div>
					</div>
					<form class="healer-transfer" @submit.prevent="moveStat">
						<label class="service-field">
							<span>Откуда перенести</span>
							<select v-model="form.from" class="ui-input">
								<option v-for="stat in page.stats" :key="stat" :value="stat">{{ $t('stats.' + stat) }}</option>
							</select>
						</label>
						<label class="service-field">
							<span>Куда перенести</span>
							<select v-model="form.to" class="ui-input">
								<option v-for="stat in page.stats" :key="stat" :value="stat">{{ $t('stats.' + stat) }}</option>
							</select>
						</label>
						<button type="submit" class="ui-button" :disabled="!canMove">Перенести за {{ page.move_stat_price }} зол.</button>
					</form>
					<p v-if="form.from === form.to" class="service-hint">Выберите разные характеристики.</p>
					<p v-else-if="user.base_stats[form.from] <= 1" class="service-hint">Выбранную характеристику больше нельзя уменьшить.</p>
					<p v-else-if="user.gold < page.move_stat_price" class="service-error">Недостаточно золота.</p>
				</div>
			</section>

			<div class="healer-services">
				<section class="service-panel healer-panel">
					<header class="service-panel-heading"><GameIcon name="clan" /><h2>Выход из клана</h2></header>
					<div class="service-panel-body">
						<p class="service-hint">Покинуть клан можно с действующей проверкой инквизиторов. Главе нужно сначала передать полномочия.</p>
						<p v-if="!user.tribe" class="service-hint">Вы не состоите в клане.</p>
						<p v-else-if="!page.can_leave_tribe" class="service-hint">Глава клана не может воспользоваться этой услугой.</p>
						<button type="button" class="ui-button" :disabled="form.processing || !page.can_leave_tribe || user.gold < page.leave_tribe_price" @click="confirmAction('leave_tribe', 'Выход из клана', 'Покинуть клан за ' + page.leave_tribe_price + ' зол.?')">
							Покинуть клан за {{ page.leave_tribe_price }} зол.
						</button>
					</div>
				</section>
				<section class="service-panel healer-panel">
					<header class="service-panel-heading"><GameIcon name="energy" /><h2>Рассеять магию тени</h2></header>
					<div class="service-panel-body">
						<p class="service-hint">Знахарь снимет действующую невидимость. Эффект прекратится сразу.</p>
						<p v-if="!page.can_dispel" class="service-hint">На вас нет магии тени.</p>
						<button type="button" class="ui-button" :disabled="form.processing || !page.can_dispel || user.gold < page.dispel_price" @click="confirmAction('dispel', 'Рассеять тень', 'Снять невидимость за ' + page.dispel_price + ' зол.?')">
							Рассеять за {{ page.dispel_price }} зол.
						</button>
					</div>
				</section>
			</div>
		</div>

		<section v-else class="service-section">
			<header class="service-heading">
				<h2>Алхимическая мастерская</h2>
				<p>Для одного зелья нужен полный набор ингредиентов. Оплата за изготовление не взимается.</p>
			</header>
			<p v-if="!page.can_craft" class="ui-notice ui-notice--blue">Сварить зелье может только алхимик. Профессию можно получить в Академии.</p>
			<div v-if="page.recipes.length" class="storefront-grid">
				<CatalogItem v-for="recipe in page.recipes" :key="recipe.id" :item="recipe" :player="user">
					<template #details>
						<div class="healer-ingredients">
							<h4>Ингредиенты</h4>
							<dl class="storefront-item-stats">
								<div v-for="ingredient in recipe.ingredients" :key="ingredient.code" :class="{ 'is-unmet': !ingredient.found || ingredient.available < ingredient.quantity }">
									<dt>{{ ingredient.title }}</dt>
									<dd>{{ ingredient.available }} / {{ ingredient.quantity }}</dd>
								</div>
							</dl>
						</div>
					</template>
					<template #actions>
						<button type="button" class="ui-button" :disabled="form.processing || !page.can_craft || !recipe.available" @click="confirmAction('craft', 'Сварить зелье', 'Изготовить «' + recipe.item.title + '»? Указанные ингредиенты будут израсходованы.', recipe.id)">
							{{ recipe.available ? 'Сварить зелье' : 'Не хватает ингредиентов' }}
						</button>
					</template>
				</CatalogItem>
			</div>
			<div v-else class="ui-empty"><GameIcon name="book" /><h3>Рецептов пока нет</h3><p>Загляните в мастерскую позже.</p></div>
		</section>

		<template #footer>
			<GameIcon name="book" />
			<span>Для зелий подходят доступные ингредиенты из рюкзака. Экипированные, подаренные и клановые вещи не расходуются.</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import MovementLink from '~/components/City/MovementLink.vue';
	import useLocation from '~/composables/useLocation.js';

	import { computed, watch } from 'vue';
	import { Link, useForm } from '@inertiajs/vue3';
	import { useI18n } from 'vue-i18n';
	import { escape } from 'lodash-es';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import CatalogItem from '~/components/City/Shop/CatalogItem.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	const location = useLocation();

	const props = defineProps({ page: { type: Object, required: true } });
	const { t } = useI18n();
	const state = useState();
	const user = computed(() => state.user);
	const tabUrl = computed(() => location.value.url + '/' + props.page.tab);
	const tabs = [
		{ id: 'services', title: 'Знахарская', icon: 'shuffle' },
		{ id: 'alchemy', title: 'Алхимка', icon: 'book' },
	];
	const form = useForm({ from: 'strength', to: 'agility', id: null });
	const canMove = computed(() => !form.processing && form.from !== form.to && user.value.base_stats[form.from] > 1 && user.value.gold >= props.page.move_stat_price);

	watch(() => props.page.tab, () => form.clearErrors());

	function submit(action, id = null) {
		if (form.processing) return;

		form.id = id;
		form.post(location.value.actions[action], { preserveScroll: true });
	}

	function confirmAction(action, title, message, id = null) {
		if (form.processing) return;

		openConfirmModal(title, escape(message), [
			{ title: 'Отмена' },
			{ title: 'Подтвердить', handler: () => submit(action, id) },
		]);
	}

	function moveStat() {
		if (!canMove.value) return;

		confirmAction('move_stat', 'Перераспределить характеристику', 'Перенести одно очко из «' + t('stats.' + form.from) + '» в «' + t('stats.' + form.to) + '» за ' + props.page.move_stat_price + ' зол.?');
	}
</script>
