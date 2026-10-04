<template>
	<ContentBlock title="Ломбард">
		<template #actions>
			<MovementLink :to="location.exit" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="location.url + '/' + page.tab" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<p v-if="page.message" class="ui-notice ui-notice--blue" role="status" v-html="page.message"></p>
		<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
			<p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
		</div>
		<nav class="ui-tabs ui-tabs--stacked">
			<Link class="ui-tab" :href="location.url + '/my-items'" :class="{ 'is-active': page.tab === 'my-items' }">
				<GameIcon name="armor" />
				Мои вещи в ломбарде
			</Link>
			<Link class="ui-tab" :href="location.url + '/deposit'" :class="{ 'is-active': page.tab === 'deposit' }">
				<GameIcon name="coins" />
				Заложить предмет
			</Link>
		</nav>
		<div class="service-terms">
			<div>
				<span>Вы получите при залоге</span>
				<strong>{{ page.deposit_percent }}% гос. цены</strong>
			</div>
			<div>
				<span>Стоимость выкупа</span>
				<strong>{{ page.withdraw_percent }}% гос. цены</strong>
			</div>
			<p>Расчёты проводятся в валюте предмета.</p>
		</div>
		<header class="service-heading">
			<h2>
				{{ page.tab === 'deposit' ? 'Предметы для залога' : 'Предметы на хранении' }}
				<span class="ui-badge">{{ page.items.length }}</span>
			</h2>
			<p>{{ page.tab === 'deposit' ? 'Выберите вещь, чтобы получить деньги под залог.' : 'Выкупите предмет, чтобы вернуть его в инвентарь.' }}</p>
		</header>
		<div v-if="page.items.length" class="storefront-grid">
			<CatalogItem v-for="entry in page.items" :key="entry.item.id" :item="entry.item" :player="user" inventory-item>
				<template v-if="page.tab === 'deposit'" #details>
					<p class="service-item-note">
						Выкуп:
						<strong>{{ entry.withdraw_price }} {{ currency(entry) }}</strong>
					</p>
				</template>
				<template #price>
					<div class="storefront-item-price">
						<span>{{ page.tab === 'deposit' ? 'Вы получите' : 'Стоимость выкупа' }}</span>
						<strong>
							<img :src="'/assets/images/currencies/' + (entry.item.price_type === 1 ? 'platinum' : 'gold') + '.png'" alt="" />
							{{ page.tab === 'deposit' ? entry.deposit_price : entry.withdraw_price }}
							<small>{{ currency(entry) }}</small>
						</strong>
					</div>
				</template>
				<template #actions>
					<button type="button" class="ui-button" :disabled="form.processing" @click="confirmAction(entry)">
						{{ page.tab === 'deposit' ? 'Заложить' : 'Выкупить' }}
					</button>
				</template>
			</CatalogItem>
		</div>
		<div v-else class="ui-empty" role="status">
			<GameIcon name="armor" />
			<h3>{{ page.tab === 'deposit' ? 'Нет вещей для залога' : 'В ломбарде нет ваших вещей' }}</h3>
			<p>
				{{
					page.tab === 'deposit'
						? 'Здесь появятся доступные для залога предметы из инвентаря.'
						: 'Вы можете оставить предмет в залог и выкупить его позже.'
				}}
			</p>
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

	import { computed } from 'vue';
	import { Link, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import CatalogItem from '~/components/City/Shop/CatalogItem.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	const location = useLocation();

	const props = defineProps({
		page: Object
	});

	const state = useState();
	const user = computed(() => state.user);

	const form = useForm({
		id: null
	});

	function currency(entry) {
		return entry.item.price_type === 1 ? 'пл.' : 'зол.';
	}

	function confirmAction(entry) {
		const deposit = props.page.tab === 'deposit';
		const message = deposit
			? 'Заложить предмет и получить ' +
				entry.deposit_price +
				' ' +
				currency(entry) +
				'? Выкуп будет стоить ' +
				entry.withdraw_price +
				' ' +
				currency(entry)
			: 'Выкупить предмет за ' + entry.withdraw_price + ' ' + currency(entry) + '?';

		openConfirmModal('Подтвердите действие', message, [
			{ title: 'Нет' },
			{
				title: 'Да',
				handler() {
					if (form.processing) return;
					const action = deposit ? 'deposit' : 'withdraw';
					form.id = entry.item.id;
					form.clearErrors();
					form.post(location.value.actions[action], { preserveScroll: true });
				},
			},
		]);
	}
</script>
