<template>
	<section>
		<h2 class="mb-3 font-bold">Подарки</h2>
		<div v-if="gifts.length" class="flex flex-wrap gap-3">
			<Popper v-for="gift in gifts" :key="gift.id">
				<img :src="gift.image" :alt="gift.title" class="max-h-20 max-w-20" loading="lazy" />
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
		<p v-else>Подарков пока нет.</p>
		<Link v-if="hasMore" :href="'/info/' + personId + '?prizes=1'" class="mt-3 block underline">Посмотреть все подарки</Link>
	</section>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import Popper from '~/components/Popper.vue';

	defineProps({
		gifts: Array,
		hasMore: Boolean,
		personId: Number,
	});
</script>
