<template>
	<ContentBlock title="Рынок">
		<div class="flex flex-wrap">
			<div class="w-9/12 flex-none xl:w-10/12 pr-4">
				<p v-if="page.message" class="message bg-red-100 text-red-700 mb-4" v-html="page.message"></p>
				<p v-for="(error, key) in form.errors" :key="key" class="text-red-700 mb-2">{{ error }}</p>
				<h3 class="font-bold mb-4">{{ sectionTitle }}</h3>

				<div v-if="page.items.length" class="shop-items grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
					<SellItem v-for="entry in page.items" :key="page.section + ':' + entry.item.id" :item="entry.item">
						<template #actions>
							<form v-if="page.section === 100" class="mt-2" @submit.prevent="sell(entry)">
								<label class="text-xs">
									Цена, зол.
									<input v-model="prices[entry.item.id]" type="text" inputmode="decimal" required maxlength="13" class="w-full" :placeholder="String(entry.min_price)">
								</label>
								<div class="text-xs">Минимум: {{ entry.min_price }} зол.</div>
								<button type="submit" class="button mt-2" :disabled="form.processing">Выставить</button>
							</form>
							<button v-else type="button" class="button mt-2" :disabled="form.processing" @click="confirmAction(entry)">
								{{ entry.is_own ? 'Снять с продажи' : 'Купить' }}
							</button>
						</template>
						<template v-if="page.section !== 100" #details>
							<div>Цена продавца: <b>{{ entry.price }} зол.</b></div>
							<div>Продавец: <b>{{ entry.seller }}</b></div>
						</template>
					</SellItem>
				</div>
				<p v-else>{{ page.section === 100 ? 'Нет вещей для продажи.' : 'В этом разделе нет товаров.' }}</p>
			</div>
			<div class="w-3/12 flex-none xl:w-2/12">
				<div class="shopnav">
					<Link href="/map?section=100"><img src="/assets/images/images/shop_sale.gif" alt="Продать предметы"></Link>
					<Link :href="'/map?section=' + page.section"><img src="/assets/images/images/refresh.gif" alt="Обновить"></Link>
					<Link href="/map/change/20"><img src="/assets/images/images/back.gif" alt="Вернуться"></Link>
				</div>
				<div class="shopotdels text-center">
					<Link href="/map" class="block">Новые поступления</Link>
					<Link href="/map?section=100" class="block">Выставить предмет</Link>
					<Link href="/map?section=101" class="block">Мои товары</Link>
				</div>
				<div v-for="group in groups" :key="group.title" class="shopotdels text-center">
					<b>{{ group.title }}</b>
					<div class="grid grid-cols-2 gap-1 mt-1">
						<Link v-for="[id, title] in group.sections" :key="id" :href="'/map?section=' + id" :class="{ 'font-bold': page.section === id }">{{ title }}</Link>
					</div>
				</div>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { computed, reactive } from 'vue';
	import { Link, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import SellItem from '~/components/City/Shop/SellItem.vue';
	import { openConfirmModal } from '~/composables/useModals.js';

	const props = defineProps({ page: Object });
	const prices = reactive({});
	const form = useForm({ action: '', id: null, price: null });
	const groups = [
		{ title: 'Оружие', sections: [[1, 'Оружие']] },
		{ title: 'Амуниция', sections: [[8, 'Шлемы'], [2, 'Доспехи'], [25, 'Штаны'], [10, 'Нарукавники'], [9, 'Перчатки'], [5, 'Щиты'], [7, 'Пояса'], [6, 'Обувь'], [11, 'Рубахи']] },
		{ title: 'Ювелирные украшения', sections: [[4, 'Ожерелья'], [3, 'Кольца'], [24, 'Серьги']] },
		{ title: 'Магия', sections: [[12, 'Свитки'], [16, 'Зелья']] },
		{ title: 'Прочее', sections: [[14, 'Эликсиры'], [21, 'Документы'], [18, 'Инструменты'], [19, 'Ресурсы'], [20, 'Драгоценные камни'], [26, 'Магические предметы']] },
	];
	const sectionTitle = computed(() => {
		if (props.page.section === 100) return 'Выставить предмет на продажу';
		if (props.page.section === 101) return 'Мои товары';
		return groups.flatMap(group => group.sections).find(([id]) => id === props.page.section)?.[1] || 'Новые поступления';
	});

	function submit(action, id, price = null) {
		if (form.processing) return;
		form.action = action;
		form.id = id;
		form.price = price;
		form.clearErrors();
		form.post('/map?section=' + props.page.section, { preserveScroll: true });
	}

	function sell(entry) {
		submit('sell', entry.item.id, prices[entry.item.id]);
	}

	function confirmAction(entry) {
		openConfirmModal('Подтвердите действие', entry.is_own ? 'Снять предмет с продажи?' : 'Купить предмет за ' + entry.price + ' зол.?', [
			{ title: 'Нет' },
			{ title: 'Да', handler() { submit(entry.is_own ? 'withdraw' : 'buy', entry.id); } },
		]);
	}
</script>