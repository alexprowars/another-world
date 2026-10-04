<template>
	<ContentBlock title="Рынок">
		<template #actions>
			<MovementLink :to="location.exit" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="location.url + '/sell'" class="ui-icon-button" title="Продать предметы">
				<GameIcon name="coins" />
			</Link>
			<Link :href="refreshUrl" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<p v-if="page.message" class="ui-notice ui-notice--blue" role="status" v-html="page.message"></p>
		<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
			<p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
		</div>
		<nav class="ui-tabs ui-tabs--stacked">
			<Link class="ui-tab" :href="location.url + '/buy'" :class="{ 'is-active': page.tab === 'buy' }">
				<GameIcon name="coins" />
				Купить
			</Link>
			<Link class="ui-tab" :href="location.url + '/sell'" :class="{ 'is-active': page.tab === 'sell' }">
				<GameIcon name="transfer" />
				Выставить предмет
			</Link>
			<Link class="ui-tab" :href="location.url + '/my-items'" :class="{ 'is-active': page.tab === 'my-items' }">
				<GameIcon name="armor" />
				Мои товары
			</Link>
		</nav>
		<div class="storefront-layout" :class="{ 'storefront-layout--inventory': page.tab !== 'buy' }">
			<nav v-if="page.tab === 'buy'" class="ui-menu storefront-departments">
				<Link :href="location.url + '/buy'" class="ui-menu-link" :class="{ 'is-active': page.section === 0 }">
					Новые поступления
					<GameIcon name="forward" />
				</Link>
				<section v-for="group in groups" :key="group.title" class="storefront-department-group">
					<h2>{{ group.title }}</h2>
					<div class="ui-menu-list storefront-department-links">
						<Link
							v-for="[id, title] in group.sections"
							:key="id"
							:href="location.url + '/buy?section=' + id"
							class="ui-menu-link"
							:class="{ 'is-active': page.section === id }"
						>
							{{ title }}
						</Link>
					</div>
				</section>
			</nav>
			<section class="storefront-catalog">
				<header class="storefront-catalog-header">
					<h2>
						{{ sectionTitle }}
						<span class="ui-badge">{{ page.items.length }}</span>
					</h2>
				</header>
				<p class="storefront-description">
					{{
						page.tab === 'sell'
							? 'Укажите цену в золоте и выставьте предмет на продажу.'
							: page.tab === 'my-items'
								? 'Здесь собраны ваши предложения. Предмет можно снять с продажи.'
								: 'Снаряжение и припасы от других игроков. Все покупки оплачиваются золотом.'
					}}
				</p>
				<div v-if="page.items.length" class="storefront-grid">
					<CatalogItem v-for="entry in page.items" :key="page.tab + ':' + entry.item.id" :item="entry.item" :player="user" inventory-item>
						<template v-if="page.tab !== 'sell'" #details>
							<p class="service-item-note">
								Продавец:
								<strong>{{ entry.seller }}</strong>
								<span v-if="entry.is_own" class="ui-badge">Ваш товар</span>
							</p>
						</template>
						<template v-if="page.tab !== 'sell'" #price>
							<div class="storefront-item-price">
								<span>Цена продавца</span>
								<strong>
									<img src="/assets/images/currencies/gold.png" alt="" />
									{{ entry.price }}
									<small>зол.</small>
								</strong>
							</div>
						</template>
						<template #actions>
							<form v-if="page.tab === 'sell'" class="service-item-actions" @submit.prevent="sell(entry)">
								<label class="service-field">
									<span>Цена, зол.</span>
									<input
										class="ui-input"
										v-model="prices[entry.item.id]"
										type="text"
										inputmode="decimal"
										required
										maxlength="13"
										:placeholder="String(entry.min_price)"
										:disabled="form.processing"
									/>
								</label>
								<p class="service-hint">Минимум: {{ entry.min_price }} зол.</p>
								<button type="submit" class="ui-button" :disabled="form.processing">Выставить на продажу</button>
							</form>
							<button
								v-else
								type="button"
								class="ui-button"
								:class="{ 'ui-button--secondary': entry.is_own }"
								:disabled="form.processing"
								@click="confirmAction(entry)"
							>
								{{ entry.is_own ? 'Снять с продажи' : 'Купить' }}
							</button>
						</template>
					</CatalogItem>
				</div>
				<div v-else class="ui-empty" role="status">
					<GameIcon name="armor" />
					<h3>
						{{
							page.tab === 'sell'
								? 'Нет вещей для продажи'
								: page.tab === 'my-items'
									? 'У вас пока нет товаров на рынке'
									: 'В этом разделе пока нет товаров'
						}}
					</h3>
					<p>
						{{
							page.tab === 'sell'
								? 'Здесь появятся предметы из инвентаря, которые можно продать.'
								: 'Выберите другой раздел или загляните позже.'
						}}
					</p>
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
	import MovementLink from '~/components/City/MovementLink.vue';
	import useLocation from '~/composables/useLocation.js';

	import { computed, reactive } from 'vue';
	import { Link, useForm, usePage } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import CatalogItem from '~/components/City/Shop/CatalogItem.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	const location = useLocation();
	const inertiaPage = usePage();
	const refreshUrl = computed(() => inertiaPage.url);

	const props = defineProps({
		page: Object
	});

	const state = useState();
	const user = computed(() => state.user);

	const prices = reactive({});

	const form = useForm({
		id: null,
		price: null
	});

	const groups = [
		{ title: 'Оружие', sections: [[1, 'Оружие']] },
		{
			title: 'Амуниция',
			sections: [
				[8, 'Шлемы'],
				[2, 'Доспехи'],
				[25, 'Штаны'],
				[10, 'Нарукавники'],
				[9, 'Перчатки'],
				[5, 'Щиты'],
				[7, 'Пояса'],
				[6, 'Обувь'],
				[11, 'Рубахи'],
			],
		},
		{
			title: 'Ювелирные украшения',
			sections: [
				[4, 'Ожерелья'],
				[3, 'Кольца'],
				[24, 'Серьги'],
			],
		},
		{
			title: 'Магия',
			sections: [
				[12, 'Свитки'],
				[16, 'Зелья'],
			],
		},
		{
			title: 'Прочее',
			sections: [
				[14, 'Эликсиры'],
				[21, 'Документы'],
				[18, 'Инструменты'],
				[19, 'Ресурсы'],
				[20, 'Драгоценные камни'],
				[26, 'Магические предметы'],
			],
		},
	];
	const sectionTitle = computed(() => {
		if (props.page.tab === 'sell') {
			return 'Выставить предмет на продажу';
		}

		if (props.page.tab === 'my-items') {
			return 'Мои товары';
		}

		return groups.flatMap(group => group.sections)
			.find(([id]) => id === props.page.section)?.[1] || 'Новые поступления';
	});

	function submit(action, id, price = null) {
		if (form.processing) {
			return;
		}

		form.id = id;
		form.price = price;

		form.clearErrors();

		const url = action === 'buy'
			? location.value.actions.buy + '?section=' + props.page.section
			: location.value.actions[action];

		form.post(url, {
			preserveScroll: true
		});
	}

	function sell(entry) {
		submit('sell', entry.item.id, prices[entry.item.id]);
	}

	function confirmAction(entry) {
		openConfirmModal('Подтвердите действие', entry.is_own ? 'Снять предмет с продажи?' : 'Купить предмет за ' + entry.price + ' зол.?', [
			{ title: 'Нет' },
			{
				title: 'Да',
				handler() {
					submit(entry.is_own ? 'withdraw' : 'buy', entry.id);
				},
			},
		]);
	}
</script>
