<template>
	<ContentBlock title="Поединки на арене" class="arena-offers">
		<template #actions>
			<Link href="/map" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</Link>
			<button type="button" :disabled="refreshing || actionForm.processing || offerModalOpen" @click="refresh" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</button>
		</template>

		<nav v-if="!page.currentOffer" class="ui-tabs arena-tabs">
			<Link
				v-for="type in types"
				:key="type.id"
				:href="`/battle?battle_type=${type.id}`"
				preserve-state
				preserve-scroll
				class="ui-tab"
				:class="{ 'is-active': page.battleType === type.id }"
			>
				<GameIcon :name="type.icon" />
				{{ type.title }}
			</Link>
		</nav>

		<div v-if="Object.keys(actionForm.errors).length" class="ui-notice ui-notice--red" role="alert">
			<p v-for="(error, field) in actionForm.errors" :key="field">{{ error }}</p>
		</div>
		<div v-if="page.offerError" class="ui-notice ui-notice--red" role="alert">
			<p>{{ page.offerError }}</p>
			<button v-if="page.canTeleport" type="button" class="ui-button" :disabled="actionForm.processing" @click="act('teleport')">
				Переместиться на арену
			</button>
		</div>

		<div class="arena-content">
			<section v-if="page.currentOffer" class="arena-section">
				<header class="arena-section-heading">
					<h2>Ваша заявка</h2>
					<span class="arena-status">Ожидание боя</span>
				</header>
				<OfferCard :offer="page.currentOffer" @refresh="refresh">
					<div v-if="page.battleType === 1" class="arena-actions">
						<template v-if="page.currentSide === 0 && page.currentOffer.members.length === 2">
							<button type="button" class="ui-button" :disabled="actionForm.processing" @click="act('start')">Начать бой</button>
							<button type="button" class="ui-button ui-button--secondary" :disabled="actionForm.processing" @click="act('dismiss')">
								Отказать сопернику
							</button>
						</template>
						<button type="button" class="ui-button ui-button--secondary" :disabled="actionForm.processing" @click="act('withdraw')">
							{{ page.currentSide === 0 ? 'Отозвать заявку' : 'Отозвать вызов' }}
						</button>
					</div>
					<p v-else class="arena-hint">Ожидаем начала боя. Список обновляется автоматически.</p>
				</OfferCard>
			</section>

			<section v-if="!page.offerError" class="arena-section">
				<header class="arena-section-heading">
					<h2>
						Открытые заявки
						<span class="arena-count">{{ page.offers.length }}</span>
					</h2>
					<button
						v-if="!page.currentOffer"
						type="button"
						class="ui-button"
						:disabled="refreshing || actionForm.processing || offerModalOpen"
						@click="createOffer"
					>
						<GameIcon name="swords" />
						Новая заявка
					</button>
				</header>
				<div v-if="!page.offers.length" class="ui-empty">
					<GameIcon name="swords" />
					<h3>Открытых заявок пока нет</h3>
					<p>
						{{
							page.currentOffer
								? 'Ваша заявка уже размещена. Дождитесь других участников.'
								: 'Подайте свою заявку или дождитесь вызова от других игроков.'
						}}
					</p>
				</div>
				<OfferCard
					v-for="offer in page.offers"
					:key="offer.id"
					:offer="offer"
					:can-join="!page.currentOffer"
					:busy="actionForm.processing"
					@join="join(offer, $event)"
					@refresh="refresh"
				/>
			</section>
		</div>
		<template #footer>
			<GameIcon name="refresh" />
			<span>Список заявок обновляется автоматически каждые 15 секунд.</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import { onBeforeUnmount, onMounted, ref } from 'vue';
	import { Link, router, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import OfferCard from '~/components/Battle/OfferCard.vue';
	import OfferForm from '~/components/Battle/OfferForm.vue';
	import { openConfirmModal, openPopupModal } from '~/composables/useModals.js';

	const props = defineProps({ page: Object });
	const types = [
		{ id: 1, title: 'PvP', icon: 'swords' },
		{ id: 2, title: 'Групповые', icon: 'users' },
		{ id: 3, title: 'Хаотические', icon: 'shuffle' },
	];
	const actionForm = useForm({ action: '' });
	const refreshing = ref(false);
	const offerModalOpen = ref(false);
	let refreshTimer;

	onMounted(() => {
		refreshTimer = setInterval(refresh, 15000);
	});
	onBeforeUnmount(() => clearInterval(refreshTimer));

	async function createOffer() {
		if (offerModalOpen.value || refreshing.value || actionForm.processing) return;
		offerModalOpen.value = true;
		try {
			await openPopupModal(OfferForm, {
				title: 'Новая заявка · ' + types.find(type => type.id === props.page.battleType).title,
				battleType: props.page.battleType,
			});
		} finally {
			offerModalOpen.value = false;
		}
	}

	function refresh() {
		if (refreshing.value || actionForm.processing || offerModalOpen.value) return;
		refreshing.value = true;
		router.reload({
			onFinish: () => {
				refreshing.value = false;
			},
		});
	}

	function act(action, extra = {}) {
		if (actionForm.processing) return;
		actionForm.action = action;
		actionForm.transform(data => ({ ...data, battle_type: props.page.battleType, ...extra })).post('/battle', { preserveScroll: true });
	}

	function join(offer, side) {
		openConfirmModal('Подтвердите действие', 'Вы действительно хотите принять эту заявку?', [
			{ title: 'Нет' },
			{ title: 'Да', handler: () => act('take', { offer: offer.id, battle_side: side }) },
		]);
	}
</script>
