<template>
	<ContentBlock title="Поединки на арене">
		<div class="mb-4 flex justify-end gap-3">
			<button type="button" :disabled="refreshing || actionForm.processing || offerForm?.processing" @click="refresh">Обновить</button>
			<Link href="/map">Вернуться</Link>
		</div>

		<nav v-if="!page.currentOffer" class="mb-4 flex flex-wrap justify-center gap-4" aria-label="Тип боя">
			<Link v-for="type in types" :key="type.id" :href="`/battle?battle_type=${type.id}`" :class="{ 'font-bold underline': page.battleType === type.id }" :aria-current="page.battleType === type.id ? 'page' : undefined">
				{{ type.title }}
			</Link>
		</nav>

		<div v-if="Object.keys(actionForm.errors).length" class="mb-4 text-red-600" role="alert">
			<p v-for="(error, field) in actionForm.errors" :key="field">{{ error }}</p>
		</div>
		<div v-if="page.offerError" class="mb-4 text-center space-y-3">
			<p class="text-red-600">{{ page.offerError }}</p>
			<button v-if="page.canTeleport" type="button" class="btn btn-primary" :disabled="actionForm.processing" @click="act('teleport')">Переместиться на арену</button>
		</div>

		<div class="space-y-4">
			<section v-if="page.currentOffer" class="space-y-2">
				<h3 class="font-bold">Ваша заявка</h3>
				<OfferCard :offer="page.currentOffer" @refresh="refresh">
					<div v-if="page.battleType === 1" class="flex flex-wrap gap-2">
						<template v-if="page.currentSide === 0 && page.currentOffer.members.length === 2">
							<button type="button" class="btn btn-primary" :disabled="actionForm.processing" @click="act('start')">Начать бой</button>
							<button type="button" class="btn btn-primary" :disabled="actionForm.processing" @click="act('dismiss')">Отказать сопернику</button>
						</template>
						<button type="button" class="btn btn-primary" :disabled="actionForm.processing" @click="act('withdraw')">{{ page.currentSide === 0 ? 'Отозвать заявку' : 'Отозвать вызов' }}</button>
					</div>
					<p v-else class="text-slate-600">Ожидаем начала боя. Список обновляется автоматически.</p>
				</OfferCard>
			</section>
			<OfferForm ref="offerForm" v-else-if="!page.offerError" :key="page.battleType" :battle-type="page.battleType"/>

			<section v-if="!page.offerError" class="space-y-3">
				<h3 class="font-bold">Открытые заявки</h3>
				<p v-if="!page.offers.length" class="py-4 text-center text-slate-500">Открытых заявок пока нет.</p>
				<OfferCard v-for="offer in page.offers" :key="offer.id" :offer="offer" :can-join="!page.currentOffer" :busy="actionForm.processing" @join="join(offer, $event)" @refresh="refresh"/>
			</section>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { onBeforeUnmount, onMounted, ref } from 'vue';
	import { Link, router, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import OfferCard from '~/components/Battle/OfferCard.vue';
	import OfferForm from '~/components/Battle/OfferForm.vue';
	import { openConfirmModal } from '~/composables/useModals.js';

	const props = defineProps({ page: Object });
	const types = [{ id: 1, title: 'PvP' }, { id: 2, title: 'Групповые' }, { id: 3, title: 'Хаотические' }];
	const actionForm = useForm({ action: '' });
	const refreshing = ref(false);
	const offerForm = ref(null);
	let refreshTimer;

	onMounted(() => {
		refreshTimer = setInterval(refresh, 15000);
	});
	onBeforeUnmount(() => clearInterval(refreshTimer));

	function refresh() {
		if (refreshing.value || actionForm.processing || offerForm.value?.processing) return;
		refreshing.value = true;
		router.reload({ onFinish: () => { refreshing.value = false; } });
	}

	function act(action, extra = {}) {
		if (actionForm.processing) return;
		actionForm.action = action;
		actionForm.transform((data) => ({ ...data, battle_type: props.page.battleType, ...extra })).post('/battle', { preserveScroll: true });
	}

	function join(offer, side) {
		openConfirmModal('Подтвердите действие', 'Вы действительно хотите принять эту заявку?', [
			{ title: 'Нет' },
			{ title: 'Да', handler: () => act('take', { offer: offer.id, battle_side: side }) },
		]);
	}
</script>
