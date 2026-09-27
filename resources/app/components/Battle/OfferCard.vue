<template>
	<article class="rounded border border-slate-300 p-3 space-y-3">
		<div class="flex flex-wrap justify-between gap-2">
			<b>Заявка №{{ offer.id }}</b>
			<div class="flex gap-2">
				<span>{{ offer.type === 1 ? 'Действует ещё:' : 'До начала:' }}</span>
				<span>{{ $formatTime(secondsLeft, ':', true) }}</span>
			</div>
		</div>

		<div v-if="offer.type === 3" class="flex flex-wrap gap-3">
			<Name v-for="member in offer.members" :key="member.id" :player="member.user"/>
		</div>
		<div v-else class="grid gap-3 sm:grid-cols-2">
			<div v-for="side in [0, 1]" :key="side" class="space-y-2">
				<b>{{ offer.type === 1 ? (side === 0 ? 'Автор заявки' : 'Соперник') : `Команда №${side + 1}` }}</b>
				<div v-for="member in team(side)" :key="member.id"><Name :player="member.user"/></div>
				<p v-if="!team(side).length" class="text-slate-500">Ожидаем участников</p>
				<p v-if="offer.type === 2">Бойцов: {{ team(side).length }} / {{ offer.capacity }}</p>
				<button v-if="canJoin && offer.type === 2" type="button" class="btn btn-primary" :disabled="busy || team(side).length >= offer.capacity" @click="emit('join', side)">
					Вступить в команду №{{ side + 1 }}
				</button>
			</div>
		</div>

		<div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
			<span>Таймаут: {{ offer.timeout / 60 }} мин.</span>
			<span v-if="offer.type !== 1">Уровни: {{ offer.min_level }}–{{ offer.max_level }}</span>
			<span v-if="offer.type === 3">Участников: {{ offer.members.length }} / {{ offer.capacity }}</span>
			<span v-if="offer.is_blood" class="text-red-600">Кровавый бой</span>
			<span v-if="!offer.use_weapons">Рукопашный бой</span>
		</div>
		<p v-if="offer.comment" class="break-words text-blue-700">{{ offer.comment }}</p>
		<button v-if="canJoin && offer.type !== 2" type="button" class="btn btn-primary" :disabled="busy || (offer.type === 3 && offer.members.length >= offer.capacity)" @click="emit('join', 0)">
			Принять вызов
		</button>
		<slot/>
	</article>
</template>

<script setup>
	import Name from '~/components/Person/Name.vue';
	import { computed, watch } from 'vue';
	import { useNow } from '@vueuse/core';

	const props = defineProps({
		offer: { type: Object, required: true },
		canJoin: Boolean,
		busy: Boolean,
	});
	const emit = defineEmits(['join', 'refresh']);
	const team = (side) => props.offer.members.filter((member) => member.side === side);
	const now = useNow({ interval: 1000 });
	const secondsLeft = computed(() => Math.max(0, Math.ceil((Date.parse(props.offer.readyAt) - now.value.getTime()) / 1000)));

	watch(secondsLeft, (seconds) => {
		if (seconds === 0) emit('refresh');
	});
</script>
