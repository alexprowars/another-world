<template>
	<section class="person-info-panel person-info-gifts ui-panel">
		<h2 class="person-info-panel-heading"><GameIcon name="gift" />Подарки</h2>
		<div v-if="gifts.length" class="person-info-gift-list">
			<Popper v-for="gift in gifts" :key="gift.id" class="person-info-gift">
				<button type="button" class="person-info-gift-button" :title="gift.title">
					<img :src="gift.image" :alt="gift.title" loading="lazy" />
				</button>
				<template #content>
					<div class="max-w-64 space-y-2 break-words text-sm">
						<p class="font-bold">{{ gift.title }}</p>
						<p>От: {{ gift.sender }}</p>
						<p v-if="gift.text" class="whitespace-pre-line">{{ gift.text }}</p>
						<p v-if="gift.date">{{ $formatDate(gift.date, 'DD.MM.YYYY') }}</p>
					</div>
				</template>
			</Popper>
		</div>
		<p v-else class="person-info-empty">Подарков пока нет.</p>
		<div v-if="hasMore" class="person-info-panel-footer">
			<Link :href="'/info/' + personId + '?prizes=1'" class="ui-button ui-button--compact ui-button--secondary">Посмотреть все подарки</Link>
		</div>
	</section>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import Popper from '~/components/Popper.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		gifts: Array,
		hasMore: Boolean,
		personId: Number,
	});
</script>
