<template>
	<ContentBlock :title="title" class="storefront">
		<template #actions>
			<Link :href="backHref" class="ui-icon-button" title="Вернуться в город">
				<GameIcon name="back" />
			</Link>
			<Link :href="sectionUrl" class="ui-icon-button" title="Обновить отдел">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<div v-if="inventoryAction" class="storefront-toolbar">
			<nav class="ui-tabs storefront-modes">
				<Link class="ui-tab" href="/map" :class="{ 'is-active': !inventoryMode }">
					<GameIcon :name="catalogIcon" />
					Купить
				</Link>
				<Link class="ui-tab" :href="'/map?section=' + inventoryAction.section" :class="{ 'is-active': inventoryMode }">
					<GameIcon :name="inventoryAction.icon" />
					{{ inventoryAction.label }}
				</Link>
			</nav>
		</div>

		<p v-if="page.message" class="ui-notice ui-notice--blue" role="status" v-html="page.message"></p>

		<div class="storefront-layout" :class="{ 'storefront-layout--inventory': inventoryMode }">
			<nav v-if="!inventoryMode" class="ui-menu storefront-departments">
				<Link href="/map" class="ui-menu-link" :class="{ 'is-active': page.section === 0 }">
					Все товары
					<GameIcon name="forward" />
				</Link>
				<section v-for="group in departments" :key="group.title" class="storefront-department-group">
					<h2>
						<GameIcon :name="group.icon" />
						{{ group.title }}
					</h2>
					<div class="ui-menu-list storefront-department-links">
						<Link
							v-for="department in group.items"
							:key="department.id"
							:href="'/map?section=' + department.id"
							class="ui-menu-link"
							:class="{ 'is-active': page.section === department.id }"
						>
							{{ department.title }}
						</Link>
					</div>
				</section>
			</nav>

			<section class="storefront-catalog">
				<header class="storefront-catalog-header">
					<div>
						<p class="storefront-eyebrow">{{ inventoryMode ? 'Ваш инвентарь' : 'Торговые ряды' }}</p>
						<h2 id="storefront-heading">
							{{ sectionTitle }}
							<span class="ui-badge">{{ filteredItems.length }}</span>
						</h2>
					</div>
					<label class="storefront-search">
						<span>Поиск в {{ inventoryMode ? 'инвентаре' : 'отделе' }}</span>
						<input class="ui-input" v-model="search" type="search" placeholder="Название предмета" />
					</label>
				</header>
				<p class="storefront-description">
					{{ inventoryMode ? inventoryAction.description : description }}
				</p>
				<label v-if="!inventoryMode && showSuitableFilter" class="storefront-suitable-filter">
					<input v-model="suitableOnly" type="checkbox" />
					Показать подходящие предметы
				</label>
				<div v-if="filteredItems.length" class="storefront-grid">
					<template v-for="item in filteredItems" :key="(inventoryMode ? 'inventory-' : 'buy-') + item.id">
						<slot name="item" :item="item" :player="user" :processing="processing">
							<CatalogItem :item="item" :player="user" :selling="inventoryMode" :processing="processing" @trade="trade(item)" />
						</slot>
					</template>
				</div>
				<div v-else class="ui-empty" role="status">
					<GameIcon :name="inventoryMode ? inventoryAction.icon : catalogIcon" />
					<h3>{{ filtering ? 'Ничего не найдено' : inventoryMode ? inventoryAction.emptyTitle : 'В этом отделе пока нет товаров' }}</h3>
					<p>
						{{
							filtering
								? showSuitableFilter && !inventoryMode
									? 'Попробуйте изменить название или отключить фильтр подходящих предметов.'
									: 'Попробуйте другое название предмета.'
								: inventoryMode
									? inventoryAction.emptyDescription
									: 'Загляните в соседний отдел или вернитесь позже.'
						}}
					</p>
					<button v-if="filtering" type="button" class="ui-button" @click="resetFilters">Сбросить фильтры</button>
					<Link v-else-if="!inventoryMode && page.section !== 0" href="/map" class="ui-button">Все товары</Link>
				</div>
			</section>
		</div>
		<template #footer>
			<GameIcon name="book" />
			<span>Наведите на изображение предмета или нажмите на него, чтобы посмотреть характеристики.</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import { computed, ref, watch } from 'vue';
	import { Link, router } from '@inertiajs/vue3';
	import { escape } from 'lodash-es';
	import { useLocalStorage } from '@vueuse/core';
	import { getUnmetItemRequirements } from '~/utils/itemRequirements.js';
	import CatalogItem from '~/components/City/Shop/CatalogItem.vue';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	const props = defineProps({
		page: { type: Object, required: true },
		title: { type: String, required: true },
		backHref: { type: String, required: true },
		departments: { type: Array, required: true },
		description: { type: String, default: 'Оружие, снаряжение и припасы для ваших приключений.' },
		catalogIcon: { type: String, default: 'swords' },
		showSuitableFilter: { type: Boolean, default: true },
		inventoryAction: {
			type: Object,
			default: () => ({
				section: 100,
				label: 'Продать',
				icon: 'coins',
				title: 'Продажа предметов',
				description: 'Выберите предмет, который хотите продать магазину.',
				emptyTitle: 'Нет предметов для продажи',
				emptyDescription: 'Здесь появятся вещи из инвентаря, которые можно продать.',
			}),
		},
	});

	const state = useState();
	const user = computed(() => state.user);
	const search = ref('');
	const processing = ref(false);
	const suitableOnly = useLocalStorage('shop.suitableOnly', false, { initOnMounted: true });
	const inventoryMode = computed(() => !!props.inventoryAction && props.page.section === props.inventoryAction.section);
	const filtering = computed(() => !!search.value.trim() || (!inventoryMode.value && props.showSuitableFilter && suitableOnly.value));
	const sectionUrl = computed(() => '/map?section=' + props.page.section);
	const sectionTitle = computed(() =>
		inventoryMode.value
			? props.inventoryAction.title
			: props.departments.flatMap(group => group.items).find(item => item.id === props.page.section)?.title || 'Все товары',
	);
	const filteredItems = computed(() => {
		const query = search.value.trim().toLocaleLowerCase('ru');
		return props.page.items.filter(item => {
			const product = inventoryMode.value ? item : item.item;
			if (!product.title.toLocaleLowerCase('ru').includes(query)) return false;
			if (inventoryMode.value || !props.showSuitableFilter || !suitableOnly.value) return true;

			return product.wearout > 0 && ![15, 16, 19, 20, 21, 22, 23].includes(product.type) && getUnmetItemRequirements(product, user.value).length === 0;
		});
	});

	watch(
		() => props.page.section,
		() => {
			search.value = '';
		},
	);

	function resetFilters() {
		search.value = '';
		if (props.showSuitableFilter && !inventoryMode.value) {
			suitableOnly.value = false;
		}
	}

	function trade(item) {
		if (processing.value) return;
		const isSale = inventoryMode.value;
		const action = isSale ? 'Продать' : 'Купить';
		const title = isSale ? item.title : item.item.title;
		const price = isSale ? ' за ' + item.price_sell + (item.price_type === 1 ? ' пл.' : ' зол.') : '';
		const url = sectionUrl.value;

		openConfirmModal(action + ' предмет', escape(action + ' «' + title + '»' + price + '?'), [
			{ title: 'Отмена' },
			{
				title: action,
				handler() {
					if (processing.value) return;
					processing.value = true;
					router.post(
						url,
						{ [isSale ? 'sell' : 'buy']: item.id },
						{
							preserveScroll: true,
							onFinish: () => {
								processing.value = false;
							},
						},
					);
				},
			},
		]);
	}
</script>
