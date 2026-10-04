<template>
	<ContentBlock title="Амбар">
		<template #actions>
			<MovementLink :to="location.exit" class="ui-icon-button" title="Вернуться на Промышленную улицу">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="tabUrl" class="ui-icon-button" title="Обновить отдел">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<p class="storefront-description">Город принимает добытые ресурсы и снабжает мастеров инструментами для работы.</p>
		<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
			<p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
		</div>

		<div class="storefront-toolbar">
			<nav class="ui-tabs storefront-modes">
				<Link :href="location.url + '/resources'" class="ui-tab" :class="{ 'is-active': selling }">
					<GameIcon name="gem" />
					Сдача ресурсов
				</Link>
				<Link :href="location.url + '/tools'" class="ui-tab" :class="{ 'is-active': !selling }">
					<GameIcon name="tools" />
					Инструменты
				</Link>
			</nav>
		</div>

		<div v-if="selling" class="service-terms">
			<div>
				<span>Принимаем</span>
				<strong>Ресурсы и драгоценные камни</strong>
			</div>
			<div>
				<span>Цена приёма ресурсов</span>
				<strong>{{ page.resource_sell_percent }}% цены каталога</strong>
			</div>
		</div>

		<section class="storefront-catalog">
			<header class="storefront-catalog-header">
				<div>
					<p class="storefront-eyebrow">{{ selling ? 'Ваш инвентарь' : 'Для работы и добычи' }}</p>
					<h2>
						{{ selling ? 'Ресурсы для сдачи' : 'Рабочие инструменты' }}
						<span class="ui-badge">{{ filteredItems.length }}</span>
					</h2>
				</div>
				<label class="storefront-search">
					<span>Поиск в отделе</span>
					<input v-model="search" type="search" class="ui-input" placeholder="Название предмета" />
				</label>
			</header>
			<p class="storefront-description">
				{{ selling ? 'Выберите ресурс, который хотите сдать в Амбар.' : 'Купленный инструмент сразу появится в вашем инвентаре.' }}
			</p>
			<div v-if="filteredItems.length" class="storefront-grid">
				<CatalogItem
					v-for="entry in filteredItems"
					:key="entry.item.id"
					:item="selling ? entry.item : entry"
					:player="user"
					:inventory-item="selling"
				>
					<template #price>
						<div class="storefront-item-price">
							<span>{{ selling ? 'Цена приёма' : 'Цена покупки' }}</span>
							<strong>
								<img :src="'/assets/images/currencies/' + (platinum(entry) ? 'platinum' : 'gold') + '.png'" alt="" />
								{{ entry.price }}
								<small>{{ platinum(entry) ? 'пл.' : 'зол.' }}</small>
							</strong>
						</div>
					</template>
					<template #actions>
						<button type="button" class="ui-button" :disabled="form.processing" @click="trade(entry)">
							{{ selling ? 'Сдать' : 'Купить' }}
						</button>
					</template>
				</CatalogItem>
			</div>
			<div v-else class="ui-empty" role="status">
				<GameIcon :name="selling ? 'gem' : 'tools'" />
				<h3>{{ search.trim() ? 'Ничего не найдено' : selling ? 'Нет ресурсов для сдачи' : 'Инструментов пока нет' }}</h3>
				<p>
					{{
						search.trim()
							? 'Попробуйте другое название предмета.'
							: selling
								? 'Здесь появятся добытые ресурсы и драгоценные камни из вашего инвентаря.'
								: 'Загляните в Амбар позже.'
					}}
				</p>
				<button v-if="search.trim()" type="button" class="ui-button" @click="search = ''">Сбросить поиск</button>
			</div>
		</section>

		<template #footer>
			<GameIcon name="book" />
			<span>Наведите на изображение предмета или нажмите на него, чтобы посмотреть характеристики.</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import MovementLink from '~/components/City/MovementLink.vue';
	import useLocation from '~/composables/useLocation.js';

	import { computed, ref, watch } from 'vue';
	import { Link, useForm } from '@inertiajs/vue3';
	import { escape } from 'lodash-es';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import CatalogItem from '~/components/City/Shop/CatalogItem.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	const location = useLocation();

	const props = defineProps({
		page: { type: Object, required: true },
	});

	const state = useState();
	const user = computed(() => state.user);
	const selling = computed(() => props.page.tab === 'resources');
	const tabUrl = computed(() => location.value.url + '/' + props.page.tab);
	const search = ref('');
	const form = useForm({ id: null });
	const filteredItems = computed(() => {
		const query = search.value.trim().toLocaleLowerCase('ru');

		return props.page.items.filter(entry => entry.item.title.toLocaleLowerCase('ru').includes(query));
	});

	watch(() => props.page.tab, () => {
		search.value = '';
		form.clearErrors();
	});

	function platinum(entry) {
		return selling.value ? entry.item.price_type === 1 : entry.item.credits > 0;
	}

	function trade(entry) {
		if (form.processing) return;

		const action = selling.value ? 'Сдать' : 'Купить';
		const actionId = selling.value ? 'sell' : 'buy';
		const itemId = selling.value ? entry.item.id : entry.id;
		const url = location.value.actions[actionId];
		const message = action + ' «' + entry.item.title + '» за ' + entry.price + (platinum(entry) ? ' пл.' : ' зол.') + '?';

		openConfirmModal(action + ' предмет', escape(message), [
			{ title: 'Отмена' },
			{
				title: action,
				handler() {
					if (form.processing) return;

					form.id = itemId;
					form.post(url, { preserveScroll: true });
				},
			},
		]);
	}
</script>
