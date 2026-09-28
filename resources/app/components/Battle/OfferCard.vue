<template>
	<article class="ui-panel arena-offer">
		<header class="arena-offer-heading">
			<h3>Заявка №{{ offer.id }}</h3>
			<div class="arena-countdown">
				<GameIcon name="hourglass" />
				<span>{{ offer.type === 1 ? 'Действует ещё:' : 'До начала:' }}</span>
				<strong>{{ $formatTime(secondsLeft, ':', true) }}</strong>
			</div>
		</header>

		<div class="arena-offer-body">
			<div v-if="offer.type === 3" class="arena-team">
				<div class="arena-team-heading">
					<h4>Участники</h4>
					<span>{{ offer.members.length }} / {{ offer.capacity }}</span>
				</div>
				<div class="arena-members">
					<Name v-for="member in offer.members" :key="member.id" :player="member.user" />
				</div>
			</div>
			<div v-else class="arena-teams">
				<div v-for="side in [0, 1]" :key="side" class="arena-team">
					<div class="arena-team-heading">
						<h4>{{ offer.type === 1 ? (side === 0 ? 'Автор заявки' : 'Соперник') : `Команда №${side + 1}` }}</h4>
						<span v-if="offer.type === 2">{{ team(side).length }} / {{ offer.capacity }}</span>
					</div>
					<div class="arena-members">
						<Name v-for="member in team(side)" :key="member.id" :player="member.user" />
						<p v-if="!team(side).length" class="arena-hint">Ожидаем участников</p>
					</div>
					<button
						v-if="canJoin && offer.type === 2"
						type="button"
						class="ui-button"
						:disabled="busy || team(side).length >= offer.capacity"
						@click="emit('join', side)"
					>
						Вступить в команду №{{ side + 1 }}
					</button>
				</div>
			</div>

			<div class="arena-offer-details">
				<span>
					Таймаут:
					<strong>{{ offer.timeout / 60 }} мин.</strong>
				</span>
				<span v-if="offer.type !== 1">
					Уровни:
					<strong>{{ offer.min_level }}–{{ offer.max_level }}</strong>
				</span>
				<span v-if="offer.is_blood" class="ui-badge ui-badge--danger">Кровавый бой</span>
				<span v-if="!offer.use_weapons" class="ui-badge">Рукопашный бой</span>
			</div>
			<p v-if="offer.comment" class="arena-offer-comment">{{ offer.comment }}</p>
		</div>
		<footer v-if="(canJoin && offer.type !== 2) || $slots.default" class="arena-offer-footer">
			<button
				v-if="canJoin && offer.type !== 2"
				type="button"
				class="ui-button"
				:disabled="busy || (offer.type === 3 && offer.members.length >= offer.capacity)"
				@click="emit('join', 0)"
			>
				<GameIcon name="swords" />
				Принять вызов
			</button>
			<slot />
		</footer>
	</article>
</template>

<script setup>
	import Name from '~/components/Person/Name.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import { computed, watch } from 'vue';
	import { useNow } from '@vueuse/core';

	const props = defineProps({
		offer: { type: Object, required: true },
		canJoin: Boolean,
		busy: Boolean,
	});
	const emit = defineEmits(['join', 'refresh']);
	const team = side => props.offer.members.filter(member => member.side === side);
	const now = useNow({ interval: 1000 });
	const secondsLeft = computed(() => Math.max(0, Math.ceil((Date.parse(props.offer.readyAt) - now.value.getTime()) / 1000)));

	watch(secondsLeft, seconds => {
		if (seconds === 0) emit('refresh');
	});
</script>
